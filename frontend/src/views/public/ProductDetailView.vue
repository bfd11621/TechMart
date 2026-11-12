<template>
  <PublicLayout>
    <!-- Loading State -->
    <div v-if="loading" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
      <div class="h-6 w-64 bg-slate-200 rounded animate-pulse mb-8"></div>
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        <div class="lg:col-span-6 h-96 bg-slate-200 rounded-2xl animate-pulse"></div>
        <div class="lg:col-span-6 space-y-4">
          <div class="h-8 w-3/4 bg-slate-200 rounded animate-pulse"></div>
          <div class="h-4 w-1/2 bg-slate-200 rounded animate-pulse"></div>
          <div class="h-32 bg-slate-200 rounded animate-pulse"></div>
        </div>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="!product" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center space-y-4">
      <AlertTriangle class="w-16 h-16 text-[#DC2626] mx-auto" />
      <h2 class="text-2xl font-black text-[#042562]">Product Not Found</h2>
      <p class="text-xs text-slate-500">The requested IT hardware model does not exist or has been unpublished.</p>
      <router-link
        to="/catalog"
        class="inline-flex items-center gap-2 bg-[#042562] text-white text-xs font-bold uppercase tracking-wider px-6 py-2.5 rounded-lg"
      >
        <span>Return to Catalog</span>
      </router-link>
    </div>

    <!-- Main Product Detail View -->
    <div v-else class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
      <!-- Breadcrumb Bar -->
      <nav class="flex items-center gap-2 text-xs font-medium text-slate-500 mb-8 overflow-x-auto whitespace-nowrap pb-2">
        <router-link to="/" class="hover:text-[#1865C0] flex items-center gap-1">
          <Home class="w-3.5 h-3.5" />
          <span>Home</span>
        </router-link>
        <ChevronRight class="w-3.5 h-3.5 text-slate-400" />
        <router-link to="/catalog" class="hover:text-[#1865C0]">Catalog</router-link>
        <ChevronRight class="w-3.5 h-3.5 text-slate-400" />
        <router-link
          v-if="product.category"
          :to="{ path: '/catalog', query: { category: product.category.slug } }"
          class="hover:text-[#1865C0]"
        >
          {{ product.category.name }}
        </router-link>
        <ChevronRight v-if="product.category" class="w-3.5 h-3.5 text-slate-400" />
        <span class="text-[#042562] font-bold truncate max-w-xs">{{ product.name }}</span>
      </nav>

      <!-- Top Showcase: Gallery & Purchasing Console -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
        <!-- Left: Image Gallery (6 cols) -->
        <div class="lg:col-span-6 space-y-4">
          <!-- Main Selected Image Box with Prev/Next Navigation -->
          <div class="relative bg-white rounded-2xl border border-slate-200 p-8 flex items-center justify-center min-h-[380px] shadow-sm overflow-hidden group">
            <span
              v-if="product.is_featured"
              class="absolute top-4 left-4 bg-[#E20913] text-white text-[11px] font-black uppercase px-3 py-1 rounded shadow tracking-wider z-10"
            >
              Enterprise Recommendation
            </span>

            <!-- Carousel Prev Button -->
            <button
              v-if="allImages.length > 1"
              type="button"
              @click="prevImage"
              class="absolute left-3 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-white/90 shadow border border-slate-200 text-slate-700 hover:text-[#042562] hover:bg-white flex items-center justify-center transition-all z-10 opacity-75 group-hover:opacity-100"
              title="Previous image"
            >
              <ChevronLeft class="w-5 h-5" />
            </button>

            <img
              :src="activeImage || allImages[0]?.image_url || product.primary_image || '/images/products/placeholder.png'"
              :alt="product.name"
              class="max-h-[320px] max-w-full object-contain drop-shadow transition-transform duration-300 hover:scale-105"
            />

            <!-- Carousel Next Button -->
            <button
              v-if="allImages.length > 1"
              type="button"
              @click="nextImage"
              class="absolute right-3 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-white/90 shadow border border-slate-200 text-slate-700 hover:text-[#042562] hover:bg-white flex items-center justify-center transition-all z-10 opacity-75 group-hover:opacity-100"
              title="Next image"
            >
              <ChevronRight class="w-5 h-5" />
            </button>
          </div>

          <!-- Thumbnails Row -->
          <div v-if="allImages.length > 1" class="flex items-center gap-3 overflow-x-auto pb-2">
            <button
              v-for="(img, idx) in allImages"
              :key="idx"
              @click="activeImage = img.image_url"
              :class="[
                'w-16 h-16 rounded-xl border-2 p-1 bg-white flex-shrink-0 flex items-center justify-center transition-all relative overflow-hidden',
                activeImage === img.image_url ? 'border-[#1865C0] shadow-md ring-2 ring-blue-100' : 'border-slate-200 hover:border-slate-400 opacity-80',
              ]"
            >
              <img :src="img.image_url" :alt="product.name" class="max-h-full max-w-full object-contain" />
              <span v-if="img.is_primary" class="absolute bottom-0 inset-x-0 bg-[#042562] text-[8px] text-white font-black text-center uppercase tracking-tighter py-0.2">
                Primary
              </span>
            </button>
          </div>
        </div>

        <!-- Right: Commercial Specifications & Actions (6 cols) -->
        <div class="lg:col-span-6 space-y-6">
          <!-- Brand & Model Tag Bar -->
          <div class="flex flex-wrap items-center justify-between gap-2 pb-2 border-b border-slate-200">
            <div class="flex items-center gap-2">
              <span class="text-xs font-black uppercase text-[#1865C0] tracking-widest bg-blue-50 px-2.5 py-1 rounded">
                {{ product.brand?.name || 'Authorized OEM' }}
              </span>
              <span class="text-xs font-mono text-slate-500">
                Model: <strong class="text-slate-800">{{ product.model_number || 'N/A' }}</strong>
              </span>
            </div>
            <span class="text-xs font-mono text-slate-500">
              SKU: <strong class="text-slate-800">{{ product.sku }}</strong>
            </span>
          </div>

          <!-- Product Title -->
          <h1 class="text-2xl sm:text-3xl font-black text-[#042562] leading-tight">
            {{ product.name }}
          </h1>

          <!-- Short Description -->
          <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
            {{ product.short_description }}
          </p>

          <!-- Stock Availability & Branch Breakdown Card -->
          <div class="bg-[#F8FAFC] rounded-xl border border-slate-200 p-4 space-y-3">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <span
                  class="w-2.5 h-2.5 rounded-full"
                  :class="product.availability_status === 'in_stock' ? 'bg-[#16A34A]' : 'bg-[#F59E0B]'"
                ></span>
                <span class="text-xs font-bold uppercase tracking-wider text-[#042562]">
                  Multi-Store Inventory Status
                </span>
              </div>
              <span
                :class="[
                  'text-xs font-black px-2.5 py-0.5 rounded-full',
                  product.availability_status === 'in_stock'
                    ? 'bg-emerald-100 text-emerald-800'
                    : 'bg-amber-100 text-amber-800',
                ]"
              >
                {{ product.availability_label || (product.availability_status === 'in_stock' ? 'In Stock' : 'Pre-Order') }}
              </span>
            </div>

            <!-- Multi-Store Branch Breakdown -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 pt-2 border-t border-slate-200 text-xs">
              <div
                v-for="inv in branchInventory"
                :key="inv.storeName"
                class="bg-white p-2.5 rounded-lg border border-slate-200"
              >
                <div class="font-bold text-[#042562] text-[11px] truncate">{{ inv.storeName }}</div>
                <div class="flex items-center justify-between mt-1">
                  <span class="text-[10px] text-slate-500">Available:</span>
                  <span
                    class="font-black text-[11px]"
                    :class="inv.quantity > 0 ? 'text-[#16A34A]' : 'text-slate-400'"
                  >
                    {{ inv.quantity > 0 ? `${inv.quantity} units` : 'Direct Dispatch' }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Manufacturer OEM Datasheet Download Banner -->
          <div class="bg-blue-50/70 rounded-xl border border-blue-200 p-4 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-lg bg-[#E20913] text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                <FileText class="w-5 h-5" />
              </div>
              <div>
                <h4 class="text-xs font-bold text-[#042562]">Official Technical Datasheet</h4>
                <p class="text-[11px] text-slate-500">
                  {{ product.datasheet_filename || `${product.sku}_technical_specifications.pdf` }}
                </p>
              </div>
            </div>

            <a
              :href="datasheetUrl"
              target="_blank"
              rel="noopener noreferrer"
              :download="product.datasheet_filename || `${product.sku}_Datasheet.pdf`"
              class="inline-flex items-center gap-1.5 bg-[#042562] hover:bg-[#063485] text-white text-xs font-bold px-4 py-2 rounded-lg transition-colors shadow-sm whitespace-nowrap"
            >
              <Download class="w-3.5 h-3.5 text-blue-200" />
              <span>Download PDF</span>
            </a>
          </div>

          <!-- Procurement Console (Qty Stepper & Add to RFQ) -->
          <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm space-y-4">
            <div class="flex flex-col sm:flex-row items-center gap-4">
              <!-- Quantity Stepper -->
              <div class="flex items-center border border-slate-300 rounded-lg overflow-hidden bg-white w-full sm:w-auto">
                <button
                  type="button"
                  @click="quantity = Math.max(1, quantity - 1)"
                  class="px-4 py-2.5 text-slate-600 hover:bg-slate-100 transition-colors font-bold"
                >
                  -
                </button>
                <input
                  type="number"
                  v-model.number="quantity"
                  min="1"
                  class="w-16 text-center font-bold text-sm text-[#172033] focus:outline-none py-2.5"
                />
                <button
                  type="button"
                  @click="quantity++"
                  class="px-4 py-2.5 text-slate-600 hover:bg-slate-100 transition-colors font-bold"
                >
                  +
                </button>
              </div>

              <!-- Primary Add to RFQ Button -->
              <button
                @click="addToCart"
                class="flex-1 w-full inline-flex items-center justify-center gap-2 bg-[#E20913] hover:bg-[#be0710] text-white py-3 px-6 rounded-lg font-bold text-xs uppercase tracking-wider shadow-md transition-all transform hover:-translate-y-0.5"
              >
                <FileSpreadsheet class="w-4 h-4" />
                <span>Add {{ quantity }}x to Quotation Cart</span>
              </button>

              <!-- Compare Toggle -->
              <button
                @click="compare.toggleCompare(product)"
                :class="[
                  'p-3 rounded-lg border transition-colors flex-shrink-0',
                  compare.isInCompare(product.id)
                    ? 'bg-[#042562] text-white border-[#042562]'
                    : 'bg-white text-slate-600 border-slate-300 hover:text-[#042562] hover:border-[#042562]',
                ]"
                :title="compare.isInCompare(product.id) ? 'Remove Compare' : 'Add to Compare'"
              >
                <Scale class="w-4 h-4" />
              </button>
            </div>

            <!-- Optional Custom Engineering Note -->
            <div>
              <label class="block text-[11px] font-semibold text-slate-500 mb-1">
                Optional configuration note for this model (e.g. transceiver specs, redundant PSU preference):
              </label>
              <input
                type="text"
                v-model="customNote"
                placeholder="Add special technical notes..."
                class="w-full bg-[#F4F6FA] text-xs text-[#172033] rounded-lg px-3 py-2 border border-slate-300 focus:outline-none focus:border-[#1865C0]"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- Tabbed Technical Specification Section -->
      <div class="mt-16 bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <!-- Tab Navigation -->
        <div class="flex items-center border-b border-slate-200 bg-[#F8FAFC] overflow-x-auto">
          <button
            @click="activeTab = 'specs'"
            :class="[
              'px-6 py-4 text-xs font-bold uppercase tracking-wider border-b-2 transition-colors whitespace-nowrap',
              activeTab === 'specs'
                ? 'border-[#042562] text-[#042562] bg-white font-black'
                : 'border-transparent text-slate-500 hover:text-slate-800',
            ]"
          >
            Technical Specifications Matrix
          </button>
          <button
            @click="activeTab = 'features'"
            :class="[
              'px-6 py-4 text-xs font-bold uppercase tracking-wider border-b-2 transition-colors whitespace-nowrap',
              activeTab === 'features'
                ? 'border-[#042562] text-[#042562] bg-white font-black'
                : 'border-transparent text-slate-500 hover:text-slate-800',
            ]"
          >
            Architecture & Key Features
          </button>
          <button
            @click="activeTab = 'description'"
            :class="[
              'px-6 py-4 text-xs font-bold uppercase tracking-wider border-b-2 transition-colors whitespace-nowrap',
              activeTab === 'description'
                ? 'border-[#042562] text-[#042562] bg-white font-black'
                : 'border-transparent text-slate-500 hover:text-slate-800',
            ]"
          >
            Full Product Overview
          </button>
          <button
            @click="activeTab = 'warranty'"
            :class="[
              'px-6 py-4 text-xs font-bold uppercase tracking-wider border-b-2 transition-colors whitespace-nowrap',
              activeTab === 'warranty'
                ? 'border-[#042562] text-[#042562] bg-white font-black'
                : 'border-transparent text-slate-500 hover:text-slate-800',
            ]"
          >
            Warranty & SLAs
          </button>
        </div>

        <!-- Tab Contents -->
        <div class="p-8">
          <!-- Tab 1: Detailed Grouped Technical Specifications -->
          <div v-show="activeTab === 'specs'" class="space-y-8">
            <div v-if="groupedSpecs && Object.keys(groupedSpecs).length" class="space-y-8">
              <div
                v-for="(specsList, groupName) in groupedSpecs"
                :key="groupName"
                class="border border-slate-200 rounded-xl overflow-hidden"
              >
                <div class="bg-[#042562] text-white px-5 py-3 text-xs font-bold uppercase tracking-wider flex items-center gap-2">
                  <CheckCircle2 class="w-4 h-4 text-[#1865C0]" />
                  <span>{{ groupName }}</span>
                </div>
                <div class="divide-y divide-slate-100">
                  <div
                    v-for="(spec, sIdx) in specsList"
                    :key="sIdx"
                    class="grid grid-cols-1 sm:grid-cols-3 px-5 py-3 text-xs hover:bg-[#F8FAFC] transition-colors"
                  >
                    <div class="font-bold text-slate-700 sm:col-span-1">{{ spec.name }}</div>
                    <div class="text-slate-900 sm:col-span-2 font-medium">
                      {{ spec.value }} <span v-if="spec.unit" class="text-slate-500 font-mono">({{ spec.unit }})</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Fallback if specs not grouped -->
            <div v-else-if="product.specifications && product.specifications.length" class="border border-slate-200 rounded-xl overflow-hidden">
              <div class="divide-y divide-slate-100">
                <div
                  v-for="(spec, sIdx) in product.specifications"
                  :key="sIdx"
                  class="grid grid-cols-1 sm:grid-cols-3 px-5 py-3 text-xs hover:bg-[#F8FAFC]"
                >
                  <div class="font-bold text-slate-700 sm:col-span-1">{{ spec.name }}</div>
                  <div class="text-slate-900 sm:col-span-2 font-medium">{{ spec.value }} {{ spec.unit }}</div>
                </div>
              </div>
            </div>

            <p v-else class="text-xs text-slate-500 italic">
              Standard OEM specifications apply. Download the official manufacturer datasheet above for complete architectural schematics.
            </p>
          </div>

          <!-- Tab 2: Key Architecture Features -->
          <div v-show="activeTab === 'features'" class="space-y-4">
            <h3 class="text-sm font-black uppercase tracking-wider text-[#042562] mb-3">
              Core Enterprise Capabilities
            </h3>
            <ul class="space-y-3">
              <li
                v-for="(feat, fIdx) in product.key_features"
                :key="fIdx"
                class="flex items-start gap-3 p-3 rounded-lg bg-[#F4F6FA] border border-slate-200"
              >
                <div class="w-6 h-6 rounded-full bg-emerald-100 text-[#16A34A] flex items-center justify-center flex-shrink-0 mt-0.5">
                  <Check class="w-3.5 h-3.5" />
                </div>
                <span class="text-xs font-semibold text-slate-800 leading-relaxed">{{ feat }}</span>
              </li>
            </ul>
          </div>

          <!-- Tab 3: Full Overview Description -->
          <div v-show="activeTab === 'description'" class="space-y-4">
            <h3 class="text-sm font-black uppercase tracking-wider text-[#042562] mb-3">
              Engineering Overview
            </h3>
            <p class="text-xs sm:text-sm text-slate-700 leading-relaxed max-w-4xl">
              {{ product.full_description || product.short_description }}
            </p>
          </div>

          <!-- Tab 4: Warranty & SLAs -->
          <div v-show="activeTab === 'warranty'" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <div class="p-4 rounded-xl border border-slate-200 bg-[#F4F6FA] space-y-2">
                <ShieldCheck class="w-6 h-6 text-[#1865C0]" />
                <h4 class="text-xs font-bold text-[#042562]">Official Manufacturer Warranty</h4>
                <p class="text-xs text-slate-600">Covered by official Tier-1 OEM factory warranties with direct serial registration.</p>
              </div>
              <div class="p-4 rounded-xl border border-slate-200 bg-[#F4F6FA] space-y-2">
                <Truck class="w-6 h-6 text-[#1865C0]" />
                <h4 class="text-xs font-bold text-[#042562]">Same-Day Dispatch</h4>
                <p class="text-xs text-slate-600">Stock orders from Central Distribution Hub or Downtown Branch dispatched on the same business day.</p>
              </div>
              <div class="p-4 rounded-xl border border-slate-200 bg-[#F4F6FA] space-y-2">
                <HelpCircle class="w-6 h-6 text-[#1865C0]" />
                <h4 class="text-xs font-bold text-[#042562]">On-Site Engineering Deployment</h4>
                <p class="text-xs text-slate-600">Optional certified field engineers available for turnkey rack installation, cabling, and turn-up.</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Related Hardware Models Carousel / Grid -->
      <div v-if="relatedProducts.length" class="mt-20">
        <div class="flex items-center justify-between mb-8">
          <div>
            <span class="text-xs font-bold tracking-widest text-[#1865C0] uppercase">Complementary Solutions</span>
            <h2 class="text-2xl font-black text-[#042562] mt-1">
              Related Datacenter Hardware
            </h2>
          </div>
          <router-link
            :to="{ path: '/catalog', query: { category: product.category?.slug } }"
            class="text-xs font-bold text-[#1865C0] hover:text-[#042562] flex items-center gap-1"
          >
            <span>View More {{ product.category?.name }}</span>
            <ArrowRight class="w-3.5 h-3.5" />
          </router-link>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <ProductCard
            v-for="rel in relatedProducts"
            :key="rel.id"
            :product="rel"
            view-mode="grid"
            @quick-view="openQuickView"
          />
        </div>
      </div>
    </div>

    <!-- Quick View Modal for Related Products -->
    <QuickViewModal
      :is-open="quickViewOpen"
      :product="selectedQuickProduct"
      @close="quickViewOpen = false"
    />
  </PublicLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import PublicLayout from '@/layouts/PublicLayout.vue';
import ProductCard from '@/components/catalog/ProductCard.vue';
import QuickViewModal from '@/components/common/QuickViewModal.vue';
import catalogService from '@/services/catalogService';
import { useCartStore } from '@/stores/cartStore';
import { useCompareStore } from '@/stores/compareStore';
import { useNotificationStore } from '@/stores/notificationStore';
import {
  Home,
  ChevronRight,
  ChevronLeft,
  AlertTriangle,
  FileText,
  Download,
  FileSpreadsheet,
  Scale,
  CheckCircle2,
  Check,
  ShieldCheck,
  Truck,
  HelpCircle,
  ArrowRight,
} from 'lucide-vue-next';

const route = useRoute();
const cart = useCartStore();
const compare = useCompareStore();
const notify = useNotificationStore();

const loading = ref(true);
const product = ref(null);
const relatedProducts = ref([]);
const activeImage = ref('');
const quantity = ref(1);
const customNote = ref('');
const activeTab = ref('specs');

const quickViewOpen = ref(false);
const selectedQuickProduct = ref(null);

const allImages = computed(() => {
  if (!product.value) return [];
  let list = [];
  if (product.value.images && product.value.images.length) {
    list = product.value.images.map(img => ({
      image_url: img.url || img.image_url,
      is_primary: !!img.is_primary,
    }));
  } else if (product.value.primary_image || product.value.primary_image_url) {
    const url = product.value.primary_image || product.value.primary_image_url;
    list = [{ image_url: url, is_primary: true }];
  }

  // Place primary image first
  return list.sort((a, b) => (b.is_primary ? 1 : 0) - (a.is_primary ? 1 : 0));
});

const prevImage = () => {
  if (!allImages.value.length) return;
  const currentIdx = allImages.value.findIndex(i => i.image_url === activeImage.value);
  const nextIdx = currentIdx <= 0 ? allImages.value.length - 1 : currentIdx - 1;
  activeImage.value = allImages.value[nextIdx].image_url;
};

const nextImage = () => {
  if (!allImages.value.length) return;
  const currentIdx = allImages.value.findIndex(i => i.image_url === activeImage.value);
  const nextIdx = (currentIdx + 1) % allImages.value.length;
  activeImage.value = allImages.value[nextIdx].image_url;
};

const groupedSpecs = computed(() => {
  if (!product.value || !product.value.specifications) return {};
  const groups = {};
  for (const spec of product.value.specifications) {
    const g = spec.group || 'General Specifications';
    if (!groups[g]) groups[g] = [];
    groups[g].push(spec);
  }
  return groups;
});

const branchInventory = computed(() => {
  if (product.value?.inventories && product.value.inventories.length) {
    return product.value.inventories.map((inv) => ({
      storeName: inv.store?.name || 'Distribution Hub',
      quantity: inv.quantity,
    }));
  }
  return [
    { storeName: 'Central Distribution Hub (HQ)', quantity: 15 },
    { storeName: 'Downtown Commercial Branch', quantity: 5 },
    { storeName: 'Airport Logistics Center', quantity: 8 },
  ];
});

const datasheetUrl = computed(() => {
  if (!product.value) return '#';
  if (product.value.datasheet_url) {
    if (product.value.datasheet_url.startsWith('http://') || product.value.datasheet_url.startsWith('https://')) {
      return product.value.datasheet_url;
    }
    const baseUrl = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api/v1';
    const host = baseUrl.replace(/\/api\/v1\/?$/, '');
    return `${host}${product.value.datasheet_url.startsWith('/') ? '' : '/'}${product.value.datasheet_url}`;
  }
  return catalogService.getDatasheetDownloadUrl(product.value.id);
});

const addToCart = () => {
  if (!product.value) return;
  cart.addItem(product.value, quantity.value);
  notify.success(`${quantity.value}x "${product.value.name}" added to quotation cart.`);
};

const openQuickView = (p) => {
  selectedQuickProduct.value = p;
  quickViewOpen.value = true;
};

const loadProductData = async () => {
  loading.value = true;
  try {
    const slug = route.params.slug;
    const res = await catalogService.getProduct(slug);
    product.value = res.data;
    activeImage.value = allImages.value[0]?.image_url || product.value.primary_image || product.value.primary_image_url || '';

    // Load related products
    if (product.value.category?.id) {
      relatedProducts.value = await catalogService.getRelatedProducts(
        product.value.id,
        product.value.category.id,
        4
      );
    }
  } catch (err) {
    console.error('Failed to load product detail', err);
    product.value = null;
  } finally {
    loading.value = false;
  }
};

watch(() => route.params.slug, () => {
  quantity.value = 1;
  loadProductData();
  window.scrollTo({ top: 0, behavior: 'smooth' });
});

onMounted(() => {
  loadProductData();
});
</script>
