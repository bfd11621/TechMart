# iRACK IT Solution — Sales Manager Guide

## 1. Role Scope & Commercial Authority
The **Sales Manager** oversees customer quotation requests, commercial pricing, quotation lifecycle approval, sales order confirmation, revenue recognition, and payment tracking:
- Reviewing incoming Requests for Quotation (RFQs) submitted through the public showroom.
- Preparing formal B2B proposals with volume discounts, 15% VAT, and 30-day price locks.
- Generating branded PDF quotation documents for enterprise clients.
- Converting approved quotations into confirmed Sales Orders (`SO-2026-XXXXX`).
- Recognizing commercial revenue and recording payment installments.
- Analyzing sales trajectories and revenue market share across categories, brands, and stores.

---

## 2. End-to-End Sales Workflow

```
[Public Showroom RFQ] ──> [Review RFQ Desk] ──> [Prepare Formal Quotation]
                                                            │
                                                            ▼ (30-Day Price Lock)
[Customer Approval] <── [Email Branded PDF] <── [Issue Quotation QT-...]
        │
        ▼
[Convert to Sales Order SO-...] ──> [Atomic Stock Deduction] ──> [Revenue Recognized]
        │
        ▼
[Record Payment Receipts RCT-...] ──> [Delivery & Fulfillment Completed]
```

### 2.1 Processing RFQ Inquiries (`/admin/rfqs`)
1. Review submitted RFQs under **"Quotation Requests (RFQs)"**.
2. Click on an RFQ row to open the inquiry drawer:
   - Client contact details (Corporate Name, Contact, Email, Phone, Project Location).
   - Requested bill of materials (Product models, SKU, requested quantities).
   - Client architectural notes or custom power/cabling specifications.
3. Advance RFQ status to `Under Review`.
4. Click **"Prepare Quotation"** to open the Quotation Proposal wizard.

### 2.2 Quotation Preparation & 30-Day Price Locks (`/admin/quotations`)
1. Review the imported line items.
2. Enter custom negotiated unit prices (e.g. project tier discounting).
3. Apply quotation-level commercial discounts (percentage or fixed amount).
4. System automatically computes subtotal, 15% VAT, and final proposal total.
5. Set quotation expiration date (defaults to +30 calendar days).
6. Click **"Save Quotation"**. A unique reference number (`QT-2026-XXXXX`) is generated.
7. Click **"Download PDF"** to generate the branded corporate quotation document with iRACK logo, itemized BOM, terms, and signature lines.
8. Email PDF to client and advance quotation status to `SENT`.

### 2.3 Quotation Conversion & Revenue Recognition (`/admin/sales-orders`)
When the customer accepts the quotation and issues an official Purchase Order (PO):
1. Advance quotation status to `APPROVED`.
2. Click **"Convert to Sales Order"**.
3. Confirm fulfilling store branch and delivery shipping address.
4. Click **"Confirm Conversion"**:
   - The system executes an atomic transaction.
   - Concurrency locks verify and deduct physical stock from store inventory.
   - The quotation is permanently locked to prevent duplicate conversions.
   - Unique Sales Order number (`SO-2026-XXXXX`) is generated.
   - **Revenue is recognized in corporate financial reports at this exact point.**

### 2.4 Recording Payment Receipts
1. Open the Sales Order slide-over drawer in `/admin/sales-orders`.
2. Click **"Record Payment"**.
3. Enter amount received, payment method (`Bank Wire`, `Cheque`, `Credit Card`, `Cash`), transaction reference, and optional deposit notes.
4. Submit. The system generates an official payment receipt number (`RCT-2026-XXXXX`), updates the paid balance, and toggles order payment status (`PARTIAL` or `PAID`).
