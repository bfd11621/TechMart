# iRACK IT Solution — Administrator User Guide

## 1. Role Scope & Capabilities
The **Super Administrator** and **General Administrator** personas possess unrestricted, consolidated oversight of the iRACK enterprise platform:
- Unrestricted access across all 3 regional distribution hubs.
- Staff user management, role creation, and granular permission matrix assignment.
- Store branch lifecycle management (adding new warehouses and demo centers).
- Master product catalogue management, specification presets, and datasheet uploads.
- Centralized system settings, promotional hero banners, dynamic CMS content, and SMTP configuration.
- Business intelligence dashboard, executive reports, and immutable audit trails.

---

## 2. Navigating the Administrative Portal

Login at `http://localhost:5173/admin/login` using your administrator credentials. The collapsible sidebar provides organized access to all modules:

### 2.1 Executive Dashboard (`/admin/dashboard`)
Displays 12 real-time operational indicators:
- **Registered Hardware Models**: Total unique SKUs in catalog.
- **Physical Stock Quantity**: Consolidated on-hand inventory across all hubs.
- **Total Inventory Valuation**: Capital tied up in physical inventory ($Quantity \times Cost$).
- **Low-Stock & Out-of-Stock Alerts**: Instant warnings for items nearing or at zero balance.
- **Confirmed Sales Revenue**: Daily, weekly, monthly, and all-time recognized commercial revenue (strictly excluding unconfirmed quotes/RFQs).
- **RFQ Inquiries & Pending Quotes**: Pipeline volume requiring sales desk attention.

### 2.2 Staff User Administration (`/admin/users`)
- **Creating Staff Accounts**: Click **"Register Staff User"**, provide full name, corporate email, phone, role, and assigned store branch.
- **Account Activation / Deactivation**: Toggle the **Active** switch on any user row. Deactivating an account immediately invalidates all active Sanctum tokens, preventing unauthorized access.
- **Direct Password Resets**: Click the key icon on any user row to set a new temporary password for the employee.

### 2.3 Roles & Granular Permission Matrix (`/admin/roles` & `/admin/permissions`)
- **Role Inspector**: Review all system and custom roles, member counts, and descriptions. Core system roles cannot be deleted.
- **Interactive Permission Matrix**: Toggle individual permissions across 14 modules (Dashboard, Users, Roles, Stores, Products, Categories, Brands, Inventory, Transfers, RFQs, Quotations, Sales, Analytics, Settings). Use **"Select All"** per module for rapid delegation.

### 2.4 Website & System Configuration (`/admin/settings`)
- **Homepage Banners**: Add promotional slides with badge text, headline, subtitle, image URL, and CTA links. Use reorder controls to adjust slide sequence.
- **Company Information**: Keep legal corporate name, logo URL, physical HQ coordinates, hotline, email desk, and social media links up to date.
- **Website Content CMS**: Edit textual content and structured JSON statistics for public showroom pages (`About Us`, `Services & Solutions`, `Privacy Policy`, `Terms & Conditions`, `Contact Info`).
- **Merchandising Rules**: Toggle public visibility of showroom sections (Hero Banner, Categories Grid, Featured Datacenter Picks, Partner Brands).
- **Email & SMTP Configuration**: Configure corporate SMTP gateway with encrypted password masking (`••••••••••••`) and execute 1-click connection handshake verification.
- **General Parameters**: Adjust system timezone, date format, default currency (`SAR`), quotation price lock validity (days), and low-stock warning thresholds.
