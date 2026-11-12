# iRACK IT Solution — Production Deployment Guide

## 1. Production Architecture Overview
The production deployment topology consists of:
- **Reverse Proxy & Web Server**: Nginx with HTTP/2 and Let's Encrypt TLS/SSL.
- **Backend Application**: PHP 8.2+ FastCGI Process Manager (PHP-FPM) running Laravel 11.
- **Frontend SPA**: Static assets built by Vite (`npm run build`) served directly by Nginx with 1-year immutable cache headers.
- **Database**: MySQL 8.0+ / MariaDB 10.4+ with InnoDB and strict SQL modes.
- **Queue Workers**: Supervisor process manager running `queue:work`.
- **Task Scheduler**: Cron running `php artisan schedule:run` every minute.

---

## 2. Server Provisioning & Dependencies (Ubuntu 22.04/24.04 LTS)

```bash
# 1. Update OS Packages
sudo apt update && sudo apt upgrade -y

# 2. Install Nginx, MySQL, Git, Unzip, Supervisor
sudo apt install -y nginx mysql-server git unzip curl supervisor certbot python3-certbot-nginx

# 3. Install PHP 8.2 and Required Extensions
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-bcmath \
    php8.2-curl php8.2-gd php8.2-zip php8.2-intl php8.2-redis

# 4. Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# 5. Install Node.js LTS (v20)
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
```

---

## 3. Application Deployment Procedure

### Step 1: Deploy Source Code
```bash
sudo mkdir -p /var/www/irack
sudo chown -R $USER:www-data /var/www/irack
cd /var/www/irack

# Clone or copy project files into /var/www/irack
```

### Step 2: Configure Production Environment (`.env`)
```bash
cd /var/www/irack/backend
cp .env.production.example .env

# Generate unique cryptographic key
php artisan key:generate

# Edit database and domain values in .env:
# APP_URL=https://irack.com
# DB_DATABASE=irack_production
# DB_USERNAME=irack_user
# DB_PASSWORD=your_secure_password
```

### Step 3: Install Dependencies & Run Database Migrations
```bash
# Backend dependencies (optimized for production)
composer install --no-dev --optimize-autoloader

# Run migrations and seed baseline data
php artisan migrate:fresh --seed --force

# Create public media storage symlink
php artisan storage:link

# Frontend dependencies and Vite production build
cd /var/www/irack/frontend
npm ci
npm run build
```

### Step 4: Directory Permissions Hardening
```bash
sudo chown -R www-data:www-data /var/www/irack/backend/storage /var/www/irack/backend/bootstrap/cache
sudo chmod -R 775 /var/www/irack/backend/storage /var/www/irack/backend/bootstrap/cache
sudo chmod 600 /var/www/irack/backend/.env
```

### Step 5: Web Server Configuration (Nginx)
```bash
# Copy provided Nginx configuration
sudo cp /var/www/irack/deploy/nginx/irack.conf /etc/nginx/sites-available/irack.conf
sudo ln -s /etc/nginx/sites-available/irack.conf /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default

# Verify syntax and reload
sudo nginx -t
sudo systemctl reload nginx

# Issue Let's Encrypt SSL certificate
sudo certbot --nginx -d irack.com -d www.irack.com -d showroom.irack.com -d admin.irack.com
```

### Step 6: Background Queue Worker & Task Scheduler
```bash
# Copy Supervisor worker configuration
sudo cp /var/www/irack/deploy/supervisor/irack-worker.conf /etc/supervisor/conf.d/
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start irack-worker:*

# Enable Crontab scheduler
sudo cp /var/www/irack/deploy/cron/irack-scheduler /etc/cron.d/irack-scheduler
sudo chmod 644 /etc/cron.d/irack-scheduler
```

---

## 4. Production Performance & Caching Commands

Execute these Laravel optimization commands upon deployment:
```bash
cd /var/www/irack/backend
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

To clear after an update:
```bash
php artisan optimize:clear
```
