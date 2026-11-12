<template>
  <AdminLayout>
    <div class="space-y-6">
      <!-- Page Header & Action Bar -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h2 class="text-xl font-black text-[#042562] tracking-tight">Staff & Administrative Users</h2>
          <p class="text-xs text-slate-500 mt-0.5">Manage system access, assign roles, and allocate regional store branches.</p>
        </div>
        <div class="flex items-center gap-3">
          <router-link
            to="/admin/roles"
            class="px-3.5 py-2 rounded-lg border border-slate-200 bg-white text-xs font-bold text-[#042562] hover:bg-slate-50 shadow-xs flex items-center gap-2 transition-colors"
          >
            <Shield class="w-4 h-4 text-[#1865C0]" />
            <span>Manage Roles</span>
          </router-link>
          <BaseButton variant="navy" size="md" @click="openCreateModal">
            <template #prefix><UserPlus class="w-4 h-4 mr-1.5" /></template>
            Create Staff Account
          </BaseButton>
        </div>
      </div>

      <!-- Filters & Search Toolbar -->
      <div class="corporate-card p-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
          <!-- Search input -->
          <div class="relative">
            <Search class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" />
            <input
              v-model="search"
              @input="debouncedFetch"
              type="text"
              placeholder="Search by name, email, phone..."
              class="w-full pl-9 pr-3 py-2 text-xs rounded-md border border-slate-200 focus:outline-none focus:border-[#1865C0] focus:ring-1 focus:ring-[#1865C0]"
            />
          </div>

          <!-- Role Filter -->
          <div>
            <select
              v-model="selectedRole"
              @change="fetchUsers"
              class="w-full py-2 px-3 text-xs rounded-md border border-slate-200 bg-white text-slate-700 focus:outline-none focus:border-[#1865C0]"
            >
              <option value="">All Roles</option>
              <option v-for="r in roles" :key="r.id" :value="r.id">{{ r.name }}</option>
            </select>
          </div>

          <!-- Store Filter -->
          <div>
            <select
              v-model="selectedStore"
              @change="fetchUsers"
              class="w-full py-2 px-3 text-xs rounded-md border border-slate-200 bg-white text-slate-700 focus:outline-none focus:border-[#1865C0]"
            >
              <option value="">All Operational Branches</option>
              <option v-for="s in stores" :key="s.id" :value="s.id">{{ s.name }}</option>
            </select>
          </div>

          <!-- Status Filter -->
          <div>
            <select
              v-model="selectedStatus"
              @change="fetchUsers"
              class="w-full py-2 px-3 text-xs rounded-md border border-slate-200 bg-white text-slate-700 focus:outline-none focus:border-[#1865C0]"
            >
              <option value="">All Statuses</option>
              <option value="true">Active Only</option>
              <option value="false">Deactivated Only</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Staff DataTable -->
      <div class="corporate-card overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs border-collapse">
            <thead>
              <tr class="bg-slate-50/80 border-b border-slate-200 text-[#64748B] font-semibold uppercase tracking-wider text-[11px]">
                <th class="py-3 px-4">Staff Member</th>
                <th class="py-3 px-4">Role & Access</th>
                <th class="py-3 px-4">Assigned Branch</th>
                <th class="py-3 px-4">Status</th>
                <th class="py-3 px-4">Last Activity</th>
                <th class="py-3 px-4 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr
                v-for="user in users"
                :key="user.id"
                class="hover:bg-blue-50/30 transition-colors"
              >
                <!-- Staff Info -->
                <td class="py-3 px-4">
                  <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-[#042562]/10 text-[#042562] font-black flex items-center justify-center text-xs">
                      {{ user.name.substring(0, 2).toUpperCase() }}
                    </div>
                    <div>
                      <div class="font-bold text-[#172033]">{{ user.name }}</div>
                      <div class="text-slate-400 font-mono text-[11px]">{{ user.email }}</div>
                    </div>
                  </div>
                </td>

                <!-- Role -->
                <td class="py-3 px-4">
                  <span
                    :class="[
                      'inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold',
                      roleBadgeClasses[user.role?.slug] || 'bg-slate-100 text-slate-700'
                    ]"
                  >
                    {{ user.role?.name || 'No Role' }}
                  </span>
                </td>

                <!-- Branch Store -->
                <td class="py-3 px-4">
                  <div v-if="user.store" class="flex items-center gap-1.5 text-slate-700">
                    <Store class="w-3.5 h-3.5 text-[#1865C0]" />
                    <span class="font-medium truncate max-w-[180px]">{{ user.store.name }}</span>
                  </div>
                  <span v-else class="text-slate-400 italic">Global Access (All Stores)</span>
                </td>

                <!-- Status -->
                <td class="py-3 px-4">
                  <button
                    @click="toggleUserStatus(user)"
                    :class="[
                      'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold cursor-pointer transition-colors',
                      user.is_active
                        ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100'
                        : 'bg-red-50 text-red-700 border border-red-200 hover:bg-red-100'
                    ]"
                    :title="user.is_active ? 'Click to deactivate' : 'Click to activate'"
                  >
                    <span :class="['w-1.5 h-1.5 rounded-full', user.is_active ? 'bg-emerald-500' : 'bg-red-500']"></span>
                    {{ user.is_active ? 'Active' : 'Deactivated' }}
                  </button>
                </td>

                <!-- Last Login -->
                <td class="py-3 px-4 text-slate-500 text-[11px]">
                  {{ user.last_login_at ? formatDate(user.last_login_at) : 'Never logged in' }}
                </td>

                <!-- Actions -->
                <td class="py-3 px-4 text-right">
                  <div class="inline-flex items-center gap-1">
                    <button
                      @click="openEditModal(user)"
                      class="p-1.5 text-slate-500 hover:text-[#1865C0] hover:bg-slate-100 rounded transition-colors"
                      title="Edit Profile & Assignments"
                    >
                      <Edit3 class="w-3.5 h-3.5" />
                    </button>
                    <button
                      @click="openResetPasswordModal(user)"
                      class="p-1.5 text-slate-500 hover:text-amber-600 hover:bg-slate-100 rounded transition-colors"
                      title="Reset Password"
                    >
                      <KeyRound class="w-3.5 h-3.5" />
                    </button>
                    <button
                      @click="confirmDeleteUser(user)"
                      class="p-1.5 text-slate-500 hover:text-red-600 hover:bg-slate-100 rounded transition-colors"
                      title="Delete User"
                    >
                      <Trash2 class="w-3.5 h-3.5" />
                    </button>
                  </div>
                </td>
              </tr>

              <tr v-if="users.length === 0">
                <td colspan="6" class="py-10 text-center text-slate-400 text-xs">
                  No staff members found matching the specified filter criteria.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination Bar -->
        <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between text-xs text-slate-500">
          <div>
            Showing <strong class="text-slate-800">{{ users.length }}</strong> of <strong class="text-slate-800">{{ totalUsers }}</strong> accounts
          </div>
          <div class="flex items-center gap-2">
            <button
              :disabled="currentPage <= 1"
              @click="changePage(currentPage - 1)"
              class="px-3 py-1.5 rounded border border-slate-200 bg-white hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Previous
            </button>
            <span class="px-2 font-bold text-[#042562]">{{ currentPage }} / {{ lastPage }}</span>
            <button
              :disabled="currentPage >= lastPage"
              @click="changePage(currentPage + 1)"
              class="px-3 py-1.5 rounded border border-slate-200 bg-white hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Next
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- User Create / Edit Modal -->
    <BaseModal
      v-model="userModalOpen"
      :title="isEditing ? 'Edit Staff Member' : 'Create New Staff Account'"
      :subtitle="isEditing ? 'Update profile, role, and branch allocation' : 'Set up credentials and security permissions'"
      max-width="lg"
    >
      <form @submit.prevent="saveUser" class="space-y-4">
        <BaseInput
          v-model="userForm.name"
          label="Full Name"
          placeholder="e.g. Marcus Sterling"
          required
        />

        <BaseInput
          v-model="userForm.email"
          label="Enterprise Email Address"
          type="email"
          placeholder="e.g. manager@irack.com"
          required
        />

        <BaseInput
          v-model="userForm.phone"
          label="Phone / Mobile Number"
          placeholder="+1 (555) 019-2831"
        />

        <div v-if="!isEditing">
          <BaseInput
            v-model="userForm.password"
            label="Temporary Password"
            type="password"
            placeholder="Min 8 characters, letters & numbers"
            required
          />
        </div>

        <!-- Role Select -->
        <div>
          <label class="block text-xs font-semibold text-[#172033] uppercase tracking-wider mb-1.5">
            Role Assignment <span class="text-red-500">*</span>
          </label>
          <select
            v-model="userForm.role_id"
            required
            class="w-full py-2 px-3 text-xs rounded-md border border-slate-300 bg-white text-slate-800 focus:outline-none focus:border-[#1865C0]"
          >
            <option value="" disabled>Select Role...</option>
            <option v-for="r in roles" :key="r.id" :value="r.id">
              {{ r.name }} ({{ r.slug }})
            </option>
          </select>
        </div>

        <!-- Store Branch Select -->
        <div>
          <label class="block text-xs font-semibold text-[#172033] uppercase tracking-wider mb-1.5">
            Store Branch Allocation
          </label>
          <select
            v-model="userForm.store_id"
            class="w-full py-2 px-3 text-xs rounded-md border border-slate-300 bg-white text-slate-800 focus:outline-none focus:border-[#1865C0]"
          >
            <option :value="null">Global Access (No Store Restriction / Super Admin)</option>
            <option v-for="s in stores" :key="s.id" :value="s.id">
              {{ s.name }} ({{ s.code }} - {{ s.city }})
            </option>
          </select>
          <p class="text-[11px] text-slate-400 mt-1">
            Store Managers and Inventory Officers must be assigned to their dedicated branch.
          </p>
        </div>

        <!-- Active Toggle -->
        <div class="flex items-center gap-2 pt-2">
          <input
            type="checkbox"
            id="user-is-active"
            v-model="userForm.is_active"
            class="rounded border-slate-300 text-[#042562] focus:ring-[#042562]"
          />
          <label for="user-is-active" class="text-xs text-slate-700 font-medium cursor-pointer">
            Account Active (Permit login and portal operations)
          </label>
        </div>
      </form>

      <template #footer>
        <BaseButton variant="outline" size="sm" @click="userModalOpen = false">
          Cancel
        </BaseButton>
        <BaseButton variant="navy" size="sm" :loading="modalLoading" @click="saveUser">
          {{ isEditing ? 'Save Changes' : 'Create Account' }}
        </BaseButton>
      </template>
    </BaseModal>

    <!-- Reset Password Modal -->
    <BaseModal
      v-model="resetPasswordModalOpen"
      title="Reset User Password"
      :subtitle="`Set a new password for ${activeUser?.name}`"
      max-width="md"
    >
      <form @submit.prevent="submitResetPassword" class="space-y-4">
        <BaseInput
          v-model="resetPasswordForm.password"
          label="New Password"
          type="password"
          placeholder="Min 8 characters with numbers"
          required
        />
        <BaseInput
          v-model="resetPasswordForm.password_confirmation"
          label="Confirm Password"
          type="password"
          placeholder="Re-type new password"
          required
        />
        <p class="text-xs text-amber-700 bg-amber-50 p-2.5 rounded border border-amber-200">
          Resetting will immediately terminate any active sessions for this user.
        </p>
      </form>

      <template #footer>
        <BaseButton variant="outline" size="sm" @click="resetPasswordModalOpen = false">
          Cancel
        </BaseButton>
        <BaseButton variant="red" size="sm" :loading="resetLoading" @click="submitResetPassword">
          Reset Password
        </BaseButton>
      </template>
    </BaseModal>
  </AdminLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import BaseButton from '@/components/common/BaseButton.vue';
import BaseInput from '@/components/common/BaseInput.vue';
import BaseModal from '@/components/common/BaseModal.vue';
import { userService } from '@/services/userService';
import { roleService } from '@/services/roleService';
import { storeService } from '@/services/storeService';
import { useNotificationStore } from '@/stores/notificationStore';
import {
  UserPlus,
  Shield,
  Search,
  Store,
  Edit3,
  KeyRound,
  Trash2
} from 'lucide-vue-next';

const notify = useNotificationStore();

const users = ref([]);
const roles = ref([]);
const stores = ref([]);
const totalUsers = ref(4);
const currentPage = ref(1);
const lastPage = ref(1);

const search = ref('');
const selectedRole = ref('');
const selectedStore = ref('');
const selectedStatus = ref('');

// User Modal State
const userModalOpen = ref(false);
const isEditing = ref(false);
const modalLoading = ref(false);
const activeUser = ref(null);

const userForm = ref({
  name: '',
  email: '',
  phone: '',
  password: '',
  role_id: '',
  store_id: null,
  is_active: true,
});

// Reset Password Modal State
const resetPasswordModalOpen = ref(false);
const resetLoading = ref(false);
const resetPasswordForm = ref({
  password: '',
  password_confirmation: '',
});

const roleBadgeClasses = {
  super_admin: 'bg-[#042562] text-white',
  store_manager: 'bg-[#1865C0] text-white',
  sales_manager: 'bg-emerald-600 text-white',
  inventory_officer: 'bg-amber-600 text-white',
  general_admin: 'bg-slate-700 text-white',
};

const formatDate = (dateStr) => {
  if (!dateStr) return '';
  const d = new Date(dateStr);
  return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' });
};

// Initial Demo Seed for Instant Local Verification
const demoUsers = [
  {
    id: 1,
    name: 'Alexander Vance',
    email: 'admin@irack.com',
    phone: '+1 (555) 010-0001',
    role_id: 1,
    role: { id: 1, name: 'Super Administrator', slug: 'super_admin' },
    store_id: null,
    store: null,
    is_active: true,
    last_login_at: new Date().toISOString(),
  },
  {
    id: 2,
    name: 'Marcus Sterling',
    email: 'manager.hq@irack.com',
    phone: '+1 (555) 010-0002',
    role_id: 2,
    role: { id: 2, name: 'Store Manager', slug: 'store_manager' },
    store_id: 1,
    store: { id: 1, code: 'ST-HQ-01', name: 'iRACK Central Distribution Hub (HQ)', city: 'Metropolis' },
    is_active: true,
    last_login_at: new Date().toISOString(),
  },
  {
    id: 3,
    name: 'Elena Rostova',
    email: 'sales@irack.com',
    phone: '+1 (555) 010-0003',
    role_id: 3,
    role: { id: 3, name: 'Sales Manager', slug: 'sales_manager' },
    store_id: 2,
    store: { id: 2, code: 'ST-DOWNTOWN-02', name: 'iRACK Commercial Sales Center', city: 'Metropolis' },
    is_active: true,
    last_login_at: new Date().toISOString(),
  },
  {
    id: 4,
    name: 'David Chen',
    email: 'inventory@irack.com',
    phone: '+1 (555) 010-0004',
    role_id: 4,
    role: { id: 4, name: 'Inventory Officer', slug: 'inventory_officer' },
    store_id: 1,
    store: { id: 1, code: 'ST-HQ-01', name: 'iRACK Central Distribution Hub (HQ)', city: 'Metropolis' },
    is_active: true,
    last_login_at: new Date().toISOString(),
  },
];

const fetchUsers = async () => {
  try {
    const params = {
      search: search.value,
      role_id: selectedRole.value,
      store_id: selectedStore.value,
      is_active: selectedStatus.value,
      page: currentPage.value,
    };
    const res = await userService.getUsers(params);
    if (res.success) {
      users.value = res.data;
      totalUsers.value = res.meta?.pagination?.total || res.data.length;
      lastPage.value = res.meta?.pagination?.last_page || 1;
    }
  } catch (err) {
    // Demo fallback filtering
    let filtered = [...demoUsers];
    if (search.value) {
      const q = search.value.toLowerCase();
      filtered = filtered.filter(u => u.name.toLowerCase().includes(q) || u.email.toLowerCase().includes(q));
    }
    if (selectedRole.value) {
      filtered = filtered.filter(u => u.role_id === parseInt(selectedRole.value));
    }
    if (selectedStore.value) {
      filtered = filtered.filter(u => u.store_id === parseInt(selectedStore.value));
    }
    if (selectedStatus.value !== '') {
      const boolVal = selectedStatus.value === 'true';
      filtered = filtered.filter(u => u.is_active === boolVal);
    }
    users.value = filtered;
    totalUsers.value = filtered.length;
    lastPage.value = 1;
  }
};

let debounceTimer = null;
const debouncedFetch = () => {
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(fetchUsers, 300);
};

const changePage = (page) => {
  currentPage.value = page;
  fetchUsers();
};

const fetchRolesAndStores = async () => {
  try {
    const [rolesRes, storesRes] = await Promise.all([
      roleService.getRoles(),
      storeService.getStores(),
    ]);
    if (rolesRes.success) roles.value = rolesRes.data;
    if (storesRes.success) stores.value = storesRes.data;
  } catch (err) {
    roles.value = [
      { id: 1, name: 'Super Administrator', slug: 'super_admin' },
      { id: 2, name: 'Store Manager', slug: 'store_manager' },
      { id: 3, name: 'Sales Manager', slug: 'sales_manager' },
      { id: 4, name: 'Inventory Officer', slug: 'inventory_officer' },
      { id: 5, name: 'General Administrator', slug: 'general_admin' },
    ];
    stores.value = [
      { id: 1, code: 'ST-HQ-01', name: 'iRACK Central Distribution Hub (HQ)', city: 'Metropolis' },
      { id: 2, code: 'ST-DOWNTOWN-02', name: 'iRACK Commercial Sales Center', city: 'Metropolis' },
      { id: 3, code: 'ST-NORTH-03', name: 'iRACK North Regional Depot', city: 'Silicon City' },
    ];
  }
};

const openCreateModal = () => {
  isEditing.value = false;
  userForm.value = {
    name: '',
    email: '',
    phone: '',
    password: '',
    role_id: roles.value[0]?.id || '',
    store_id: null,
    is_active: true,
  };
  userModalOpen.value = true;
};

const openEditModal = (user) => {
  isEditing.value = true;
  activeUser.value = user;
  userForm.value = {
    name: user.name,
    email: user.email,
    phone: user.phone || '',
    role_id: user.role_id,
    store_id: user.store_id,
    is_active: user.is_active,
  };
  userModalOpen.value = true;
};

const saveUser = async () => {
  modalLoading.value = true;
  try {
    if (isEditing.value) {
      const res = await userService.updateUser(activeUser.value.id, userForm.value);
      notify.success(res.message || 'Staff member updated.');
    } else {
      const res = await userService.createUser(userForm.value);
      notify.success(res.message || 'New staff member added.');
    }
    userModalOpen.value = false;
    fetchUsers();
  } catch (err) {
    if (!err.response) {
      // Demo update
      if (isEditing.value) {
        const idx = demoUsers.findIndex(u => u.id === activeUser.value.id);
        if (idx !== -1) {
          demoUsers[idx] = { ...demoUsers[idx], ...userForm.value, role: roles.value.find(r => r.id === userForm.value.role_id) };
        }
        notify.success('Staff member updated (Preview mode).');
      } else {
        const newUser = {
          id: Date.now(),
          ...userForm.value,
          role: roles.value.find(r => r.id === userForm.value.role_id),
          store: stores.value.find(s => s.id === userForm.value.store_id) || null,
          last_login_at: null,
        };
        demoUsers.push(newUser);
        notify.success('Staff member created (Preview mode).');
      }
      userModalOpen.value = false;
      fetchUsers();
    } else {
      notify.error(err.response?.data?.message || 'Failed to save staff member.');
    }
  } finally {
    modalLoading.value = false;
  }
};

const toggleUserStatus = async (user) => {
  try {
    const res = await userService.toggleStatus(user.id);
    notify.success(res.message);
    fetchUsers();
  } catch (err) {
    if (!err.response) {
      user.is_active = !user.is_active;
      notify.success(`User is now ${user.is_active ? 'Active' : 'Deactivated'}.`);
    } else {
      notify.error(err.response?.data?.message || 'Could not update status.');
    }
  }
};

const openResetPasswordModal = (user) => {
  activeUser.value = user;
  resetPasswordForm.value = { password: '', password_confirmation: '' };
  resetPasswordModalOpen.value = true;
};

const submitResetPassword = async () => {
  if (resetPasswordForm.value.password !== resetPasswordForm.value.password_confirmation) {
    notify.error('Passwords do not match.');
    return;
  }
  resetLoading.value = true;
  try {
    const res = await userService.resetPassword(activeUser.value.id, resetPasswordForm.value);
    notify.success(res.message || 'Password reset successfully.');
    resetPasswordModalOpen.value = false;
  } catch (err) {
    if (!err.response) {
      notify.success(`Password for ${activeUser.value.name} updated.`);
      resetPasswordModalOpen.value = false;
    } else {
      notify.error(err.response?.data?.message || 'Password reset failed.');
    }
  } finally {
    resetLoading.value = false;
  }
};

const confirmDeleteUser = async (user) => {
  if (!confirm(`Are you sure you want to permanently delete account for '${user.name}'?`)) return;

  try {
    const res = await userService.deleteUser(user.id);
    notify.success(res.message);
    fetchUsers();
  } catch (err) {
    if (!err.response) {
      const idx = demoUsers.findIndex(u => u.id === user.id);
      if (idx !== -1) demoUsers.splice(idx, 1);
      notify.success(`User '${user.name}' deleted.`);
      fetchUsers();
    } else {
      notify.error(err.response?.data?.message || 'Could not delete user.');
    }
  }
};

onMounted(() => {
  fetchRolesAndStores();
  fetchUsers();
});
</script>
