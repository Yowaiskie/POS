# KTV POS System: Manage Rooms & Inventory Flow

Dokumento ito para ipaliwanag ang sunod-sunod na proseso (step-by-step) kung paano gumagana ang **Manage Rooms**, pag-add ng **Orders**, at ang epekto nito sa **Inventory**.

---

## 1. Pag-start ng Room Session (Start Session)
Kapag pinindot ang "Start Session" sa isang Room:
1.  **Room Status:** Nagbabago ang status ng Room mula `Available` patungong `Occupied`.
2.  **Room Session:** May nabubuong record sa `room_sessions` table na naglalaman ng `started_at` at `ends_at`.
3.  **Promo Selection (Optional):**
    *   Kung may piniling **Promo Set** (halimbawa: "Beer Bucket Promo"), gagawa ang system ng initial **Order**.
    *   **Inventory Impact:** Ang mga items na kasama sa promo (halimbawa: 5 San Mig Light) ay **agad na ibabawas** sa inventory (`MenuItem` stock). Gagamit ang system ng `deductStock` method sa `InventoryService`.

---

## 2. Pag-add ng Order sa Occupied Room (Add Order)
Habang ang room ay `Occupied`, pwedeng mag-add ng karagdagang orders:
1.  **Selection:** Pipili ang waiter ng item mula sa Menu.
2.  **Availability Check:** Titingnan ng system kung may sapat na `stock_quantity`. Kung zero na ang stock, hindi papayagan ang order.
3.  **Order Entry:** Maidaragdag ang item sa `order_items` table na naka-link sa active `room_session_id`.
4.  **Inventory Impact (Crucial):** 
    *   Sa stage na ito, **HINDI PA** binabawas ang stock sa database. 
    *   Ang `is_stock_deducted` flag sa `order_items` ay mananatiling `false`.
    *   Ginagawa ito para kung sakaling magkaroon ng pagkakamali o kanselasyon habang nasa loob pa ang customer, madaling i-edit ang order nang hindi nagugulo ang inventory count.

---

## 3. Pag-bill Out (Checkout Room)
Ito ang pinaka-importanteng stage kung saan nagaganap ang final calculations:
1.  **Room Charge:** Kakalkulahin ng `RoomBillingService` ang bayad sa oras (Hourly Rate) base sa tinagal ng customer at sa pricing tiers (Daytime, Night, Weekend).
2.  **Order Finalization:**
    *   Lahat ng "Open" orders na ginawa sa step #2 ay kukunin ng system.
    *   **Inventory Impact:** Dito na tatawagin ang `deductStock` para sa **lahat** ng karagdagang orders. Ang `stock_quantity` ng bawat `MenuItem` ay mababawasan na sa database.
3.  **Payment:** Itatala ang payment method (Cash/GCash) at ang total amount received.
4.  **Room Cleanup:** Ang Room status ay babalik sa `Available`, at ang Session ay mamarkahan bilang `Completed`.

---

## Buod ng Inventory Impact

| Action | Kailan nababawasan ang Stock? | Bakit? |
| :--- | :--- | :--- |
| **Promo Set (Start Session)** | **Agad-agad** | Dahil sigurado nang ilalabas ang items sa pagsisimula pa lang. |
| **Additional Order (Room)** | **Sa Bill Out pa** | Para payagan ang real-time adjustments/cancellations habang occupied ang room. |
| **Short Order (Walk-in)** | **Sa Checkout** | Dahil bayad agad bago makuha ang item. |

---

*Note: Ang logic na ito ay matatagpuan sa `OrderFlowService.php` at `InventoryService.php`.*
