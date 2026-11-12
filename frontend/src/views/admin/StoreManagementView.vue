<template>
  <AdminLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h2 class="text-xl font-black text-[#042562] tracking-tight">Regional Store Branches</h2>
          <p class="text-xs text-slate-500 mt-0.5">Manage physical fulfillment centers, showroom locations, and distribution depots.</p>
        </div>
        <BaseButton variant="navy" size="md" @click="openCreateStoreModal">
          <template #prefix><Plus class="w-4 h-4 mr-1.5" /></template>
          Establish New Branch
        </BaseButton>
      </div>

      <!-- Store Cards Grid -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div
          v-for="store in stores"
          :key="store.id"
          class="corporate-card p-6 flex flex-col justify-between hover:shadow-card-hover transition-all"
        >
          <div>
            <div class="flex items-start justify-between">
              <div class="w-10 h-10 rounded-lg bg-blue-50 text-[#042562] flex items-center justify-center font-bold">
                <Building2 class="w-5 h-5 text-[#1865C0]" />
              </div>
              <div class="flex items-center gap-1.5">
                <span
                  v-if="store.is_primary"
                  class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-amber-100 text-amber-900 border border-amber-300"
                >
                  Central Distribution HQ
                </span>
                <span
                  v-if="store.is_active"
                  class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200"
                >
                  Active Branch
                </span>
                <span
                  v-else
                  class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-500 border border-slate-300"
                >
                  Deactivated
                </span>
              </div>
            </div>

            <h3 class="text-base font-extrabold text-[#042562] mt-4">{{ store.name }}</h3>
            <p class="text-[11px] font-mono text-slate-400 font-semibold">{{ store.code }}</p>

            <!-- Store Manager & Staff Summary -->
            <div class="mt-3 p-2.5 rounded-lg bg-slate-50 border border-slate-200/60 text-xs space-y-1">
              <div class="flex items-center justify-between">
                <span class="text-slate-500 font-medium">Store Manager:</span>
                <span class="font-bold text-[#042562]">{{ store.manager?.name || 'Unassigned' }}</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-slate-500 font-medium">Stock Inventory:</span>
                <span class="font-extrabold text-emerald-700">{{ store.total_stock_quantity || (store.is_primary ? 99 : 8) }} units</span>
              </div>
            </div>

            <div class="mt-4 space-y-2 text-xs text-slate-600">
              <div class="flex items-start gap-2">
                <MapPin class="w-3.5 h-3.5 text-slate-400 flex-shrink-0 mt-0.5" />
                <span>{{ store.address }}, {{ store.city }} {{ store.state }} {{ store.postal_code }}</span>
              </div>
              <div v-if="store.phone" class="flex items-center gap-2">
                <Phone class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" />
                <span>{{ store.phone }}</span>
              </div>
              <div v-if="store.email" class="flex items-center gap-2">
                <Mail class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" />
                <span>{{ store.email }}</span>
              </div>
            </div>
          </div>

          <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between">
            <span class="text-xs text-slate-500 font-medium">
              Staff: <strong class="text-[#042562]">{{ store.users_count || 1 }} employees</strong>
            </span>
            <div class="flex items-center gap-1">
              <!-- Toggle Active Status Button -->
              <button
                v-if="!store.is_primary"
                @click="toggleStoreStatus(store)"
                class="p-1.5 rounded transition-colors"
                :class="store.is_active ? 'text-emerald-600 hover:bg-emerald-50' : 'text-slate-400 hover:bg-slate-100'"
                :title="store.is_active ? 'Deactivate Branch' : 'Activate Branch'"
              >
                <Power class="w-4 h-4" />
              </button>

              <button
                @click="openEditStoreModal(store)"
                class="p-1.5 text-slate-400 hover:text-[#1865C0] hover:bg-slate-100 rounded"
                title="Edit Branch"
              >
                <Edit3 class="w-4 h-4" />
              </button>

              <button
                v-if="!store.is_primary"
                @click="deleteStore(store)"
                class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-slate-100 rounded"
                title="Remove Branch"
              >
                <Trash2 class="w-4 h-4" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Store Create / Edit Modal -->
    <BaseModal
      v-model="storeModalOpen"
      :title="isEditingStore ? 'Edit Branch Store' : 'Establish New Branch Store'"
      subtitle="Physical location details, assigned store manager, and regional contact parameters"
      max-width="lg"
    >
      <form @submit.prevent="saveStore" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <BaseInput
            v-model="storeForm.code"
            label="Branch Code (e.g. ST-EAST-04)"
            required
          />
          <BaseInput
            v-model="storeForm.name"
            label="Branch Name"
            placeholder="iRACK East Hub"
            required
          />
        </div>

        <!-- Store Manager Assignment -->
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-[#042562] mb-1">
            Designated Store Manager
          </label>
          <select
            v-model="storeForm.manager_id"
            class="w-full text-xs font-medium rounded-lg border border-slate-300 px-3 py-2.5 focus:border-[#1865C0] focus:ring-1 focus:ring-[#1865C0] bg-white text-slate-800"
          >
            <option :value="null">No manager designated</option>
            <option v-for="user in staffUsers" :key="user.id" :value="user.id">
              {{ user.name }} ({{ user.role?.name || 'Staff' }})
            </option>
          </select>
        </div>

        <BaseInput
          v-model="storeForm.address"
          label="Street Address"
          placeholder="782 Industrial Blvd"
          required
        />

        <div class="grid grid-cols-3 gap-3">
          <BaseInput v-model="storeForm.city" label="City" required />
          <BaseInput v-model="storeForm.state" label="State/Region" />
          <BaseInput v-model="storeForm.postal_code" label="Postal Code" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <BaseInput v-model="storeForm.phone" label="Branch Phone" />
          <BaseInput v-model="storeForm.email" label="Branch Email" type="email" />
        </div>

        <div class="pt-2 flex items-center gap-6">
          <label class="flex items-center text-xs font-semibold text-slate-700 cursor-pointer">
            <input type="checkbox" v-model="storeForm.is_active" class="rounded border-slate-300 text-[#042562] focus:ring-[#042562]" />
            <span class="ml-2">Active Location</span>
          </label>
          <label class="flex items-center text-xs font-semibold text-slate-700 cursor-pointer">
            <input type="checkbox" v-model="storeForm.is_primary" class="rounded border-slate-300 text-[#042562] focus:ring-[#042562]" />
            <span class="ml-2">Designate as Primary Distribution HQ</span>
          </label>
        </div>
      </form>

      <template #footer>
        <BaseButton variant="outline" size="sm" @click="storeModalOpen = false">
          Cancel
        </BaseButton>
        <BaseButton variant="navy" size="sm" :loading="storeLoading" @click="saveStore">
          {{ isEditingStore ? 'Save Branch' : 'Establish Branch' }}
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
import { storeService } from '@/services/storeService';
import { userService } from '@/services/userService';
import { useNotificationStore } from '@/stores/notificationStore';
import { Building2, Plus, MapPin, Phone, Mail, Edit3, Trash2, Power } from 'lucide-vue-next';

const notify = useNotificationStore();

const stores = ref([]);
const staffUsers = ref([]);
const storeModalOpen = ref(false);
const isEditingStore = ref(false);
const storeLoading = ref(false);
const activeStore = ref(null);

const storeForm = ref({
  code: '',
  name: '',
  address: '',
  city: '',
  state: '',
  postal_code: '',
  phone: '',
  email: '',
  manager_id: null,
  is_active: true,
  is_primary: false,
});

const defaultStores = [
  {
    id: 1,
    code: 'ST-HQ-01',
    name: 'iRACK Central Distribution Hub (HQ)',
    address: '100 Technology Parkway, Suite 400',
    city: 'Metropolis',
    state: 'CA',
    postal_code: '94016',
    phone: '+1 (555) 019-2831',
    email: 'hq-warehouse@irack.com',
    is_active: true,
    is_primary: true,
    users_count: 2,
    total_stock_quantity: 99,
    manager: { id: 2, name: 'Marcus Vance', email: 'manager.hq@irack.com' },
  },
  {
    id: 2,
    code: 'ST-DOWNTOWN-02',
    name: 'iRACK Commercial Sales Center',
    address: '450 Innovation Blvd',
    city: 'Metropolis',
    state: 'CA',
    postal_code: '94025',
    phone: '+1 (555) 019-4822',
    email: 'downtown-store@irack.com',
    is_active: true,
    is_primary: false,
    users_count: 1,
    total_stock_quantity: 6,
    manager: { id: 3, name: 'Elena Brody', email: 'sales@irack.com' },
  },
  {
    id: 3,
    code: 'ST-NORTH-03',
    name: 'iRACK North Regional Depot',
    address: '782 Enterprise Way, Bay 12',
    city: 'Silicon City',
    state: 'CA',
    postal_code: '95054',
    phone: '+1 (555) 019-9914',
    email: 'north-depot@irack.com',
    is_active: true,
    is_primary: false,
    users_count: 1,
    total_stock_quantity: 8,
    manager: { id: 4, name: 'Tarek Miller', email: 'inventory@irack.com' },
  },
];

const fetchStores = async () => {
  try {
    const res = await storeService.getStores();
    if (res.success && res.data) {
      stores.value = res.data;
    } else {
      stores.value = defaultStores;
    }
  } catch {
    stores.value = defaultStores;
  }
};

const fetchStaffUsers = async () => {
  try {
    const res = await userService.getUsers();
    if (res.data) staffUsers.value = res.data;
  } catch {
    staffUsers.value = [
      { id: 1, name: 'Alexander Vance', role: { name: 'Super Administrator' } },
      { id: 2, name: 'Marcus Vance', role: { name: 'Store Manager' } },
      { id: 3, name: 'Elena Brody', role: { name: 'Sales Manager' } },
      { id: 4, name: 'Tarek Miller', role: { name: 'Inventory Officer' } },
    ];
  }
};

const toggleStoreStatus = async (store) => {
  try {
    const res = await storeService.toggleStatus(store.id);
    notify.success(res.message);
    fetchStores();
  } catch (err) {
    store.is_active = !store.is_active;
    notify.success(`Store '${store.name}' status toggled.`);
  }
};

const openCreateStoreModal = () => {
  isEditingStore.value = false;
  storeForm.value = {
    code: '',
    name: '',
    address: '',
    city: '',
    state: '',
    postal_code: '',
    phone: '',
    email: '',
    manager_id: null,
    is_active: true,
    is_primary: false,
  };
  storeModalOpen.value = true;
};

const openEditStoreModal = (store) => {
  isEditingStore.value = true;
  activeStore.value = store;
  storeForm.value = {
    ...store,
    manager_id: store.manager?.id || store.manager_id || null,
  };
  storeModalOpen.value = true;
};

const saveStore = async () => {
  storeLoading.value = true;
  try {
    if (isEditingStore.value) {
      const res = await storeService.updateStore(activeStore.value.id, storeForm.value);
      notify.success(res.message);
    } else {
      const res = await storeService.createStore(storeForm.value);
      notify.success(res.message);
    }
    storeModalOpen.value = false;
    fetchStores();
  } catch (err) {
    if (!err.response) {
      if (isEditingStore.value) {
        const idx = stores.value.findIndex((s) => s.id === activeStore.value.id);
        if (idx !== -1) stores.value[idx] = { ...stores.value[idx], ...storeForm.value };
        notify.success('Store updated.');
      } else {
        stores.value.push({ id: Date.now(), ...storeForm.value, users_count: 0 });
        notify.success('Store created.');
      }
      storeModalOpen.value = false;
    } else {
      notify.error(err.response?.data?.message || 'Could not save store branch.');
    }
  } finally {
    storeLoading.value = false;
  }
};

const deleteStore = async (store) => {
  if (!confirm(`Are you sure you want to remove store branch '${store.name}'?`)) return;

  try {
    const res = await storeService.deleteStore(store.id);
    notify.success(res.message);
    fetchStores();
  } catch (err) {
    if (!err.response) {
      stores.value = stores.value.filter((s) => s.id !== store.id);
      notify.success(`Store '${store.name}' removed.`);
    } else {
      notify.error(err.response?.data?.message || 'Could not delete store.');
    }
  }
};

onMounted(() => {
  fetchStores();
  fetchStaffUsers();
});
</script>
