# iRACK IT Solution — System Architecture Documentation

## 1. Executive Summary
The **iRACK IT Solution Online Product Showroom, Multi-Store Inventory & Sales Management System** is a unified B2B web application engineered specifically for enterprise IT infrastructure distribution (compute servers, spine-leaf switches, storage arrays, online UPS, and modular rack enclosures).

The platform serves two core personas through a unified database and business logic layer:
1. **Public Customer Showroom**: An interactive product catalogue where enterprise clients inspect hardware architectures, examine verified OEM datasheets, compile multi-item quotation baskets, and submit formal Requests for Quotation (RFQs).
2. **Administrative Management Portal**: A secure, role-based operations portal where authorized staff manage multi-store inventories, process RFQs, generate price-protected formal quotations, convert quotes into confirmed sales orders, recognize revenue, record payment receipts, analyze time-series sales, and manage dynamic website configurations.

---

## 2. Architectural Blueprint & Component Topology

```
+-----------------------------------------------------------------------------------+
|                              CLIENT TIER (BROWSER)                                |
|                                                                                   |
|  +-------------------------------------+   +------------------------------------+ |
|  |     PUBLIC CUSTOMER SHOWROOM        |   |   ADMINISTRATIVE MANAGEMENT PORTAL | |
|  |   - Hero Banners & Search Strip     |   |   - Management Executive Dashboard | |
|  |   - 9 IT Categories Taxonomy        |   |   - Multi-Store Inventory Engine   | |
|  |   - Product Comparison Matrix       |   |   - RFQ & Quotation Proposal Desks | |
|  |   - RFQ Quotation Cart Checkout     |   |   - Sales Orders & Payment Ledgers | |
|  |   - OEM PDF Datasheet Downloads     |   |   - Apache ECharts Analytics & BI  | |
|  +-------------------------------------+   +------------------------------------+ |
|                     \                                 /                           |
|                      \                               /                            |
|          Vue.js 3 SPA (Vite + Tailwind CSS + Pinia + Vue Router + Axios)          |
+-----------------------------------------------------------------------------------+
                                         |
                   HTTPS REST API (JSON) + Sanctum Bearer Tokens
                                         |
+-----------------------------------------------------------------------------------+
|                        APPLICATION SERVER (LARAVEL 11)                            |
|                                                                                   |
|  +-----------------------------------------------------------------------------+  |
|  |                         GATEWAY & SECURITY MIDDLEWARE                       |  |
|  |   - SecurityHeaders (OWASP: CSP, X-Frame, X-Content, HSTS)                  |  |
|  |   - Rate Limiting (Throttle: 5/min on Auth, 10/min on RFQs, 60/min General)  |  |
|  |   - Laravel Sanctum Token Authentication & Expiration Verification          |  |
|  |   - Granular RBAC CheckPermission Middleware                                |  |
|  |   - Store Scoping Isolation (CheckStoreScope)                               |  |
|  +-----------------------------------------------------------------------------+  |
|                                         |                                         |
|  +-----------------------------------------------------------------------------+  |
|  |                           DOMAIN SERVICE LAYER                              |  |
|  |   - InventoryService (Atomic locks, movements, transfers, cycle counting)   |  |
|  |   - QuotationService (Calculations, 30-day locks, Barryvdh DomPDF generator)|  |
|  |   - SalesOrderService (Strict revenue recognition, receipt generation)      |  |
|  |   - AnalyticsService (Dashboard KPIs, ECharts aggregation, stock aging)     |  |
|  |   - ReportService (A4 PDF reports & streaming UTF-8 BOM CSV exports)        |  |
|  |   - ConfigurationService (Settings CRUD, secret encryption, SMTP tests)     |  |
|  |   - FileUploadService (MIME validation, sanitization, public disk storage)   |  |
|  |   - AuditService (Automated immutable activity logging)                     |  |
|  +-----------------------------------------------------------------------------+  |
+-----------------------------------------------------------------------------------+
                                         |
                Pessimistic Row Locks (`lockForUpdate()`) + Transactions
                                         |
+-----------------------------------------------------------------------------------+
|                             DATABASE TIER (MYSQL)                                 |
|                                                                                   |
|  +-------------------------------------+   +------------------------------------+ |
|  |        SECURITY & IDENTITY          |   |          CATALOG & EAV             | |
|  |   - users, roles, permissions       |   |   - categories (nested tree)       | |
|  |   - role_permissions, audit_logs    |   |   - brands, products               | |
|  |   - personal_access_tokens          |   |   - product_specifications         | |
|  +-------------------------------------+   |   - product_images                 | |
|                                            +------------------------------------+ |
|  +-------------------------------------+   +------------------------------------+ |
|  |       MULTI-STORE INVENTORY         |   |         COMMERCIAL WORKFLOW        | |
|  |   - stores (3 regional hubs)        |   |   - rfq_requests, rfq_items        | |
|  |   - store_inventories (balances)    |   |   - quotations, quotation_items    | |
|  |   - stock_movements (audit ledger)  |   |   - quotation_status_histories     | |
|  |   - stock_movement_items            |   |   - sales_orders, sales_order_items| |
|  +-------------------------------------+   |   - payments (receipt ledgers)     | |
|                                            |   - banners, system_settings, cms  | |
|                                            +------------------------------------+ |
+-----------------------------------------------------------------------------------+
```

---

## 3. Technology Stack Specification

| Tier | Component | Selection & Version | Rationale |
| :--- | :--- | :--- | :--- |
| **Backend Framework** | Application Core | **Laravel 11.x / PHP 8.2+** | Robust MVC, Eloquent ORM, declarative validation, migration engine, and security ecosystem. |
| **Authentication** | API Token Manager | **Laravel Sanctum** | Lightweight, high-performance personal access tokens with custom expiration and ability scopes. |
| **PDF Generation** | Server-side Engine | **Barryvdh DomPDF** | Renders HTML/CSS templates into pixel-accurate corporate letterhead PDF quotations and reports. |
| **Database** | Relational Store | **MySQL 8.0+ / MariaDB 10.4+** | ACID-compliant storage engine (InnoDB) supporting row-level locks and foreign key constraints. |
| **Frontend Framework** | Client SPA | **Vue.js 3 (`<script setup>`)** | Reactive Composition API, clean code organization, and high-performance DOM rendering. |
| **Build Tool** | Bundler & Dev Server | **Vite 5.x** | Sub-second HMR, optimized Rollup production builds with automated route chunk splitting. |
| **State Management** | Global Client Stores | **Pinia 2.x** | Modular stores (`authStore`, `cartStore`, `compareStore`, `notificationStore`) with localStorage persistence. |
| **Styling & Icons** | UI Design System | **Tailwind CSS 3.x + Lucide Icons**| Utility-first responsive corporate design system adhering to iRACK branding standards. |
| **Data Visualization** | Charting Library | **Apache ECharts 5.x** | Enterprise interactive charts (gradient area curves, category doughnuts, horizontal vendor bars). |
| **HTTP Client** | API Communication | **Axios 1.x** | Configured interceptors for automated bearer token injection and 401 unauthenticated handling. |

---

## 4. Fundamental Business Rules & Architectural Tenets

### Rule 1: RFQ vs. Confirmed Sale Separation
An inquiry submitted via the public showroom is strictly a **Request for Quotation (RFQ)**. It creates no financial obligation, charges no credit card, and does **not** recognize commercial revenue.

### Rule 2: Strict Revenue Recognition
Revenue is recognized **exclusively** upon the creation or confirmation of an authorized **Sales Order** (`OrderStatus::CONFIRMED`, `PROCESSING`, or `COMPLETED`). RFQs, pending quotations, expired proposals, and cancelled orders are mathematically barred from recognized revenue metrics.

### Rule 3: Zero Negative Stock & Atomic Inventory Deductions
Store stock deductions occur under strict database transactions utilizing InnoDB pessimistic row locks (`lockForUpdate()`). If on-hand available stock ($Quantity - Reserved$) is less than requested quantity, the transaction is rejected immediately with an explanatory exception.

### Rule 4: Data Isolation & Store-Restricted Roles
Store Managers and Inventory Officers are restricted to their assigned physical store branch via `CheckStoreScope` middleware. Only Super Administrators, Sales Managers, and General Administrators possess multi-store consolidated operational scope.

### Rule 5: Sensitive Credential Security
SMTP passwords, database secrets, and private keys are encrypted at rest using Laravel's AES-256-CBC cipher (`Crypt::encryptString`) and masked as `••••••••••••` during serialization. Secret updates ignore the mask placeholder to prevent accidental credential corruption.
