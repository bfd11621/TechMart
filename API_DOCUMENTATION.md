# iRACK IT Solution — RESTful API Documentation

## Standard Response Format

### Success Response Envelope
```json
{
  "success": true,
  "message": "Operation completed successfully.",
  "data": {},
  "meta": {
    "pagination": {
      "current_page": 1,
      "per_page": 15,
      "total": 50,
      "last_page": 4
    }
  }
}
```

### Error Response Envelope
```json
{
  "success": false,
  "message": "Validation error or business logic exception.",
  "errors": {
    "field_name": [
      "Specific error description."
    ]
  }
}
```

---

## Implemented Endpoints Directory

### 1. System & Diagnostics
- `GET /api/v1/health` (Public) — Real-time health diagnostic, DB connectivity, and software version check.

---

### 2. Authentication & Session Security (`/api/v1/auth`)

#### `POST /api/v1/auth/login`
- **Access**: Public (Rate-limited: 5 attempts per minute per IP/email)
- **Payload**:
  ```json
  {
    "email": "admin@irack.com",
    "password": "Admin@iRACK2026!"
  }
  ```
- **Response**: Returns Sanctum bearer token, user profile, assigned store, and permissions array.

#### `POST /api/v1/auth/logout`
- **Access**: Authenticated (`auth:sanctum`)
- **Description**: Revokes the active access token and logs audit event.

#### `GET /api/v1/auth/me`
- **Access**: Authenticated (`auth:sanctum`)
- **Description**: Returns current authenticated staff profile with full role, permissions, and active store.

#### `POST /api/v1/auth/forgot-password`
- **Access**: Public (Rate-limited: 5 attempts per minute)
- **Payload**: `{ "email": "admin@irack.com" }`
- **Description**: Generates secure 64-character reset token valid for 60 minutes.

#### `POST /api/v1/auth/reset-password`
- **Access**: Public
- **Payload**:
  ```json
  {
    "email": "admin@irack.com",
    "token": "64_character_token",
    "password": "NewSecurePassword2026!",
    "password_confirmation": "NewSecurePassword2026!"
  }
  ```

#### `PUT /api/v1/auth/profile`
- **Access**: Authenticated (`auth:sanctum`)
- **Payload**: `{ "name": "Alexander Vance", "email": "admin@irack.com", "phone": "+1 (555) 010-0001" }`

#### `PUT /api/v1/auth/change-password`
- **Access**: Authenticated (`auth:sanctum`)
- **Payload**:
  ```json
  {
    "current_password": "OldPassword123!",
    "new_password": "NewPassword2026!",
    "new_password_confirmation": "NewPassword2026!"
  }
  ```

---

### 3. User Management (`/api/v1/admin/users`) — Protected (Sanctum + RBAC)

| Method | Endpoint | Permission Required | Description |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/v1/admin/users` | `users.view` | Paginated staff list with search, role, store, and status filtering |
| `POST` | `/api/v1/admin/users` | `users.create` | Create new staff account with role and branch assignment |
| `GET` | `/api/v1/admin/users/{id}` | `users.view` | Retrieve single staff profile details |
| `PUT` | `/api/v1/admin/users/{id}` | `users.update` | Update staff profile, role, and branch assignment |
| `PATCH` | `/api/v1/admin/users/{id}/status` | `users.status` | Activate or deactivate account (revokes sessions if deactivated) |
| `POST` | `/api/v1/admin/users/{id}/reset-password` | `users.update` | Direct administrative password reset |
| `DELETE` | `/api/v1/admin/users/{id}` | `users.delete` | Delete account (protected: cannot delete self or primary Super Admin) |

---

### 4. Role & Permission Management (`/api/v1/admin/roles`) — Protected

| Method | Endpoint | Permission Required | Description |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/v1/admin/roles` | `roles.view` | List all system and custom roles with member counts |
| `POST` | `/api/v1/admin/roles` | `roles.create` | Create custom role with optional initial permissions |
| `GET` | `/api/v1/admin/roles/{id}` | `roles.view` | View role details and assigned permission IDs |
| `PUT` | `/api/v1/admin/roles/{id}` | `roles.update` | Update custom role title and description |
| `DELETE` | `/api/v1/admin/roles/{id}` | `roles.delete` | Delete custom role (system roles and roles with users are protected) |
| `GET` | `/api/v1/admin/permissions` | `roles.permissions` | List all 41 granular permissions grouped by 14 functional modules |
| `POST` | `/api/v1/admin/roles/{id}/permissions` | `roles.permissions` | Synchronize assigned permission IDs for a role |

---

### 5. Store Branches (`/api/v1/admin/stores`) — Protected

| Method | Endpoint | Permission Required | Description |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/v1/admin/stores` | `stores.view` | List all physical store branches and distribution hubs |
| `POST` | `/api/v1/admin/stores` | `stores.create` | Establish new physical branch |
| `GET` | `/api/v1/admin/stores/{id}` | `stores.view` | View store details and staff allocation |
| `PUT` | `/api/v1/admin/stores/{id}` | `stores.update` | Update store contact details and primary HQ designation |
| `DELETE` | `/api/v1/admin/stores/{id}` | `stores.delete` | Remove branch (primary HQ and stores with inventory/users protected) |

---

### 6. Public Showroom & Product Catalog (`/api/v1/public`)

| Method | Endpoint | Access | Description |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/v1/public/categories` | Public | Active root categories with subcategory hierarchy and product counts |
| `GET` | `/api/v1/public/brands` | Public | Active partner brands with model counts |
| `GET` | `/api/v1/public/products` | Public | Paginated product search with filters (`search`, `category`, `brand`, `featured`, `availability`) |
| `GET` | `/api/v1/public/products/{slug}` | Public | Full product specification sheet, key features, image gallery, and multi-store availability |
| `GET` | `/api/v1/public/products/{id}/datasheet` | Public | Direct stream download for manufacturer PDF datasheet |
| `POST` | `/api/v1/public/contact` | Public (Throttled: 10/min) | Submit technical consultation inquiry; returns unique reference ticket |

---

### 7. Administrative Categories (`/api/v1/admin/categories`) — Protected

| Method | Endpoint | Permission Required | Description |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/v1/admin/categories` | `categories.view` | Tree or flat list of categories with subcategories and product counts |
| `POST` | `/api/v1/admin/categories` | `categories.create` | Create new root category or subcategory with optional image |
| `GET` | `/api/v1/admin/categories/{id}` | `categories.view` | Category details with children |
| `PUT` | `/api/v1/admin/categories/{id}` | `categories.update` | Update category name, description, parent, or image |
| `PATCH` | `/api/v1/admin/categories/{id}/status` | `categories.update` | Toggle category active/inactive status |
| `DELETE` | `/api/v1/admin/categories/{id}` | `categories.delete` | Delete category (blocked if category contains products or subcategories) |

---

### 8. Administrative Brands (`/api/v1/admin/brands`) — Protected

| Method | Endpoint | Permission Required | Description |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/v1/admin/brands` | `brands.view` | List brands with search and product counts |
| `POST` | `/api/v1/admin/brands` | `brands.create` | Register new hardware manufacturer brand |
| `GET` | `/api/v1/admin/brands/{id}` | `brands.view` | View brand details and assigned products |
| `PUT` | `/api/v1/admin/brands/{id}` | `brands.update` | Update brand information, logo, or website |
| `PATCH` | `/api/v1/admin/brands/{id}/status` | `brands.update` | Toggle brand active/inactive status |
| `DELETE` | `/api/v1/admin/brands/{id}` | `brands.delete` | Remove brand (blocked if products are linked to the brand) |

---

### 9. Administrative Products (`/api/v1/admin/products`) — Protected

| Method | Endpoint | Permission Required | Description |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/v1/admin/products` | `products.view` | Paginated product table with search, category, brand, and stock status filters |
| `POST` | `/api/v1/admin/products` | `products.create` | Create product with dynamic technical specifications & key features |
| `GET` | `/api/v1/admin/products/{id}` | `products.view` | Full product record including grouped specs, gallery, and store inventory |
| `PUT` | `/api/v1/admin/products/{id}` | `products.update` | Update product details, sync specs matrix, and edit key features |
| `PATCH` | `/api/v1/admin/products/{id}/publish` | `products.update` | Toggle publication status for public showroom visibility |
| `PATCH` | `/api/v1/admin/products/{id}/featured` | `products.update` | Toggle homepage featured flag |
| `POST` | `/api/v1/admin/products/{id}/images` | `products.update` | Upload gallery image (JPEG/PNG/WebP, max 5MB) |
| `DELETE` | `/api/v1/admin/products/{id}/images/{imageId}` | `products.update` | Remove gallery image and unlink file from disk |
| `PATCH` | `/api/v1/admin/products/{id}/images/{imageId}/primary` | `products.update` | Set specified image as the primary product photo |
| `POST` | `/api/v1/admin/products/{id}/datasheet` | `products.update` | Upload manufacturer technical datasheet (PDF, max 15MB) |
| `DELETE` | `/api/v1/admin/products/{id}/datasheet` | `products.update` | Delete manufacturer datasheet PDF |
| `DELETE` | `/api/v1/admin/products/{id}` | `products.delete` | Delete or archive product (prevents deletion if active inventory or orders exist) |

---

### 10. Public & Administrative RFQ Endpoints (`/api/v1/public/rfqs` & `/api/v1/admin/rfqs`)

#### `POST /api/v1/public/rfqs` (Public Showroom)
- **Access**: Public (Rate-limited: 10 submissions per minute per IP)
- **Description**: Submits a customer B2B Request for Quotation basket atomically with `DB::transaction`. Generates unique `RFQ-YYYY-XXXXX` reference, logs initial `NEW_REQUEST` status history, and sends dual non-blocking notifications to admin and customer.
- **Strict Business Rule**: Creates inquiry record only; stock quantities and financial revenue are never modified.
- **Request Payload**:
  ```json
  {
    "customer_name": "Marcus Vance",
    "company_name": "Apex Cloud Systems Ltd",
    "customer_email": "procurement@apexcloud.com",
    "customer_phone": "+1 (408) 555-0199",
    "location": "Central Distribution Hub (HQ)",
    "customer_notes": "Required for Tier-3 data center expansion in Q1 2027.",
    "items": [
      {
        "product_id": 1,
        "quantity": 4,
        "notes": "Redundant 1100W titanium PSUs required."
      },
      {
        "product_id": 2,
        "quantity": 2,
        "notes": "Dual AC power supplies with C13 cords."
      }
    ]
  }
  ```
- **Response**:
  ```json
  {
    "success": true,
    "message": "Quotation request submitted successfully. Our engineering team will review and contact you shortly.",
    "data": {
      "id": 1,
      "reference_number": "RFQ-2026-00001",
      "customer_name": "Marcus Vance",
      "company_name": "Apex Cloud Systems Ltd",
      "status": "new_request",
      "status_label": "New Request",
      "items_count": 2,
      "created_at": "2026-11-11T10:15:00Z"
    }
  }
  ```

#### Administrative RFQ Endpoints (`/api/v1/admin/rfqs`) — Protected

| Method | Endpoint | Permission Required | Description |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/v1/admin/rfqs` | `quotations.view` | List RFQ requests with filters (search, status, date range, location) and pagination |
| `GET` | `/api/v1/admin/rfqs/{id}` | `quotations.view` | Detailed RFQ view with line items, customer details, and full chronological audit timeline |
| `PATCH` | `/api/v1/admin/rfqs/{id}/status` | `quotations.update` | Advance/transition RFQ status (1 of 8 valid states) with mandatory staff notes and audit trail |
| `PUT` | `/api/v1/admin/rfqs/{id}/notes` | `quotations.update` | Update internal technical/commercial staff notes |
| `DELETE` | `/api/v1/admin/rfqs/{id}` | `quotations.delete` | Soft delete/archive an RFQ request record |

##### RFQ Status Transitions
Valid lifecycle values:
- `new_request` (New Request)
- `under_review` (Under Review)
- `quotation_prepared` (Quotation Prepared)
- `sent_to_customer` (Sent to Customer)
- `approved` (Approved)
- `rejected` (Rejected)
- `converted_to_order` (Converted to Sales Order)
- `completed` (Completed)

---

### 11. Multi-Store Inventory & Stock Movements (`/api/v1/admin/inventory` & `/api/v1/admin/stock-movements`) — Protected

#### Store Inventory Endpoints (`/api/v1/admin/inventory`)

| Method | Endpoint | Permission Required | Description |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/v1/admin/inventory` | `inventory.view` | Paginated store inventories with filters (`store_id`, `category_id`, `brand_id`, `status`, `search`, `shelf_location`) |
| `GET` | `/api/v1/admin/inventory/dashboard` | `inventory.view` | Executive KPI metrics (total products, physical units, valuation, low-stock, out-of-stock, store distribution) |
| `GET` | `/api/v1/admin/inventory/reports` | `reports.view` | Inventory reports (`current_stock`, `low_stock`, `aging` analysis with 4 brackets) |
| `GET` | `/api/v1/admin/inventory/{id}` | `inventory.view` | Detailed inventory record with 3-store breakdown and specifications |
| `PATCH` | `/api/v1/admin/inventory/{id}/alert` | `inventory.update` | Update low-stock warning threshold (`min_alert_quantity`) and `shelf_location` |

#### Stock Movements Endpoints (`/api/v1/admin/stock-movements`)

| Method | Endpoint | Permission Required | Description |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/v1/admin/stock-movements` | `inventory.view` | Paginated transaction audit log with filters (`type`, `store_id`, `search`, `from_date`, `to_date`) |
| `GET` | `/api/v1/admin/stock-movements/{id}` | `inventory.view` | Detailed movement record with line items, cost prices, authorized user, and stores |
| `POST` | `/api/v1/admin/stock-movements/receive` | `inventory.create` | Goods Inward: Receive inventory from OEM supplier and automatically increment store balance |
| `POST` | `/api/v1/admin/stock-movements/issue` | `inventory.create` | Goods Outward: Issue stock (deployment, RMA, demo, scrap) with strict negative-stock validation |
| `POST` | `/api/v1/admin/stock-movements/transfer` | `stock_transfers.create` | Atomic inter-store transfer utilizing pessimistic locking (`lockForUpdate()`) across branches |
| `POST` | `/api/v1/admin/stock-movements/adjust` | `inventory.adjust` | Physical audit cycle count reconciliation with mandatory reason (min 5 chars) and delta logging |

#### Enhanced Store Endpoints (`/api/v1/admin/stores`)

| Method | Endpoint | Permission Required | Description |
| :--- | :--- | :--- | :--- |
| `PATCH` | `/api/v1/admin/stores/{store}/status` | `stores.update` | Toggle store active/inactive status (cannot deactivate primary HQ) |
| `POST` | `/api/v1/admin/stores/{store}/employees` | `stores.update` | Synchronize authorized employee user IDs allocated to the branch |

##### Stock Movement Types
- `RECEIVE`: Goods Inward from supplier (`+` quantity)
- `ISSUE`: Goods Outward for project, RMA, or write-off (`-` quantity)
- `TRANSFER_OUT` / `TRANSFER_IN`: Inter-store cross-branch movement
- `ADJUSTMENT`: Manual physical inventory audit reconciliation (`+/-` variance delta)
- `SALE_DEDUCTION`: Sales Order confirmation allocation
- `RETURN`: Client return restock

---

## 12. Quotations & Sales Management Endpoints (Phase 7)

### 12.1 Commercial Quotation Management (`/api/v1/admin/quotations`)

| Method | Endpoint | Permission Required | Description |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/v1/admin/quotations` | `quotations.view` | Paginated proposals index with status tabs, search, store filter, and pipeline totals |
| `POST` | `/api/v1/admin/quotations` | `quotations.create` | Create formal quotation proposal (from RFQ or direct solution) |
| `GET` | `/api/v1/admin/quotations/{id}` | `quotations.view` | Retrieve detailed quotation proposal with items, specifications, and audit timeline |
| `PUT` | `/api/v1/admin/quotations/{id}` | `quotations.update` | Modify quotation pricing, discounts, line items, and validity |
| `PATCH` | `/api/v1/admin/quotations/{id}/status` | `quotations.approve` | Advance quotation lifecycle state (`PREPARED`, `SENT`, `APPROVED`, `REJECTED`, `EXPIRED`) |
| `GET` | `/api/v1/admin/quotations/{id}/pdf` | `quotations.pdf` | Generate and download branded A4 PDF proposal document |
| `DELETE` | `/api/v1/admin/quotations/{id}` | `quotations.update` | Archive/delete draft or rejected quotation |

#### Quotation Lifecycle Statuses:
- `DRAFT`: Preliminary proposal under development
- `PREPARED`: Technical and commercial review complete, ready for client delivery
- `SENT`: Dispatched to client with 30-day price lock
- `APPROVED`: Client officially accepted and signed commercial proposal
- `REJECTED`: Client declined proposal
- `EXPIRED`: Validity period elapsed without order confirmation
- `CONVERTED`: Converted to confirmed Sales Order; physical stock deducted

---

### 12.2 Sales Order & Payment Management (`/api/v1/admin/sales-orders`)

| Method | Endpoint | Permission Required | Description |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/v1/admin/sales-orders` | `sales.view` | Paginated confirmed sales orders with payment status, customer, and store filters |
| `GET` | `/api/v1/admin/sales-orders/dashboard` | `sales.view` | Executive sales KPIs strictly enforcing revenue recognition rules |
| `POST` | `/api/v1/admin/sales-orders` | `sales.create` | Direct walk-in / phone purchase registration with automatic inventory allocation |
| `GET` | `/api/v1/admin/sales-orders/{id}` | `sales.view` | Sales order ledger with allocated BOM items, payment receipts, and balance due |
| `POST` | `/api/v1/admin/quotations/{id}/convert` | `sales.confirm` | Convert approved quotation to sales order, deduct physical stock, and recognize revenue |
| `POST` | `/api/v1/admin/sales-orders/{id}/payments` | `sales.payments` | Record payment transaction, issue unique receipt `RCT-YYYY-XXXXX`, and update balance |

#### Order Statuses:
- `CONFIRMED`: Official order confirmed; revenue recognized; inventory allocated
- `PROCESSING`: Equipment picking, staging, and pre-configuration in progress
- `COMPLETED`: Hardware dispatched and accepted at client facility
- `CANCELLED`: Order cancelled; stock released back to inventory; excluded from revenue

#### Payment Statuses:
- `UNPAID`: Zero payments recorded against order
- `PARTIAL`: Advance or partial installments received ($0 < \text{Paid} < \text{Total}$)
- `PAID`: Settled in full ($\text{Paid} \ge \text{Total}$)
- `REFUNDED`: Payment returned to customer upon order cancellation

---

## 13. Business Intelligence & Reporting Endpoints (Phase 8)

### 13.1 Analytics & Visualizations (`/api/v1/admin/analytics`)

| Method | Endpoint | Permission Required | Description |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/v1/admin/analytics/dashboard` | `dashboard.view` | High-level executive dashboard metrics (12 key operational indicators) |
| `GET` | `/api/v1/admin/analytics/sales` | `analytics.view` | Multi-dimensional sales analytics with time-series trend, category, brand, and store breakdowns |
| `GET` | `/api/v1/admin/analytics/best-sellers` | `analytics.view` | Top-selling hardware models ranked by quantity sold and revenue generated |
| `GET` | `/api/v1/admin/analytics/slow-moving` | `analytics.view` | Slow-moving and stagnant stock analysis with capital risk calculations and age metrics |
| `GET` | `/api/v1/admin/analytics/store-comparison` | `analytics.view` | Side-by-side store branch performance comparison matrix |

### 13.2 Report Exports & Historical Access (`/api/v1/admin/reports`)

| Method | Endpoint | Permission Required | Description |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/v1/admin/reports/sales/pdf` | `reports.export` | Stream or download branded A4 Sales Performance PDF report |
| `GET` | `/api/v1/admin/reports/sales/csv` | `reports.export` | Stream and download Sales Transactions CSV ledger with UTF-8 BOM |
| `GET` | `/api/v1/admin/reports/inventory/pdf` | `reports.export` | Stream or download branded Landscape Multi-Store Inventory Audit PDF |
| `GET` | `/api/v1/admin/reports/inventory/csv` | `reports.export` | Stream and download Store Stock Balances CSV ledger |
| `GET` | `/api/v1/admin/reports/history` | `reports.view` | Retrieve paginated historical records of generated reports |

---

## 14. Website & System Configuration Endpoints (Phase 9)

### 14.1 Public Configuration Endpoints (`/api/v1/public`)

| Method | Endpoint | Auth Required | Description |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/v1/public/settings` | None | Retrieve sanitized public website settings (company, merchandising, navigation, general) |
| `GET` | `/api/v1/public/banners` | None | Retrieve list of active homepage promotional banners ordered by sort position |
| `GET` | `/api/v1/public/content/{slug}` | None | Retrieve published CMS page content (`about-us`, `services`, `privacy-policy`, `terms-conditions`, `contact-info`) |

### 14.2 Administrative Settings & CMS Management (`/api/v1/admin`)

| Method | Endpoint | Permission Required | Description |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/v1/admin/settings` | `settings.view` | Retrieve all system settings organized by functional configuration group |
| `PUT` | `/api/v1/admin/settings/{group}` | `settings.update` | Update settings group (e.g., `company`, `merchandising`, `navigation`, `email`, `general`) with audit trail |
| `POST` | `/api/v1/admin/settings/email/test` | `settings.update` | Perform live SMTP server handshake verification and socket connectivity test |
| `GET` | `/api/v1/admin/banners` | `settings.view` | Retrieve all homepage promotional banners including inactive records |
| `POST` | `/api/v1/admin/banners` | `settings.update` | Create a new promotional banner record |
| `PUT` | `/api/v1/admin/banners/{id}` | `settings.update` | Update existing promotional banner details, image URL, CTA buttons, or status |
| `DELETE` | `/api/v1/admin/banners/{id}` | `settings.update` | Remove a promotional banner record |
| `POST` | `/api/v1/admin/banners/reorder` | `settings.update` | Update display order of multiple banners via ordered array of IDs |
| `GET` | `/api/v1/admin/content/{slug}` | `settings.view` | Retrieve editable CMS page record by unique slug |
| `PUT` | `/api/v1/admin/content/{slug}` | `settings.update` | Update title, subtitle, markdown/HTML body content, or structured JSON section metadata |
