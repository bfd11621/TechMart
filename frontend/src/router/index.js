import { createRouter, createWebHistory } from 'vue-router';
import HomeView from '@/views/public/HomeView.vue';
import { useAuthStore } from '@/stores/authStore';
import { useNotificationStore } from '@/stores/notificationStore';

const routes = [
  // Public Showroom Routes (Phase 4)
  {
    path: '/',
    name: 'home',
    component: HomeView,
    meta: { title: 'Online IT Infrastructure Showroom' },
  },
  {
    path: '/catalog',
    name: 'catalog',
    component: () => import('@/views/public/CatalogView.vue'),
    meta: { title: 'Hardware Catalog & Datacenter Equipment' },
  },
  {
    path: '/categories',
    name: 'categories',
    component: () => import('@/views/public/CategoriesView.vue'),
    meta: { title: 'Hardware Categories & Subcategories' },
  },
  {
    path: '/product/:slug',
    name: 'product-detail',
    component: () => import('@/views/public/ProductDetailView.vue'),
    meta: { title: 'Technical Specifications & Architecture' },
  },
  {
    path: '/products/:slug',
    redirect: (to) => `/product/${to.params.slug}`,
  },
  {
    path: '/compare',
    name: 'product-compare',
    component: () => import('@/views/public/ProductCompareView.vue'),
    meta: { title: 'Technical Specifications Comparison' },
  },
  {
    path: '/contact',
    name: 'contact',
    component: () => import('@/views/public/ContactView.vue'),
    meta: { title: 'Contact Solutions Engineering & Hubs' },
  },
  {
    path: '/about',
    name: 'about',
    component: () => import('@/views/public/AboutView.vue'),
    meta: { title: 'About iRACK IT Solution' },
  },
  {
    path: '/services',
    name: 'services',
    component: () => import('@/views/public/ServicesView.vue'),
    meta: { title: 'Turnkey IT Services & Solutions' },
  },
  {
    path: '/brands',
    name: 'brands',
    component: () => import('@/views/public/BrandsView.vue'),
    meta: { title: 'Authorized Hardware Manufacturers' },
  },
  {
    path: '/privacy',
    name: 'privacy',
    component: () => import('@/views/public/PrivacyView.vue'),
    meta: { title: 'Privacy Policy' },
  },
  {
    path: '/terms',
    name: 'terms',
    component: () => import('@/views/public/TermsView.vue'),
    meta: { title: 'Terms of Procurement' },
  },
  {
    path: '/cart',
    name: 'cart',
    component: () => import('@/views/public/CartView.vue'),
    meta: { title: 'Quotation Cart & Request Submission' },
  },

  // Staff Authentication & Recovery Routes
  {
    path: '/admin/login',
    name: 'admin-login',
    component: () => import('@/views/auth/LoginView.vue'),
    meta: { title: 'Staff Login' },
  },
  {
    path: '/admin/forgot-password',
    name: 'admin-forgot-password',
    component: () => import('@/views/auth/ForgotPasswordView.vue'),
    meta: { title: 'Forgot Password' },
  },
  {
    path: '/admin/reset-password',
    name: 'admin-reset-password',
    component: () => import('@/views/auth/ResetPasswordView.vue'),
    meta: { title: 'Reset Password' },
  },

  // Protected Administrative Routes (Sanctum + RBAC)
  {
    path: '/admin/dashboard',
    name: 'admin-dashboard',
    component: () => import('@/views/admin/DashboardView.vue'),
    meta: { requiresAuth: true, title: 'Executive Dashboard' },
  },
  {
    path: '/admin/profile',
    name: 'admin-profile',
    component: () => import('@/views/admin/UserProfileView.vue'),
    meta: { requiresAuth: true, title: 'Staff Profile' },
  },
  {
    path: '/admin/users',
    name: 'admin-users',
    component: () => import('@/views/admin/UserManagementView.vue'),
    meta: { requiresAuth: true, permission: 'users.view', title: 'Staff User Management' },
  },
  {
    path: '/admin/roles',
    name: 'admin-roles',
    component: () => import('@/views/admin/RoleManagementView.vue'),
    meta: { requiresAuth: true, permission: 'roles.view', title: 'Role Management' },
  },
  {
    path: '/admin/permissions',
    name: 'admin-permissions',
    component: () => import('@/views/admin/PermissionAssignmentView.vue'),
    meta: { requiresAuth: true, permission: 'roles.permissions', title: 'Permission Matrix' },
  },
  {
    path: '/admin/stores',
    name: 'admin-stores',
    component: () => import('@/views/admin/StoreManagementView.vue'),
    meta: { requiresAuth: true, permission: 'stores.view', title: 'Store Branches' },
  },
  {
    path: '/admin/categories',
    name: 'admin-categories',
    component: () => import('@/views/admin/CategoryManagementView.vue'),
    meta: { requiresAuth: true, permission: 'categories.view', title: 'Category Hierarchy' },
  },
  {
    path: '/admin/brands',
    name: 'admin-brands',
    component: () => import('@/views/admin/BrandManagementView.vue'),
    meta: { requiresAuth: true, permission: 'brands.view', title: 'Partner Brands' },
  },
  {
    path: '/admin/products',
    name: 'admin-products',
    component: () => import('@/views/admin/ProductManagementView.vue'),
    meta: { requiresAuth: true, permission: 'products.view', title: 'Product Catalog Management' },
  },
  {
    path: '/admin/products/create',
    name: 'admin-products-create',
    component: () => import('@/views/admin/ProductFormView.vue'),
    meta: { requiresAuth: true, permission: 'products.create', title: 'Register Hardware Model' },
  },
  {
    path: '/admin/products/:id/edit',
    name: 'admin-products-edit',
    component: () => import('@/views/admin/ProductFormView.vue'),
    meta: { requiresAuth: true, permission: 'products.update', title: 'Edit Hardware Model' },
  },
  {
    path: '/admin/inventory',
    name: 'admin-inventory',
    component: () => import('@/views/admin/InventoryManagementView.vue'),
    meta: { requiresAuth: true, permission: 'inventory.view', title: 'Multi-Store Inventory' },
  },
  {
    path: '/admin/rfqs',
    name: 'admin-rfqs',
    component: () => import('@/views/admin/RfqManagementView.vue'),
    meta: { requiresAuth: true, permission: 'quotations.view', title: 'Quotation Requests (RFQs)' },
  },
  {
    path: '/admin/quotations',
    name: 'admin-quotations',
    component: () => import('@/views/admin/QuotationManagementView.vue'),
    meta: { requiresAuth: true, permission: 'quotations.view', title: 'Quotations Management' },
  },
  {
    path: '/admin/sales-orders',
    name: 'admin-sales-orders',
    component: () => import('@/views/admin/SalesOrderManagementView.vue'),
    meta: { requiresAuth: true, permission: 'sales.view', title: 'Sales & Orders' },
  },
  {
    path: '/admin/reports',
    name: 'admin-reports',
    component: () => import('@/views/admin/AnalyticsReportsView.vue'),
    meta: { requiresAuth: true, permission: 'reports.view', title: 'Analytics & Reports' },
  },
  {
    path: '/admin/settings',
    name: 'admin-settings',
    component: () => import('@/views/admin/SystemSettingsView.vue'),
    meta: { requiresAuth: true, permission: 'settings.view', title: 'System Settings' },
  },

  // Fallback route
  {
    path: '/:pathMatch(.*)*',
    redirect: '/',
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition;
    }
    return { top: 0 };
  },
});

router.beforeEach((to, from, next) => {
  document.title = `${to.meta.title ? to.meta.title + ' — ' : ''}iRACK IT Solution`;

  const auth = useAuthStore();
  const notify = useNotificationStore();

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    next({ name: 'admin-login', query: { redirect: to.fullPath } });
    return;
  }

  if (to.name === 'admin-login' && auth.isAuthenticated) {
    next({ name: 'admin-dashboard' });
    return;
  }

  // RBAC Permission Check
  if (to.meta.permission && !auth.hasPermission(to.meta.permission)) {
    notify.error(`Access Forbidden: Your role lacks permission '${to.meta.permission}'.`);
    next({ name: 'admin-dashboard' });
    return;
  }

  next();
});

export default router;
