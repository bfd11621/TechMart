<?php

use App\Http\Controllers\Api\V1\Admin\AnalyticsController;
use App\Http\Controllers\Api\V1\Admin\BrandController;
use App\Http\Controllers\Api\V1\Admin\CategoryController;
use App\Http\Controllers\Api\V1\Admin\ConfigurationController;
use App\Http\Controllers\Api\V1\Admin\InventoryController;
use App\Http\Controllers\Api\V1\Admin\PermissionController;
use App\Http\Controllers\Api\V1\Admin\ProductController;
use App\Http\Controllers\Api\V1\Admin\QuotationController;
use App\Http\Controllers\Api\V1\Admin\ReportController;
use App\Http\Controllers\Api\V1\Admin\RfqManagementController;
use App\Http\Controllers\Api\V1\Admin\RoleController;
use App\Http\Controllers\Api\V1\Admin\SalesOrderController;
use App\Http\Controllers\Api\V1\Admin\StockMovementController;
use App\Http\Controllers\Api\V1\Admin\StoreController;
use App\Http\Controllers\Api\V1\Admin\UserController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Auth\PasswordResetController;
use App\Http\Controllers\Api\V1\Auth\ProfileController;
use App\Http\Controllers\Api\V1\HealthCheckController;
use App\Http\Controllers\Api\V1\Public\CatalogController;
use App\Http\Controllers\Api\V1\Public\ContactController;
use App\Http\Controllers\Api\V1\Public\PublicConfigurationController;
use App\Http\Controllers\Api\V1\Public\RfqSubmissionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — iRACK IT Solution
|--------------------------------------------------------------------------
*/

Route::get('/brands', [CatalogController::class, 'brands']);

Route::prefix('v1')->group(function () {

    // 1. Health Diagnostics
    Route::get('/health', [HealthCheckController::class, 'index']);
    Route::get('/brands', [CatalogController::class, 'brands']);

    // 2. Public Showroom Endpoints
    Route::prefix('public')->group(function () {
        Route::get('/categories', [CatalogController::class, 'categories']);
        Route::get('/brands', [CatalogController::class, 'brands']);
        Route::get('/products', [CatalogController::class, 'products']);
        Route::get('/products/{slug}', [CatalogController::class, 'showProduct']);
        Route::get('/products/{product}/datasheet', [CatalogController::class, 'downloadDatasheet']);
        Route::post('/contact', [ContactController::class, 'submit'])->middleware('throttle:10,1');
        Route::post('/rfqs', [RfqSubmissionController::class, 'store'])->middleware('throttle:10,1');
        Route::get('/settings', [PublicConfigurationController::class, 'settings']);
        Route::get('/banners', [PublicConfigurationController::class, 'banners']);
        Route::get('/content/{slug}', [PublicConfigurationController::class, 'content']);
    });

    // 3. Authentication & Password Recovery
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
        Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword'])->middleware('throttle:5,1');
        Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);

        // Authenticated Session Actions
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/me', [AuthController::class, 'me']);
            Route::put('/profile', [ProfileController::class, 'updateProfile']);
            Route::put('/change-password', [ProfileController::class, 'changePassword']);
        });
    });

    // 4. Protected Administration Portal (Sanctum + RBAC)
    Route::prefix('admin')->middleware(['auth:sanctum'])->group(function () {

        // User Management
        Route::prefix('users')->group(function () {
            Route::get('/', [UserController::class, 'index'])->middleware('check.permission:users.view');
            Route::post('/', [UserController::class, 'store'])->middleware('check.permission:users.create');
            Route::get('/{user}', [UserController::class, 'show'])->middleware('check.permission:users.view');
            Route::put('/{user}', [UserController::class, 'update'])->middleware('check.permission:users.update');
            Route::patch('/{user}/status', [UserController::class, 'toggleStatus'])->middleware('check.permission:users.status');
            Route::post('/{user}/reset-password', [UserController::class, 'resetPassword'])->middleware('check.permission:users.update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->middleware('check.permission:users.delete');
        });

        // Role & Permission Management
        Route::prefix('roles')->group(function () {
            Route::get('/', [RoleController::class, 'index'])->middleware('check.permission:roles.view');
            Route::post('/', [RoleController::class, 'store'])->middleware('check.permission:roles.create');
            Route::get('/{role}', [RoleController::class, 'show'])->middleware('check.permission:roles.view');
            Route::put('/{role}', [RoleController::class, 'update'])->middleware('check.permission:roles.update');
            Route::delete('/{role}', [RoleController::class, 'destroy'])->middleware('check.permission:roles.delete');
            Route::post('/{role}/permissions', [PermissionController::class, 'syncRolePermissions'])->middleware('check.permission:roles.permissions');
        });

        Route::get('/permissions', [PermissionController::class, 'index'])->middleware('check.permission:roles.permissions');

        // Store Branch Management
        Route::prefix('stores')->group(function () {
            Route::get('/', [StoreController::class, 'index'])->middleware('check.permission:stores.view');
            Route::post('/', [StoreController::class, 'store'])->middleware('check.permission:stores.create');
            Route::get('/{store}', [StoreController::class, 'show'])->middleware('check.permission:stores.view');
            Route::put('/{store}', [StoreController::class, 'update'])->middleware('check.permission:stores.update');
            Route::patch('/{store}/status', [StoreController::class, 'toggleStatus'])->middleware('check.permission:stores.update');
            Route::post('/{store}/employees', [StoreController::class, 'assignEmployees'])->middleware('check.permission:stores.update');
            Route::delete('/{store}', [StoreController::class, 'destroy'])->middleware('check.permission:stores.delete');
        });

        // Category Management
        Route::prefix('categories')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])->middleware('check.permission:categories.view');
            Route::post('/', [CategoryController::class, 'store'])->middleware('check.permission:categories.create');
            Route::get('/{category}', [CategoryController::class, 'show'])->middleware('check.permission:categories.view');
            Route::put('/{category}', [CategoryController::class, 'update'])->middleware('check.permission:categories.update');
            Route::patch('/{category}/status', [CategoryController::class, 'toggleStatus'])->middleware('check.permission:categories.update');
            Route::delete('/{category}', [CategoryController::class, 'destroy'])->middleware('check.permission:categories.delete');
        });

        // Brand Management
        Route::prefix('brands')->group(function () {
            Route::get('/', [BrandController::class, 'index'])->middleware('check.permission:brands.view');
            Route::post('/', [BrandController::class, 'store'])->middleware('check.permission:brands.create');
            Route::get('/{brand}', [BrandController::class, 'show'])->middleware('check.permission:brands.view');
            Route::match(['put', 'post'], '/{brand}', [BrandController::class, 'update'])->middleware('check.permission:brands.update');
            Route::patch('/{brand}/status', [BrandController::class, 'toggleStatus'])->middleware('check.permission:brands.update');
            Route::delete('/{brand}', [BrandController::class, 'destroy'])->middleware('check.permission:brands.delete');
        });

        // Product Catalog Management
        Route::prefix('products')->group(function () {
            Route::get('/', [ProductController::class, 'index'])->middleware('check.permission:products.view');
            Route::post('/', [ProductController::class, 'store'])->middleware('check.permission:products.create');
            Route::get('/{product}', [ProductController::class, 'show'])->middleware('check.permission:products.view');
            Route::put('/{product}', [ProductController::class, 'update'])->middleware('check.permission:products.update');
            Route::post('/{product}', [ProductController::class, 'update'])->middleware('check.permission:products.update');
            Route::patch('/{product}/publish', [ProductController::class, 'togglePublish'])->middleware('check.permission:products.update');
            Route::patch('/{product}/featured', [ProductController::class, 'toggleFeatured'])->middleware('check.permission:products.update');
            Route::post('/{product}/images', [ProductController::class, 'uploadImage'])->middleware('check.permission:products.update');
            Route::delete('/{product}/images/{image}', [ProductController::class, 'deleteImage'])->middleware('check.permission:products.update');
            Route::patch('/{product}/images/{image}/primary', [ProductController::class, 'setPrimaryImage'])->middleware('check.permission:products.update');
            Route::post('/{product}/datasheet', [ProductController::class, 'uploadDatasheet'])->middleware('check.permission:products.update');
            Route::delete('/{product}/datasheet', [ProductController::class, 'deleteDatasheet'])->middleware('check.permission:products.update');
            Route::delete('/{product}', [ProductController::class, 'destroy'])->middleware('check.permission:products.delete');
        });

        // Request for Quotation (RFQ) Administration
        Route::prefix('rfqs')->group(function () {
            Route::get('/', [RfqManagementController::class, 'index'])->middleware('check.permission:quotations.view');
            Route::get('/{rfq}', [RfqManagementController::class, 'show'])->middleware('check.permission:quotations.view');
            Route::patch('/{rfq}/status', [RfqManagementController::class, 'updateStatus'])->middleware('check.permission:quotations.update');
            Route::put('/{rfq}/notes', [RfqManagementController::class, 'updateNotes'])->middleware('check.permission:quotations.update');
            Route::delete('/{rfq}', [RfqManagementController::class, 'destroy'])->middleware('check.permission:quotations.delete');
        });

        // Multi-Store Inventory Management
        Route::prefix('inventory')->group(function () {
            Route::get('/', [InventoryController::class, 'index'])->middleware('check.permission:inventory.view');
            Route::get('/dashboard', [InventoryController::class, 'dashboard'])->middleware('check.permission:inventory.view');
            Route::get('/reports', [InventoryController::class, 'reports'])->middleware('check.permission:reports.view');
            Route::get('/{id}', [InventoryController::class, 'show'])->middleware('check.permission:inventory.view');
            Route::patch('/{id}/alert', [InventoryController::class, 'updateAlert'])->middleware('check.permission:inventory.update');
        });

        // Stock Movements (Receiving, Issuing, Transfers, Adjustments)
        Route::prefix('stock-movements')->group(function () {
            Route::get('/', [StockMovementController::class, 'index'])->middleware('check.permission:inventory.view');
            Route::get('/{id}', [StockMovementController::class, 'show'])->middleware('check.permission:inventory.view');
            Route::post('/receive', [StockMovementController::class, 'receive'])->middleware('check.permission:inventory.create');
            Route::post('/issue', [StockMovementController::class, 'issue'])->middleware('check.permission:inventory.create');
            Route::post('/transfer', [StockMovementController::class, 'transfer'])->middleware('check.permission:stock_transfers.create');
            Route::post('/adjust', [StockMovementController::class, 'adjust'])->middleware('check.permission:inventory.adjust');
        });

        // Quotation Management
        Route::prefix('quotations')->group(function () {
            Route::get('/', [QuotationController::class, 'index'])->middleware('check.permission:quotations.view');
            Route::post('/', [QuotationController::class, 'store'])->middleware('check.permission:quotations.create');
            Route::get('/{quotation}', [QuotationController::class, 'show'])->middleware('check.permission:quotations.view');
            Route::put('/{quotation}', [QuotationController::class, 'update'])->middleware('check.permission:quotations.update');
            Route::patch('/{quotation}/status', [QuotationController::class, 'updateStatus'])->middleware('check.permission:quotations.approve');
            Route::get('/{quotation}/pdf', [QuotationController::class, 'downloadPdf'])->middleware('check.permission:quotations.pdf');
            Route::post('/{quotation}/convert', [SalesOrderController::class, 'convert'])->middleware('check.permission:sales.confirm');
            Route::delete('/{quotation}', [QuotationController::class, 'destroy'])->middleware('check.permission:quotations.update');
        });

        // Sales Order Management
        Route::prefix('sales-orders')->group(function () {
            Route::get('/', [SalesOrderController::class, 'index'])->middleware('check.permission:sales.view');
            Route::get('/dashboard', [SalesOrderController::class, 'dashboard'])->middleware('check.permission:sales.view');
            Route::post('/', [SalesOrderController::class, 'store'])->middleware('check.permission:sales.create');
            Route::get('/{order}', [SalesOrderController::class, 'show'])->middleware('check.permission:sales.view');
            Route::post('/{order}/payments', [SalesOrderController::class, 'recordPayment'])->middleware('check.permission:sales.payments');
        });

        // Business Intelligence & Sales Analytics
        Route::prefix('analytics')->group(function () {
            Route::get('/dashboard', [AnalyticsController::class, 'dashboard'])->middleware('check.permission:dashboard.view');
            Route::get('/sales', [AnalyticsController::class, 'sales'])->middleware('check.permission:analytics.view');
            Route::get('/best-sellers', [AnalyticsController::class, 'bestSellers'])->middleware('check.permission:analytics.view');
            Route::get('/slow-moving', [AnalyticsController::class, 'slowMoving'])->middleware('check.permission:analytics.view');
            Route::get('/store-comparison', [AnalyticsController::class, 'storeComparison'])->middleware('check.permission:analytics.view');
        });

        // Enterprise Reporting & Exports (PDF/CSV)
        Route::prefix('reports')->group(function () {
            Route::get('/sales/pdf', [ReportController::class, 'salesPdf'])->middleware('check.permission:reports.export');
            Route::get('/sales/csv', [ReportController::class, 'salesCsv'])->middleware('check.permission:reports.export');
            Route::get('/inventory/pdf', [ReportController::class, 'inventoryPdf'])->middleware('check.permission:reports.export');
            Route::get('/inventory/csv', [ReportController::class, 'inventoryCsv'])->middleware('check.permission:reports.export');
            Route::get('/history', [ReportController::class, 'history'])->middleware('check.permission:reports.view');
        });

        // Centralized Website & System Configuration
        Route::prefix('settings')->group(function () {
            Route::get('/', [ConfigurationController::class, 'getSettings'])->middleware('check.permission:settings.view');
            Route::match(['put', 'post'], '/{group}', [ConfigurationController::class, 'updateGroup'])->middleware('check.permission:settings.update');
            Route::post('/test-email', [ConfigurationController::class, 'testEmail'])->middleware('check.permission:settings.update');
        });

        // Homepage Promotional Banners Management
        Route::prefix('banners')->group(function () {
            Route::get('/', [ConfigurationController::class, 'getBanners'])->middleware('check.permission:settings.view');
            Route::post('/', [ConfigurationController::class, 'storeBanner'])->middleware('check.permission:settings.update');
            Route::put('/{banner}', [ConfigurationController::class, 'updateBanner'])->middleware('check.permission:settings.update');
            Route::delete('/{banner}', [ConfigurationController::class, 'deleteBanner'])->middleware('check.permission:settings.update');
            Route::post('/reorder', [ConfigurationController::class, 'reorderBanners'])->middleware('check.permission:settings.update');
        });

        // Website Content CMS Management
        Route::prefix('content')->group(function () {
            Route::get('/{slug}', [ConfigurationController::class, 'getPageContent'])->middleware('check.permission:settings.view');
            Route::put('/{slug}', [ConfigurationController::class, 'updatePageContent'])->middleware('check.permission:settings.update');
        });
    });
});
