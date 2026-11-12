<template>
  <div class="min-h-screen bg-[#F4F6FA] flex">
    <!-- Desktop Sidebar -->
    <aside
      :class="[
        'bg-[#042562] text-white flex flex-col justify-between transition-all duration-200 z-30',
        sidebarCollapsed ? 'w-20' : 'w-64'
      ]"
    >
      <div>
        <!-- Sidebar Brand Header (White background for logo visibility) -->
        <div class="h-16 px-4 flex items-center justify-between border-b border-slate-200 bg-white">
          <router-link to="/admin/dashboard" v-if="!sidebarCollapsed" class="flex items-center space-x-2.5">
            <AppLogo variant="dark" size="sm" layout="horizontal" />
            <span class="text-[9px] px-1 py-0.5 rounded bg-[#E20913] text-white uppercase font-black tracking-wider">Admin</span>
          </router-link>
          <div v-else class="mx-auto">
            <AppLogo variant="dark" size="sm" :icon-only="true" />
          </div>

          <button
            @click="sidebarCollapsed = !sidebarCollapsed"
            class="text-slate-500 hover:text-[#042562] p-1.5 rounded hover:bg-slate-100 hidden lg:block transition-colors"
          >
            <ChevronLeft v-if="!sidebarCollapsed" class="w-4 h-4" />
            <ChevronRight v-else class="w-4 h-4" />
          </button>
        </div>

        <!-- Store Context Indicator -->
        <div class="p-3 bg-navy-900/60 border-b border-navy-700/50">
          <div v-if="!sidebarCollapsed" class="text-xs">
            <span class="text-slate-400 block text-[10px] uppercase font-semibold">Active Operational Branch</span>
            <div class="flex items-center gap-1.5 mt-0.5 text-white font-medium truncate">
              <StoreIcon class="w-3.5 h-3.5 text-[#1865C0] flex-shrink-0" />
              <span class="truncate">{{ auth.currentStore?.name || 'All Regional Branches (HQ)' }}</span>
            </div>
          </div>
          <div v-else class="flex justify-center" title="Active Branch Context">
            <StoreIcon class="w-4 h-4 text-[#1865C0]" />
          </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="p-3 space-y-1 text-sm font-medium">
          <router-link
            v-for="item in visibleNavItems"
            :key="item.path"
            :to="item.path"
            :class="[
              'flex items-center gap-3 px-3 py-2 rounded-lg transition-colors',
              $route.path === item.path
                ? 'bg-[#1865C0] text-white shadow-sm'
                : 'text-slate-300 hover:bg-navy-700/60 hover:text-white'
            ]"
            :title="sidebarCollapsed ? item.label : ''"
          >
            <component :is="item.icon" class="w-4 h-4 flex-shrink-0" />
            <span v-if="!sidebarCollapsed" class="truncate">{{ item.label }}</span>
            <span
              v-if="!sidebarCollapsed && item.badge"
              class="ml-auto text-[10px] font-bold px-1.5 py-0.5 rounded bg-[#E20913] text-white"
            >
              {{ item.badge }}
            </span>
          </router-link>
        </nav>
      </div>

      <!-- Bottom User Profile Section -->
      <div class="p-3 border-t border-navy-700 bg-[#021840]">
        <div class="flex items-center justify-between">
          <router-link
            to="/admin/profile"
            v-if="!sidebarCollapsed"
            class="flex items-center space-x-2.5 min-w-0 hover:opacity-85 transition-opacity"
            title="View Profile"
          >
            <div class="w-8 h-8 rounded-full bg-[#1865C0]/30 border border-[#1865C0] flex items-center justify-center text-xs font-bold text-white uppercase">
              {{ (auth.user?.name || 'Admin').substring(0, 2) }}
            </div>
            <div class="min-w-0">
              <p class="text-xs font-semibold text-white truncate">{{ auth.user?.name || 'Dagim Wondim' }}</p>
              <p class="text-[10px] text-blue-300 truncate capitalize">{{ auth.user?.role?.name || 'Super Administrator' }}</p>
            </div>
          </router-link>
          <button
            @click="handleLogout"
            class="text-slate-400 hover:text-[#DC2626] p-1.5 rounded transition-colors"
            title="Sign out of Admin Portal"
          >
            <LogOut class="w-4 h-4" />
          </button>
        </div>
      </div>
    </aside>

    <!-- Mobile Sidebar Drawer Backdrop -->
    <transition
      enter-active-class="transition-opacity duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="mobileSidebarOpen"
        class="fixed inset-0 bg-black/60 backdrop-blur-xs z-50 lg:hidden"
        @click="mobileSidebarOpen = false"
      ></div>
    </transition>

    <!-- Mobile Sidebar Drawer Panel -->
    <div
      :class="[
        'fixed top-0 bottom-0 left-0 w-72 max-w-[85vw] z-50 flex flex-col shadow-2xl transition-transform duration-300 ease-in-out lg:hidden',
        mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full'
      ]"
    >
      <!-- Top Section: White background for crisp logo -->
      <div class="h-16 px-4 bg-white border-b border-slate-200 flex items-center justify-between flex-shrink-0">
        <router-link to="/admin/dashboard" @click="mobileSidebarOpen = false" class="flex items-center space-x-2">
          <AppLogo variant="dark" size="sm" layout="horizontal" />
          <span class="text-[9px] px-1 py-0.5 rounded bg-[#E20913] text-white uppercase font-black tracking-wider">Admin</span>
        </router-link>
        <button
          type="button"
          @click="mobileSidebarOpen = false"
          class="p-1.5 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100"
        >
          <X class="w-5 h-5" />
        </button>
      </div>

      <!-- Rest of sidebar below: Corporate Primary Blue -->
      <div class="bg-[#042562] text-white flex-1 flex flex-col justify-between overflow-y-auto p-3">
        <div>
          <!-- Store Context Indicator -->
          <div class="p-3 bg-navy-900/60 rounded-lg border border-navy-700/50 mb-3">
            <span class="text-slate-400 block text-[10px] uppercase font-semibold">Active Operational Branch</span>
            <div class="flex items-center gap-1.5 mt-0.5 text-white font-medium text-xs truncate">
              <StoreIcon class="w-3.5 h-3.5 text-[#1865C0] flex-shrink-0" />
              <span class="truncate">{{ auth.currentStore?.name || 'All Regional Branches (HQ)' }}</span>
            </div>
          </div>

          <!-- Nav Items -->
          <nav class="space-y-1 text-xs font-medium">
            <router-link
              v-for="item in visibleNavItems"
              :key="item.path"
              :to="item.path"
              @click="mobileSidebarOpen = false"
              :class="[
                'flex items-center gap-3 px-3 py-2 rounded-lg transition-colors',
                $route.path === item.path
                  ? 'bg-[#1865C0] text-white shadow-sm'
                  : 'text-slate-300 hover:bg-navy-700/60 hover:text-white'
              ]"
            >
              <component :is="item.icon" class="w-4 h-4 flex-shrink-0" />
              <span class="truncate">{{ item.label }}</span>
              <span
                v-if="item.badge"
                class="ml-auto text-[10px] font-bold px-1.5 py-0.5 rounded bg-[#E20913] text-white"
              >
                {{ item.badge }}
              </span>
            </router-link>
          </nav>
        </div>

        <!-- User profile in drawer -->
        <div class="pt-3 border-t border-navy-700 flex items-center justify-between">
          <router-link
            to="/admin/profile"
            @click="mobileSidebarOpen = false"
            class="flex items-center space-x-2 min-w-0"
          >
            <div class="w-8 h-8 rounded-full bg-[#1865C0]/30 border border-[#1865C0] flex items-center justify-center text-xs font-bold text-white uppercase">
              {{ (auth.user?.name || 'Admin').substring(0, 2) }}
            </div>
            <div class="min-w-0">
              <p class="text-xs font-semibold text-white truncate">{{ auth.user?.name || 'Dagim Wondim' }}</p>
              <p class="text-[10px] text-blue-300 truncate capitalize">{{ auth.user?.role?.name || 'Super Admin' }}</p>
            </div>
          </router-link>
          <button
            @click="handleLogout"
            class="text-slate-400 hover:text-[#DC2626] p-1.5 rounded transition-colors"
          >
            <LogOut class="w-4 h-4" />
          </button>
        </div>
      </div>
    </div>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
      <!-- Top Header -->
      <header class="h-16 bg-white border-b border-slate-200 px-6 flex items-center justify-between shadow-xs">
        <div class="flex items-center gap-4">
          <!-- Mobile Sidebar Toggle -->
          <button
            @click="mobileSidebarOpen = !mobileSidebarOpen"
            class="lg:hidden text-slate-500 hover:text-slate-800"
          >
            <Menu class="w-5 h-5" />
          </button>

          <!-- Breadcrumb / Header Title -->
          <div>
            <h1 class="text-base font-bold text-[#042562] tracking-tight">
              {{ currentTitle }}
            </h1>
            <p class="text-[11px] text-slate-500">iRACK Multi-Store Enterprise Management</p>
          </div>
        </div>

        <!-- Top Right Actions -->
        <div class="flex items-center space-x-4">
          <!-- Public Showroom Shortcut -->
          <router-link
            to="/"
            target="_blank"
            class="text-xs font-medium text-slate-600 hover:text-[#042562] flex items-center gap-1.5 px-3 py-1.5 rounded-md border border-slate-200 hover:border-slate-300 transition-colors"
          >
            <ExternalLink class="w-3.5 h-3.5 text-[#1865C0]" />
            <span>Public Showroom</span>
          </router-link>

          <!-- User Profile shortcut pill -->
          <router-link
            to="/admin/profile"
            class="flex items-center gap-2 pl-2 pr-3 py-1 rounded-full bg-slate-100 hover:bg-slate-200 transition-colors text-xs font-semibold text-[#042562]"
          >
            <div class="w-6 h-6 rounded-full bg-[#042562] text-white flex items-center justify-center text-[10px] font-bold">
              {{ (auth.user?.name || 'A').substring(0, 1) }}
            </div>
            <span>Profile</span>
          </router-link>
        </div>
      </header>

      <!-- View Slot Content -->
      <main class="flex-1 overflow-y-auto p-6">
        <slot></slot>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';
import { useNotificationStore } from '@/stores/notificationStore';
import { authService } from '@/services/authService';
import AppLogo from '@/components/common/AppLogo.vue';
import {
  LayoutDashboard,
  Layers,
  FolderTree,
  Tag,
  Boxes,
  Building,
  FileQuestion,
  FileText,
  BadgeDollarSign,
  TrendingUp,
  Users,
  Shield,
  Key,
  Settings,
  ChevronLeft,
  ChevronRight,
  Store as StoreIcon,
  LogOut,
  Menu,
  ExternalLink,
  X
} from 'lucide-vue-next';

const route = useRoute();
const router = useRouter();
const auth = useAuthStore();
const notify = useNotificationStore();

const sidebarCollapsed = ref(false);
const mobileSidebarOpen = ref(false);

const navItems = [
  { label: 'Executive Dashboard', path: '/admin/dashboard', icon: LayoutDashboard },
  { label: 'Product Catalog', path: '/admin/products', icon: Layers, permission: 'products.view' },
  { label: 'Category Hierarchy', path: '/admin/categories', icon: FolderTree, permission: 'categories.view' },
  { label: 'Partner Brands', path: '/admin/brands', icon: Tag, permission: 'brands.view' },
  { label: 'Multi-Store Inventory', path: '/admin/inventory', icon: Boxes, permission: 'inventory.view' },
  { label: 'Store Branches', path: '/admin/stores', icon: Building, permission: 'stores.view' },
  { label: 'RFQ Inquiries', path: '/admin/rfqs', icon: FileQuestion, permission: 'quotations.view', badge: 'New' },
  { label: 'Quotations', path: '/admin/quotations', icon: FileText, permission: 'quotations.view' },
  { label: 'Sales & Orders', path: '/admin/sales-orders', icon: BadgeDollarSign, permission: 'sales.view' },
  { label: 'Analytics & Reports', path: '/admin/reports', icon: TrendingUp, permission: 'reports.view' },
  { label: 'Staff Users', path: '/admin/users', icon: Users, permission: 'users.view' },
  { label: 'Roles & Access', path: '/admin/roles', icon: Shield, permission: 'roles.view' },
  { label: 'Permission Matrix', path: '/admin/permissions', icon: Key, permission: 'roles.permissions' },
  { label: 'Settings', path: '/admin/settings', icon: Settings, permission: 'settings.view' },
];

const visibleNavItems = computed(() => {
  return navItems.filter(item => {
    if (!item.permission) return true;
    return auth.hasPermission(item.permission);
  });
});

const currentTitle = computed(() => {
  const match = navItems.find(i => i.path === route.path);
  return match ? match.label : 'Management Portal';
});

const handleLogout = async () => {
  try {
    await authService.logout();
  } catch (err) {
    // ignore
  } finally {
    auth.logout();
    notify.info('You have safely logged out of the iRACK administrative portal.');
    router.push('/admin/login');
  }
};
</script>
