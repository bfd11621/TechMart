<template>
  <PublicLayout>
    <!-- Header Title Bar -->
    <div class="bg-[#042562] text-white py-8 border-b border-navy-800">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
          <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-blue-300 uppercase tracking-wider mb-1">
              <router-link to="/" class="hover:text-white">Home</router-link>
              <span>/</span>
              <span class="text-white">Product Catalog</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black">Enterprise Hardware Showroom</h1>
          </div>
          <div class="text-xs text-blue-200">
            Showing <strong class="text-white">{{ totalProducts }}</strong> verified IT hardware models
          </div>
        </div>
      </div>
    </div>

    <!-- Main Catalog Workspace -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Left Column: Faceted Filter Sidebar -->
        <aside class="lg:col-span-3 space-y-6">
          <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm space-y-6">
            <!-- Filter Header -->
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
              <div class="flex items-center gap-2">
                <SlidersHorizontal class="w-4 h-4 text-[#1865C0]" />
                <h3 class="text-xs font-black uppercase tracking-wider text-[#042562]">
                  Faceted Filters
                </h3>
              </div>
              <button
                v-if="hasActiveFilters"
                @click="resetAllFilters"
                class="text-xs font-semibold text-[#E20913] hover:underline"
              >
                Reset All
              </button>
            </div>

            <!-- Search by Name, Model, or SKU -->
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                Keyword / Model
              </label>
              <div class="relative">
                <input
                  type="text"
                  v-model="filters.search"
                  @input="debounceSearch"
                  placeholder="e.g. R760, Catalyst, 42U..."
                  class="w-full bg-[#F4F6FA] text-xs text-[#172033] rounded-lg pl-8 pr-3 py-2 border border-slate-300 focus:outline-none focus:border-[#1865C0]"
                />
                <Search class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" />
              </div>
            </div>

            <!-- Category Filter Accordion -->
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                Categories
              </label>
              <div class="space-y-1.5 max-h-60 overflow-y-auto pr-1">
                <div
                  @click="setCategory('')"
                  :class="[
                    'flex items-center justify-between p-2 rounded-lg text-xs cursor-pointer transition-colors',
                    !filters.category ? 'bg-[#042562] text-white font-bold' : 'hover:bg-slate-100 text-slate-700',
                  ]"
                >
                  <span>All Categories</span>
                  <span class="text-[10px] font-mono opacity-80">{{ allCategoryTotal }}</span>
                </div>

                <div
                  v-for="cat in categories"
                  :key="cat.id"
                  @click="setCategory(cat.slug)"
                  :class="[
                    'flex items-center justify-between p-2 rounded-lg text-xs cursor-pointer transition-colors',
                    filters.category === cat.slug ? 'bg-[#042562] text-white font-bold' : 'hover:bg-slate-100 text-slate-700',
                  ]"
                >
                  <span class="truncate">{{ cat.name }}</span>
                  <span class="text-[10px] font-mono opacity-80">{{ cat.products_count || '0' }}</span>
                </div>
              </div>
            </div>

            <!-- Manufacturer / Brand Filter -->
            <div class="border-t border-slate-100 pt-5">
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                Manufacturer Brand
              </label>
              <div class="space-y-1.5 max-h-52 overflow-y-auto pr-1">
                <div
                  @click="setBrand('')"
                  :class="[
                    'flex items-center justify-between p-2 rounded-lg text-xs cursor-pointer transition-colors',
                    !filters.brand ? 'bg-[#1865C0] text-white font-bold' : 'hover:bg-slate-100 text-slate-700',
                  ]"
                >
                  <span>All Brands</span>
                </div>
                <div
                  v-for="b in brands"
                  :key="b.id"
                  @click="setBrand(b.slug)"
                  :class="[
                    'flex items-center justify-between p-2 rounded-lg text-xs cursor-pointer transition-colors',
                    filters.brand === b.slug ? 'bg-[#1865C0] text-white font-bold' : 'hover:bg-slate-100 text-slate-700',
                  ]"
                >
                  <span class="truncate">{{ b.name }}</span>
                  <span class="text-[10px] font-mono opacity-80">{{ b.products_count || '0' }}</span>
                </div>
              </div>
            </div>

            <!-- Stock Availability Filter -->
            <div class="border-t border-slate-100 pt-5">
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                Availability
              </label>
              <div class="space-y-1 text-xs">
                <label class="flex items-center gap-2 p-1.5 hover:bg-slate-50 rounded cursor-pointer">
                  <input
                    type="radio"
                    value=""
                    v-model="filters.availability"
                    @change="fetchProducts"
                    class="text-[#1865C0] focus:ring-0"
                  />
                  <span>All Stock Conditions</span>
                </label>
                <label class="flex items-center gap-2 p-1.5 hover:bg-slate-50 rounded cursor-pointer">
                  <input
                    type="radio"
                    value="in_stock"
                    v-model="filters.availability"
                    @change="fetchProducts"
                    class="text-[#1865C0] focus:ring-0"
                  />
                  <span class="flex items-center gap-1.5 text-emerald-700 font-semibold">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> In Stock (Immediate Dispatch)
                  </span>
                </label>
              </div>
            </div>
          </div>
        </aside>

        <!-- Right Column: Products Stream & Controls -->
        <main class="lg:col-span-9 space-y-6">
          <!-- Top Control Strip -->
          <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
            <!-- Active Filters Summary -->
            <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
              <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                Sort:
              </span>
              <select
                v-model="filters.sort"
                @change="fetchProducts"
                class="bg-[#F4F6FA] text-xs font-semibold text-[#172033] rounded-lg px-3 py-1.5 border border-slate-300 focus:outline-none focus:border-[#1865C0]"
              >
                <option value="newest">Newest Arrivals</option>
                <option value="name_asc">Model Name (A to Z)</option>
                <option value="name_desc">Model Name (Z to A)</option>
              </select>
            </div>

            <!-- View Mode Switcher -->
            <div class="flex items-center gap-2 self-end sm:self-auto">
              <span class="text-xs text-slate-500 mr-1 hidden sm:inline">View:</span>
              <button
                @click="viewMode = 'grid'"
                :class="[
                  'p-2 rounded-lg border transition-colors',
                  viewMode === 'grid'
                    ? 'bg-[#042562] text-white border-[#042562]'
                    : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50',
                ]"
                title="Grid View"
              >
                <Grid class="w-4 h-4" />
              </button>
              <button
                @click="viewMode = 'list'"
                :class="[
                  'p-2 rounded-lg border transition-colors',
                  viewMode === 'list'
                    ? 'bg-[#042562] text-white border-[#042562]'
                    : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50',
                ]"
                title="List View"
              >
                <List class="w-4 h-4" />
              </button>
            </div>
          </div>

          <!-- Active Filter Pills -->
          <div v-if="hasActiveFilters" class="flex flex-wrap items-center gap-2">
            <span class="text-xs text-slate-500">Active Filters:</span>
            <span
              v-if="filters.search"
              class="inline-flex items-center gap-1.5 text-xs bg-blue-50 text-[#1865C0] font-semibold px-2.5 py-1 rounded-full border border-blue-200"
            >
              Search: "{{ filters.search }}"
              <button @click="clearSearch" class="hover:text-red-500"><X class="w-3 h-3" /></button>
            </span>
            <span
              v-if="filters.category"
              class="inline-flex items-center gap-1.5 text-xs bg-blue-50 text-[#1865C0] font-semibold px-2.5 py-1 rounded-full border border-blue-200"
            >
              Category: {{ getCategoryName(filters.category) }}
              <button @click="setCategory('')" class="hover:text-red-500"><X class="w-3 h-3" /></button>
            </span>
            <span
              v-if="filters.brand"
              class="inline-flex items-center gap-1.5 text-xs bg-blue-50 text-[#1865C0] font-semibold px-2.5 py-1 rounded-full border border-blue-200"
            >
              Brand: {{ getBrandName(filters.brand) }}
              <button @click="setBrand('')" class="hover:text-red-500"><X class="w-3 h-3" /></button>
            </span>
            <span
              v-if="filters.availability"
              class="inline-flex items-center gap-1.5 text-xs bg-emerald-50 text-emerald-800 font-semibold px-2.5 py-1 rounded-full border border-emerald-200"
            >
              In Stock Only
              <button @click="filters.availability = ''; fetchProducts()" class="hover:text-red-500"><X class="w-3 h-3" /></button>
            </span>
          </div>

          <!-- Loading State -->
          <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="i in 6" :key="i" class="h-80 rounded-xl bg-slate-200 animate-pulse"></div>
          </div>

          <!-- Empty State -->
          <div
            v-else-if="products.length === 0"
            class="bg-white rounded-xl border border-slate-200 p-12 text-center space-y-4"
          >
            <div class="w-16 h-16 rounded-full bg-blue-50 text-[#042562] mx-auto flex items-center justify-center">
              <Search class="w-8 h-8" />
            </div>
            <h3 class="text-lg font-black text-[#042562]">No IT Hardware Models Found</h3>
            <p class="text-xs text-slate-500 max-w-sm mx-auto">
              We couldn't find products matching your current search parameters. Try clearing your filters or searching for another keyword.
            </p>
            <button
              @click="resetAllFilters"
              class="inline-flex items-center gap-2 bg-[#042562] hover:bg-[#063485] text-white text-xs font-bold uppercase tracking-wider px-5 py-2.5 rounded-lg transition-colors"
            >
              <span>Reset All Filters</span>
            </button>
          </div>

          <!-- Products Grid / List View -->
          <div
            v-else
            :class="[
              viewMode === 'grid'
                ? 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6'
                : 'space-y-4',
            ]"
          >
            <ProductCard
              v-for="product in products"
              :key="product.id"
              :product="product"
              :view-mode="viewMode"
              @quick-view="openQuickView"
            />
          </div>

          <!-- Pagination Controls -->
          <div
            v-if="pagination.last_page > 1"
            class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm flex items-center justify-between"
          >
            <div class="text-xs text-slate-500">
              Page <strong>{{ pagination.current_page }}</strong> of <strong>{{ pagination.last_page }}</strong>
            </div>

            <div class="flex items-center gap-2">
              <button
                @click="changePage(pagination.current_page - 1)"
                :disabled="pagination.current_page <= 1"
                class="px-3 py-1.5 rounded-lg border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed"
              >
                Previous
              </button>

              <button
                v-for="p in pagination.last_page"
                :key="p"
                @click="changePage(p)"
                :class="[
                  'w-8 h-8 rounded-lg text-xs font-bold transition-colors',
                  p === pagination.current_page
                    ? 'bg-[#042562] text-white'
                    : 'text-slate-700 hover:bg-slate-100',
                ]"
              >
                {{ p }}
              </button>

              <button
                @click="changePage(pagination.current_page + 1)"
                :disabled="pagination.current_page >= pagination.last_page"
                class="px-3 py-1.5 rounded-lg border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed"
              >
                Next
              </button>
            </div>
          </div>
        </main>
      </div>
    </div>

    <!-- Quick View Modal -->
    <QuickViewModal
      :is-open="quickViewOpen"
      :product="selectedQuickProduct"
      @close="quickViewOpen = false"
    />
  </PublicLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import PublicLayout from '@/layouts/PublicLayout.vue';
import ProductCard from '@/components/catalog/ProductCard.vue';
import QuickViewModal from '@/components/common/QuickViewModal.vue';
import catalogService from '@/services/catalogService';
import {
  SlidersHorizontal,
  Search,
  Grid,
  List,
  X,
} from 'lucide-vue-next';

const route = useRoute();
const router = useRouter();

const loading = ref(true);
const products = ref([]);
const categories = ref([]);
const brands = ref([]);
const viewMode = ref('grid');

const totalProducts = ref(0);
const pagination = reactive({
  current_page: 1,
  last_page: 1,
  per_page: 12,
});

const filters = reactive({
  search: route.query.search || '',
  category: route.query.category || '',
  brand: route.query.brand || '',
  availability: route.query.availability || '',
  sort: route.query.sort || 'newest',
  featured: route.query.featured || '',
});

const quickViewOpen = ref(false);
const selectedQuickProduct = ref(null);

const allCategoryTotal = computed(() => {
  return categories.value.reduce((acc, c) => acc + (c.products_count || 0), 0);
});

const hasActiveFilters = computed(() => {
  return (
    !!filters.search ||
    !!filters.category ||
    !!filters.brand ||
    !!filters.availability ||
    !!filters.featured
  );
});

const getCategoryName = (slug) => {
  const cat = categories.value.find((c) => c.slug === slug);
  return cat ? cat.name : slug;
};

const getBrandName = (slug) => {
  const b = brands.value.find((brand) => brand.slug === slug);
  return b ? b.name : slug;
};

let searchTimer = null;
const debounceSearch = () => {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => {
    fetchProducts();
  }, 350);
};

const clearSearch = () => {
  filters.search = '';
  fetchProducts();
};

const setCategory = (slug) => {
  filters.category = slug;
  pagination.current_page = 1;
  fetchProducts();
};

const setBrand = (slug) => {
  filters.brand = slug;
  pagination.current_page = 1;
  fetchProducts();
};

const resetAllFilters = () => {
  filters.search = '';
  filters.category = '';
  filters.brand = '';
  filters.availability = '';
  filters.featured = '';
  filters.sort = 'newest';
  pagination.current_page = 1;
  fetchProducts();
};

const changePage = (p) => {
  if (p < 1 || p > pagination.last_page) return;
  pagination.current_page = p;
  fetchProducts();
  window.scrollTo({ top: 180, behavior: 'smooth' });
};

const openQuickView = (product) => {
  selectedQuickProduct.value = product;
  quickViewOpen.value = true;
};

const fetchProducts = async () => {
  loading.value = true;
  try {
    const params = {
      page: pagination.current_page,
      per_page: pagination.per_page,
      search: filters.search || undefined,
      category: filters.category || undefined,
      brand: filters.brand || undefined,
      availability: filters.availability || undefined,
      featured: filters.featured ? 1 : undefined,
      sort: filters.sort,
    };

    // Update URL query parameters cleanly
    router.replace({ path: '/catalog', query: params });

    const res = await catalogService.getProducts(params);
    products.value = res.data || [];
    if (res.meta?.pagination) {
      totalProducts.value = res.meta.pagination.total;
      pagination.current_page = res.meta.pagination.current_page;
      pagination.last_page = res.meta.pagination.last_page;
    } else {
      totalProducts.value = products.value.length;
    }
  } catch (err) {
    console.error('Failed to load products', err);
  } finally {
    loading.value = false;
  }
};

watch(
  () => route.query,
  (newQuery) => {
    if (newQuery.search !== undefined) filters.search = newQuery.search;
    if (newQuery.category !== undefined) filters.category = newQuery.category;
    if (newQuery.brand !== undefined) filters.brand = newQuery.brand;
    if (newQuery.featured !== undefined) filters.featured = newQuery.featured;
  }
);

onMounted(async () => {
  try {
    const [catsRes, brandsRes] = await Promise.all([
      catalogService.getCategories(),
      catalogService.getBrands(),
    ]);
    categories.value = catsRes.data || [];
    brands.value = brandsRes.data || [];
  } catch (e) {
    console.error(e);
  }
  fetchProducts();
});
</script>
