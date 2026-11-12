# iRACK IT Solution — Installation & Setup Guide

## 1. System Requirements

### Hardware Requirements
- **Development / Evaluation**: 4-Core CPU, 8 GB RAM, 20 GB SSD storage.
- **Production Server**: 8-Core CPU (or higher), 16 GB - 32 GB ECC RAM, NVMe SSD storage with RAID 1/10 redundancy.

### Software Prerequisites
- **Operating System**: Windows 10/11 / Windows Server 2022 OR Linux (Ubuntu 22.04 LTS / 24.04 LTS, Debian 12, RHEL 9).
- **PHP**: PHP 8.2 or 8.3 (Required extensions: `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `filter`, `gd`, `hash`, `mbstring`, `openssl`, `pcre`, `pdo`, `pdo_mysql`, `session`, `tokenizer`, `xml`).
- **Web Server**: Nginx 1.24+ (recommended) or Apache 2.4+.
- **Database**: MySQL 8.0+ or MariaDB 10.4+ (InnoDB default storage engine).
- **Node.js**: Node.js v18.x or v20.x LTS + npm v10.x.
- **Dependency Managers**: Composer 2.7+.

---

## 2. Automated One-Click Installation (Windows Development Environment)

The repository includes pre-configured automation scripts in the project root:

1. **Step 1: Install Dependencies**:
   Double click or execute from the repository root:
   ```cmd
   setup.bat
   ```
   This script:
   - Installs all Composer dependencies (`composer install`).
   - Copies `.env.example` to `.env` if `.env` does not exist.
   - Generates the cryptographic `APP_KEY` via `php artisan key:generate`.
   - Creates the public media storage symbolic link via `php artisan storage:link`.
   - Installs all Node.js npm packages in `frontend/` (`npm install`).

2. **Step 2: Database Migration & Seeding**:
   Start MySQL from your XAMPP Control Panel, then run:
   ```cmd
   migrate.bat
   ```
   This script runs `php artisan migrate:fresh --seed`, which seeds all 41 granular permissions, 5 system roles, 3 regional store branches, administrative users, category hierarchy, partner brands, verified hardware models, quotations, sales orders, hero banners, and CMS pages.

3. **Step 3: Launch Applications**:
   - Double-click `start-backend.bat` (starts Laravel API at `http://localhost:8000`).
   - Double-click `start-frontend.bat` (starts Vite dev server at `http://localhost:5173`).

---

## 3. Manual Installation (Linux / macOS / Custom CLI)

### 3.1 Backend Setup
```bash
# Clone and enter backend directory
cd /var/www/irack/backend

# Install PHP packages
composer install --no-dev --optimize-autoloader

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure database credentials in .env:
# DB_HOST=127.0.0.1
# DB_DATABASE=techmart
# DB_USERNAME=your_db_user
# DB_PASSWORD=your_db_password

# Run database migrations and seeders
php artisan migrate:fresh --seed --force

# Create public media storage link
php artisan storage:link

# Set proper directory permissions
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

### 3.2 Frontend Setup
```bash
# Enter frontend directory
cd /var/www/irack/frontend

# Install Node dependencies
npm install

# For local development:
npm run dev

# For production deployment:
npm run build
```

---

## 4. Verification & Initial Logins

Navigate to:
- **Public Showroom**: `http://localhost:5173`
- **Admin Management Portal**: `http://localhost:5173/admin/login`

Pre-seeded administrative credentials:
| Persona | Email | Password | Assigned Scope |
| :--- | :--- | :--- | :--- |
| **Super Administrator** | `admin@irack.com` | `Admin@iRACK2026!` | All branches & full system permissions |
| **Store Manager** | `manager.hq@irack.com` | `Manager@iRACK2026!` | Central Distribution Hub (HQ) |
| **Sales Manager** | `sales@irack.com` | `Sales@iRACK2026!` | Downtown Commercial Center |
| **Inventory Officer** | `inventory@irack.com` | `Inventory@iRACK2026!` | Central Distribution Hub (HQ) |
