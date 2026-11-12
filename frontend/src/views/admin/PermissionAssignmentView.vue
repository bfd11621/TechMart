<template>
  <AdminLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h2 class="text-xl font-black text-[#042562] tracking-tight">Granular Permission Matrix</h2>
          <p class="text-xs text-slate-500 mt-0.5">Enforce strict backend access policies per module and capability.</p>
        </div>
        <div class="flex items-center gap-3">
          <router-link
            to="/admin/roles"
            class="px-3.5 py-2 rounded-lg border border-slate-200 bg-white text-xs font-bold text-[#042562] hover:bg-slate-50 shadow-xs"
          >
            ← Back to Roles
          </router-link>
          <BaseButton
            v-if="selectedRole?.slug !== 'super_admin'"
            variant="navy"
            size="md"
            :loading="saving"
            @click="savePermissions"
          >
            <template #prefix><Save class="w-4 h-4 mr-1.5" /></template>
            Save Permissions
          </BaseButton>
        </div>
      </div>

      <!-- Role Selector Tabs -->
      <div class="corporate-card p-2">
        <div class="flex flex-wrap gap-1.5">
          <button
            v-for="role in roles"
            :key="role.id"
            @click="selectRole(role)"
            :class="[
              'px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2',
              selectedRoleId === role.id
                ? 'bg-[#042562] text-white shadow-sm'
                : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'
            ]"
          >
            <Shield class="w-3.5 h-3.5" :class="selectedRoleId === role.id ? 'text-[#1865C0]' : 'text-slate-400'" />
            <span>{{ role.name }}</span>
            <span
              v-if="role.slug === 'super_admin'"
              class="text-[9px] px-1 py-0.2 rounded bg-amber-400 text-amber-950 font-black uppercase"
            >
              All Access
            </span>
          </button>
        </div>
      </div>

      <!-- Super Admin Notice Banner -->
      <div
        v-if="selectedRole?.slug === 'super_admin'"
        class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-[#042562] text-xs flex items-start gap-3"
      >
        <Lock class="w-5 h-5 text-[#1865C0] flex-shrink-0 mt-0.5" />
        <div>
          <strong class="font-bold block text-sm">Super Administrator Role Immunity</strong>
          <p class="text-slate-600 mt-0.5">
            The Super Administrator role has absolute system sovereignty. All 30+ permissions across all modules are automatically granted and permanently enforced on both backend and frontend layers.
          </p>
        </div>
      </div>

      <!-- Permissions Selection Controls Bar -->
      <div v-else class="flex items-center justify-between px-1">
        <div class="text-xs text-slate-500">
          Configuring <strong class="text-[#042562]">{{ selectedRole?.name }}</strong>:
          <span class="font-bold text-[#1865C0] ml-1">{{ selectedPermissionIds.length }} permissions granted</span>
        </div>
        <div class="flex items-center gap-2">
          <button
            @click="selectAll"
            class="text-xs font-semibold text-[#1865C0] hover:underline"
          >
            Select All
          </button>
          <span class="text-slate-300">•</span>
          <button
            @click="deselectAll"
            class="text-xs font-semibold text-slate-500 hover:text-red-600 hover:underline"
          >
            Deselect All
          </button>
        </div>
      </div>

      <!-- Grouped Permission Modules Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div
          v-for="group in permissionGroups"
          :key="group.module"
          class="corporate-card p-5 space-y-4"
        >
          <!-- Module Header -->
          <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
              <div class="w-7 h-7 rounded bg-blue-50 text-[#042562] flex items-center justify-center font-bold text-xs">
                {{ group.label.substring(0, 2).toUpperCase() }}
              </div>
              <h3 class="text-sm font-bold text-[#042562]">{{ group.label }}</h3>
            </div>
            <button
              v-if="selectedRole?.slug !== 'super_admin'"
              @click="toggleGroupAll(group)"
              class="text-[11px] font-semibold text-[#1865C0] hover:underline"
            >
              {{ isGroupAllSelected(group) ? 'Clear' : 'Select Group' }}
            </button>
          </div>

          <!-- Module Permissions Checkbox List -->
          <div class="space-y-2.5">
            <label
              v-for="perm in group.permissions"
              :key="perm.id"
              :class="[
                'flex items-start gap-3 p-2 rounded-lg cursor-pointer transition-colors',
                selectedPermissionIds.includes(perm.id) || selectedRole?.slug === 'super_admin'
                  ? 'bg-blue-50/50 border border-blue-200/60'
                  : 'hover:bg-slate-50 border border-transparent'
              ]"
            >
              <input
                type="checkbox"
                :value="perm.id"
                :disabled="selectedRole?.slug === 'super_admin'"
                :checked="selectedRole?.slug === 'super_admin' || selectedPermissionIds.includes(perm.id)"
                @change="togglePermission(perm.id)"
                class="mt-1 rounded border-slate-300 text-[#042562] focus:ring-[#042562] disabled:opacity-75"
              />
              <div class="text-xs">
                <span class="font-bold text-[#172033] block">{{ perm.name }}</span>
                <span class="text-slate-400 font-mono text-[10px] block">{{ perm.slug }}</span>
                <p class="text-slate-500 text-[11px] mt-0.5">{{ perm.description }}</p>
              </div>
            </label>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import AdminLayout from '@/layouts/AdminLayout.vue';
import BaseButton from '@/components/common/BaseButton.vue';
import { roleService } from '@/services/roleService';
import { useNotificationStore } from '@/stores/notificationStore';
import { Shield, Key, Lock, Save } from 'lucide-vue-next';

const route = useRoute();
const notify = useNotificationStore();

const roles = ref([]);
const selectedRoleId = ref(null);
const selectedPermissionIds = ref([]);
const saving = ref(false);

const permissionGroups = ref([]);

const selectedRole = computed(() => {
  return roles.value.find(r => r.id === selectedRoleId.value);
});

// Demo fallback permissions
const fallbackGroups = [
  {
    module: 'dashboard',
    label: 'Dashboard & Metrics',
    permissions: [
      { id: 1, name: 'Dashboard View', slug: 'dashboard.view', description: 'View Executive Dashboard & High-Level KPIs' }
    ]
  },
  {
    module: 'users',
    label: 'User Management',
    permissions: [
      { id: 2, name: 'Users View', slug: 'users.view', description: 'View Administrative Staff Directory' },
      { id: 3, name: 'Users Create', slug: 'users.create', description: 'Create New Staff Accounts' },
      { id: 4, name: 'Users Update', slug: 'users.update', description: 'Modify Staff Profiles and Roles' },
      { id: 5, name: 'Users Delete', slug: 'users.delete', description: 'Delete Staff Accounts' },
      { id: 6, name: 'Users Status', slug: 'users.status', description: 'Activate or Deactivate Staff Access' },
    ]
  },
  {
    module: 'roles',
    label: 'Roles & Permissions',
    permissions: [
      { id: 7, name: 'Roles View', slug: 'roles.view', description: 'View Roles and Capabilities' },
      { id: 8, name: 'Roles Create', slug: 'roles.create', description: 'Create Custom Roles' },
      { id: 9, name: 'Roles Update', slug: 'roles.update', description: 'Modify Role Details' },
      { id: 10, name: 'Roles Delete', slug: 'roles.delete', description: 'Delete Custom Roles' },
      { id: 11, name: 'Roles Permissions', slug: 'roles.permissions', description: 'Assign and Synchronize Granular Permissions' },
    ]
  },
  {
    module: 'stores',
    label: 'Store Branches',
    permissions: [
      { id: 12, name: 'Stores View', slug: 'stores.view', description: 'View Branch Stores and Warehouses' },
      { id: 13, name: 'Stores Create', slug: 'stores.create', description: 'Establish New Branch Store' },
      { id: 14, name: 'Stores Update', slug: 'stores.update', description: 'Modify Branch Store Information' },
      { id: 15, name: 'Stores Delete', slug: 'stores.delete', description: 'Remove Branch Stores' },
    ]
  },
  {
    module: 'products',
    label: 'Product Catalog',
    permissions: [
      { id: 16, name: 'Products View', slug: 'products.view', description: 'Browse and Search Hardware Products' },
      { id: 17, name: 'Products Create', slug: 'products.create', description: 'Add New Products and Specification Groups' },
      { id: 18, name: 'Products Update', slug: 'products.update', description: 'Modify Product Pricing, Specs, and Datasheets' },
      { id: 19, name: 'Products Delete', slug: 'products.delete', description: 'Remove Products from Catalog' },
    ]
  },
  {
    module: 'inventory',
    label: 'Multi-Store Inventory',
    permissions: [
      { id: 20, name: 'Inventory View', slug: 'inventory.view', description: 'Inspect Store-Specific Stock Balances' },
      { id: 21, name: 'Inventory Receive', slug: 'inventory.receive', description: 'Receive Incoming Inventory Shipments' },
      { id: 22, name: 'Inventory Issue', slug: 'inventory.issue', description: 'Issue Stock for Dispatch or Maintenance' },
      { id: 23, name: 'Inventory Adjust', slug: 'inventory.adjust', description: 'Perform Authorized Stock Adjustments with Reason' },
    ]
  },
  {
    module: 'stock_transfers',
    label: 'Stock Transfers',
    permissions: [
      { id: 24, name: 'Stock Transfers View', slug: 'stock_transfers.view', description: 'View Inter-Store Stock Transfer Ledgers' },
      { id: 25, name: 'Stock Transfers Create', slug: 'stock_transfers.create', description: 'Initiate Inter-Store Transfers' },
      { id: 26, name: 'Stock Transfers Approve', slug: 'stock_transfers.approve', description: 'Approve and Receive In-Transit Stock Transfers' },
    ]
  },
  {
    module: 'quotations',
    label: 'RFQs & Quotations',
    permissions: [
      { id: 27, name: 'Quotations View', slug: 'quotations.view', description: 'View RFQ Requests and Formal Quotations' },
      { id: 28, name: 'Quotations Create', slug: 'quotations.create', description: 'Draft New Formal Quotations' },
      { id: 29, name: 'Quotations Update', slug: 'quotations.update', description: 'Modify Quotation Line Items, Discounts, and Taxes' },
      { id: 30, name: 'Quotations Approve', slug: 'quotations.approve', description: 'Approve or Reject Client Quotations' },
      { id: 31, name: 'Quotations PDF', slug: 'quotations.pdf', description: 'Generate and Download Branded PDF Quotations' },
    ]
  },
  {
    module: 'sales',
    label: 'Sales & Orders',
    permissions: [
      { id: 32, name: 'Sales View', slug: 'sales.view', description: 'View Confirmed Sales Orders and Ledgers' },
      { id: 33, name: 'Sales Create', slug: 'sales.create', description: 'Register Direct Walk-In or Corporate Sales' },
      { id: 34, name: 'Sales Confirm', slug: 'sales.confirm', description: 'Confirm Orders and Recognize Sales Revenue' },
      { id: 35, name: 'Sales Payments', slug: 'sales.payments', description: 'Record Full and Partial Payment Receipts' },
      { id: 36, name: 'Sales Invoice', slug: 'sales.invoice', description: 'Generate Commercial Invoices and Delivery Slips' },
    ]
  },
  {
    module: 'reports',
    label: 'Reports & Analytics',
    permissions: [
      { id: 37, name: 'Analytics View', slug: 'analytics.view', description: 'Inspect Interactive Charts and BI Metrics' },
      { id: 38, name: 'Reports View', slug: 'reports.view', description: 'Access Store and Consolidated Sales Reports' },
      { id: 39, name: 'Reports Export', slug: 'reports.export', description: 'Export Financial and Stock Reports to PDF/CSV' },
    ]
  },
  {
    module: 'settings',
    label: 'Website & Content Settings',
    permissions: [
      { id: 40, name: 'Settings View', slug: 'settings.view', description: 'View System & Website Content Settings' },
      { id: 41, name: 'Settings Update', slug: 'settings.update', description: 'Modify Corporate Settings and Branding' },
    ]
  }
];

const loadData = async () => {
  try {
    const [rolesRes, permsRes] = await Promise.all([
      roleService.getRoles(),
      roleService.getPermissions(),
    ]);

    if (rolesRes.success) roles.value = rolesRes.data;
    if (permsRes.success) {
      permissionGroups.value = permsRes.data.grouped;
    }
  } catch (err) {
    roles.value = [
      { id: 1, name: 'Super Administrator', slug: 'super_admin', permission_ids: [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41] },
      { id: 2, name: 'Store Manager', slug: 'store_manager', permission_ids: [1,12,16,20,21,22,23,24,25,26,27,28,31,32,33,34,35,36,37,38,39] },
      { id: 3, name: 'Sales Manager', slug: 'sales_manager', permission_ids: [1,12,16,20,27,28,29,30,31,32,33,34,35,36,37,38] },
      { id: 4, name: 'Inventory Officer', slug: 'inventory_officer', permission_ids: [1,12,16,20,21,22,23,24,25,26] },
      { id: 5, name: 'General Administrator', slug: 'general_admin', permission_ids: [1,12,16,17,18,20,27,32,37,38,40,41] },
    ];
    permissionGroups.value = fallbackGroups;
  }

  // Pre-select role based on query param or default to Store Manager
  const queryRoleId = route.query.role ? parseInt(route.query.role) : 2;
  const initialRole = roles.value.find(r => r.id === queryRoleId) || roles.value[1] || roles.value[0];
  if (initialRole) {
    selectRole(initialRole);
  }
};

const selectRole = (role) => {
  selectedRoleId.value = role.id;
  selectedPermissionIds.value = [...(role.permission_ids || role.permissions?.map(p => p.id) || [])];
};

const togglePermission = (id) => {
  if (selectedRole.value?.slug === 'super_admin') return;
  const index = selectedPermissionIds.value.indexOf(id);
  if (index > -1) {
    selectedPermissionIds.value.splice(index, 1);
  } else {
    selectedPermissionIds.value.push(id);
  }
};

const isGroupAllSelected = (group) => {
  return group.permissions.every(p => selectedPermissionIds.value.includes(p.id));
};

const toggleGroupAll = (group) => {
  const ids = group.permissions.map(p => p.id);
  if (isGroupAllSelected(group)) {
    selectedPermissionIds.value = selectedPermissionIds.value.filter(id => !ids.includes(id));
  } else {
    ids.forEach(id => {
      if (!selectedPermissionIds.value.includes(id)) {
        selectedPermissionIds.value.push(id);
      }
    });
  }
};

const selectAll = () => {
  const allIds = [];
  permissionGroups.value.forEach(g => {
    g.permissions.forEach(p => allIds.push(p.id));
  });
  selectedPermissionIds.value = allIds;
};

const deselectAll = () => {
  selectedPermissionIds.value = [];
};

const savePermissions = async () => {
  saving.value = true;
  try {
    const res = await roleService.syncRolePermissions(selectedRoleId.value, selectedPermissionIds.value);
    notify.success(res.message || 'Permissions updated successfully.');
  } catch (err) {
    if (!err.response) {
      notify.success(`Permissions for '${selectedRole.value?.name}' saved (Preview mode).`);
    } else {
      notify.error(err.response?.data?.message || 'Failed to update permissions.');
    }
  } finally {
    saving.value = false;
  }
};

onMounted(loadData);
</script>
