# iRACK IT Solution — Database Schema Documentation

## 1. Relational Entity Overview
The database uses the MySQL InnoDB engine to guarantee ACID transaction integrity, strict foreign key constraints, cascade and restrict behaviors, and row-level pessimistic locking for inventory operations.

---

## 2. Entity Relationship Diagram (ERD)

```
 [roles] 1 ──────────── < [role_permissions] > ──────────── 1 [permissions]
    │
    │ 1
    ▼
  [users] 1 ─────────── < [audit_logs]
    │  │
    │  └────────── 1:1 ─── [stores] 1 (Branch Allocation)
    │                         │
    │                         ├────── < [store_inventories] > ────── 1 [products]
    │                         │                                            │
    │                         ├────── < [stock_movements]                  ├─ < [product_images]
    │                         │              │                             ├─ < [product_specifications]
    │                         │              ▼                             │
    │                         │         [stock_movement_items]             │
    │                         │                                            │
    ▼                         ▼                                            ▼
[rfq_requests] 1 ─── < [rfq_items] > ──────────────────────────────────────┘
    │
    ▼ 1:1
[quotations] 1 ───── < [quotation_items] > ────────────────────────────────┘
    │   │
    │   └─────────── < [quotation_status_histories]
    ▼ 1:1
[sales_orders] 1 ─── < [sales_order_items] > ──────────────────────────────┘
    │
    └─────────────── < [payments] (Receipts RCT-YYYY-XXXXX)

 [banners]             [system_settings]             [website_contents]
 [generated_reports]
```

---

## 3. Detailed Data Dictionary

### 3.1 Security & Access Control

#### `roles`
Stores authorization roles.
| Column | Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Unique role identifier |
| `name` | VARCHAR(100) | NOT NULL, UNIQUE | Display role name |
| `slug` | VARCHAR(100) | NOT NULL, UNIQUE | Programmatic slug |
| `description` | VARCHAR(255) | NULLABLE | Role scope description |
| `is_system` | BOOLEAN | DEFAULT FALSE | Protects core system roles |
| `created_at` / `updated_at` | TIMESTAMP | NULLABLE | Standard audit timestamps |

#### `permissions`
Granular functional permissions across 14 modules.
| Column | Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| `name` | VARCHAR(100) | NOT NULL | Human-readable name |
| `slug` | VARCHAR(100) | NOT NULL, UNIQUE | Permission slug (e.g., `quotations.create`) |
| `module` | VARCHAR(50) | NOT NULL, INDEX | Functional category |

#### `users`
Administrative staff accounts.
| Column | Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Staff user identifier |
| `name` | VARCHAR(255) | NOT NULL | Full name |
| `email` | VARCHAR(255) | NOT NULL, UNIQUE | Corporate email address |
| `password` | VARCHAR(255) | NOT NULL | Bcrypt hashed password (cost 12) |
| `phone` | VARCHAR(50) | NULLABLE | Contact telephone |
| `role_id` | BIGINT UNSIGNED | FOREIGN KEY -> `roles.id` | Assigned role |
| `store_id` | BIGINT UNSIGNED | NULLABLE, FOREIGN KEY -> `stores.id` | Assigned branch scope |
| `is_active` | BOOLEAN | DEFAULT TRUE, INDEX | Active status toggle |
| `two_factor_secret` | TEXT | NULLABLE | Encrypted TOTP secret |
| `last_login_at` | TIMESTAMP | NULLABLE | Last session timestamp |
| `last_login_ip` | VARCHAR(45) | NULLABLE | IPv4/IPv6 login address |

---

### 3.2 Product Catalog & Specifications

#### `categories`
Hierarchical taxonomy tree.
| Column | Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Category ID |
| `parent_id` | BIGINT UNSIGNED | NULLABLE, FK -> `categories.id` (CASCADE) | Parent category for subcategory nesting |
| `name` | VARCHAR(150) | NOT NULL | Category title |
| `slug` | VARCHAR(160) | NOT NULL, UNIQUE | URL-safe slug |
| `description` | TEXT | NULLABLE | Technical scope description |
| `image_url` | VARCHAR(500) | NULLABLE | Category visual banner |
| `sort_order` | INT | DEFAULT 0, INDEX | Display position |
| `is_active` | BOOLEAN | DEFAULT TRUE, INDEX | Active status |

#### `brands`
Authorized hardware manufacturer partners.
| Column | Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Brand ID |
| `name` | VARCHAR(100) | NOT NULL, UNIQUE | Manufacturer name |
| `slug` | VARCHAR(120) | NOT NULL, UNIQUE | URL slug |
| `logo_url` | VARCHAR(500) | NULLABLE | Partner logo graphic |
| `website` | VARCHAR(255) | NULLABLE | Official manufacturer URL |
| `is_active` | BOOLEAN | DEFAULT TRUE, INDEX | Active status |

#### `products`
Master hardware models.
| Column | Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Product ID |
| `category_id` | BIGINT UNSIGNED | NOT NULL, FK -> `categories.id` | Assigned classification |
| `brand_id` | BIGINT UNSIGNED | NOT NULL, FK -> `brands.id` | Hardware manufacturer |
| `sku` | VARCHAR(100) | NOT NULL, UNIQUE, INDEX | Unique hardware stock-keeping unit |
| `name` | VARCHAR(255) | NOT NULL | Official product title |
| `slug` | VARCHAR(270) | NOT NULL, UNIQUE | URL slug |
| `model_number` | VARCHAR(100) | NULLABLE, INDEX | Manufacturer model designation |
| `short_description`| TEXT | NULLABLE | Summary overview |
| `full_description` | LONGTEXT | NULLABLE | Full architectural overview |
| `key_features` | JSON | NULLABLE | Array of key bullet features |
| `base_price` | DECIMAL(12,2)| NOT NULL | Standard catalogue price (SAR) |
| `cost_price` | DECIMAL(12,2)| DEFAULT 0.00 | Confidential internal acquisition cost (SAR) |
| `datasheet_url` | VARCHAR(500) | NULLABLE | Public storage path to verified PDF |
| `datasheet_filename`| VARCHAR(255)| NULLABLE | Original PDF filename |
| `is_featured` | BOOLEAN | DEFAULT FALSE, INDEX | Showroom showcase toggle |
| `is_published` | BOOLEAN | DEFAULT TRUE, INDEX | Public visibility toggle |
| `availability_status`| VARCHAR(50)| DEFAULT 'IN_STOCK', INDEX | `IN_STOCK`, `LOW_STOCK`, `PRE_ORDER` |

#### `product_specifications`
Dynamic architectural specifications.
| Column | Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Specification ID |
| `product_id` | BIGINT UNSIGNED | NOT NULL, FK -> `products.id` (CASCADE) | Target hardware unit |
| `spec_group` | VARCHAR(100) | NOT NULL, INDEX | Architecture group (Processor, Memory, Ports) |
| `spec_name` | VARCHAR(100) | NOT NULL | Parameter label |
| `spec_value` | TEXT | NOT NULL | Parameter technical value |
| `unit` | VARCHAR(30) | NULLABLE | Optional engineering unit (GHz, GB, U, Watts) |
| `sort_order` | INT | DEFAULT 0 | Display sequence |

---

### 3.3 Multi-Store Inventory & Movements

#### `stores`
Physical branches and regional fulfillment hubs.
| Column | Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Store ID |
| `code` | VARCHAR(50) | NOT NULL, UNIQUE | Branch code (`ST-HQ-01`) |
| `name` | VARCHAR(150) | NOT NULL | Facility name |
| `email` / `phone` | VARCHAR(100) | NULLABLE | Contact coordinates |
| `address` / `city` | VARCHAR(255) | NOT NULL | Geographic location |
| `is_active` | BOOLEAN | DEFAULT TRUE, INDEX | Operational status |
| `is_primary` | BOOLEAN | DEFAULT FALSE | Flag for central headquarters |

#### `store_inventories`
Store-specific stock balances.
| Column | Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Balance record ID |
| `store_id` | BIGINT UNSIGNED | NOT NULL, FK -> `stores.id` | Target facility |
| `product_id` | BIGINT UNSIGNED | NOT NULL, FK -> `products.id` | Target product model |
| `quantity` | INT | NOT NULL, DEFAULT 0 | Physical quantity on hand |
| `reserved_quantity`| INT | NOT NULL, DEFAULT 0 | Committed quantity |
| `min_alert_quantity`| INT | NOT NULL, DEFAULT 5 | Low-stock threshold |
| `shelf_location` | VARCHAR(100) | NULLABLE | Aisle/rack/shelf coordinate |
| `last_movement_at` | TIMESTAMP | NULLABLE | Timestamp of last stock delta |
| Unique Index: `(store_id, product_id)` to prevent duplicate inventory records |

#### `stock_movements` & `stock_movement_items`
Immutable transaction ledger.
| Column | Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `movement_number` | VARCHAR(50) | NOT NULL, UNIQUE | Transaction serial (`RCV-2026-XXXXX`) |
| `type` | VARCHAR(50) | NOT NULL, INDEX | `RECEIVE`, `ISSUE`, `TRANSFER`, `ADJUSTMENT`, `SALE_DEDUCTION` |
| `source_store_id` | BIGINT UNSIGNED | NULLABLE, FK -> `stores.id` | Origin facility |
| `destination_store_id`| BIGINT UNSIGNED | NULLABLE, FK -> `stores.id` | Destination facility |
| `user_id` | BIGINT UNSIGNED | NOT NULL, FK -> `users.id` | Authorized operator |
| `reason` / `notes` | TEXT | NULLABLE | Operational justification |

---

### 3.4 Commercial Sales & Quotations

#### `rfq_requests` & `rfq_items`
Customer quotation inquiries.
| Column | Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `reference_number` | VARCHAR(50) | NOT NULL, UNIQUE, INDEX | Customer reference (`RFQ-2026-XXXXX`) |
| `customer_name` | VARCHAR(150) | NOT NULL | Contact name |
| `organization_name`| VARCHAR(200) | NOT NULL | Enterprise client |
| `email` / `phone` | VARCHAR(100) | NOT NULL | Client coordinates |
| `status` | VARCHAR(50) | NOT NULL, INDEX | 8 lifecycle stages |

#### `quotations` & `quotation_items`
Commercial B2B proposals with 30-day price locks.
| Column | Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `quotation_number` | VARCHAR(50) | NOT NULL, UNIQUE, INDEX | Formal quote number (`QT-2026-XXXXX`) |
| `rfq_request_id` | BIGINT UNSIGNED | NULLABLE, FK -> `rfq_requests.id` | Linked RFQ |
| `store_id` | BIGINT UNSIGNED | NOT NULL, FK -> `stores.id` | Fulfilling branch |
| `status` | VARCHAR(50) | NOT NULL, INDEX | 7 stages (`DRAFT`, `PREPARED`, `SENT`, `APPROVED`, etc.) |
| `subtotal` | DECIMAL(12,2)| NOT NULL | Sum of item lines |
| `discount_amount` | DECIMAL(12,2)| DEFAULT 0.00 | Applied commercial rebate |
| `tax_amount` | DECIMAL(12,2)| NOT NULL | VAT calculation (e.g. 15%) |
| `total_amount` | DECIMAL(12,2)| NOT NULL | Total gross proposal (SAR) |
| `valid_until` | DATE | NOT NULL | Price-lock expiration date |

#### `sales_orders` & `payments`
Confirmed revenue ledger and official payment receipts.
| Column | Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `order_number` | VARCHAR(50) | NOT NULL, UNIQUE, INDEX | Sales order ID (`SO-2026-XXXXX`) |
| `quotation_id` | BIGINT UNSIGNED | NULLABLE, UNIQUE, FK -> `quotations.id` | Converted quotation (prevents duplicate orders) |
| `status` | VARCHAR(50) | NOT NULL, INDEX | `CONFIRMED`, `PROCESSING`, `COMPLETED`, `CANCELLED` |
| `payment_status` | VARCHAR(50) | NOT NULL, INDEX | `UNPAID`, `PARTIAL`, `PAID`, `REFUNDED` |
| `total_amount` | DECIMAL(12,2)| NOT NULL | Recognized revenue figure |
| `paid_amount` | DECIMAL(12,2)| DEFAULT 0.00 | Total settled installments |
| `receipt_number` | VARCHAR(50) | IN `payments`: NOT NULL, UNIQUE | Official receipt (`RCT-2026-XXXXX`) |
