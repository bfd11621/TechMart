# iRACK IT Solution — Online Product Showroom, Multi-Store Inventory & Sales Management System

Enterprise-grade B2B web application integrating an online IT product showroom with a multi-store inventory, RFQ, quotation, and sales management portal.

---

## 🎨 Corporate Branding & Design System
- **Primary Navy**: `#042562`
- **Corporate Blue**: `#1865C0`
- **Accent Red**: `#E20913`
- **Light Background**: `#F4F6FA`
- **White**: `#FFFFFF`
- **Dark Text**: `#172033`
- **Secondary Text**: `#64748B`
- **Borders**: `#E2E8F0`
- **Success / Warning / Error**: `#16A34A` / `#F59E0B` / `#DC2626`

---

## 🛠️ Technology Stack
- **Backend**: Laravel 11 / PHP 8.2+, Laravel Sanctum, MySQL / MariaDB, DomPDF
- **Frontend**: Vue.js 3 (`<script setup>` Composition API), Vite, Pinia, Vue Router, Tailwind CSS, Lucide Icons, Apache ECharts, Axios
- **Architecture**: Decoupled RESTful API with atomic database transactions (`DB::transaction`) and pessimistic row locking (`lockForUpdate()`)

---

## 📂 Directory Layout
```
TechMart/
├── backend/                       # Laravel 11 RESTful API Application
│   ├── app/
│   │   ├── Enums/                 # RoleEnum, StockMovementType, RfqStatus, QuotationStatus, OrderStatus
│   │   ├── Http/Controllers/Api/  # Versioned API controllers
│   │   ├── Http/Middleware/       # SecurityHeaders, CheckPermission, CheckStoreScope
│   │   ├── Models/                # Eloquent models (Store, User, Product, Inventory, RFQ, etc.)
│   │   ├── Services/              # InventoryService, QuotationService, SalesOrderService, AnalyticsService
│   │   └── Traits/ApiResponse.php # Standardized API response format
│   ├── database/migrations/       # 3NF relational migrations
│   ├── database/seeders/          # Enterprise seeders (stores, roles, demo products, settings)
│   ├── routes/api.php             # /api/v1 routes
│   └── scripts/                   # Automated backup and restoration scripts (.bat & .sh)
├── frontend/                      # Vue.js 3 + Vite + Tailwind CSS SPA
│   ├── src/
│   │   ├── components/common/     # AppLogo, BaseButton, BaseBadge, BaseToast, BaseModal, BaseInput
│   │   ├── layouts/               # PublicLayout, AdminLayout, AuthLayout
│   │   ├── router/                # Navigation guards, RBAC checks, and lazy-loaded routes
│   │   ├── services/              # API singletons (catalog, inventory, quotes, sales, config)
│   │   ├── stores/                # Pinia stores (auth, cart, compare, notifications)
│   │   └── views/                 # 32 views (Showroom, Catalog, RFQ Cart, Admin Management)
│   ├── tailwind.config.js         # Custom corporate color tokens
│   └── vite.config.js             # Dev proxy to backend port 8000
├── deploy/                        # Production server deployment assets
│   ├── nginx/irack.conf           # Production Nginx server block (HTTP/2, SSL, SPA history)
│   ├── apache/irack.conf          # Production Apache VirtualHost configuration
│   ├── supervisor/irack-worker.conf # Background queue worker daemon config
│   └── cron/irack-scheduler       # Task scheduler crontab
├── docs/                          # Comprehensive 10-Manual Technical Documentation Suite
│   ├── 01_SYSTEM_ARCHITECTURE.md
│   ├── 02_DATABASE_SCHEMA.md
│   ├── 03_API_DOCUMENTATION.md
│   ├── 04_INSTALLATION_GUIDE.md
│   ├── 05_ADMINISTRATOR_USER_GUIDE.md
│   ├── 06_STORE_MANAGER_GUIDE.md
│   ├── 07_INVENTORY_OFFICER_GUIDE.md
│   ├── 08_SALES_MANAGER_GUIDE.md
│   ├── 09_BACKUP_AND_RECOVERY_GUIDE.md
│   └── 10_PRODUCTION_DEPLOYMENT_GUIDE.md
├── PROJECT_PROGRESS.md            # Incremental 10-phase tracking ledger (100% Completed)
├── API_DOCUMENTATION.md           # Master API specification (Sections 1 to 14)
├── README.md                      # Setup and execution guide
├── setup.bat                      # One-click dependency installer
├── start-backend.bat              # Launch Laravel API server
├── start-frontend.bat             # Launch Vite frontend server
└── migrate.bat                    # Run migrations and seeders
```

---

## 🚀 Quick Start Guide

### 1. Prerequisites
- **PHP 8.2+** (Installed in XAMPP: `C:\xampp\php\php.exe`)
- **MySQL / MariaDB** (Running in XAMPP on port 3306)
- **Node.js & npm** (Installed via NVM: `C:\nvm4w\nodejs`)
- **Composer** (Installed in: `C:\ProgramData\ComposerSetup\bin\composer.bat`)

### 2. Automated One-Click Setup
In Windows File Explorer or terminal, simply double-click or run:
```cmd
setup.bat
```
This will install Composer packages, configure `.env`, generate the app key, and install npm frontend packages.

### 3. Database Migration & Seeding
Ensure MySQL is started in your XAMPP Control Panel, then execute:
```cmd
migrate.bat
```
*(Or manually in `backend/`: `php artisan migrate:fresh --seed`)*

### 4. Running the Application
Launch both servers using the provided scripts:
1. **Backend Server**: Run `start-backend.bat` (serves API at `http://localhost:8000`)
2. **Frontend App**: Run `start-frontend.bat` (serves SPA at `http://localhost:5173`)

Open your browser to:
- **Public Showroom**: [http://localhost:5173](http://localhost:5173)
- **Admin Management Portal**: [http://localhost:5173/admin/login](http://localhost:5173/admin/login)

---

## 🔑 Pre-Seeded Demonstration Accounts

| Role | Email | Password | Scope & Branch |
| :--- | :--- | :--- | :--- |
| **Super Administrator** | `admin@irack.com` | `Admin@iRACK2026!` | All branches & full permissions |
| **Store Manager** | `manager.hq@irack.com` | `Manager@iRACK2026!` | Central Distribution Hub (HQ) |
| **Sales Manager** | `sales@irack.com` | `Sales@iRACK2026!` | Downtown Commercial Center |
| **Inventory Officer** | `inventory@irack.com` | `Inventory@iRACK2026!` | Central Distribution Hub (HQ) |

---

## 🌐 Public Customer Website (Implemented in Phase 4)
- **Showroom Homepage**: [`http://localhost:5173`](http://localhost:5173)
  - Features the official **iRACK TECH-MART** branding, hero banners, operational stock network highlights, quick search, featured categories, featured products, and company overview.
- **Shop / Product Catalog**: [`http://localhost:5173/catalog`](http://localhost:5173/catalog)
  - Dual Grid/List views, faceted filters (keyword, category, brand, stock availability), sorting (newest, A-Z, Z-A), responsive pagination, and instant Quick View modal.
- **Hardware Categories Directory**: [`http://localhost:5173/categories`](http://localhost:5173/categories)
  - Taxonomy of the 9 core categories and all child subcategories with product counts and direct navigation.
- **Product Details & Architecture Specs**: [`http://localhost:5173/product/dell-poweredge-r760-2u-rack-server`](http://localhost:5173/product/dell-poweredge-r760-2u-rack-server)
  - Interactive image gallery with thumbnail switcher, multi-store stock breakdown across 3 hubs, OEM PDF datasheet download, tabbed technical specifications matrix, and quantity stepper for quotation cart.
- **Side-by-Side Comparison Matrix**: [`http://localhost:5173/compare`](http://localhost:5173/compare)
  - Compare up to 4 models side-by-side with common specification rows and persistent floating drawer.
- **Authorized Hardware Brands**: [`http://localhost:5173/brands`](http://localhost:5173/brands)
  - Directory of Tier-1 partner manufacturers (Cisco, Dell, HPE, Schneider Electric, Ubiquiti, Fortinet, Synology, Lenovo, Eaton).
- **Turnkey Engineering Services**: [`http://localhost:5173/services`](http://localhost:5173/services)
  - Datacenter containment, structured cabling, spine-leaf switching, online UPS, and 24/7 SLA maintenance.
- **Corporate Profile (About Us)**: [`http://localhost:5173/about`](http://localhost:5173/about)
  - Company background, mission, ISO 9001:2015 certifications, and infrastructure statistics.
- **Shopping Cart & RFQ Request**: [`http://localhost:5173/cart`](http://localhost:5173/cart)
  - Dedicated B2B quotation basket: line-item table, quantity steppers, per-item engineering notes, RFQ inquiry form with company and location fields, and instant confirmation screen with unique reference number generation (`RFQ-YYYY-XXXXX`).
- **Contact & Regional Distribution Hubs**: [`http://localhost:5173/contact`](http://localhost:5173/contact)
  - 3 regional fulfillment centers with addresses, phone numbers, interactive consultation form with reference ticket generation, and maps representation.
- **Legal & Procurement Policies**: [`http://localhost:5173/terms`](http://localhost:5173/terms) & [`http://localhost:5173/privacy`](http://localhost:5173/privacy)
  - RFQ status rules, 30-day price locks, and corporate data handling.

---

## 🛡️ Administrative Modules (Implemented in Phases 2, 3, 5, 6, 7 & 8)
- **Website & System Configuration**: [`http://localhost:5173/admin/settings`](http://localhost:5173/admin/settings)
  - Centralized administrative configuration portal allowing authorized administrators to manage showroom content and system parameters without editing source code:
    1. **Homepage Hero Banners**: Add, edit, toggle active status, and reorder banners with title, subtitle, badge, CTA buttons, and background imagery.
    2. **Company Information**: Legal corporate name, logo, mission overview, office coordinates, hotlines, emails, and social media handles.
    3. **Website Content CMS**: Full rich editor for public content pages (`About Us`, `Services & Solutions`, `Privacy Policy`, `Terms & Conditions`, `Contact Info`).
    4. **Merchandising Rules**: Section visibility toggles (Hero Banner, Categories Grid, Featured Products, Brands Carousel) and custom headline overrides.
    5. **Navigation Management**: Primary header links and structured footer category link collections.
    6. **Email & SMTP Configuration**: SMTP server settings with encrypted password masking (`••••••••••••`), TLS/SSL encryption, RFQ distribution lists, and real-time socket handshake connectivity testing.
    7. **General Parameters**: System timezone, date formatting, default currency (SAR), price-lock validity period (days), and low-stock alert thresholds.
- **Business Intelligence & Reporting**: [`http://localhost:5173/admin/reports`](http://localhost:5173/admin/reports)
  - Interactive sales analytics with Apache ECharts (time-series revenue gradient curves, category doughnut charts, manufacturer brand comparisons).
  - Best-selling hardware rankings by dispatched quantity and revenue generated.
  - Slow-moving and stagnant stock analysis with tied-up capital risk indicators, differentiating stock age from time since last sale.
  - Side-by-side multi-store performance comparison matrix.
  - Branded PDF and CSV report generator (Sales Performance Report, Inventory Valuation Audit) with historical archive.
- **Commercial Quotation Management**: [`http://localhost:5173/admin/quotations`](http://localhost:5173/admin/quotations)
  - 7-Stage Quotation Lifecycle (`DRAFT`, `PREPARED`, `SENT`, `APPROVED`, `REJECTED`, `EXPIRED`, `CONVERTED`).
  - Prepare B2B formal proposals with line-item BOM pricing, commercial discounts, 15% VAT calculation, and 30-day price locks.
  - Branded PDF generation using Barryvdh DomPDF with letterhead, signatures, and terms.
  - One-click atomic conversion of approved quotations into confirmed Sales Orders (`SO-YYYY-XXXXX`).
- **Sales Orders & Payment Tracking**: [`http://localhost:5173/admin/sales-orders`](http://localhost:5173/admin/sales-orders)
  - Executive Sales KPIs enforcing the **Revenue Recognition Rule** (only confirmed orders count toward revenue; RFQs and quotations are strictly segregated).
  - Concurrency-safe physical stock deduction with row-level locks (`lockForUpdate()`) and negative-stock prevention.
  - Direct walk-in / phone sales registration.
  - Official payment receipt generation (`RCT-YYYY-XXXXX`) supporting wire transfer, cheque, credit card, and cash with real-time balance due calculations.
- **Multi-Store Inventory & Movements**: [`http://localhost:5173/admin/inventory`](http://localhost:5173/admin/inventory)
  - Real-time physical inventory monitoring across all 3 regional fulfillment hubs (Central HQ, Commercial Center, North Regional Depot).
  - Four interactive transactional modals:
    1. **Stock Receiving**: Record incoming shipments from suppliers, credit destination store, and log purchase reference.
    2. **Stock Issuing**: Deduct stock for deployment, RMA, or demo with strict negative-stock prevention.
    3. **Inter-Store Transfers**: Atomic cross-branch transfers utilizing row-level locks (`lockForUpdate()`) guaranteeing zero stock loss.
    4. **Stock Adjustments**: Cycle count reconciliation requiring mandatory explanation and variance delta logging.
  - Multi-store breakdown drawer, real-time transaction movement log, aging analysis report (0-30d fast, 31-60d normal, 61-90d slow, 90d+ stagnant), and printable valuation ledger.
- **Quotation Requests (RFQs)**: [`http://localhost:5173/admin/rfqs`](http://localhost:5173/admin/rfqs)
  - Review submitted customer RFQs, filter across 8 lifecycle statuses (`New Request`, `Under Review`, `Quotation Prepared`, `Sent to Customer`, `Approved`, `Rejected`, `Converted to Order`, `Completed`), inspect requested hardware BOM line items, advance status with full user audit trails, manage internal review notes, and launch quotation preparation.
- **Product Catalog Management**: [`http://localhost:5173/admin/products`](http://localhost:5173/admin/products)
  - Paginated enterprise inventory, real-time search, multi-filter by category/brand/availability, publish status switches, and datasheet downloads.
- **Product Creator & Specs Matrix**: [`http://localhost:5173/admin/products/create`](http://localhost:5173/admin/products/create)
  - 4-tab product authoring wizard: classification, commercial descriptions & key features, flexible technical specifications with presets (Servers, Switches, NAS, UPS, Racks), and multi-image gallery & PDF datasheet uploader.
- **Category Hierarchy**: [`http://localhost:5173/admin/categories`](http://localhost:5173/admin/categories)
  - Multi-level category tree with subcategory nesting, image banners, status toggles, and live product counts across the 9 core IT categories.
- **Brand & Vendor Directory**: [`http://localhost:5173/admin/brands`](http://localhost:5173/admin/brands)
  - Hardware manufacturer partner directory with vendor logos, official websites, and catalog counts.
- **Staff User Management**: [`http://localhost:5173/admin/users`](http://localhost:5173/admin/users)
  - Create staff accounts, assign roles, allocate store branches, toggle active status, and reset passwords.
- **Roles & Capabilities**: [`http://localhost:5173/admin/roles`](http://localhost:5173/admin/roles)
  - Inspect system roles, member counts, and create custom enterprise roles.
- **Granular Permission Matrix**: [`http://localhost:5173/admin/permissions`](http://localhost:5173/admin/permissions)
  - Interactive matrix configuring permissions across 14 modules with Select All controls.
- **Store Branches**: [`http://localhost:5173/admin/stores`](http://localhost:5173/admin/stores)
  - Manage physical retail branches and central warehouses.
- **Staff Profile & Password**: [`http://localhost:5173/admin/profile`](http://localhost:5173/admin/profile)
  - Update profile information, change passwords, and inspect assigned store scope.

---

## 📚 Complete Technical Documentation Suite (`docs/`)

The repository includes a comprehensive 10-manual operational and architectural documentation suite:

1. **[System Architecture Guide](docs/01_SYSTEM_ARCHITECTURE.md)**: High-level architectural topology, technology stack rationale, and core business policies.
2. **[Database Schema Documentation](docs/02_DATABASE_SCHEMA.md)**: Entity Relationship Diagram (ERD), table dictionaries, indexes, and foreign key constraints.
3. **[API Documentation Reference](docs/03_API_DOCUMENTATION.md)** & **[Master API Specification](API_DOCUMENTATION.md)**: Complete request/response schemas for all public and administrative endpoints.
4. **[Installation & Setup Guide](docs/04_INSTALLATION_GUIDE.md)**: Step-by-step local and server installation instructions.
5. **[Administrator User Guide](docs/05_ADMINISTRATOR_USER_GUIDE.md)**: Operational guide for Super Administrators and General Administrators.
6. **[Store Manager Guide](docs/06_STORE_MANAGER_GUIDE.md)**: Standard operating procedures for branch management, stock receiving, issuing, and transfers.
7. **[Inventory Officer Guide](docs/07_INVENTORY_OFFICER_GUIDE.md)**: Frontline warehouse logistics, stock intake, and picking procedures.
8. **[Sales Manager Guide](docs/08_SALES_MANAGER_GUIDE.md)**: Commercial proposals, quotation workflow, sales order conversion, and payment recording.
9. **[Backup & Disaster Recovery Guide](docs/09_BACKUP_AND_RECOVERY_GUIDE.md)**: Automated MySQL backup scripts, 30-day retention policies, and recovery procedures.
10. **[Production Deployment Guide](docs/10_PRODUCTION_DEPLOYMENT_GUIDE.md)**: Nginx, Apache, PHP-FPM, Supervisor daemon, Crontab, and SSL provisioning.

---

## 🔒 Security & Concurrency Verification
- **OWASP Security Headers**: Globally enforced via `SecurityHeaders` middleware.
- **Strict Rate Limiting**: Brute-force lockout on authentication (`5 req/min`), spam prevention on RFQs (`10 req/min`), general throttling (`60 req/min`).
- **Pessimistic Concurrency**: Row-level locking (`lockForUpdate()`) and atomic database transactions (`DB::transaction`) prevent race conditions and negative inventory balances.
- **Revenue Recognition Rule**: Commercial revenue is recognized strictly on confirmed Sales Orders (`SO-YYYY-XXXXX`); RFQs and quotations are strictly segregated.
- **Sensitive Credential Protection**: AES-256 encrypted storage (`Crypt::encryptString`) and masked API responses (`••••••••••••`).

---

## 💾 Backup & Disaster Recovery Commands
- **Windows Automated Backup**: `backend\scripts\backup-database.bat`
- **Windows Interactive Restore**: `backend\scripts\restore-database.bat`
- **Linux Automated Backup**: `/var/www/irack/backend/scripts/backup.sh`
- **Linux Disaster Recovery**: `/var/www/irack/backend/scripts/restore.sh`

