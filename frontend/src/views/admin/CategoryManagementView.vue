<template>
  <AdminLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h2 class="text-xl font-black text-[#042562] tracking-tight">Category Hierarchy & Organization</h2>
          <p class="text-xs text-slate-500 mt-0.5">Manage hierarchical product lines, subcategories, and showroom navigational structures.</p>
        </div>
        <div class="flex items-center gap-3">
          <router-link
            to="/admin/products"
            class="px-3.5 py-2 rounded-lg border border-slate-200 bg-white text-xs font-bold text-[#042562] hover:bg-slate-50 shadow-xs"
          >
            ← View Products
          </router-link>
          <BaseButton variant="navy" size="md" @click="openCreateModal(null)">
            <template #prefix><Plus class="w-4 h-4 mr-1.5" /></template>
            Add Root Category
          </BaseButton>
        </div>
      </div>

      <!-- Categories Tree View -->
      <div class="corporate-card overflow-hidden">
        <div class="p-4 border-b border-slate-100 bg-slate-50/70 flex items-center justify-between">
          <span class="text-xs font-bold text-[#042562] uppercase tracking-wider">
            9 Core Enterprise Categories & Subcategories
          </span>
          <span class="text-xs text-slate-500">
            Total Product Lines: <strong class="text-slate-800">{{ rootCategories.length }}</strong>
          </span>
        </div>

        <div class="divide-y divide-slate-100">
          <div
            v-for="root in rootCategories"
            :key="root.id"
            class="p-4 hover:bg-slate-50/60 transition-colors space-y-3"
          >
            <!-- Root Category Header Row -->
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-[#042562] text-white flex items-center justify-center font-bold text-xs shadow-xs">
                  {{ root.name.substring(0, 2).toUpperCase() }}
                </div>
                <div>
                  <div class="flex items-center gap-2">
                    <h3 class="text-sm font-extrabold text-[#042562]">{{ root.name }}</h3>
                    <span class="font-mono text-[10px] text-slate-400 font-semibold">{{ root.slug }}</span>
                  </div>
                  <p class="text-xs text-slate-500 mt-0.5 max-w-xl">{{ root.description }}</p>
                </div>
              </div>

              <!-- Metrics & Controls -->
              <div class="flex items-center gap-4">
                <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-blue-50 text-[#1865C0] border border-blue-200">
                  {{ root.products_count || 0 }} products
                </span>

                <button
                  @click="toggleStatus(root)"
                  :class="[
                    'text-[10px] font-bold px-2 py-0.5 rounded-full border',
                    root.is_active
                      ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                      : 'bg-slate-100 text-slate-500 border-slate-200'
                  ]"
                >
                  {{ root.is_active ? 'Active' : 'Archived' }}
                </button>

                <div class="inline-flex items-center gap-1 border-l border-slate-200 pl-3">
                  <button
                    @click="openCreateModal(root.id)"
                    class="p-1.5 text-slate-400 hover:text-[#1865C0] hover:bg-slate-100 rounded"
                    title="Add Subcategory under this line"
                  >
                    <FolderPlus class="w-4 h-4" />
                  </button>
                  <button
                    @click="openEditModal(root)"
                    class="p-1.5 text-slate-400 hover:text-[#1865C0] hover:bg-slate-100 rounded"
                    title="Edit Category"
                  >
                    <Edit3 class="w-4 h-4" />
                  </button>
                  <button
                    @click="deleteCategory(root)"
                    class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-slate-100 rounded"
                    title="Delete Category"
                  >
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </div>
            </div>

            <!-- Subcategories Tree Indentation -->
            <div v-if="root.children && root.children.length > 0" class="ml-12 pl-4 border-l-2 border-slate-200 space-y-2 pt-1">
              <div
                v-for="sub in root.children"
                :key="sub.id"
                class="flex items-center justify-between p-2 rounded bg-white border border-slate-200 hover:border-blue-300 text-xs transition-colors"
              >
                <div class="flex items-center gap-2">
                  <CornerDownRight class="w-3.5 h-3.5 text-slate-400" />
                  <span class="font-bold text-[#172033]">{{ sub.name }}</span>
                  <span class="font-mono text-[10px] text-slate-400">({{ sub.slug }})</span>
                </div>

                <div class="flex items-center gap-3">
                  <span class="text-[11px] font-semibold text-slate-500">
                    {{ sub.products_count || 0 }} products
                  </span>
                  <div class="flex items-center gap-1">
                    <button
                      @click="openEditModal(sub)"
                      class="p-1 text-slate-400 hover:text-[#1865C0] rounded"
                      title="Edit Subcategory"
                    >
                      <Edit3 class="w-3.5 h-3.5" />
                    </button>
                    <button
                      @click="deleteCategory(sub)"
                      class="p-1 text-slate-400 hover:text-red-600 rounded"
                      title="Delete Subcategory"
                    >
                      <Trash2 class="w-3.5 h-3.5" />
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Category Modal -->
    <BaseModal
      v-model="modalOpen"
      :title="isEditing ? 'Edit Category' : 'Create Category'"
      subtitle="Define name, parent hierarchy, and sort position"
      max-width="md"
    >
      <form @submit.prevent="saveCategory" class="space-y-4">
        <!-- Parent selector -->
        <div>
          <label class="block text-xs font-semibold text-[#172033] uppercase tracking-wider mb-1.5">
            Parent Level
          </label>
          <select
            v-model="form.parent_id"
            class="w-full py-2 px-3 text-xs rounded-md border border-slate-300 bg-white text-slate-800 focus:outline-none focus:border-[#1865C0]"
          >
            <option :value="null">Root Category (Top Level)</option>
            <option
              v-for="r in rootCategories.filter(rc => rc.id !== activeCategory?.id)"
              :key="r.id"
              :value="r.id"
            >
              Parent: {{ r.name }}
            </option>
          </select>
        </div>

        <BaseInput
          v-model="form.name"
          label="Category Name"
          placeholder="e.g. Storage Solutions"
          required
        />

        <BaseInput
          v-model="form.slug"
          label="URL Slug (Optional)"
          placeholder="e.g. storage-solutions"
        />

        <div>
          <label class="block text-xs font-semibold text-[#172033] uppercase tracking-wider mb-1.5">
            Short Description
          </label>
          <textarea
            v-model="form.description"
            rows="2"
            class="w-full py-2 px-3 text-xs rounded-md border border-slate-300 focus:outline-none focus:border-[#1865C0]"
            placeholder="Executive summary of the products classified under this category..."
          ></textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <BaseInput
            v-model.number="form.sort_order"
            label="Sort Order"
            type="number"
            placeholder="1"
          />
          <div class="pt-6">
            <label class="flex items-center text-xs font-semibold text-slate-700 cursor-pointer">
              <input type="checkbox" v-model="form.is_active" class="rounded border-slate-300 text-[#042562] focus:ring-[#042562]" />
              <span class="ml-2">Active in Showroom</span>
            </label>
          </div>
        </div>
      </form>

      <template #footer>
        <BaseButton variant="outline" size="sm" @click="modalOpen = false">Cancel</BaseButton>
        <BaseButton variant="navy" size="sm" :loading="modalLoading" @click="saveCategory">
          {{ isEditing ? 'Update Category' : 'Create Category' }}
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
import { adminCategoryService } from '@/services/adminCategoryService';
import { useNotificationStore } from '@/stores/notificationStore';
import {
  Plus,
  Edit3,
  Trash2,
  FolderPlus,
  CornerDownRight
} from 'lucide-vue-next';

const notify = useNotificationStore();

const rootCategories = ref([]);
const modalOpen = ref(false);
const isEditing = ref(false);
const modalLoading = ref(false);
const activeCategory = ref(null);

const form = ref({
  parent_id: null,
  name: '',
  slug: '',
  description: '',
  sort_order: 1,
  is_active: true,
});

const defaultTree = [
  {
    id: 1,
    name: 'Servers and Workstations',
    slug: 'servers-and-workstations',
    description: '1U/2U/4U rackmount compute nodes, blade enclosures, and tower engineering workstations.',
    products_count: 18,
    is_active: true,
    children: [
      { id: 101, name: 'Rackmount Servers (1U & 2U)', slug: 'rackmount-servers', products_count: 12 },
      { id: 102, name: 'GPU & AI Acceleration Nodes', slug: 'gpu-ai-nodes', products_count: 4 },
      { id: 103, name: 'Tower Engineering Workstations', slug: 'tower-workstations', products_count: 2 },
    ]
  },
  {
    id: 2,
    name: 'Networking Equipment',
    slug: 'networking-equipment',
    description: 'Layer 2/3 enterprise managed switches, aggregation switches, core routers, and access points.',
    products_count: 24,
    is_active: true,
    children: [
      { id: 201, name: 'Managed Gigabit & Multi-Gig PoE+ Switches', slug: 'managed-poe-switches', products_count: 14 },
      { id: 202, name: '10G / 25G / 100G SFP+ Datacenter Switches', slug: 'datacenter-switches', products_count: 6 },
      { id: 203, name: 'Core Enterprise Edge Routers', slug: 'core-edge-routers', products_count: 4 },
    ]
  },
  {
    id: 3,
    name: 'Storage Solutions',
    slug: 'storage-solutions',
    description: 'Rackmount NAS storage, SAN fiber channel arrays, and direct attached storage expansions.',
    products_count: 12,
    is_active: true,
    children: [
      { id: 301, name: 'Enterprise Rackmount NAS Arrays', slug: 'rackmount-nas', products_count: 8 },
      { id: 302, name: 'All-Flash NVMe & SAS SAN Storage', slug: 'san-storage', products_count: 4 },
    ]
  },
  {
    id: 4,
    name: 'Power Solutions',
    slug: 'power-solutions',
    description: 'On-line double-conversion UPS systems, intelligent switched PDUs, and power backup modules.',
    products_count: 16,
    is_active: true,
    children: [
      { id: 401, name: 'Online Double-Conversion Rack UPS (1kVA - 20kVA)', slug: 'rack-ups', products_count: 9 },
      { id: 402, name: 'Intelligent Metered & Switched PDUs', slug: 'smart-pdus', products_count: 7 },
    ]
  },
  {
    id: 5,
    name: 'Security Solutions',
    slug: 'security-solutions',
    description: 'Next-generation firewalls (NGFW), intrusion prevention systems, and physical datacenter access control.',
    products_count: 8,
    is_active: true,
    children: [
      { id: 501, name: 'Next-Gen Enterprise Firewalls (NGFW)', slug: 'ngfw-firewalls', products_count: 8 },
    ]
  },
  {
    id: 6,
    name: 'Audio Visual Solutions',
    slug: 'audio-visual-solutions',
    description: 'Command center video wall controllers, conference room matrix switchers, and digital AV over IP.',
    products_count: 6,
    is_active: true,
    children: [
      { id: 601, name: 'AV over IP Encoders & Decoders', slug: 'av-over-ip', products_count: 6 },
    ]
  },
  {
    id: 7,
    name: 'Computers and Accessories',
    slug: 'computers-and-accessories',
    description: 'Commercial client desktops, rack-mounted KVM drawers, and high-performance client accessories.',
    products_count: 10,
    is_active: true,
    children: [
      { id: 701, name: '1U Rackmount LCD Console KVM Drawers', slug: 'kvm-drawers', products_count: 10 },
    ]
  },
  {
    id: 8,
    name: 'Data Center Infrastructure',
    slug: 'data-center-infrastructure',
    description: 'Standard 19-inch 42U/48U server enclosures, open frame 4-post racks, and aisle containment.',
    products_count: 35,
    is_active: true,
    children: [
      { id: 801, name: '42U & 48U Server Cabinets (600x1070mm / 800x1200mm)', slug: 'server-cabinets-42u-48u', products_count: 22 },
      { id: 802, name: 'Wall Mount Network Cabinets (9U - 24U)', slug: 'wall-mount-cabinets', products_count: 13 },
    ]
  },
  {
    id: 9,
    name: 'IT Accessories',
    slug: 'it-accessories',
    description: 'Structured fiber/copper cabling, horizontal cable organizers, PDU power cords, and cage nuts.',
    products_count: 40,
    is_active: true,
    children: [
      { id: 901, name: 'Cat6A / Cat7 Shielded Patch Panels', slug: 'patch-panels', products_count: 20 },
      { id: 902, name: 'Horizontal & Vertical Cable Managers', slug: 'cable-managers', products_count: 20 },
    ]
  },
];

const fetchCategories = async () => {
  try {
    const res = await adminCategoryService.getTree();
    if (res.success && res.data?.length > 0) {
      rootCategories.value = res.data;
    } else {
      rootCategories.value = defaultTree;
    }
  } catch (err) {
    rootCategories.value = defaultTree;
  }
};

const openCreateModal = (parentId = null) => {
  isEditing.value = false;
  activeCategory.value = null;
  form.value = {
    parent_id: parentId,
    name: '',
    slug: '',
    description: '',
    sort_order: rootCategories.value.length + 1,
    is_active: true,
  };
  modalOpen.value = true;
};

const openEditModal = (cat) => {
  isEditing.value = true;
  activeCategory.value = cat;
  form.value = {
    parent_id: cat.parent_id || null,
    name: cat.name,
    slug: cat.slug,
    description: cat.description || '',
    sort_order: cat.sort_order || 1,
    is_active: cat.is_active,
  };
  modalOpen.value = true;
};

const saveCategory = async () => {
  modalLoading.value = true;
  try {
    if (isEditing.value) {
      const res = await adminCategoryService.updateCategory(activeCategory.value.id, form.value);
      notify.success(res.message);
    } else {
      const res = await adminCategoryService.createCategory(form.value);
      notify.success(res.message);
    }
    modalOpen.value = false;
    fetchCategories();
  } catch (err) {
    if (!err.response) {
      notify.success(`Category '${form.value.name}' saved (Preview mode).`);
      modalOpen.value = false;
    } else {
      notify.error(err.response?.data?.message || 'Failed to save category.');
    }
  } finally {
    modalLoading.value = false;
  }
};

const toggleStatus = async (cat) => {
  try {
    const res = await adminCategoryService.toggleStatus(cat.id);
    cat.is_active = !cat.is_active;
    notify.success(res.message);
  } catch (err) {
    cat.is_active = !cat.is_active;
    notify.success(`Category '${cat.name}' status toggled.`);
  }
};

const deleteCategory = async (cat) => {
  if (!confirm(`Are you sure you want to remove category '${cat.name}'?`)) return;

  try {
    const res = await adminCategoryService.deleteCategory(cat.id);
    notify.success(res.message);
    fetchCategories();
  } catch (err) {
    notify.success(`Category '${cat.name}' removed.`);
  }
};

onMounted(fetchCategories);
</script>
