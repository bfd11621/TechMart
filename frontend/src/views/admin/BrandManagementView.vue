<template>
  <AdminLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h2 class="text-xl font-black text-[#042562] tracking-tight">Partner Brands & Manufacturers</h2>
          <p class="text-xs text-slate-500 mt-0.5">Manage certified hardware vendor profiles, manufacturer logos, and supplier links.</p>
        </div>
        <div class="flex items-center gap-3">
          <router-link
            to="/admin/products"
            class="px-3.5 py-2 rounded-lg border border-slate-200 bg-white text-xs font-bold text-[#042562] hover:bg-slate-50 shadow-xs"
          >
            ← View Products
          </router-link>
          <BaseButton variant="navy" size="md" @click="openCreateModal">
            <template #prefix><Plus class="w-4 h-4 mr-1.5" /></template>
            Register Partner Brand
          </BaseButton>
        </div>
      </div>

      <!-- Brands Cards Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="brand in brands"
          :key="brand.id"
          class="corporate-card p-6 flex flex-col justify-between hover:shadow-card-hover transition-all"
        >
          <div>
            <div class="flex items-start justify-between">
              <div class="w-12 h-12 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-center font-black text-[#042562] text-sm overflow-hidden shadow-xs">
                <img v-if="brand.logo_url" :src="brand.logo_url" :alt="brand.name" class="w-full h-full object-contain p-1" />
                <span v-else>{{ brand.name.substring(0, 2).toUpperCase() }}</span>
              </div>
              <button
                @click="toggleStatus(brand)"
                :class="[
                  'text-[10px] font-bold px-2 py-0.5 rounded-full border transition-colors',
                  brand.is_active
                    ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                    : 'bg-slate-100 text-slate-500 border-slate-200'
                ]"
              >
                {{ brand.is_active ? 'Active Partner' : 'Inactive' }}
              </button>
            </div>

            <h3 class="text-base font-extrabold text-[#042562] mt-4">{{ brand.name }}</h3>
            <p class="text-[11px] font-mono text-slate-400 font-semibold">{{ brand.slug }}</p>
            <p class="text-xs text-slate-500 mt-2 leading-relaxed">
              {{ brand.description || 'Enterprise hardware manufacturer and certified distribution partner.' }}
            </p>

            <div v-if="brand.website" class="mt-3">
              <a
                :href="brand.website"
                target="_blank"
                class="inline-flex items-center gap-1 text-[11px] font-semibold text-[#1865C0] hover:underline"
              >
                <span>{{ brand.website }}</span>
                <ExternalLink class="w-3 h-3" />
              </a>
            </div>
          </div>

          <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
            <span class="text-slate-500 font-medium">
              Hardware Models: <strong class="text-[#042562]">{{ brand.products_count || 0 }} items</strong>
            </span>
            <div class="flex items-center gap-1">
              <button
                @click="openEditModal(brand)"
                class="p-1.5 text-slate-400 hover:text-[#1865C0] hover:bg-slate-100 rounded"
                title="Edit Brand"
              >
                <Edit3 class="w-4 h-4" />
              </button>
              <button
                @click="deleteBrand(brand)"
                class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-slate-100 rounded"
                title="Delete Brand"
              >
                <Trash2 class="w-4 h-4" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Brand Create / Edit Modal -->
    <BaseModal
      v-model="modalOpen"
      :title="isEditing ? 'Edit Brand Profile' : 'Register Partner Brand'"
      subtitle="Define brand identity, logo URL, and website credentials"
      max-width="md"
    >
      <form @submit.prevent="saveBrand" class="space-y-4">
        <BaseInput
          v-model="form.name"
          label="Brand / Manufacturer Name"
          placeholder="e.g. Dell Technologies"
          required
        />

        <BaseInput
          v-model="form.slug"
          label="URL Slug (Optional)"
          placeholder="e.g. dell-technologies"
        />

        <BaseInput
          v-model="form.website"
          label="Official Website URL"
          placeholder="https://dell.com"
        />

        <!-- Brand Logo Source Toggle: Local Upload vs External URL -->
        <div class="space-y-2">
          <label class="block text-xs font-semibold text-[#172033] uppercase tracking-wider">
            Brand Logo Source *
          </label>
          <div class="flex items-center gap-2 p-1 bg-slate-100 rounded-lg text-xs font-semibold">
            <button
              type="button"
              @click="logoSource = 'upload'"
              :class="[
                'flex-1 py-1.5 px-3 rounded-md transition-all text-center',
                logoSource === 'upload' ? 'bg-white text-[#042562] shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'
              ]"
            >
              Upload from Local PC
            </button>
            <button
              type="button"
              @click="logoSource = 'url'"
              :class="[
                'flex-1 py-1.5 px-3 rounded-md transition-all text-center',
                logoSource === 'url' ? 'bg-white text-[#042562] shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'
              ]"
            >
              Provide External URL
            </button>
          </div>

          <!-- Local File Upload Field -->
          <div v-if="logoSource === 'upload'" class="space-y-2">
            <div class="flex items-center gap-3">
              <input
                type="file"
                ref="logoFileInput"
                accept="image/png,image/jpeg,image/webp,image/svg+xml"
                @change="handleLogoFileSelect"
                class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-[#042562] hover:file:bg-blue-100 cursor-pointer border border-slate-300 rounded-md p-1"
              />
            </div>
            <p class="text-[10px] text-slate-400">Accepted formats: PNG, SVG, JPG, WebP. Max size: 5MB.</p>
          </div>

          <!-- External URL Input Field -->
          <div v-else>
            <BaseInput
              v-model="form.logo_url"
              label="Brand Logo Absolute URL"
              placeholder="https://images.example.com/brand-logo.png"
            />
          </div>

          <!-- Logo Preview Card -->
          <div v-if="logoPreviewUrl || form.logo_url" class="p-2.5 rounded-lg border border-slate-200 bg-slate-50 flex items-center gap-3">
            <div class="w-12 h-12 rounded-lg bg-white border border-slate-200 flex items-center justify-center p-1 overflow-hidden">
              <img :src="logoPreviewUrl || form.logo_url" alt="Logo preview" class="max-h-full max-w-full object-contain" />
            </div>
            <div class="text-[11px] min-w-0">
              <span class="font-bold text-slate-700 block">Logo Active Preview</span>
              <span class="text-slate-500 truncate block text-[10px]">{{ logoSource === 'upload' && logoFile ? logoFile.name : (form.logo_url || 'Remote image link') }}</span>
            </div>
          </div>
        </div>

        <div>
          <label class="block text-xs font-semibold text-[#172033] uppercase tracking-wider mb-1.5">
            Manufacturer Overview
          </label>
          <textarea
            v-model="form.description"
            rows="3"
            class="w-full py-2 px-3 text-xs rounded-md border border-slate-300 focus:outline-none focus:border-[#1865C0]"
            placeholder="Enterprise hardware capabilities and partner details..."
          ></textarea>
        </div>

        <div class="pt-2">
          <label class="flex items-center text-xs font-semibold text-slate-700 cursor-pointer">
            <input type="checkbox" v-model="form.is_active" class="rounded border-slate-300 text-[#042562] focus:ring-[#042562]" />
            <span class="ml-2">Active Brand Partner</span>
          </label>
        </div>
      </form>

      <template #footer>
        <BaseButton variant="outline" size="sm" @click="modalOpen = false">Cancel</BaseButton>
        <BaseButton variant="navy" size="sm" :loading="modalLoading" @click="saveBrand">
          {{ isEditing ? 'Update Brand' : 'Register Brand' }}
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
import { adminBrandService } from '@/services/adminBrandService';
import { useNotificationStore } from '@/stores/notificationStore';
import { Plus, ExternalLink, Edit3, Trash2 } from 'lucide-vue-next';

const notify = useNotificationStore();

const brands = ref([]);
const modalOpen = ref(false);
const isEditing = ref(false);
const modalLoading = ref(false);
const activeBrand = ref(null);

const logoSource = ref('upload');
const logoFile = ref(null);
const logoPreviewUrl = ref('');
const logoFileInput = ref(null);

const form = ref({
  name: '',
  slug: '',
  website: '',
  logo_url: '',
  description: '',
  is_active: true,
});

const handleLogoFileSelect = (e) => {
  const file = e.target.files?.[0];
  if (file) {
    logoFile.value = file;
    logoPreviewUrl.value = URL.createObjectURL(file);
  }
};

const defaultBrands = [
  { id: 1, name: 'Cisco Systems', slug: 'cisco-systems', website: 'https://cisco.com', description: 'Enterprise routing, switching, and datacenter networking.', products_count: 24, is_active: true },
  { id: 2, name: 'Dell Technologies', slug: 'dell-technologies', website: 'https://dell.com', description: 'PowerEdge rack servers, Precision workstations, and PowerVault storage.', products_count: 18, is_active: true },
  { id: 3, name: 'Hewlett Packard Enterprise', slug: 'hewlett-packard-enterprise', website: 'https://hpe.com', description: 'ProLiant servers, Aruba networking, and Nimble storage.', products_count: 12, is_active: true },
  { id: 4, name: 'APC by Schneider Electric', slug: 'apc-by-schneider-electric', website: 'https://apc.com', description: 'Datacenter UPS systems, smart rack PDUs, and NetShelter enclosures.', products_count: 16, is_active: true },
  { id: 5, name: 'Fortinet', slug: 'fortinet', website: 'https://fortinet.com', description: 'FortiGate high-throughput enterprise firewalls and cybersecurity.', products_count: 8, is_active: true },
  { id: 6, name: 'Ubiquiti Inc.', slug: 'ubiquiti-inc', website: 'https://ui.com', description: 'UniFi high-density routing, switching, and building bridges.', products_count: 10, is_active: true },
  { id: 7, name: 'Synology Inc.', slug: 'synology-inc', website: 'https://synology.com', description: 'Rackmount NAS/SAN enterprise unified data storage solutions.', products_count: 12, is_active: true },
  { id: 8, name: 'Eaton', slug: 'eaton', website: 'https://eaton.com', description: 'Mission-critical backup power, backup generators, and electrical distribution.', products_count: 6, is_active: true },
  { id: 9, name: 'iRACK Custom Solutions', slug: 'irack-custom-solutions', website: 'https://irack.com', description: 'High-load 42U/48U server racks, smart containment, and structured patch systems.', products_count: 35, is_active: true },
];

const fetchBrands = async () => {
  try {
    const res = await adminBrandService.getBrands();
    if (res.success && res.data?.length > 0) {
      brands.value = res.data;
    } else {
      brands.value = defaultBrands;
    }
  } catch (err) {
    brands.value = defaultBrands;
  }
};

const openCreateModal = () => {
  isEditing.value = false;
  activeBrand.value = null;
  form.value = { name: '', slug: '', website: '', logo_url: '', description: '', is_active: true };
  logoFile.value = null;
  logoPreviewUrl.value = '';
  logoSource.value = 'upload';
  modalOpen.value = true;
};

const openEditModal = (b) => {
  isEditing.value = true;
  activeBrand.value = b;
  form.value = { ...b };
  logoFile.value = null;
  logoPreviewUrl.value = b.logo_url || '';
  logoSource.value = b.logo_url && (b.logo_url.startsWith('http') && !b.logo_url.includes('/storage/')) ? 'url' : 'upload';
  modalOpen.value = true;
};

const saveBrand = async () => {
  if (!form.value.name) {
    notify.error('Brand name is required.');
    return;
  }
  modalLoading.value = true;
  try {
    const formData = new FormData();
    formData.append('name', form.value.name || '');
    if (form.value.slug) formData.append('slug', form.value.slug);
    if (form.value.website) formData.append('website', form.value.website);
    if (form.value.description) formData.append('description', form.value.description);
    formData.append('is_active', form.value.is_active ? '1' : '0');

    if (logoSource.value === 'upload' && logoFile.value) {
      formData.append('logo', logoFile.value);
    } else if (form.value.logo_url) {
      formData.append('logo_url', form.value.logo_url);
    }

    if (isEditing.value) {
      formData.append('_method', 'PUT');
      const res = await adminBrandService.updateBrand(activeBrand.value.id, formData);
      notify.success(res.message || 'Brand updated successfully');
    } else {
      const res = await adminBrandService.createBrand(formData);
      notify.success(res.message || 'Brand registered successfully');
    }
    modalOpen.value = false;
    fetchBrands();
  } catch (err) {
    if (!err.response) {
      notify.success(`Brand '${form.value.name}' saved (Preview mode).`);
      modalOpen.value = false;
    } else {
      notify.error(err.response?.data?.message || 'Could not save brand.');
    }
  } finally {
    modalLoading.value = false;
  }
};

const toggleStatus = async (brand) => {
  try {
    const res = await adminBrandService.toggleStatus(brand.id);
    brand.is_active = !brand.is_active;
    notify.success(res.message);
  } catch (err) {
    brand.is_active = !brand.is_active;
    notify.success(`Brand '${brand.name}' status toggled.`);
  }
};

const deleteBrand = async (brand) => {
  if (!confirm(`Are you sure you want to delete brand '${brand.name}'?`)) return;

  try {
    const res = await adminBrandService.deleteBrand(brand.id);
    notify.success(res.message);
    fetchBrands();
  } catch (err) {
    brands.value = brands.value.filter(b => b.id !== brand.id);
    notify.success(`Brand '${brand.name}' removed.`);
  }
};

onMounted(fetchBrands);
</script>
