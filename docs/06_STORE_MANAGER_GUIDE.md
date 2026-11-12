# iRACK IT Solution — Store Manager Guide

## 1. Role Scope & Operational Responsibilities
The **Store Manager** oversees day-to-day operations at an assigned physical branch (e.g., Central Distribution Hub, Downtown Commercial Center, or Airport Logistics Terminal):
- Facility-specific inventory oversight, minimum threshold monitoring, and shelf re-organization.
- Authorizing stock receiving from suppliers and purchase orders.
- Authorizing stock issuance for customer order fulfillment, RMA replacements, and internal engineering demos.
- Initiating and approving inter-store stock transfers between regional hubs.
- Performing authorized physical cycle-count adjustments with mandatory variance justifications.
- Inspecting branch-specific sales trends and stock aging metrics.

---

## 2. Inventory Operations Workflow

### 2.1 Multi-Store Inventory Hub (`/admin/inventory`)
When a Store Manager logs in, the interface automatically focuses on their assigned branch:
- **Available Balance**: Calculated dynamically as $Quantity - Reserved$.
- **Stock Status Badges**:
  - `In Stock` (Green): Healthy stock levels above threshold.
  - `Low Stock` (Amber): Available quantity $\le$ minimum alert threshold.
  - `Out of Stock` (Red): Inventory depleted; immediate restocking needed.
- **Shelf Locations**: Update shelf/rack coordinates (`Rack B-04`, `Bay 12`) to speed up warehouse picking.

### 2.2 Receiving Incoming Supplier Shipments
1. Click **"Receive Stock"** button on the inventory dashboard.
2. Select target product, received quantity, supplier name, purchase reference number (e.g., `PO-2026-8812`), unit cost (SAR), and delivery notes.
3. Submit transaction. The system automatically creates a `RECEIVE` movement record (`RCV-2026-XXXXX`), atomically increases the branch balance, updates the product's unit cost price, and logs the operation to the audit trail.

### 2.3 Issuing Stock for Dispatch or Maintenance
1. Click **"Issue Stock"**.
2. Select target hardware model, quantity to deduct, operational reason (e.g. `Client Fulfillment`, `RMA Advance Replacement`, `Engineering Lab Testing`), and reference number.
3. Submit transaction. If the requested quantity exceeds available stock, the system enforces the **Zero Negative Stock Rule** and rejects the operation with an error. Otherwise, the stock is deducted atomically.

### 2.4 Inter-Store Stock Transfers
When moving stock between branches:
1. Click **"Inter-Store Transfer"**.
2. Select Destination Store, target product, and quantity.
3. System verifies source branch availability, creates an in-transit movement record (`TR-2026-XXXXX`), deducts stock from source, and credits the destination store upon confirmed receipt.

### 2.5 Stock Cycle-Count Adjustments
If a physical shelf audit reveals a discrepancy:
1. Click **"Adjust Stock"**.
2. Enter the new verified physical count.
3. The system computes the variance ($\Delta = \text{New} - \text{Current}$).
4. Select a mandatory adjustment reason:
   - `Physical Inventory Count Variance`
   - `Damaged or Compromised Goods`
   - `Expired Hardware / Firmware Incompatibility`
   - `Supplier Discrepancy Reconciliation`
5. Submit. All adjustments are logged with the manager's user ID and timestamp for financial audit scrutiny.
