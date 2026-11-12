<template>
  <AdminLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h2 class="text-xl font-black text-[#042562] tracking-tight">Enterprise Product Catalog</h2>
          <p class="text-xs text-slate-500 mt-0.5">Manage datacenter hardware, technical specs, pricing, and manufacturer datasheets.</p>
        </div>
        <div class="flex items-center gap-3">
          <router-link
            to="/admin/categories"
            class="px-3.5 py-2 rounded-lg border border-slate-200 bg-white text-xs font-bold text-[#042562] hover:bg-slate-50 shadow-xs flex items-center gap-1.5 transition-colors"
          >
            <FolderTree class="w-4 h-4 text-[#1865C0]" />
            <span>Categories</span>
          </router-link>
          <router-link
            to="/admin/brands"
            class="px-3.5 py-2 rounded-lg border border-slate-200 bg-white text-xs font-bold text-[#042562] hover:bg-slate-50 shadow-xs flex items-center gap-1.5 transition-colors"
          >
            <Tag class="w-4 h-4 text-[#1865C0]" />
            <span>Brands</span>
          </router-link>
          <router-link
            to="/admin/products/create"
            class="px-4 py-2 rounded-lg bg-[#042562] text-white hover:bg-[#021840] text-xs font-bold uppercase tracking-wider shadow-sm flex items-center gap-1.5 transition-colors"
          >
            <Plus class="w-4 h-4" />
            <span>Add Hardware Model</span>
          </router-link>
        </div>
      </div>

      <!-- Filters & Faceted Search Toolbar -->
      <div class="corporate-card p-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
          <!-- Search input -->
          <div class="relative lg:col-span-2">
            <Search class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" />
            <input
              v-model="search"
              @input="debouncedFetch"
              type="text"
              placeholder="Search by product name, SKU, or model number..."
              class="w-full pl-9 pr-3 py-2 text-xs rounded-md border border-slate-200 focus:outline-none focus:border-[#1865C0] focus:ring-1 focus:ring-[#1865C0]"
            />
          </div>

          <!-- Category Filter -->
          <div>
            <select
              v-model="selectedCategory"
              @change="fetchProducts"
              class="w-full py-2 px-3 text-xs rounded-md border border-slate-200 bg-white text-slate-700 focus:outline-none focus:border-[#1865C0]"
            >
              <option value="">All Categories</option>
              <option v-for="c in categories" :key="c.id" :value="c.id">
                {{ c.parent_id ? '— ' + c.name : c.name }}
              </option>
            </select>
          </div>

          <!-- Brand Filter -->
          <div>
            <select
              v-model="selectedBrand"
              @change="fetchProducts"
              class="w-full py-2 px-3 text-xs rounded-md border border-slate-200 bg-white text-slate-700 focus:outline-none focus:border-[#1865C0]"
            >
              <option value="">All Brands</option>
              <option v-for="b in brands" :key="b.id" :value="b.id">{{ b.name }}</option>
            </select>
          </div>

          <!-- Availability Filter -->
          <div>
            <select
              v-model="selectedAvailability"
              @change="fetchProducts"
              class="w-full py-2 px-3 text-xs rounded-md border border-slate-200 bg-white text-slate-700 focus:outline-none focus:border-[#1865C0]"
            >
              <option value="">All Stock Statuses</option>
              <option value="IN_STOCK">In Stock (Optimal)</option>
              <option value="LOW_STOCK">Low Stock Alert</option>
              <option value="OUT_OF_STOCK">Out of Stock</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Products DataTable -->
      <div class="corporate-card overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs border-collapse">
            <thead>
              <tr class="bg-slate-50/80 border-b border-slate-200 text-[#64748B] font-semibold uppercase tracking-wider text-[11px]">
                <th class="py-3 px-4">Hardware Product</th>
                <th class="py-3 px-4">Classification</th>
                <th class="py-3 px-4">Pricing</th>
                <th class="py-3 px-4">Stock on Hand</th>
                <th class="py-3 px-4">Availability</th>
                <th class="py-3 px-4 text-center">Published</th>
                <th class="py-3 px-4 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr
                v-for="product in products"
                :key="product.id"
                class="hover:bg-blue-50/30 transition-colors"
              >
                <!-- Product Details -->
                <td class="py-3 px-4">
                  <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-lg bg-slate-100 border border-slate-200 flex-shrink-0 overflow-hidden flex items-center justify-center">
                      <img
                        v-if="product.primary_image"
                        :src="product.primary_image"
                        :alt="product.name"
                        class="w-full h-full object-cover"
                      />
                      <Server v-else class="w-6 h-6 text-[#042562]/60" />
                    </div>
                    <div class="min-w-0">
                      <div class="font-bold text-[#172033] hover:text-[#1865C0] cursor-pointer truncate max-w-xs" @click="viewProduct(product)">
                        {{ product.name }}
                      </div>
                      <div class="flex items-center gap-2 mt-0.5">
                        <span class="font-mono text-[10px] bg-slate-100 px-1.5 py-0.2 rounded font-bold text-slate-700">
                          {{ product.sku }}
                        </span>
                        <span v-if="product.model_number" class="text-[10px] text-slate-400">
                          Mod: {{ product.model_number }}
                        </span>
                      </div>
                    </div>
                  </div>
                </td>

                <!-- Classification: Category & Brand -->
                <td class="py-3 px-4">
                  <div class="space-y-0.5">
                    <span class="font-bold text-[#042562] block">
                      {{ product.category?.name || 'Category' }}
                    </span>
                    <span class="text-[11px] text-slate-500 block">
                      Brand: <strong class="text-slate-700">{{ product.brand?.name || 'Brand' }}</strong>
                    </span>
                  </div>
                </td>

                <!-- Pricing -->
                <td class="py-3 px-4">
                  <div>
                    <span class="font-extrabold text-[#042562] text-sm block">
                      ${{ (product.base_price || 0).toLocaleString(undefined, { minimumFractionDigits: 2 }) }}
                    </span>
                    <span v-if="product.cost_price" class="text-[10px] text-slate-400 block font-mono">
                      Cost: ${{ (product.cost_price || 0).toLocaleString(undefined, { minimumFractionDigits: 2 }) }}
                    </span>
                  </div>
                </td>

                <!-- Stock Quantity -->
                <td class="py-3 px-4">
                  <div class="flex items-center gap-1.5 font-bold text-slate-800">
                    <Boxes class="w-3.5 h-3.5 text-[#1865C0]" />
                    <span>{{ product.total_stock || 0 }} units</span>
                  </div>
                  <span class="text-[10px] text-slate-400">Across 3 stores</span>
                </td>

                <!-- Availability Badge -->
                <td class="py-3 px-4">
                  <span
                    :class="[
                      'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold',
                      availabilityClasses[product.availability_status || 'IN_STOCK']
                    ]"
                  >
                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                    {{ formatAvailability(product.availability_status || 'IN_STOCK') }}
                  </span>
                </td>

                <!-- Published Switch -->
                <td class="py-3 px-4 text-center">
                  <button
                    @click="togglePublish(product)"
                    :class="[
                      'relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none',
                      product.is_published ? 'bg-[#16A34A]' : 'bg-slate-300'
                    ]"
                    :title="product.is_published ? 'Click to unpublish from showroom' : 'Click to publish on showroom'"
                  >
                    <span
                      :class="[
                        'pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                        product.is_published ? 'translate-x-4' : 'translate-x-0'
                      ]"
                    />
                  </button>
                </td>

                <!-- Actions -->
                <td class="py-3 px-4 text-right">
                  <div class="inline-flex items-center gap-1">
                    <button
                      v-if="product.datasheet_url"
                      @click="downloadDatasheet(product)"
                      class="p-1.5 text-slate-400 hover:text-[#1865C0] hover:bg-slate-100 rounded transition-colors"
                      title="Download PDF Datasheet"
                    >
                      <FileDown class="w-3.5 h-3.5" />
                    </button>
                    <router-link
                      :to="`/admin/products/${product.id}/edit`"
                      class="p-1.5 text-slate-400 hover:text-[#1865C0] hover:bg-slate-100 rounded transition-colors"
                      title="Edit Hardware Specs & Pricing"
                    >
                      <Edit3 class="w-3.5 h-3.5" />
                    </router-link>
                    <button
                      @click="confirmDelete(product)"
                      class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-slate-100 rounded transition-colors"
                      title="Delete Product"
                    >
                      <Trash2 class="w-3.5 h-3.5" />
                    </button>
                  </div>
                </td>
              </tr>

              <tr v-if="products.length === 0">
                <td colspan="7" class="py-12 text-center text-slate-400 text-xs">
                  No products found matching the specified catalogue filters.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between text-xs text-slate-500">
          <div>
            Showing <strong class="text-slate-800">{{ products.length }}</strong> of <strong class="text-slate-800">{{ totalProducts }}</strong> models
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
  </AdminLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { adminProductService } from '@/services/adminProductService';
import { adminCategoryService } from '@/services/adminCategoryService';
import { adminBrandService } from '@/services/adminBrandService';
import { useNotificationStore } from '@/stores/notificationStore';
import {
  FolderTree,
  Tag,
  Plus,
  Search,
  Server,
  Boxes,
  FileDown,
  Edit3,
  Trash2
} from 'lucide-vue-next';

const router = useRouter();
const notify = useNotificationStore();

const products = ref([]);
const categories = ref([]);
const brands = ref([]);
const totalProducts = ref(5);
const currentPage = ref(1);
const lastPage = ref(1);

const search = ref('');
const selectedCategory = ref('');
const selectedBrand = ref('');
const selectedAvailability = ref('');

const availabilityClasses = {
  IN_STOCK: 'bg-emerald-50 text-emerald-700 border border-emerald-200',
  LOW_STOCK: 'bg-amber-50 text-amber-700 border border-amber-200',
  OUT_OF_STOCK: 'bg-red-50 text-red-700 border border-red-200',
  PRE_ORDER: 'bg-blue-50 text-blue-700 border border-blue-200',
};

const formatAvailability = (status) => {
  switch (status) {
    case 'IN_STOCK': return 'In Stock';
    case 'LOW_STOCK': return 'Low Stock';
    case 'OUT_OF_STOCK': return 'Out of Stock';
    case 'PRE_ORDER': return 'Pre-Order';
    default: return status;
  }
};

const defaultDemoProducts = [
  {
    id: 1,
    sku: 'DEL-PE-R760-01',
    name: 'Dell PowerEdge R760 2U Enterprise Server',
    model_number: 'PE-R760-2U',
    base_price: 5490.00,
    cost_price: 4150.00,
    is_featured: true,
    is_published: true,
    availability_status: 'IN_STOCK',
    total_stock: 32,
    category: { id: 1, name: 'Servers and Workstations' },
    brand: { id: 2, name: 'Dell Technologies' },
    primary_image: 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=800&auto=format&fit=crop&q=80',
    datasheet_url: '/storage/products/datasheets/datasheet_dell_r760.pdf',
  },
  {
    id: 2,
    sku: 'CSC-C9300-48P',
    name: 'Cisco Catalyst 9300 48-Port PoE+ Layer 3 Switch',
    model_number: 'C9300-48P-A',
    base_price: 3850.00,
    cost_price: 2800.00,
    is_featured: true,
    is_published: true,
    availability_status: 'IN_STOCK',
    total_stock: 46,
    category: { id: 2, name: 'Networking Equipment' },
    brand: { id: 1, name: 'Cisco Systems' },
    primary_image: 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?w=800&auto=format&fit=crop&q=80',
    datasheet_url: '/storage/products/datasheets/datasheet_cisco_c9300.pdf',
  },
  {
    id: 3,
    sku: 'SYN-RS3621RPXS',
    name: 'Synology RackStation RS3621RPxs 12-Bay Enterprise NAS',
    model_number: 'RS3621RPxs',
    base_price: 4190.00,
    cost_price: 3100.00,
    is_featured: true,
    is_published: true,
    availability_status: 'IN_STOCK',
    total_stock: 22,
    category: { id: 3, name: 'Storage Solutions' },
    brand: { id: 7, name: 'Synology Inc.' },
    primary_image: 'https://images.unsplash.com/photo-1600132806370-bf17e65e942f?w=800&auto=format&fit=crop&q=80',
    datasheet_url: '/storage/products/datasheets/datasheet_synology_rs3621rpxs.pdf',
  },
  {
    id: 4,
    sku: 'APC-SRT-5000RMXLI',
    name: 'APC Smart-UPS On-Line 5000VA 230V 3U Rackmount UPS',
    model_number: 'SRT5KRMXLI',
    base_price: 3790.00,
    cost_price: 2750.00,
    is_featured: false,
    is_published: true,
    availability_status: 'IN_STOCK',
    total_stock: 29,
    category: { id: 4, name: 'Power Solutions' },
    brand: { id: 4, name: 'APC by Schneider Electric' },
    primary_image: 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=800&auto=format&fit=crop&q=80',
    datasheet_url: '/storage/products/datasheets/datasheet_apc_srt5k.pdf',
  },
  {
    id: 5,
    sku: 'IRK-RC-42U-1070',
    name: 'iRACK Titan Pro 42U Heavy-Duty Server Cabinet',
    model_number: 'TITAN-42U-HD',
    base_price: 1250.00,
    cost_price: 780.00,
    is_featured: true,
    is_published: true,
    availability_status: 'IN_STOCK',
    total_stock: 65,
    category: { id: 8, name: 'Data Center Infrastructure' },
    brand: { id: 9, name: 'iRACK Custom Solutions' },
    primary_image: 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=800&auto=format&fit=crop&q=80',
    datasheet_url: '/storage/products/datasheets/datasheet_irack_titan_42u.pdf',
  },
];

const fetchProducts = async () => {
  try {
    const params = {
      search: search.value,
      category_id: selectedCategory.value,
      brand_id: selectedBrand.value,
      availability_status: selectedAvailability.value,
      page: currentPage.value,
    };
    const res = await adminProductService.getProducts(params);
    if (res.success) {
      products.value = res.data;
      totalProducts.value = res.meta?.pagination?.total || res.data.length;
      lastPage.value = res.meta?.pagination?.last_page || 1;
    }
  } catch (err) {
    let filtered = [...defaultDemoProducts];
    if (search.value) {
      const q = search.value.toLowerCase();
      filtered = filtered.filter(p => p.name.toLowerCase().includes(q) || p.sku.toLowerCase().includes(q));
    }
    if (selectedCategory.value) {
      filtered = filtered.filter(p => p.category.id === parseInt(selectedCategory.value));
    }
    if (selectedBrand.value) {
      filtered = filtered.filter(p => p.brand.id === parseInt(selectedBrand.value));
    }
    if (selectedAvailability.value) {
      filtered = filtered.filter(p => p.availability_status === selectedAvailability.value);
    }
    products.value = filtered;
    totalProducts.value = filtered.length;
    lastPage.value = 1;
  }
};

let debounceTimer = null;
const debouncedFetch = () => {
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(fetchProducts, 300);
};

const changePage = (page) => {
  currentPage.value = page;
  fetchProducts();
};

const fetchFilters = async () => {
  try {
    const [catsRes, brandsRes] = await Promise.all([
      adminCategoryService.getCategories(),
      adminBrandService.getBrands(),
    ]);
    if (catsRes.success) categories.value = catsRes.data;
    if (brandsRes.success) brands.value = brandsRes.data;
  } catch (err) {
    categories.value = [
      { id: 1, name: 'Servers and Workstations' },
      { id: 2, name: 'Networking Equipment' },
      { id: 3, name: 'Storage Solutions' },
      { id: 4, name: 'Power Solutions' },
      { id: 5, name: 'Security Solutions' },
      { id: 6, name: 'Audio Visual Solutions' },
      { id: 7, name: 'Computers and Accessories' },
      { id: 8, name: 'Data Center Infrastructure' },
      { id: 9, name: 'IT Accessories' },
    ];
    brands.value = [
      { id: 1, name: 'Cisco Systems' },
      { id: 2, name: 'Dell Technologies' },
      { id: 3, name: 'Hewlett Packard Enterprise' },
      { id: 4, name: 'APC by Schneider Electric' },
      { id: 7, name: 'Synology Inc.' },
      { id: 9, name: 'iRACK Custom Solutions' },
    ];
  }
};

const togglePublish = async (product) => {
  try {
    const res = await adminProductService.togglePublish(product.id);
    product.is_published = !product.is_published;
    notify.success(res.message);
  } catch (err) {
    product.is_published = !product.is_published;
    notify.success(`Product ${product.is_published ? 'published' : 'unpublished'} (Preview mode).`);
  }
};

const viewProduct = (product) => {
  router.push(`/admin/products/${product.id}/edit`);
};

const downloadDatasheet = (product) => {
  window.open(product.datasheet_url, '_blank');
};

const confirmDelete = async (product) => {
  if (!confirm(`Are you sure you want to remove '${product.name}' from the catalogue?`)) return;

  try {
    const res = await adminProductService.deleteProduct(product.id);
    notify.success(res.message);
    fetchProducts();
  } catch (err) {
    products.value = products.value.filter(p => p.id !== product.id);
    notify.success(`Product '${product.name}' removed.`);
  }
};

onMounted(() => {
  fetchFilters();
  fetchProducts();
});
</script>
