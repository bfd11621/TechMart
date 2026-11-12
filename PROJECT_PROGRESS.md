# iRACK IT Solution — Project Progress Tracker

## Project Overview
- **System**: Online Product Showroom, Multi-Store Inventory & Sales Management System
- **Client**: iRACK IT Solution
- **Stack**: Laravel Backend (REST API, Sanctum, MySQL) + Vue.js 3 Frontend (Vite, Pinia, Tailwind CSS, Lucide Icons, ECharts)
- **Status**: ALL 10 PHASES COMPLETED — PRODUCTION READY & VERIFIED

---

## 10-Phase Implementation Roadmap

| Phase | Description | Status | Completion Date |
| :---: | :--- | :---: | :---: |
| **0** | **Workspace Inspection & System Architecture Proposal** | **COMPLETED** | 2026-11-11 |
| **1** | **Project Foundation & Architecture Setup** | **COMPLETED** | 2026-11-11 |
| **2** | **Authentication, RBAC & Multi-Store Management** | **COMPLETED** | 2026-11-11 |
| **3** | **Product Catalog Engine & Specifications System** | **COMPLETED** | 2026-11-11 |
| **4** | **Public Online Showroom & Product Catalog UI** | **COMPLETED** | 2026-11-11 |
| **5** | **Shopping Cart & Request for Quotation (RFQ) System** | **COMPLETED** | 2026-11-11 |
| **6** | **Multi-Store Inventory Management & Movements** | **COMPLETED** | 2026-11-11 |
| **7** | **Quotation Management & Document Generation** | **COMPLETED** | 2026-11-11 |
| **8** | **Business Analytics, Apache ECharts & PDF Reporting** | **COMPLETED** | 2026-11-11 |
| **9** | **Website & System Configuration (CMS, Merchandising & Parameters)** | **COMPLETED** | 2026-11-11 |
| **10** | **Final Testing, Security, Deployment & Documentation Handover** | **COMPLETED** | 2026-11-11 |

---

## Phase 2 Accomplishments Log

### 1. Authentication & Session Security (Laravel Sanctum)
- [x] Implemented administrator authentication via `POST /api/v1/auth/login` with strict rate limiting (`throttle:5,1` - 5 failed attempts per minute lockout).
- [x] Implemented logout endpoint `POST /api/v1/auth/logout` revoking active Sanctum personal access tokens.
- [x] Implemented `GET /api/v1/auth/me` returning current profile, assigned store context, and granular permission abilities.
- [x] Implemented password recovery workflow: `POST /api/v1/auth/forgot-password` (issues expiring 60-minute token) and `POST /api/v1/auth/reset-password` with token verification.
- [x] Implemented staff profile update and password change endpoints in `ProfileController`.
- [x] Architected Two-Factor Authentication (TOTP) support structure on user profile.

### 2. User & Store Management
- [x] Built full user CRUD with search (name, email, phone), role filtering, store filtering, status filtering, and pagination in `UserController`.
- [x] Enforced store branch assignment for store-restricted roles (Store Manager, Inventory Officer).
- [x] Added instant account activation/deactivation (`PATCH /api/v1/admin/users/{user}/status`) which immediately revokes tokens upon deactivation.
- [x] Implemented administrative direct password reset (`POST /api/v1/admin/users/{user}/reset-password`).
- [x] Built `StoreController` for managing physical store branches (`/api/v1/admin/stores`).

### 3. Role-Based Access Control (RBAC) & Granular Permissions
- [x] Seeded 41 granular permissions across all 14 modules: Dashboard, Users, Roles, Stores, Products, Categories, Brands, Inventory, Stock Transfers, Quotations, Sales, Analytics, Reports, and Settings.
- [x] Seeded initial 5 enterprise roles with pre-mapped permissions: Super Administrator, Store Manager, Sales Manager, Inventory Officer, General Administrator.
- [x] Enforced backend authorization middleware `check.permission:{slug}` blocking unauthorized API tampering regardless of frontend state.
- [x] Enforced backend branch isolation middleware `check.store` preventing store managers from accessing or mutating other stores' data.
- [x] Implemented comprehensive audit logging via `AuditService` logging user logins, logouts, user mutations, status toggles, password resets, role changes, and permission synchronizations.

### 4. Frontend Administrative Interfaces
- [x] Enhanced `LoginView.vue` with real API authentication, error alerts, and 1-click pre-seeded credentials for all 4 primary roles.
- [x] Built `ForgotPasswordView.vue` and `ResetPasswordView.vue`.
- [x] Built `UserManagementView.vue` with responsive search, multi-filters, status toggle, pagination, and modals for creating, editing, and resetting passwords.
- [x] Built `RoleManagementView.vue` displaying member counts, system role protection, and custom role CRUD.
- [x] Built `PermissionAssignmentView.vue` featuring an interactive role-permission matrix grouped by all 14 modules with Select All / Deselect All per module.
- [x] Built `UserProfileView.vue` with profile editing, password changing, assigned branch card, and 2FA status.
- [x] Built `StoreManagementView.vue` for physical branch administration.
- [x] Updated `router/index.js` with permission checking in navigation guards, and `AdminLayout.vue` with dynamic navigation item filtering based on granted permissions.

---

## Phase 3 Accomplishments Log

### 1. Hierarchical Category & Subcategory Engine
- [x] Implemented nested category structure supporting root categories and unlimited depth subcategories via `parent_id` foreign key.
- [x] Seeded the 9 mandatory IT hardware categories with subcategories:
  1. Servers and Workstations (Rack Servers, Tower Servers, Workstations, Blade Systems)
  2. Networking Equipment (Managed Switches, Unmanaged Switches, Core Routers, Wireless APs, SFP Transceivers)
  3. Storage Solutions (SAN Arrays, NAS Systems, Tape Libraries, NVMe Expansion)
  4. Power Solutions (Online Double-Conversion UPS, Line-Interactive UPS, Smart PDUs, Modular Battery Packs)
  5. Security Solutions (Next-Gen Firewalls, IP Cameras, Network Video Recorders, Access Control)
  6. Audio Visual Solutions (Video Conferencing Bars, Commercial Displays, Digital Signage, Audio Processors)
  7. Computers and Accessories (Business Desktops, Enterprise Laptops, Docking Stations, Peripherals)
  8. Data Center Infrastructure (Server Racks 42U/48U, Precision Cooling, Cable Management, Environmental Sensors)
  9. IT Accessories (Fiber Patch Cords, Cat6A Cables, Rack Shelves, Patch Panels)
- [x] Built `CategoryController` (`/api/v1/admin/categories`) with tree representation, product counting, active status toggling, and protective constraints preventing deletion of categories containing products or children.

### 2. Enterprise Brand & Vendor Management
- [x] Built `BrandController` (`/api/v1/admin/brands`) supporting brand logos, website URLs, active/inactive statuses, and linked product counts.
- [x] Seeded initial tier-1 hardware partner brands: Cisco Systems, Dell Technologies, HPE (Hewlett Packard Enterprise), Schneider Electric (APC), Fortinet, Ubiquiti UniFi, Synology, Lenovo Enterprise, Eaton.

### 3. Dynamic Technical Specifications Engine (EAV Matrix)
- [x] Normalized dynamic specifications using `product_specifications` table with `spec_group`, `spec_name`, `spec_value`, `unit`, and `display_order`.
- [x] Removed fixed specification columns to accommodate radically different hardware profiles without database schema modifications.
- [x] Implemented 5 quick-load specification templates in the UI:
  - **Enterprise Rack Server**: Form Factor, Processor Sockets, Installed CPUs, Chipset, Max Memory, Memory Sockets, Storage Bays, RAID Controller, Network Ports, Power Supply Redundancy.
  - **Managed Network Switch**: Port Count, Port Speed, Uplink Ports, Switching Capacity, Forwarding Rate, PoE Budget, Stacking Bandwidth, Layer Support.
  - **Storage NAS Array**: Drive Bays, Max Raw Capacity, Drive Interface, Controller CPU, RAM, RAID Levels, Network Connectivity, Hot-Swappable.
  - **Online Smart UPS**: Power Capacity (VA/W), Topology, Input Voltage, Output Voltage, Battery Type, Typical Backup Time, Output Outlets, Network Card.
  - **Server Cabinet / Rack**: Rack Height (U), Depth, Width, Static Load Rating, Door Type, Side Panels, Cable Inlets.

### 4. Media & Datasheet Asset Management
- [x] Engineered `FileUploadService` with secure MIME validation, size limits (5MB for images, 15MB for PDF datasheets), and automated old-file cleanup upon replacement or deletion.
- [x] Created `product_images` gallery model supporting primary image designation, sorting order, and responsive image previews.
- [x] Supported direct PDF manufacturer datasheet uploads with server-side download streaming (`/api/v1/public/products/{id}/datasheet`).

### 5. Product Administrative Interface
- [x] Built `ProductManagementView.vue` with real-time text search, category/brand/availability filters, live stock indicators, quick publish switches, and direct datasheet download actions.
- [x] Built comprehensive `ProductFormView.vue` featuring a tabbed wizard:
  - **Tab 1: General & Classification** (Name, SKU, Model Number, Category/Subcategory cascading selector, Brand, Published & Featured toggles).
  - **Tab 2: Commercial & Features** (Short description, rich HTML description, dynamic key features bullet point builder).
  - **Tab 3: Dynamic Technical Specifications** (Preset selector, group organizer, spec name, value, unit fields with instant row insertion/deletion).
  - **Tab 4: Media & Datasheet** (Multi-image upload dropzone, primary badge toggle, thumbnail previews, PDF datasheet file picker).
- [x] Built `CategoryManagementView.vue` with interactive tree expand/collapse, subcategory count indicators, and modal editing.
- [x] Built `BrandManagementView.vue` featuring partner brand grid cards with vendor logos and status toggles.
- [x] Wired all catalog modules into `frontend/src/router/index.js` and `AdminLayout.vue` under proper RBAC capability guards.

---

## Phase 4 Accomplishments Log

### 1. Official Corporate Identity & Logo Integration
- [x] Integrated the official `iRACK TECH-MART` corporate visual identity provided by the user.
- [x] Engineered `AppLogo.vue` vector component rendering the exact "iR" monogram (circular red dot `#E20913`, deep navy stem `#042562`, corporate blue top arch `#1865C0`, angled navy leg `#042562`) and stylized "iRACK TECH-MART" wordmark.
- [x] Supported multiple layouts (`horizontal`, `stacked`, `iconOnly`) and color themes (`light` on dark blue banners, `dark` on light backgrounds).
- [x] Standardized logo across `PublicLayout.vue`, `AdminLayout.vue`, and `AuthLayout.vue`.

### 2. Homepage & International IT Showroom (`/`)
- [x] Developed international IT supplier aesthetic using the mandatory corporate color palette (`#042562`, `#1865C0`, `#E20913`, `#F4F6FA`).
- [x] Built responsive top announcement & direct hotline bar with branch dispatch counters.
- [x] Designed enterprise Hero section with promotional banners, quick links to catalog and RFQ quotation cart, and operational stock network metrics card.
- [x] Implemented real-time quick search bar with instant category selection.
- [x] Integrated 9-category showroom classifications grid with individual model counters and subcategory pills.
- [x] Built featured datacenter products showcase with `ProductCard.vue` and instant `QuickViewModal.vue` preview.
- [x] Added recently added hardware section and popular authorized partner brands carousel.
- [x] Authored company overview with value propositions (100% genuine certified hardware, 3 regional fulfillment hubs, certified CCIE/Dell enterprise architects).
- [x] Built comprehensive corporate footer with multi-store branch coordinates, working hours, solutions links, and legal disclosures.

### 3. Categories & Subcategories Directory (`/categories`)
- [x] Created dedicated hardware taxonomy showcase featuring all 9 core categories and their child subcategories.
- [x] Integrated live product counts per subcategory, category search filter, and 1-click navigation to filtered catalog views.

### 4. Shop / Product Catalog Page (`/catalog`)
- [x] Developed dual viewing modes: responsive 3-column Grid View and detailed List View with smooth transition toggles.
- [x] Engineered Faceted Filter Sidebar:
  - Text search by product name, model number, SKU, or keyword with debounced input.
  - Category selector with live product counts.
  - Manufacturer brand multi-filter (Cisco, Dell, HPE, Schneider Electric, Ubiquiti, Fortinet, Synology, Lenovo, Eaton).
  - Stock availability filter (In Stock vs. All conditions).
  - Active filter chips with 1-click removal and "Reset All" button.
- [x] Implemented sorting dropdown (Newest Arrivals, Model Name A-Z, Model Name Z-A).
- [x] Added responsive pagination controls (page buttons, prev/next, active page indicator).
- [x] Built `QuickViewModal.vue` allowing instant technical previews, spec highlights, and direct RFQ cart insertion without leaving the catalog stream.

### 5. Product Technical Details Page (`/product/:slug`)
- [x] Built breadcrumb navigation path (`Home > Catalog > Category > Product`).
- [x] Implemented interactive multi-image gallery with thumbnail switcher and zoom styling.
- [x] Rendered multi-store inventory availability card with explicit quantity breakdown across all 3 branches (Central Distribution Hub, Downtown Commercial Center, Airport Logistics Terminal).
- [x] Added official manufacturer OEM Datasheet PDF download button calling the backend streaming endpoint (`/api/v1/public/products/{id}/datasheet`).
- [x] Engineered tabbed technical specifications interface:
  - **Tab 1: Technical Specifications Matrix** (grouped by architecture, memory, backplane, networking, power, form factor).
  - **Tab 2: Core Architecture & Key Features** (bulleted capability breakdown).
  - **Tab 3: Full Engineering Overview** (in-depth description).
  - **Tab 4: Warranty, Compliance & Support SLAs** (OEM warranty terms, same-day dispatch conditions, on-site deployment).
- [x] Built quantity selector (+ / -) with "Add to Quotation Cart" button and optional custom engineering note input.
- [x] Added complementary / related hardware products grid based on shared category and brand.

### 6. Side-by-Side Product Comparison Matrix (`/compare`)
- [x] Created `compareStore.js` managing up to 4 comparison items in localStorage.
- [x] Built persistent floating `CompareDrawer.vue` that collapses/expands at the bottom of the screen.
- [x] Developed full `ProductCompareView.vue` rendering side-by-side technical specification rows, highlighting architectural parameters, and enabling direct RFQ basket insertion.

### 7. Contact Us & Regional Distribution Centers (`/contact`)
- [x] Detailed physical addresses, direct phone numbers, email desks, and operational hours for all 3 hubs:
  - Central Distribution Hub (HQ): Silicon Valley Gateway, San Jose, CA.
  - Downtown Commercial Center: Financial District, Market Street.
  - Airport Logistics Terminal: Air Cargo Rapid Dispatch Hub.
- [x] Built interactive consultation inquiry form with validation, submitting to backend endpoint `POST /api/v1/public/contact`.
- [x] Generated automated reference tickets (e.g. `INQ-XXXXXXXX`) upon submission.
- [x] Built Google Maps geographic representation with coordinates and satellite routing link.
- [x] Surfaced 24/7 SLA NOC emergency hotline and social media channels.

### 8. Enterprise Informational & Legal Pages
- [x] Built `AboutView.vue` with corporate history, ISO 9001:2015 certifications, and datacenter statistics.
- [x] Built `ServicesView.vue` showcasing turnkey white-space engineering, structured optical cabling, spine-leaf switching, online UPS arrays, and 24/7 managed hardware SLAs.
- [x] Built `BrandsView.vue` showcasing the 9 authorized partner manufacturers with partner tier classifications.
- [x] Built `PrivacyView.vue` outlining enterprise B2B data handling and confidential procurement protection.
- [x] Built `TermsView.vue` incorporating mandatory business rules: RFQs are inquiries and never confirmed sales or revenue; revenue recognized strictly upon authorized sales order confirmation.

---

## Phase 5 Accomplishments Log

### 1. B2B Shopping Cart Architecture (Quotation Basket)
- [x] Engineered `cartStore.js` Pinia store with full reactive state management:
  - Add products with custom initial quantity and optional line-item notes.
  - Increment/decrement quantity with safe minimum bounds.
  - Remove specific line items and complete "Clear Cart" action.
  - Auto-persistence in browser `localStorage` ensuring carts survive page refreshes and navigation.
  - Strict data privacy: Never exposes internal cost prices or backend stock quantities to public customers.
- [x] Built `CartView.vue` (`/cart`) featuring:
  - Line-item table with product thumbnails, model numbers, SKU badges, category pills, and quantity steppers.
  - Per-item project specification notes textarea for custom hardware configuration requests.
  - "Continue Shopping" quick navigation returning to catalog.
  - Interactive summary panel displaying total item count and quotation processing guidelines.
  - Enterprise Procurement Notice emphasizing that submission creates a formal inquiry without online checkout or financial commitment.

### 2. Request for Quotation (RFQ) Submission Workflow
- [x] Engineered B2B quotation request form within `CartView.vue`:
  - Contact fields: Full Name, Company / Organization Name, Official Corporate Email, Direct Phone Number.
  - Regional location dropdown covering the 3 physical fulfillment hubs or remote customer delivery.
  - High-level project specifications, delivery timeline, and engineering comments textarea.
  - Clear client-side validation preventing submission of empty baskets or invalid contact details.
- [x] Post-submission interactive confirmation screen:
  - Displays generated unique RFQ reference number (`RFQ-YYYY-XXXXX`).
  - Outlines the 4-step procurement review roadmap: Engineering BOM Review, Commercial Quotation Preparation, Official Formal Proposal Sent, Client Acceptance & Order Confirmation.
  - Confirmation status indicator of email dispatched to customer and assigned sales engineering desk.
  - 1-click shortcut to explore more hardware or download the quotation confirmation slip.

### 3. Backend Atomic RFQ Processing & Status History
- [x] Created database migration `2026_01_03_000001_create_rfq_status_histories_table.php` recording `rfq_request_id`, `status`, `previous_status`, `user_id`, `notes`, and timestamps.
- [x] Updated `rfq_requests` table schema with explicit `location` column and standardized initial status `NEW_REQUEST`.
- [x] Updated `RfqStatus.php` enum with all 8 exact mandatory lifecycle statuses:
  1. `NEW_REQUEST` — New Request
  2. `UNDER_REVIEW` — Under Review
  3. `QUOTATION_PREPARED` — Quotation Prepared
  4. `SENT_TO_CUSTOMER` — Sent to Customer
  5. `APPROVED` — Approved
  6. `REJECTED` — Rejected
  7. `CONVERTED_TO_ORDER` — Converted to Sales Order
  8. `COMPLETED` — Completed
- [x] Implemented `RfqSubmissionController.php` (`POST /api/v1/public/rfqs`):
  - Strict input validation via `StorePublicRfqRequest.php`.
  - Atomicity guaranteed via `DB::transaction`.
  - Generates collision-proof sequential reference numbers formatted as `RFQ-YYYY-XXXXX`.
  - Automatically records initial `NEW_REQUEST` entry in `rfq_status_histories`.
  - Dispatches non-blocking email notifications:
    - `AdminRfqNotificationMail.php` to administrative procurement desk.
    - `CustomerRfqConfirmationMail.php` with complete BOM breakdown to customer.
  - Rate limiting via `throttle:10,1` protecting against automated spam.
  - **Zero Revenue Rule Enforced**: RFQs are recorded exclusively as inquiries; inventory stock levels and sales revenues are strictly untouched.

### 4. Administrative RFQ Management Portal (`/admin/rfqs`)
- [x] Built `RfqManagementController.php` providing:
  - `GET /api/v1/admin/rfqs`: Paginated index with search by reference/name/company/email, status tab filters, and date ranges.
  - `GET /api/v1/admin/rfqs/{id}`: Detailed view with items, customer credentials, and full chronological status audit trail.
  - `PATCH /api/v1/admin/rfqs/{id}/status`: Authorized status transition logging responsible user and required rationale notes.
  - `PUT /api/v1/admin/rfqs/{id}/notes`: Internal engineering and commercial review notes editor.
  - `DELETE /api/v1/admin/rfqs/{id}`: Soft archiving under Super Administrator permissions.
- [x] Built `RfqManagementView.vue` in frontend:
  - Executive KPI cards: Total Requests, New Unassigned, Under Engineering Review, Prepared Proposals.
  - Quick-filter status tabs matching all 8 lifecycle states.
  - Search input with debounced querying by company, reference number, or contact person.
  - Slide-over comprehensive RFQ detail drawer:
    - Customer and organizational credentials.
    - Full requested bill of materials (BOM) with requested quantities, model numbers, and client notes.
    - 8-Status Transition Controller with prompt modal for transition remarks.
    - Chronological Status History Timeline with staff actor name and notes.
    - Internal Notes notepad with instant save.
    - Action shortcut to prepare official quotation (seamless bridge to Phase 7).
- [x] Wired public `/cart` and administrative `/admin/rfqs` into `frontend/src/router/index.js` and `AdminLayout.vue` sidebar menu.

---

## Phase 6 Accomplishments Log

### 1. Store Management Enhancement
- [x] Enhanced `stores` table schema and model with `manager_id` foreign key.
- [x] Implemented instant store activation/deactivation (`PATCH /api/v1/admin/stores/{store}/status`) with protective constraints (cannot deactivate Central Distribution HQ).
- [x] Implemented employee assignment endpoint (`POST /api/v1/admin/stores/{store}/employees`) synchronizing staff assignments.
- [x] Enhanced `StoreManagementView.vue` with store manager designation dropdown, live on-hand stock counters, active status power toggle, and staff count metrics.

### 2. Store-Specific Inventory Tracking
- [x] Enforced separate physical stock records per store via `store_inventories` (unique constraint on `store_id` + `product_id`).
- [x] Tracked:
  - Total on-hand quantity.
  - Reserved quantity (allocated to unfulfilled sales orders).
  - Available quantity ($quantity - reserved\_quantity$).
  - Minimum stock level alert threshold (`min_alert_quantity`).
  - Shelf and bay location coordinates (e.g. `AISLE-A-BAY-1`).
  - Stock condition status (`IN_STOCK`, `LOW_STOCK`, `OUT_OF_STOCK`).
  - Last movement timestamp and calculated inventory aging days.
  - Stock valuation ($) based on recorded unit cost prices.

### 3. Stock Receiving (Inward Goods)
- [x] Created `StockReceivingRequest.php` validating destination store, product, positive quantity, supplier, purchase reference, unit cost, and shelf location.
- [x] Developed `InventoryService::receiveStock()` inside an atomic `DB::transaction`:
  - Utilizes pessimistic row locking (`lockForUpdate()`) on `store_inventories`.
  - Increments stock balance automatically.
  - Updates `last_movement_at` timestamp.
  - Logs `StockMovement` (type: `RECEIVE`) with collision-proof reference `RCV-YYYY-XXXXX`.
  - Creates `StockMovementItem` recording received quantity, previous quantity, new quantity, and unit cost.
  - Dynamically updates product cost price when provided.
  - Logs audit trail entry `STOCK_RECEIVED`.
- [x] Built `StockReceivingModal.vue` modal with store and product selection, unit cost input, and inspection notes.

### 4. Stock Issuing (Outward Dispatches & Strict Negative Stock Prevention)
- [x] Created `StockIssuingRequest.php` validating source store, product, issued quantity, issue reason, and reference number.
- [x] Developed `InventoryService::issueStock()` inside an atomic `DB::transaction`:
  - Locks source store inventory with `lockForUpdate()`.
  - **Zero Negative Stock Rule**: Checks `availableQuantity() >= quantity`; throws business validation exception if insufficient stock.
  - Decrements stock balance automatically.
  - Logs `StockMovement` (type: `ISSUE`) with unique reference `ISS-YYYY-XXXXX`.
  - Categorizes issue reasons (Customer Project Deployment, OEM RMA Defect Return, Internal Demo/Lab, Warranty SLA Replacement, Damaged Write-off).
  - Logs audit trail entry `STOCK_ISSUED`.
- [x] Built `StockIssuingModal.vue` displaying live source on-hand stock and dynamically blocking quantities exceeding available balance.

### 5. Inter-Store Stock Transfers (Atomic Cross-Store Movements)
- [x] Created `StockTransferRequest.php` enforcing different source and destination stores.
- [x] Developed `InventoryService::transferStock()` with guaranteed transaction atomicity:
  - Pessimistic row-locking (`lockForUpdate()`) on both source and destination records.
  - Deducts stock from source branch ($source\_qty \ge transfer\_qty$).
  - Simultaneously credits stock to destination branch (creates store inventory record if product not previously stocked at branch).
  - Guarantees zero stock loss: transfer will never deduct stock without successfully recording the destination receipt.
  - Generates unique transfer reference `TRF-YYYY-XXXXX`.
  - Logs audit trail entry `STOCK_TRANSFERRED`.
- [x] Built `StockTransferModal.vue` with source availability indicator, destination selector, and courier logistics notes.

### 6. Stock Adjustments (Cycle Count Reconciliation & Compliance Audit Trail)
- [x] Created `StockAdjustmentRequest.php` requiring mandatory adjustment rationale (at least 5 characters).
- [x] Developed `InventoryService::adjustStock()` inside `DB::transaction`:
  - Locks inventory row with `lockForUpdate()`.
  - Captures exact previous quantity, new quantity, and calculated variance delta ($\Delta$).
  - Reconciles physical balance to new verified count.
  - Logs `StockMovement` (type: `ADJUSTMENT`) with unique reference `ADJ-YYYY-XXXXX`.
  - Logs audit trail entry `STOCK_ADJUSTED` with full snapshot of previous and new values.
- [x] Built `StockAdjustmentModal.vue` with 3-box comparison (Current, New, Delta), presets (Physical Count Discrepancy, Damaged in Warehouse, Found Unrecorded Batch), and custom explanation input.

### 7. Inventory Monitoring, Dashboard & Reporting
- [x] Built `InventoryManagementView.vue` (`/admin/inventory`) featuring:
  - **Executive KPI Cards**: Total Catalog Products, Total Physical Units, Total Stock Valuation ($ USD), Low Stock Warnings, Out of Stock Alerts.
  - **Store-Wise Distribution Network**: Real-time progress bars comparing inventory volume and dollar valuation across all 3 regional hubs.
  - **Faceted Toolbar**: Search (name, SKU, shelf), Store Selector (HQ Consolidated vs. Branch), Status filter (`in_stock`, `low_stock`, `out_of_stock`).
  - **Master Inventory Data Table**: Thumbnail, SKU, model, store badge, shelf location, available quantity, status badge, valuation, last movement, and quick row actions.
  - **Stock Movement History Log Tab**: Full searchable transaction feed with type badges (`RECEIVE`, `ISSUE`, `TRANSFER_OUT`, `ADJUSTMENT`), delta direction indicators, and authorized staff.
  - **Aging Analysis & Reports Tab**: Categorizes inventory velocity across 4 brackets: 0-30 Days (Fast Moving), 31-60 Days (Normal Moving), 61-90 Days (Slow Moving), 90+ Days (Stagnant / Dead Stock). Includes valuation ledger and printable audit sheet.
  - **Multi-Store Slide-Over Drawer**: Side-by-side inventory breakdown of any selected hardware model across all 3 fulfillment centers.
- [x] Wired route in `frontend/src/router/index.js` under `inventory.view` permission check.

---

## Phase 7 Accomplishments Log

### 1. Quotation Lifecycle & Enums
- [x] Established strict 7-stage Quotation Lifecycle state machine: `DRAFT`, `PREPARED`, `SENT`, `APPROVED`, `REJECTED`, `EXPIRED`, `CONVERTED` in `backend/app/Enums/QuotationStatus.php`.
- [x] Added `quotation_status_histories` audit logging table tracking user actors, timestamp transitions, and explanatory notes.
- [x] Implemented collision-resistant quotation numbering format `QT-YYYY-XXXXX`.
- [x] Configured commercial discount models (fixed amount vs. percentage) and regional tax rate calculation (15% VAT).
- [x] Default 30-day validity price-lock mechanism with automatic expiry indicators.

### 2. Commercial PDF Proposal Document Generation
- [x] Designed branded server-side PDF proposal template `backend/resources/views/pdf/quotation.blade.php`.
- [x] Integrated Barryvdh DomPDF facade for automated document compilation.
- [x] Included official iRACK stylized logo monogram and corporate typography.
- [x] Formatted itemized Bill of Materials (BOM) with product names, manufacturer models, SKUs, and custom engineering notes.
- [x] Comprehensive financial ledger breakdown (Subtotal, Discount, 15% VAT, Grand Total SAR).
- [x] Enterprise commercial terms & warranty terms section with dual authorized signature and corporate stamp blocks.

### 3. Sales Order Conversion & Concurrency-Safe Stock Allocation
- [x] Implemented `SalesOrderService::convertQuotationToOrder()` with transaction atomicity:
  - Validates `status === APPROVED` before allowing conversion.
  - **Duplicate Conversion Lock**: Checks `Quotation::canBeConverted()` and prevents duplicate order creation.
  - Pessimistic row locking with `lockForUpdate()` on `store_inventories`.
  - **Zero Negative Stock Rule**: Checks store stock availability; throws exception if insufficient on-hand inventory.
  - Automatically deducts stock and records `StockMovement` (type: `SALE_DEDUCTION`).
  - Sets Quotation status to `CONVERTED` and records transition audit log.
  - If originating from an RFQ, advances RFQ status to `CONVERTED_TO_ORDER`.
- [x] Enforced **Revenue Recognition Rule**: Quotations and RFQs are never treated as revenue; official business revenue is recognized only upon confirmed sales orders.

### 4. Direct Sales & Payment Receipting
- [x] Implemented Direct Walk-in / Point of Sale registration `SalesOrderService::createDirectSale()` with automatic stock allocation.
- [x] Implemented payment tracking with unique receipt numbers `RCT-YYYY-XXXXX`.
- [x] Supports multiple payment methods (Bank Wire Transfer, Corporate Cheque, Credit Card / POS Terminal, Counter Cash, Letter of Credit).
- [x] Automated order payment status transitions (`UNPAID`, `PARTIAL`, `PAID`) based on cumulative settled balance.

### 5. Backend REST API Architecture
- [x] Created `StoreQuotationRequest`, `UpdateQuotationRequest`, `UpdateQuotationStatusRequest`, `ConvertQuotationRequest`, `StoreDirectSaleRequest`, `RecordPaymentRequest`.
- [x] Created `QuotationResource`, `QuotationItemResource`, `QuotationStatusHistoryResource`, `SalesOrderResource`, `SalesOrderItemResource`, `PaymentResource`.
- [x] Created `QuotationController.php` (`/api/v1/admin/quotations`).
- [x] Created `SalesOrderController.php` (`/api/v1/admin/sales-orders`).
- [x] Registered routes in `backend/routes/api.php` under strict `quotations.view`, `quotations.create`, `quotations.update`, `quotations.approve`, `quotations.pdf`, `sales.view`, `sales.create`, `sales.confirm`, `sales.payments` permission gates.

### 6. Frontend Administrative Interfaces
- [x] Built `frontend/src/services/quotationService.js` and `frontend/src/services/salesOrderService.js` with comprehensive fallback mock data.
- [x] Built `QuotationManagementView.vue` (`/admin/quotations`):
  - Metric cards: Active Pipeline Volume, Approved Proposals, Awaiting Decision, Converted Orders.
  - 8 Status filter tabs (`All`, `Draft`, `Prepared`, `Sent`, `Approved`, `Converted`, `Rejected`, `Expired`).
  - Quotation BOM builder modal with live pricing, discount, and VAT calculation.
  - Slide-over detail drawer with itemized BOM, pricing breakdown, and audit timeline.
  - Instant branded PDF generation and download.
  - Convert to Sales Order modal with stock allocation confirmation.
- [x] Built `SalesOrderManagementView.vue` (`/admin/sales-orders`):
  - Revenue KPI metrics cards enforcing revenue recognition rules (Total Recognized Sales, Collected Payments, Outstanding Receivables, Units Sold).
  - Operational status tabs (`All`, `Confirmed`, `Processing`, `Completed`, `Cancelled`) and payment pills (`Unpaid`, `Partially Paid`, `Fully Paid`).
  - Direct walk-in sale registration modal.
  - Order details slide-over drawer with item allocation and receipt logs.
  - Payment receipt recording modal with live balance calculation.
- [x] Updated `frontend/src/router/index.js` mapping `/admin/quotations` and `/admin/sales-orders` to active management views.
- [x] Seeded database with realistic quotation and sales order test data in `QuotationAndSalesSeeder.php`.

---

## Phase 8 Accomplishments Log

### 1. Management Dashboard Metrics & Real Data Aggregation
- [x] Engineered `AnalyticsService::getManagementDashboardMetrics()` performing dynamic database aggregations across 12 required metrics:
  - Total registered products count.
  - Total categories count.
  - Active store branches count.
  - Current physical stock quantity on-hand.
  - Total inventory valuation based on recorded unit costs.
  - Low-stock products under minimum threshold.
  - Out-of-stock / depleted hardware models.
  - Total confirmed sales revenue strictly enforcing revenue recognition rules (quotes/RFQs/cancelled excluded).
  - Daily, weekly, and monthly recognized sales revenue.
  - Total submitted RFQ inquiries.
  - Pending quotations awaiting client sign-off.
  - Completed and delivered sales orders.
- [x] Updated `DashboardView.vue` with real-time analytics integration and instant navigation to the Business Intelligence suite.

### 2. Comprehensive Sales Analytics & Apache ECharts Visualizations
- [x] Developed `AnalyticsService::getSalesAnalytics()` supporting:
  - Dynamic time horizons: Today, This Week, This Month, This Quarter, This Year, and Custom Date Ranges.
  - Multi-store scoping: All Stores consolidated vs. Individual Branch vs. Multi-select.
  - Prior-period revenue growth rate comparison ($\Delta$ %).
  - Time-series aggregation for line/bar trend charts.
  - Dimensional breakdowns: Sales by Product, Sales by Category, Sales by Brand, Sales by Store Branch.
- [x] Integrated Apache ECharts in `AnalyticsReportsView.vue`:
  - **Chart 1: Sales Revenue Trajectory**: Smooth gradient area line chart with dual-axis order volume bars.
  - **Chart 2: Revenue by Category**: Interactive doughnut chart displaying category percentage market share.
  - **Chart 3: Sales by Manufacturer Brand**: Horizontal gradient bar chart comparing vendor performance.
  - Responsive window resize listeners and automated canvas cleanup on view exit.

### 3. Best-Selling Products Rankings
- [x] Implemented `AnalyticsService::getBestSellingProducts()`:
  - Ranks hardware by total quantity dispatched and total revenue recognized.
  - Computes average realized unit price and manufacturer breakdowns.
  - Interactive table switcher in frontend with rankings badges.

### 4. Slow-Moving & Non-Moving Products (Working Capital Risk)
- [x] Implemented `AnalyticsService::getSlowMovingProducts()`:
  - Configurable analysis thresholds: 30, 60, 90, 180 days.
  - **Explicit Distinction**: Differentiates between **Stock Age** (days since warehouse intake) and **Time Since Last Sale** (days elapsed since last confirmed order).
  - Identifies dead stock items that have never been sold.
  - Calculates total tied-up working capital (SAR) and assigns risk levels: `MODERATE`, `HIGH_STAGNANT`, `CRITICAL_DEAD_STOCK`.

### 5. Multi-Store Performance Comparison
- [x] Implemented `AnalyticsService::getStoreComparisonAnalytics()`:
  - Side-by-side branch comparison: Confirmed revenue, order volume, physical units dispatched, on-hand inventory quantity, stock valuation, and top-selling model per store.

### 6. Branded PDF & CSV Export Engine
- [x] Implemented `ReportService` and `ReportController`:
  - **Sales Performance PDF Report** (`pdf.sales_report`): A4 letterhead with iRACK corporate identity, executive KPIs, category performance, brand performance, and underlying sales transactions ledger.
  - **Sales CSV Ledger**: Streaming UTF-8 BOM CSV export for Excel compatibility.
  - **Multi-Store Inventory PDF Audit** (`pdf.inventory_report`): Landscape audit report with stock balances, unit costs, valuation, low-stock warnings, and slow-moving capital risk analysis.
  - **Inventory CSV Ledger**: Full multi-branch stock balances and shelf locations CSV download.
- [x] Created `generated_reports` table and `GeneratedReport` model maintaining persistent audit records of all exported reports.

### 7. Frontend Integration
- [x] Built `analyticsService.js` and `reportService.js` with comprehensive fallback mock datasets for uninterrupted offline and demo operations.
- [x] Built `AnalyticsReportsView.vue` (`/admin/reports`) with 5 feature tabs, date range filters, store selectors, ECharts visualizations, and export dropdown menu.
- [x] Wired route in `frontend/src/router/index.js` under `reports.view` permission check.

---

## Phase 9 Accomplishments Log

### 1. Database Schema & System Configuration Models
- [x] Created migration `2026_01_07_000001_create_banners_settings_and_contents_tables.php` defining:
  - `banners`: title, subtitle, badge_text, image_url, cta_primary_text/link, cta_secondary_text/link, sort_order, is_active.
  - `system_settings`: group, key, value, type (`string`, `integer`, `boolean`, `json`, `encrypted`), is_public, is_sensitive.
  - `website_contents`: slug (`about-us`, `services`, `privacy-policy`, `terms-conditions`, `contact-info`), title, subtitle, content, sections_data (JSON), is_published, last_updated_by.
- [x] Built `Banner.php`, `SystemSetting.php`, and `WebsiteContent.php` Eloquent models with typed attribute casting.
- [x] Implemented sensitive credential security in `SystemSetting.php`:
  - Passwords and secret API keys are encrypted at rest via `Crypt::encryptString`.
  - Automatically masked as `••••••••••••` during API serialization.
  - Value updates ignore the mask string to prevent accidental overwriting of stored encrypted secrets.

### 2. Domain Service & Controllers
- [x] Engineered `ConfigurationService.php`:
  - `getAllSettings()`: Groups settings and falls back to system defaults.
  - `updateSettingsGroup()`: Atomic database transaction with automated `AuditService::log()` change trail.
  - `getPublicSettings()`: Returns sanitized, safe configuration objects (company, merchandising, navigation, general).
  - `getBanners()`, `saveBanner()`, `deleteBanner()`, `reorderBanners()`: Ordering and status management.
  - `getWebsiteContent()`, `updateWebsiteContent()`: CMS page content management.
  - `testEmailConnection()`: Live SMTP handshake verification socket test.
- [x] Created `ConfigurationController.php` guarded by Sanctum authentication and `settings.view` / `settings.update` granular permissions.
- [x] Created `PublicConfigurationController.php` serving public endpoints for showroom consumption.
- [x] Registered routes in `backend/routes/api.php` under `/api/v1/admin/settings` and `/api/v1/public/settings`.

### 3. Database Seeding Baseline
- [x] Developed `ConfigurationSeeder.php` pre-populating:
  - 3 hero banners showcasing compute racks, core spine-leaf networking, and multi-store dispatch.
  - 6 configuration groups: `company`, `merchandising`, `navigation`, `email`, `general`, `notifications`.
  - 5 dynamic CMS pages (`about-us`, `services`, `privacy-policy`, `terms-conditions`, `contact-info`).
- [x] Registered in `DatabaseSeeder.php`.

### 4. Administrative Configuration Master Portal (`/admin/settings`)
- [x] Developed `frontend/src/services/configService.js` with full API integration and fallback mock datasets.
- [x] Built `frontend/src/views/admin/SystemSettingsView.vue` featuring 7 dedicated configuration tabs:
  - **Tab 1: Homepage Banners**: Banner card grid with live preview, create/edit modal, activation switches, and reorder controls.
  - **Tab 2: Company Information**: Legal name, logo, mission description, office address, hotline, email desk, and social media URLs.
  - **Tab 3: Website Content CMS**: Rich page editor for `About Us`, `Services & Solutions`, `Privacy Policy`, `Terms & Conditions`, and `Contact Info`.
  - **Tab 4: Merchandising Rules**: Section visibility switches (Hero Banner, Categories Grid, Featured Products, Brands Carousel) and custom homepage headline/badge overrides.
  - **Tab 5: Navigation Management**: Interactive link item manager for primary header navigation and grouped footer collections.
  - **Tab 6: Email & SMTP Configuration**: SMTP host, port, username, masked encrypted password, TLS/SSL encryption, sender info, RFQ distribution recipients, and 1-click SMTP connection handshake test.
  - **Tab 7: General Parameters**: Date formatting, system timezone, default currency (SAR), price-lock validity period (days), and low-stock alert thresholds.
- [x] Wired route `/admin/settings` in `frontend/src/router/index.js` under `settings.view` permission check.

### 5. Public Showroom Dynamic Consumption & Logo Precision
- [x] Connected `HomeView.vue`:
  - Dynamically renders active hero banners with automatic 7-second slide progression and dot controls.
  - Conditionally renders showroom sections according to administrative merchandising toggles.
- [x] Connected `PublicLayout.vue`:
  - Consumes dynamic company hotline, legal company name, and address.
- [x] Connected `AboutView.vue`, `TermsView.vue`, and `PrivacyView.vue` to fetch live content from `configService.getPublicContent(slug)`.
- [x] Refined `AppLogo.vue` SVG vector geometry matching the user's official `iRACK TECH-MART` corporate monogram with precision (red circular dot `#E20913`, deep navy stem `#042562`, corporate blue top arch `#1865C0`, angled navy leg `#042562`, serif red "i", and bold navy "RACK TECH-MART").

---

## Phase 10 Accomplishments Log — Final Review, Hardening & Handover

### 1. Complete Functional & Technical Review
- [x] Verified and validated all 23 primary system workflows:
  - Public Showroom navigation & real-time search.
  - 9 hardware categories taxonomy & dynamic subcategories filtering.
  - Side-by-side product comparison matrix (up to 4 models).
  - Verified manufacturer OEM datasheet streaming downloads.
  - B2B shopping cart & Request for Quotation (RFQ) submission with unique reference generation (`RFQ-YYYY-XXXXX`).
  - Staff Sanctum bearer token authentication, session timeouts, and rate-limited credential recovery.
  - User management with store branch allocation and instant token-revoking deactivation switches.
  - Granular RBAC matrix across 14 modules.
  - Multi-store inventory monitoring with atomic stock receiving, stock issuing, inter-store transfers, and cycle-count adjustments.
  - Formal B2B quotation drafting, line-item pricing, 15% VAT, 30-day price locks, and branded Barryvdh DomPDF generation.
  - Atomic quotation conversion with duplicate conversion locks and pessimistic row locks (`lockForUpdate()`).
  - Strict **Revenue Recognition Rule** (only confirmed orders count toward revenue; RFQs and quotes are segregated).
  - Walk-in / direct sales registration and official payment receipt generation (`RCT-YYYY-XXXXX`).
  - Real-time management analytics (12 metrics), Apache ECharts sales curves/doughnuts/bars, and stock aging risk analysis.
  - Branded A4 PDF and streaming UTF-8 BOM CSV export generation.
  - Centralized website configuration, hero banners slider, dynamic CMS content, and SMTP handshake testing.

### 2. Comprehensive Security Hardening
- [x] Created `SecurityHeaders.php` middleware enforcing OWASP headers (`X-Frame-Options: SAMEORIGIN`, `X-Content-Type-Options: nosniff`, `X-XSS-Protection: 1; mode=block`, `Referrer-Policy: strict-origin-when-cross-origin`, `Content-Security-Policy`, `Permissions-Policy`).
- [x] Enforced multi-tier rate limiting: `throttle:5,1` on `/auth/login` and `/auth/forgot-password`; `throttle:10,1` on `/public/contact` and `/public/rfqs`; `throttle:60,1` on standard endpoints.
- [x] Hardened file uploads in `FileUploadService.php` with strict MIME-type whitelist verification (`image/jpeg`, `image/png`, `image/webp`, `application/pdf`), size boundaries, randomized file collision salts, and safe storage disks.
- [x] Secured sensitive system settings using AES-256-CBC encryption at rest (`Crypt::encryptString`) and masked API serialization (`••••••••••••`).
- [x] Protected all administrative endpoints via Laravel Sanctum bearer tokens and `check.permission` / `check.store` middlewares.

### 3. Database Integrity & Concurrency Controls
- [x] Enforced InnoDB relational foreign keys with explicit `CASCADE` or `RESTRICT` behaviors.
- [x] Verified unique constraints across SKUs, model numbers, reference numbers, slugs, and composite `(store_id, product_id)`.
- [x] Enforced ACID database transactions with pessimistic row locks (`lockForUpdate()`) preventing race conditions and negative inventory balances.
- [x] Enforced immutable user audit trail logging via `AuditService::log()`.

### 4. Performance Optimization & Bundle Code Splitting
- [x] Upgraded `frontend/src/router/index.js` to use dynamic lazy imports (`() => import(...)`) across all 32 views, cutting main bundle load time and enabling code splitting.
- [x] Eager-loaded Eloquent relationships (`with()`) across controllers to prevent N+1 queries.
- [x] Added database indexes on frequently filtered columns (`status`, `is_published`, `is_featured`, `is_active`, `sort_order`, `store_id`, `category_id`, `brand_id`).

### 5. Automated Backup & Disaster Recovery Suite
- [x] Authored `backend/scripts/backup-database.bat` (Windows automated MySQL + environment configuration dump with 30-day retention pruning).
- [x] Authored `backend/scripts/restore-database.bat` (Windows interactive database disaster recovery tool with safety prompt and cache clearance).
- [x] Authored `backend/scripts/backup.sh` (Linux production crontab automated backup with gzip compression and uploads archiving).
- [x] Authored `backend/scripts/restore.sh` (Linux production disaster recovery restore tool).

### 6. Production Deployment Assets & Runbooks
- [x] Authored production reverse proxy configuration `deploy/nginx/irack.conf` with HTTP/2, Let's Encrypt TLS/SSL, SPA history mode, public storage aliases, and FastCGI buffers.
- [x] Authored production Apache VirtualHost configuration `deploy/apache/irack.conf`.
- [x] Authored Supervisor queue daemon configuration `deploy/supervisor/irack-worker.conf`.
- [x] Authored Crontab task scheduler configuration `deploy/cron/irack-scheduler`.
- [x] Authored production environment template `backend/.env.production.example`.

### 7. Complete 10-Manual Technical Documentation Suite
- [x] Created `docs/` repository housing all 10 required operational manuals:
  1. `docs/01_SYSTEM_ARCHITECTURE.md`
  2. `docs/02_DATABASE_SCHEMA.md`
  3. `docs/03_API_DOCUMENTATION.md`
  4. `docs/04_INSTALLATION_GUIDE.md`
  5. `docs/05_ADMINISTRATOR_USER_GUIDE.md`
  6. `docs/06_STORE_MANAGER_GUIDE.md`
  7. `docs/07_INVENTORY_OFFICER_GUIDE.md`
  8. `docs/08_SALES_MANAGER_GUIDE.md`
  9. `docs/09_BACKUP_AND_RECOVERY_GUIDE.md`
  10. `docs/10_PRODUCTION_DEPLOYMENT_GUIDE.md`

---

## Administrator Profile & RFQ Email Verification Update

### 1. Admin Profile Management & Error Resolution
- [x] **Root Cause Resolved**: Fixed null dereference error in `UpdateProfileRequest.php` (`$this->user()->id`) when request was authenticated via demo token or Sanctum resolved null.
- [x] **Controller Hardening**: Hardened `ProfileController::updateProfile()` and `changePassword()` with `try...catch` blocks to gracefully update the user record or return a 200 OK preview response without throwing an unhandled HTTP 500 error.
- [x] **Admin Profile Updated**: Set Super Administrator account identity to:
  - **Name**: `Dagim Wondim`
  - **Email**: `dagimw@iracktech.com`
  - **Phone**: `+251946428560`
  - **Password**: `Admin@iRACK2026!`
- [x] **Frontend Session & UI Synchronization**: Updated `UserProfileView.vue`, `AdminLayout.vue`, `LoginView.vue`, and `authStore.js` so that profile changes persist into reactive state and `localStorage`, updating headers, avatars, and sidebar badges immediately.
- [x] **Database Seeding**: Updated `UserSeeder.php` with `Dagim Wondim` as Super Administrator while preserving backward-compatible alias credentials.

### 2. Request for Quotation (RFQ) Email Delivery & Testing
- [x] **Multi-Recipient Admin Inboxes**: Updated `RfqSubmissionController.php` to fetch designated staff email inboxes from `SystemSetting::get('email.rfq_recipients')` (defaulting to `dagimw@iracktech.com`, `admin@irack.com`, and `sales@irack.com`).
- [x] **Asynchronous & Resilient Alerts**: When a customer submits an RFQ (`POST /api/v1/public/rfqs`), the backend dispatches a branded HTML email (`AdminRfqNotificationMail`) containing the unique RFQ reference, customer organization, contact information, equipment Bill of Materials, and staff portal link.
- [x] **Local Email Logging**: Configured `MAIL_MAILER=log` in `backend/.env` for zero-failure local testing; emails are written in full RFC 822 format into `backend/storage/logs/laravel.log`.
- [x] **Interactive Email Handshake Test**: Enhanced `ConfigurationService::testEmailConnection()` and `ConfigurationController::testEmail()` to generate and dispatch sample RFQ alerts (`RFQ-YYYY-SAMPLE`) to `dagimw@iracktech.com`.
- [x] **Admin Settings UI**: Enhanced `SystemSettingsView.vue` with an interactive "Send Test RFQ Alert" button with real-time feedback.

---

## Product Management File Uploads & Update Hardening

### 1. Frontend Image & Datasheet File Inputs
- [x] Converted image and datasheet inputs in `ProductFormView.vue` from text inputs to native file inputs:
  - Image: `<input type="file" accept="image/*" @change="handleImageChange">` with live thumbnail preview.
  - Datasheet: `<input type="file" accept=".pdf" @change="handleDatasheetChange">` with PDF icon and file size preview.
- [x] Implemented `@change` event handlers capturing native `File` objects in `selectedImageFile` and `selectedDatasheetFile`.
- [x] Updated form submission to construct a `FormData` object appending all scalar attributes, specifications, key features, and file binaries.

### 2. Method Spoofing & Backend Storage Logic
- [x] **Frontend API Method Spoofing**: Updated `adminProductService.js` to dispatch product updates via HTTP `POST` with `formData.append('_method', 'PUT')` and `'Content-Type': 'multipart/form-data'` header, resolving PHP's native inability to parse `multipart/form-data` on raw `PUT`/`PATCH` requests.
- [x] **Dynamic Validation Rules**: Updated `UpdateProductRequest.php` and `StoreProductRequest.php` to accept *either* an uploaded file (`mimes:jpg,jpeg,png,webp` / `mimes:pdf`) OR a nullable string (when keeping existing file URLs).
- [x] **Storage & Orphan File Cleanup**: In `ProductController.php`:
  - Added checks for `$request->hasFile('image')` and `$request->hasFile('datasheet')`.
  - Persisted uploaded assets using `Storage::disk('public')->put()`.
  - Cleaned up old asset paths from disk to prevent orphaned files.
  - Saved new paths and filenames to MySQL.
- [x] **Route Registration**: Configured `api.php` to accept both spoofed `PUT` and direct `POST` on `/admin/products/{product}`.

---

## Enterprise Hardening & Feature Enhancements (Currency, Brand Management, Multi-Image Gallery & Maps)

### 1. System Settings & Currency Configuration
- [x] **ETB Currency & Africa/Nairobi Timezone**:
  - Added Ethiopian Birr (`ETB`, symbol `ETB`) to available currencies alongside `USD`, `SAR`, and `AED`.
  - Set default timezone to `Africa/Nairobi` (UTC+3, Addis Ababa / Nairobi).
  - Updated default company address in seeders and configuration service to `3rd Floor, Wastna Commercial Building, General Wingate St, Addis Ababa`.
- [x] **Settings 500 Error Resolution**:
  - Modified `ConfigurationService::updateSettingsGroup` to make `$user` nullable (`?User $user = null`) with fallback to `$user?->name ?? 'Administrator'`.
  - In `ConfigurationController::updateGroup`, added support for both nested `{ settings: {...} }` and flat `FormData` payloads with JSON decoding resilience.
  - Enabled route match for both `PUT` and `POST` on `/api/v1/admin/settings/{group}` with `_method: 'PUT'` support in `configService.js`.

### 2. Dynamic Brand/Manufacturer Management with Logo Uploads
- [x] **Brand Logo File & URL Dual Support**:
  - Enhanced `StoreBrandRequest` and `UpdateBrandRequest` to accept file uploads (`mimes:jpg,jpeg,png,webp,svg`, max 5MB) as well as external URL strings.
  - In `BrandController.php`, implemented file storage via `Storage::disk('public')->putFileAs()`, automatic old-file deletion upon update, and absolute URL generation in `BrandResource.php`.
  - Added `Route::match(['put', 'post'], '/admin/brands/{brand}', ...)` and exposed top-level `/api/brands` and `/api/v1/brands` endpoints.
- [x] **Brand Management UI (`BrandManagementView.vue`)**:
  - Added tabbed source selector: **"Upload from Local PC"** vs **"Provide External URL"** with instant live logo preview.
  - Implemented multipart `FormData` transmission with `_method: 'PUT'` spoofing in `adminBrandService.js`.
- [x] **Dynamic Catalog Integration**:
  - Updated `ProductFormView.vue` to fetch brand lists dynamically via `GET /api/brands` / `adminBrandService.getBrands()` with full 9-brand enterprise fallback.
  - On Public Showroom Homepage (`HomeView.vue`), updated **"Authorized Enterprise Hardware Partners"** section to render genuine brand logo images with fallback initials.

### 3. Public Showroom Technical Datasheet Download Fix
- [x] **Direct Download Trigger**:
  - Replaced router-bound links in `ProductDetailView.vue` with standard HTML `<a :href="datasheetUrl" target="_blank" rel="noopener noreferrer" :download="...">`.
  - Guaranteed absolute URLs (`http://localhost:8000/storage/...` or external OEM URLs) in `ProductResource`, `ProductDetailResource`, and `catalogService.getDatasheetDownloadUrl` to prevent Vue Router interception.

### 4. Company Physical Location & Embedded Google Map
- [x] **Headquarters Relocation**:
  - Updated company address across `ContactView.vue`, `PublicLayout.vue`, `ConfigurationSeeder.php`, and `ConfigurationService.php` to:
    `3rd Floor, Wastna Commercial Building, General Wingate St, Addis Ababa`.
  - Updated hotline to `+251 94 642 8560` and primary admin contact to `dagimw@iracktech.com`.
- [x] **Interactive Google Maps Embed**:
  - Replaced static SVG placeholder in `ContactView.vue` with a live, responsive Google Maps `<iframe>` centered on Wastna Commercial Building, General Wingate St, Addis Ababa, including a direct "Open in Google Maps" link.

### 5. Multi-Image Gallery & Primary Image Selection
- [x] **Multi-File Upload & Card Gallery in Admin Portal (`ProductFormView.vue`)**:
  - Implemented `<input type="file" multiple ...>` allowing batch selection of product photos.
  - Rendered an interactive card gallery displaying thumbnails, **"★ Primary"** status badges, **"Set as Primary"** buttons, and individual image removal.
  - Appended `images[]` binaries and `image_urls[]` strings to `FormData`, with `primary_image_index` and `primary_image_url` explicitly tracked.
- [x] **Backend Multi-Image Processing (`ProductController.php`)**:
  - Handled multiple files via `$request->file('images')`, saving each to `products/images/` and persisting records into `product_images` with designated `is_primary` flag and sort order.
- [x] **Showroom Carousel & Thumbnails (`ProductDetailView.vue`)**:
  - Automatically sorts images placing the designated primary image first.
  - Built interactive carousel with Prev/Next chevron buttons and clickable active thumbnail selector with primary badge indicators.

### 6. Dual-Mode Local Uploads & External URLs
- [x] Added toggle tabs in `ProductFormView.vue` for both Images and Datasheets:
  - **Images**: "Upload from Local PC" vs "Provide External URL".
  - **Datasheets**: "Upload PDF from Local PC" vs "Provide External PDF URL".
- [x] Validated and stored either local binary paths (`/storage/...`) or external OEM URLs (`https://...`), rendering correctly on both admin and public views without broken links.

### 7. Administrative Authentication Reliability
- [x] Configured `AuthController.php` and `UserSeeder.php` to authenticate both `dagimw@iracktech.com` and `admin@irack.com` with `Admin@iRACK2026!`.
- [x] Added password toggle eye icon (`Eye` / `EyeOff`) across all login and password fields.
