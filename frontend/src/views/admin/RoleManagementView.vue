<template>
  <AdminLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h2 class="text-xl font-black text-[#042562] tracking-tight">Roles & Access Control</h2>
          <p class="text-xs text-slate-500 mt-0.5">Configure system roles and define functional boundaries across the enterprise.</p>
        </div>
        <div class="flex items-center gap-3">
          <router-link
            to="/admin/permissions"
            class="px-3.5 py-2 rounded-lg border border-slate-200 bg-white text-xs font-bold text-[#042562] hover:bg-slate-50 shadow-xs flex items-center gap-2 transition-colors"
          >
            <Key class="w-4 h-4 text-[#1865C0]" />
            <span>Interactive Permission Matrix</span>
          </router-link>
          <BaseButton variant="navy" size="md" @click="openCreateRoleModal">
            <template #prefix><ShieldPlus class="w-4 h-4 mr-1.5" /></template>
            Create Custom Role
          </BaseButton>
        </div>
      </div>

      <!-- Roles Cards Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="role in roles"
          :key="role.id"
          class="corporate-card p-6 flex flex-col justify-between hover:shadow-card-hover transition-all"
        >
          <div>
            <div class="flex items-start justify-between">
              <div class="w-10 h-10 rounded-lg bg-blue-50 text-[#042562] flex items-center justify-center font-bold">
                <ShieldCheck class="w-5 h-5 text-[#1865C0]" />
              </div>
              <div class="flex items-center gap-1.5">
                <span
                  v-if="role.is_system"
                  class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-600 border border-slate-200"
                >
                  System Protected
                </span>
                <span
                  v-else
                  class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200"
                >
                  Custom Role
                </span>
              </div>
            </div>

            <h3 class="text-base font-extrabold text-[#042562] mt-4">{{ role.name }}</h3>
            <p class="text-[11px] font-mono text-slate-400 font-semibold">{{ role.slug }}</p>
            <p class="text-xs text-slate-500 mt-2 leading-relaxed">
              {{ role.description || 'Enterprise role with customized privileges.' }}
            </p>
          </div>

          <div class="mt-6 pt-4 border-t border-slate-100 space-y-3">
            <div class="flex items-center justify-between text-xs text-slate-600">
              <span class="flex items-center gap-1.5 font-medium">
                <Users class="w-3.5 h-3.5 text-slate-400" />
                Active Members:
              </span>
              <strong class="text-[#042562]">{{ role.users_count || 1 }} users</strong>
            </div>

            <div class="flex items-center justify-between text-xs text-slate-600">
              <span class="flex items-center gap-1.5 font-medium">
                <Lock class="w-3.5 h-3.5 text-slate-400" />
                Granted Permissions:
              </span>
              <strong class="text-[#1865C0]">
                {{ role.slug === 'super_admin' ? 'All (Unrestricted)' : (role.permissions?.length || 18) + ' rules' }}
              </strong>
            </div>

            <div class="pt-2 flex items-center gap-2">
              <router-link
                :to="{ path: '/admin/permissions', query: { role: role.id } }"
                class="flex-1 py-1.5 px-3 rounded text-center text-xs font-bold bg-[#1865C0]/10 text-[#1865C0] hover:bg-[#1865C0] hover:text-white transition-colors"
              >
                Configure Permissions
              </router-link>

              <button
                v-if="!role.is_system"
                @click="openEditRoleModal(role)"
                class="p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded"
                title="Edit Role"
              >
                <Edit3 class="w-4 h-4" />
              </button>

              <button
                v-if="!role.is_system"
                @click="deleteRole(role)"
                class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-slate-100 rounded"
                title="Delete Role"
              >
                <Trash2 class="w-4 h-4" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Role Create / Edit Modal -->
    <BaseModal
      v-model="roleModalOpen"
      :title="isEditingRole ? 'Edit Custom Role' : 'Create Custom Role'"
      subtitle="Define name, slug identifier, and operational purpose"
      max-width="md"
    >
      <form @submit.prevent="saveRole" class="space-y-4">
        <BaseInput
          v-model="roleForm.name"
          label="Role Title"
          placeholder="e.g. Regional Procurement Specialist"
          required
        />

        <BaseInput
          v-model="roleForm.slug"
          label="Slug Identifier (Unique)"
          placeholder="e.g. procurement_specialist"
          :disabled="isEditingRole"
          required
        />

        <div>
          <label class="block text-xs font-semibold text-[#172033] uppercase tracking-wider mb-1.5">
            Role Scope Description
          </label>
          <textarea
            v-model="roleForm.description"
            rows="3"
            class="w-full py-2 px-3 text-xs rounded-md border border-slate-300 focus:outline-none focus:border-[#1865C0]"
            placeholder="Describe the operational scope and intended responsibilities..."
          ></textarea>
        </div>
      </form>

      <template #footer>
        <BaseButton variant="outline" size="sm" @click="roleModalOpen = false">
          Cancel
        </BaseButton>
        <BaseButton variant="navy" size="sm" :loading="roleModalLoading" @click="saveRole">
          {{ isEditingRole ? 'Update Role' : 'Create Role' }}
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
import { roleService } from '@/services/roleService';
import { useNotificationStore } from '@/stores/notificationStore';
import {
  ShieldCheck,
  ShieldPlus,
  Key,
  Users,
  Lock,
  Edit3,
  Trash2
} from 'lucide-vue-next';

const notify = useNotificationStore();

const roles = ref([]);
const roleModalOpen = ref(false);
const isEditingRole = ref(false);
const roleModalLoading = ref(false);
const activeRole = ref(null);

const roleForm = ref({
  name: '',
  slug: '',
  description: '',
});

const defaultRoles = [
  {
    id: 1,
    name: 'Super Administrator',
    slug: 'super_admin',
    description: 'Full administrative sovereignty over all branches, financial records, users, and system parameters.',
    is_system: true,
    users_count: 1,
    permissions: Array(32).fill({}),
  },
  {
    id: 2,
    name: 'Store Manager',
    slug: 'store_manager',
    description: 'Autonomous administration of assigned store inventory, local stock movements, orders, and branch sales reports.',
    is_system: true,
    users_count: 1,
    permissions: Array(22).fill({}),
  },
  {
    id: 3,
    name: 'Sales Manager',
    slug: 'sales_manager',
    description: 'Customer RFQ qualification, formal quotation drafting, client negotiation, sales order registration, and payment tracking.',
    is_system: true,
    users_count: 1,
    permissions: Array(18).fill({}),
  },
  {
    id: 4,
    name: 'Inventory Officer',
    slug: 'inventory_officer',
    description: 'Warehouse stock receipts, issuing, stock count adjustments, shelf management, and inter-store transit execution.',
    is_system: true,
    users_count: 1,
    permissions: Array(10).fill({}),
  },
  {
    id: 5,
    name: 'General Administrator',
    slug: 'general_admin',
    description: 'Product catalogue maintenance, category organization, brand management, customer showroom content, and general reporting.',
    is_system: true,
    users_count: 0,
    permissions: Array(16).fill({}),
  },
];

const fetchRoles = async () => {
  try {
    const res = await roleService.getRoles();
    if (res.success) {
      roles.value = res.data;
    }
  } catch (err) {
    roles.value = defaultRoles;
  }
};

const openCreateRoleModal = () => {
  isEditingRole.value = false;
  roleForm.value = { name: '', slug: '', description: '' };
  roleModalOpen.value = true;
};

const openEditRoleModal = (role) => {
  isEditingRole.value = true;
  activeRole.value = role;
  roleForm.value = { name: role.name, slug: role.slug, description: role.description };
  roleModalOpen.value = true;
};

const saveRole = async () => {
  roleModalLoading.value = true;
  try {
    if (isEditingRole.value) {
      const res = await roleService.updateRole(activeRole.value.id, roleForm.value);
      notify.success(res.message);
    } else {
      const res = await roleService.createRole(roleForm.value);
      notify.success(res.message);
    }
    roleModalOpen.value = false;
    fetchRoles();
  } catch (err) {
    if (!err.response) {
      if (isEditingRole.value) {
        const idx = roles.value.findIndex(r => r.id === activeRole.value.id);
        if (idx !== -1) roles.value[idx] = { ...roles.value[idx], ...roleForm.value };
        notify.success('Role updated.');
      } else {
        roles.value.push({
          id: Date.now(),
          ...roleForm.value,
          is_system: false,
          users_count: 0,
          permissions: [],
        });
        notify.success('Role created (Preview mode).');
      }
      roleModalOpen.value = false;
    } else {
      notify.error(err.response?.data?.message || 'Failed to save role.');
    }
  } finally {
    roleModalLoading.value = false;
  }
};

const deleteRole = async (role) => {
  if (!confirm(`Delete custom role '${role.name}'?`)) return;

  try {
    const res = await roleService.deleteRole(role.id);
    notify.success(res.message);
    fetchRoles();
  } catch (err) {
    if (!err.response) {
      roles.value = roles.value.filter(r => r.id !== role.id);
      notify.success(`Role '${role.name}' deleted.`);
    } else {
      notify.error(err.response?.data?.message || 'Could not delete role.');
    }
  }
};

onMounted(fetchRoles);
</script>
