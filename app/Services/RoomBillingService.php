<?php

namespace App\Services;

use App\Models\RoomSession;

class RoomBillingService
{
    /**
     * Calculate the room charge based on a tiered pricing snapshot.
     *
     * @param  RoomSession  $session
     * @param  \stdClass|null  $config  Pricing snapshot decoded as stdClass
     * @return array  ['charge' => float, 'breakdown' => array]
     */
    public function calculateCharge(RoomSession $session, $config = null): array
    {
        // If no snapshot provided, fall back to live config
        if (is_null($config)) {
            $config = \App\Models\RoomPricing::first();
            
            if (is_null($config)) {
                $config = (object) [
                    'price_30_min' => 100.0,
                    'price_60_min' => 350.0,
                    'overtime_unit_minutes' => 10,
                    'overtime_unit_price' => 50.0,
                    'grace_period_minutes' => 10,
                ];
            }
        }

        // Calculate the effective end time (whichever is later: the reserved ends_at or current time)
        $effectiveEnd = $session->ends_at;
        $now = now();
        
        if ($now->gt($session->ends_at)) {
            $effectiveEnd = $now;
        }

        $totalMinutes = (int) max(0, $effectiveEnd->diffInMinutes($session->started_at, false));
        
        // Subtract promo duration if any
        if (($session->promo_duration_hours ?? 0) > 0) {
            $totalMinutes = max(0, $totalMinutes - ($session->promo_duration_hours * 60));
        }
        
        // Grace Period check
        if ($totalMinutes <= ($config->grace_period_minutes ?? 10)) {
            return [
                'charge' => 0.0,
                'breakdown' => [
                    'total_minutes' => $totalMinutes,
                    'is_free' => true,
                    'total_charge' => 0.0,
                ],
            ];
        }

        $fullHours = floor($totalMinutes / 60);
        $remainingMinutes = $totalMinutes % 60;
        
        // Base Hourly Charge
        $hourlyCharge = $fullHours * $config->price_60_min;
        
        $minutesCharge = 0;
        $calculationLog = [];

        if ($remainingMinutes > 0) {
            if ($remainingMinutes >= 30) {
                // Tier 1: Half Hour Block
                $minutesCharge = $config->price_30_min;
                $calculationLog[] = "Added 30-min block: P" . $config->price_30_min;
                
                $overtimeAfter30 = $remainingMinutes - 30;
                if ($overtimeAfter30 > 0) {
                    // Calculate loose minutes after the 30-min block
                    $ovUnits = ceil($overtimeAfter30 / $config->overtime_unit_minutes);
                    $incCharge = $ovUnits * $config->overtime_unit_price;
                    
                    // Cap check: If overtime + 30m is more than a full hour, just charge the full hour
                    $tierTotal = $config->price_30_min + $incCharge;
                    if ($tierTotal > $config->price_60_min) {
                        $minutesCharge = $config->price_60_min;
                        $calculationLog[] = "Overtime exceeded 1hr tier, capped at: P" . $config->price_60_min;
                    } else {
                        $minutesCharge += $incCharge;
                        $calculationLog[] = "Added {$overtimeAfter30}m overtime: P{$incCharge}";
                    }
                }
            } else {
                // Tier 0: Just loose minutes
                $ovUnits = ceil($remainingMinutes / $config->overtime_unit_minutes);
                $incCharge = $ovUnits * $config->overtime_unit_price;
                
                // Cap check: If loose minutes cost more than the 30-min block
                if ($incCharge > $config->price_30_min) {
                    $minutesCharge = $config->price_30_min;
                    $calculationLog[] = "Loose minutes exceeded 30m tier, capped at: P" . $config->price_30_min;
                } else {
                    $minutesCharge = $incCharge;
                    $calculationLog[] = "Added {$remainingMinutes}m overtime: P{$incCharge}";
                }
            }
        }

        $totalCharge = (float) ($hourlyCharge + $minutesCharge);

        return [
            'charge' => $totalCharge,
            'breakdown' => [
                'total_minutes' => $totalMinutes,
                'full_hours' => $fullHours,
                'remaining_minutes' => $remainingMinutes,
                'hourly_rate' => $config->price_60_min,
                'hourly_total' => $hourlyCharge,
                'minutes_charge' => $minutesCharge,
                'calculation_steps' => $calculationLog,
                'total_charge' => $totalCharge,
                'currency' => 'PHP',
                'timestamp' => now()->toIso8601String(),
            ],
        ];
    }
}
