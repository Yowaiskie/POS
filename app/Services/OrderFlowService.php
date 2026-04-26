<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Room;
use App\Models\RoomSession;
use App\Models\PromoSet;
use App\Models\RoomPricing;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class OrderFlowService
{
    protected $inventoryService;
    protected $billingService;

    public function __construct(InventoryService $inventoryService, RoomBillingService $billingService)
    {
        $this->inventoryService = $inventoryService;
        $this->billingService = $billingService;
    }

    /**
     * Start a room session with optional promo set.
     */
    public function startRoomSession(Room $room, array $data): RoomSession
    {
        return DB::transaction(function () use ($room, $data) {
            $duration = $data['duration'] ?? 1;
            $promoSetId = $data['promo_set_id'] ?? null;
            
            $promoSet = null;
            if ($promoSetId) {
                $promoSet = PromoSet::with('items.menuItem')->find($promoSetId);
                if ($promoSet) {
                    $duration = $promoSet->duration_hours;
                }
            }

            $session = $room->sessions()->create([
                'started_at' => now(),
                'ends_at' => now()->addHours($duration),
                'status' => 'active',
                'promo_duration_hours' => $promoSet ? $promoSet->duration_hours : 0,
            ]);

            // Snapshot pricing config
            $pricingConfig = RoomPricing::first();
            $session->pricing_snapshot = $pricingConfig ? $pricingConfig->toJson() : null;
            $session->save();

            if ($promoSet) {
                $transactionId = 'TRX-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
                
                $order = $session->orders()->create([
                    'order_type' => 'room',
                    'user_id' => auth()->id() ?? 1,
                    'status' => 'open',
                    'transaction_id' => $transactionId,
                    'promo_name' => $promoSet->name,
                    'promo_price' => $promoSet->price,
                ]);

                // Promo Charge
                $order->items()->create([
                    'name' => $promoSet->name . ' (Promo Charge)',
                    'unit_price' => $promoSet->price,
                    'quantity' => 1,
                    'is_stock_deducted' => true, // Charges don't have stock
                ]);

                // Included Items (Immediate Deduction)
                foreach ($promoSet->items as $promoItem) {
                    if ($promoItem->menuItem) {
                        $item = $order->items()->create([
                            'menu_item_id' => $promoItem->menu_item_id,
                            'name' => $promoItem->menuItem->name . ' (Promo Included)',
                            'unit_price' => 0,
                            'quantity' => $promoItem->quantity,
                            'is_included_in_promo' => true,
                        ]);
                        
                        $this->inventoryService->deductStock($item);
                    }
                }
            }

            return $session;
        });
    }

    /**
     * Bill out a room session.
     */
    public function billOutRoom(RoomSession $session, array $data): void
    {
        DB::transaction(function () use ($session, $data) {
            $openOrders = $session->orders()->where('status', 'open')->with('items.menuItem')->get();

            // 1. Validate & Deduct Stock for all additional items
            foreach ($openOrders as $order) {
                foreach ($order->items as $orderItem) {
                    $this->inventoryService->deductStock($orderItem);
                }
            }

            // 2. Finalize Session Status
            $session->update([
                'status' => 'completed',
                'ends_at' => now(),
            ]);

            // 3. Billing Calculations
            $pricingSnapshot = $session->pricing_snapshot ? json_decode($session->pricing_snapshot) : null;
            $chargeResult = $this->billingService->calculateCharge($session, $pricingSnapshot);
            
            $session->room_charge = $chargeResult['charge'];
            $session->billing_breakdown = json_encode($chargeResult['breakdown']);
            $session->save();

            // 4. Audit Log
            DB::table('room_billing_audit')->insert([
                'room_session_id' => $session->id,
                'room_charge' => $session->room_charge,
                'pricing_snapshot' => $session->pricing_snapshot,
                'billing_breakdown' => $session->billing_breakdown,
                'recorded_at' => now(),
            ]);

            // 5. Close Orders
            $userId = auth()->id() ?? 1;
            foreach ($openOrders as $order) {
                $order->update([
                    'status'           => 'paid',
                    'payment_method'   => $data['payment_method'] ?? 'cash',
                    'amount_received'  => $data['amount_received'] ?? $order->total_amount,
                    'reference_number' => $data['reference_number'] ?? null,
                    'closed_at'        => now(),
                    'user_id'          => $order->user_id ?? $userId,
                ]);
            }
        });
    }

    /**
     * Checkout a short order.
     */
    public function checkoutShortOrder(Order $order, array $data): void
    {
        DB::transaction(function () use ($order, $data) {
            // 1. Deduct Stock
            foreach ($order->items as $orderItem) {
                $this->inventoryService->deductStock($orderItem);
            }

            // 2. Close Order
            $order->update([
                'status'           => 'paid',
                'payment_method'   => $data['payment_method'] ?? 'cash',
                'amount_received'  => $data['amount_received'] ?? $order->total_amount,
                'reference_number' => $data['reference_number'] ?? null,
                'closed_at'        => now(),
                'user_id'          => $order->user_id ?? (auth()->id() ?? 1),
                'location'         => $data['location'] ?? null,
                'dining_option'    => $data['dining_option'] ?? null,
            ]);
        });
    }
}
