# iRACK IT Solution — Inventory Officer Guide

## 1. Role Scope & Warehouse Operations
The **Inventory Officer** is the frontline logistics specialist responsible for accurate stock intake, shelving, stock count verification, and physical warehouse movements at the assigned fulfillment center:
- Inspecting incoming OEM hardware containers (Cisco, Dell, HPE, APC, Ubiquiti).
- Recording stock receiving against supplier delivery manifests.
- Staging and issuing hardware for outbound shipments.
- Barcode/SKU scanning and shelf location maintenance.
- Monitoring low-stock alert notifications.

---

## 2. Standard Operating Procedures (SOPs)

### 2.1 Goods Inward Inspection & Receiving
1. Inspect shipment packages for seal integrity, tamper-evident tape, and physical freight damage.
2. Verify hardware model numbers and serials against the supplier packing slip.
3. Open the **iRACK Administrative Portal** at `http://localhost:5173/admin/inventory`.
4. Click **"Receive Stock"**.
5. Select the matching product SKU from the dropdown (e.g., `CIS-CAT-C9300-48P`).
6. Enter the verified count, supplier name, supplier PO/invoice number, and internal unit cost.
7. Enter shelf location coordinate (e.g. `Aisle 3, Shelf B2`).
8. Click **"Confirm Receiving"**. The system records transaction `RCV-2026-XXXXX` and updates the warehouse ledger.

### 2.2 Order Staging & Picking
When a Sales Order is converted by the sales desk:
1. Review the generated Sales Order picking slip (`SO-2026-XXXXX`).
2. Locate the items using the recorded shelf coordinates.
3. Verify that physical serial numbers match the BOM specifications.
4. Stage items in the dispatch staging bay for logistics carrier collection.

### 2.3 Monitoring Stock Depletion Alerts
- On the inventory screen, filter by **"Low Stock"** and **"Out of Stock"**.
- Hardware models highlighted in Amber require purchase requisition before the on-hand quantity falls below safety thresholds.
- Notify the Store Manager when high-demand items (e.g. 100G optical transceivers or 42U rack enclosures) drop below the re-order point.
