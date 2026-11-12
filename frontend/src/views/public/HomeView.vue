<template>
  <PublicLayout>
    <!-- 1. Hero Section with Dynamic Enterprise Promotional Banners -->
    <section
      v-if="merchandisingSettings.show_hero_banner !== false"
      class="relative bg-gradient-to-r from-[#042562] via-[#063485] to-[#1865C0] text-white overflow-hidden py-16 lg:py-20 transition-all duration-500"
    >
      <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:20px_20px]"></div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
          <!-- Left Banner Text -->
          <div class="lg:col-span-7 space-y-6">
            <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 text-xs font-bold uppercase tracking-wider text-blue-200">
              <span class="w-2 h-2 rounded-full bg-[#E20913] animate-pulse"></span>
              {{ activeBanner?.badge_text || merchandisingSettings.homepage_promotional_badge || 'Enterprise Datacenter Solutions & Turnkey IT Systems' }}
            </div>

            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight leading-tight">
              {{ activeBanner?.title || merchandisingSettings.homepage_headline || 'Next-Generation Server Racks, Compute Nodes & Optical Networks' }}
            </h1>

            <p class="text-sm sm:text-base text-blue-100 max-w-xl leading-relaxed">
              {{ activeBanner?.subtitle || merchandisingSettings.homepage_subheadline || 'Equip your enterprise with tier-1 certified hardware from Cisco, Dell, HPE, APC, and Ubiquiti. Verified engineering datasheets, multi-store stock visibility, and instant quotation carts.' }}
            </p>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-4 pt-2">
              <router-link
                :to="activeBanner?.cta_primary_link || '/catalog'"
                class="inline-flex items-center gap-2 bg-[#E20913] hover:bg-[#be0710] text-white px-6 py-3 rounded-lg font-bold text-xs uppercase tracking-wider shadow-lg transition-all transform hover:-translate-y-0.5"
              >
                <span>{{ activeBanner?.cta_primary_text || 'Browse IT Showroom' }}</span>
                <ArrowRight class="w-4 h-4" />
              </router-link>

              <router-link
                :to="activeBanner?.cta_secondary_link || '/cart'"
                class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white border border-white/30 px-6 py-3 rounded-lg font-bold text-xs uppercase tracking-wider transition-all"
              >
                <FileSpreadsheet class="w-4 h-4 text-blue-300" />
                <span>{{ activeBanner?.cta_secondary_text || 'Submit Quotation Basket' }}</span>
              </router-link>
            </div>

            <!-- Banner Navigation Dots if multiple banners -->
            <div v-if="banners.length > 1" class="flex items-center gap-2 pt-4">
              <button
                v-for="(banner, idx) in banners"
                :key="banner.id || idx"
                @click="currentBannerIndex = idx"
                :class="[
                  'h-2 rounded-full transition-all duration-300',
                  currentBannerIndex === idx ? 'w-8 bg-[#E20913]' : 'w-2 bg-white/40 hover:bg-white/70'
                ]"
                :title="`Slide ${idx + 1}: ${banner.title}`"
              ></button>
            </div>
          </div>

          <!-- Right Showcase Card or Custom Banner Graphic -->
          <div class="lg:col-span-5">
            <div v-if="activeBanner?.image_url" class="relative rounded-2xl overflow-hidden shadow-2xl border border-white/20 group">
              <img
                :src="activeBanner.image_url"
                :alt="activeBanner.title"
                class="w-full h-80 object-cover transform group-hover:scale-105 transition-transform duration-500"
              />
              <div class="absolute inset-0 bg-gradient-to-t from-[#042562]/80 via-transparent to-transparent flex items-end p-6">
                <span class="text-xs font-bold text-white bg-[#042562]/80 backdrop-blur-sm px-3 py-1 rounded">
                  {{ activeBanner.badge_text || 'Featured Technology' }}
                </span>
              </div>
            </div>

            <div v-else class="bg-white rounded-2xl p-6 text-[#172033] shadow-2xl border border-blue-200/50 space-y-4">
              <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                  <div class="w-2.5 h-2.5 rounded-full bg-[#16A34A] animate-ping"></div>
                  <span class="text-xs font-bold uppercase tracking-wider text-[#042562]">
                    Operational Stock Network
                  </span>
                </div>
                <span class="text-[11px] font-bold text-[#1865C0] bg-blue-50 px-2 py-0.5 rounded">
                  3 Active Hubs
                </span>
              </div>

              <!-- Metrics Grid -->
              <div class="grid grid-cols-2 gap-3">
                <div class="p-3.5 rounded-xl bg-[#F4F6FA] border border-slate-200">
                  <div class="text-2xl font-black text-[#042562]">9</div>
                  <div class="text-xs font-bold text-slate-700">Hardware Categories</div>
                  <div class="text-[10px] text-slate-500">Servers, SAN, Switching, UPS</div>
                </div>
                <div class="p-3.5 rounded-xl bg-[#F4F6FA] border border-slate-200">
                  <div class="text-2xl font-black text-[#1865C0]">100%</div>
                  <div class="text-xs font-bold text-slate-700">Factory Datasheets</div>
                  <div class="text-[10px] text-slate-500">OEM Verified Specifications</div>
                </div>
                <div class="p-3.5 rounded-xl bg-[#F4F6FA] border border-slate-200">
                  <div class="text-2xl font-black text-[#E20913]">0 min</div>
                  <div class="text-xs font-bold text-slate-700">Instant RFQ Cart</div>
                  <div class="text-[10px] text-slate-500">No Credit Card Required</div>
                </div>
                <div class="p-3.5 rounded-xl bg-[#F4F6FA] border border-slate-200">
                  <div class="text-2xl font-black text-[#042562]">3 Hubs</div>
                  <div class="text-xs font-bold text-slate-700">Multi-Store Stock</div>
                  <div class="text-[10px] text-slate-500">Atomic Stock Tracking</div>
                </div>
              </div>

              <div class="pt-2 text-center border-t border-slate-100">
                <router-link
                  to="/catalog?featured=true"
                  class="text-xs font-bold text-[#1865C0] hover:text-[#042562] inline-flex items-center gap-1"
                >
                  <span>Explore Featured Datacenter Models</span>
                  <ArrowRight class="w-3.5 h-3.5" />
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- 2. Quick Search & Filter Strip -->
    <section class="bg-white border-b border-slate-200 py-6 shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <form @submit.prevent="executeSearch" class="flex flex-col md:flex-row items-center gap-3">
          <div class="relative flex-1 w-full">
            <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5" />
            <input
              type="text"
              v-model="heroSearch"
              placeholder="Search by product name, model number, SKU or technical feature..."
              class="w-full bg-[#F4F6FA] text-sm text-[#172033] rounded-lg pl-10 pr-4 py-2.5 border border-slate-300 focus:outline-none focus:border-[#1865C0] focus:bg-white transition-colors"
            />
          </div>

          <div class="flex items-center gap-3 w-full md:w-auto">
            <select
              v-model="selectedCategoryFilter"
              class="bg-[#F4F6FA] text-xs font-medium text-[#172033] rounded-lg px-3 py-2.5 border border-slate-300 focus:outline-none focus:border-[#1865C0] w-full md:w-48"
            >
              <option value="">All Categories</option>
              <option v-for="cat in categories" :key="cat.id" :value="cat.slug">
                {{ cat.name }}
              </option>
            </select>

            <button
              type="submit"
              class="inline-flex items-center justify-center gap-2 bg-[#042562] hover:bg-[#063485] text-white text-xs font-bold uppercase tracking-wider px-6 py-2.5 rounded-lg transition-colors whitespace-nowrap"
            >
              <span>Search Showroom</span>
            </button>
          </div>
        </form>
      </div>
    </section>

    <!-- 3. Featured Categories Grid -->
    <section
      v-if="merchandisingSettings.show_categories_grid !== false"
      class="py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"
    >
      <div class="flex flex-col md:flex-row md:items-end justify-between mb-10">
        <div>
          <span class="text-xs font-bold tracking-widest text-[#1865C0] uppercase">Showroom Classifications</span>
          <h2 class="text-2xl sm:text-3xl font-extrabold text-[#042562] mt-1">
            Engineered Hardware Categories
          </h2>
        </div>
        <router-link
          to="/categories"
          class="text-xs font-bold text-[#1865C0] hover:text-[#042562] flex items-center gap-1 mt-3 md:mt-0"
        >
          <span>View All Categories & Subcategories</span>
          <ArrowRight class="w-3.5 h-3.5" />
        </router-link>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-6">
        <div
          v-for="cat in categories.slice(0, 6)"
          :key="cat.id"
          @click="navigateToCategory(cat.slug)"
          class="corporate-card bg-white rounded-xl border border-slate-200 p-6 hover:shadow-card-hover transition-all duration-200 group cursor-pointer flex flex-col justify-between"
        >
          <div>
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 rounded-xl bg-blue-50 text-[#042562] flex items-center justify-center group-hover:bg-[#042562] group-hover:text-white transition-colors">
                <component :is="getCategoryIcon(cat.slug)" class="w-6 h-6" />
              </div>
              <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 group-hover:bg-blue-100 group-hover:text-[#1865C0] transition-colors">
                {{ cat.products_count || '10+' }} models
              </span>
            </div>

            <h3 class="text-base font-bold text-[#042562] group-hover:text-[#1865C0] transition-colors">
              {{ cat.name }}
            </h3>

            <p class="text-xs text-slate-500 mt-2 line-clamp-2 leading-relaxed">
              {{ cat.description }}
            </p>
          </div>

          <!-- Subcategories Pills -->
          <div v-if="cat.children && cat.children.length" class="mt-4 pt-3 border-t border-slate-100 flex flex-wrap gap-1.5">
            <span
              v-for="sub in cat.children.slice(0, 3)"
              :key="sub.id"
              class="text-[10px] font-semibold bg-[#F4F6FA] text-slate-600 px-2 py-0.5 rounded border border-slate-200"
            >
              {{ sub.name }}
            </span>
          </div>
        </div>
      </div>
    </section>

    <!-- 4. Featured Datacenter Products Showcase -->
    <section
      v-if="merchandisingSettings.show_featured_products !== false"
      class="py-16 bg-white border-y border-slate-200"
    >
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10">
          <div>
            <span class="text-xs font-bold tracking-widest text-[#E20913] uppercase">High-Performance Hardware</span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-[#042562] mt-1">
              Featured Datacenter & Enterprise Picks
            </h2>
          </div>
          <router-link
            to="/catalog?featured=true"
            class="text-xs font-bold text-[#1865C0] hover:text-[#042562] flex items-center gap-1 mt-3 md:mt-0"
          >
            <span>Explore All Featured</span>
            <ArrowRight class="w-3.5 h-3.5" />
          </router-link>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <ProductCard
            v-for="product in featuredProducts"
            :key="product.id"
            :product="product"
            view-mode="grid"
            @quick-view="openQuickView"
          />
        </div>
      </div>
    </section>

    <!-- 5. Recently Added & New Arrivals -->
    <section class="py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex flex-col md:flex-row md:items-end justify-between mb-10">
        <div>
          <span class="text-xs font-bold tracking-widest text-[#1865C0] uppercase">Latest Arrivals</span>
          <h2 class="text-2xl sm:text-3xl font-extrabold text-[#042562] mt-1">
            New Technology In Stock
          </h2>
        </div>
        <router-link
          to="/catalog"
          class="text-xs font-bold text-[#1865C0] hover:text-[#042562] flex items-center gap-1 mt-3 md:mt-0"
        >
          <span>View All In Stock</span>
          <ArrowRight class="w-3.5 h-3.5" />
        </router-link>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <ProductCard
          v-for="product in recentProducts"
          :key="product.id"
          :product="product"
          view-mode="grid"
          @quick-view="openQuickView"
        />
      </div>
    </section>

    <!-- 6. Popular Manufacturer Brands -->
    <section
      v-if="merchandisingSettings.show_brand_carousel !== false"
      class="py-16 bg-[#F8FAFC] border-y border-slate-200"
    >
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-10">
          <span class="text-xs font-bold tracking-widest text-[#1865C0] uppercase">Global Manufacturer Ecosystem</span>
          <h2 class="text-2xl sm:text-3xl font-black text-[#042562] mt-1">
            Authorized Enterprise Hardware Partners
          </h2>
          <p class="text-xs sm:text-sm text-slate-600 mt-2">
            Direct distribution agreements ensuring authentic serial numbers, full manufacturer warranties, and direct RMA support.
          </p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
          <div
            v-for="brand in brands"
            :key="brand.id"
            @click="navigateToBrand(brand.slug)"
            class="bg-white p-4 rounded-xl border border-slate-200 hover:border-[#1865C0] hover:shadow-md transition-all text-center cursor-pointer group flex flex-col items-center justify-center min-h-[105px]"
          >
            <div class="w-12 h-12 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-center mb-2 group-hover:border-blue-200 transition-colors overflow-hidden p-1">
              <img
                v-if="brand.logo_url"
                :src="brand.logo_url"
                :alt="brand.name"
                class="max-h-full max-w-full object-contain group-hover:scale-105 transition-transform"
                @error="$event.target.style.display = 'none'"
              />
              <span v-else class="text-xs font-black text-[#042562]">
                {{ brand.name.substring(0, 2).toUpperCase() }}
              </span>
            </div>
            <span class="text-xs font-bold text-[#172033] group-hover:text-[#1865C0] transition-colors truncate max-w-full px-1">
              {{ brand.name }}
            </span>
            <span class="text-[10px] text-slate-500 mt-0.5">
              {{ brand.products_count ? `${brand.products_count} models` : 'Enterprise Partner' }}
            </span>
          </div>
        </div>
      </div>
    </section>

    <!-- 7. Company Overview & Value Propositions -->
    <section class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
        <div class="space-y-6">
          <span class="text-xs font-bold tracking-widest text-[#E20913] uppercase">About iRACK IT Solution</span>
          <h2 class="text-3xl font-black text-[#042562] leading-tight">
            Complete Enterprise IT Infrastructure Provider & System Integrator
          </h2>
          <p class="text-sm text-slate-600 leading-relaxed">
            iRACK IT Solution specializes in providing turnkey data center solutions, mission-critical server hardware, structured fiber/copper cabling, intelligent power distribution, and multi-cloud networking.
          </p>

          <div class="space-y-4 pt-2">
            <div class="flex items-start gap-3">
              <div class="w-8 h-8 rounded-lg bg-blue-50 text-[#1865C0] flex items-center justify-center flex-shrink-0 mt-0.5">
                <ShieldCheck class="w-4 h-4" />
              </div>
              <div>
                <h4 class="text-xs font-bold text-[#042562]">100% Genuine Certified Hardware</h4>
                <p class="text-xs text-slate-500">Every unit is covered by factory warranty with full traceable documentation and compliance certificates.</p>
              </div>
            </div>

            <div class="flex items-start gap-3">
              <div class="w-8 h-8 rounded-lg bg-blue-50 text-[#1865C0] flex items-center justify-center flex-shrink-0 mt-0.5">
                <Truck class="w-4 h-4" />
              </div>
              <div>
                <h4 class="text-xs font-bold text-[#042562]">3 Regional Distribution Hubs</h4>
                <p class="text-xs text-slate-500">Central HQ, Downtown Commercial Center, and Airport Logistics Bay for same-day dispatch.</p>
              </div>
            </div>

            <div class="flex items-start gap-3">
              <div class="w-8 h-8 rounded-lg bg-blue-50 text-[#1865C0] flex items-center justify-center flex-shrink-0 mt-0.5">
                <Users class="w-4 h-4" />
              </div>
              <div>
                <h4 class="text-xs font-bold text-[#042562]">Certified System Architects</h4>
                <p class="text-xs text-slate-500">Cisco CCIE, Dell Certified Enterprise Engineers, and Schneider Electric Certified Data Center Associates.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Architectural Image / Visual Showcase -->
        <div class="relative bg-gradient-to-br from-[#042562] to-[#1865C0] rounded-2xl p-8 text-white shadow-xl overflow-hidden space-y-6">
          <div class="flex items-center justify-between border-b border-white/20 pb-4">
            <span class="text-xs font-bold uppercase tracking-wider text-blue-200">Solution Scope</span>
            <span class="text-xs font-mono text-emerald-400 font-bold">Turnkey B2B</span>
          </div>

          <div class="space-y-3">
            <div class="p-4 rounded-xl bg-white/10 backdrop-blur border border-white/20">
              <h4 class="text-sm font-bold text-white">Tier III/IV Datacenter Deployment</h4>
              <p class="text-xs text-blue-100 mt-1">High-density server racks, hot/cold aisle containment, and busway electrical distribution.</p>
            </div>
            <div class="p-4 rounded-xl bg-white/10 backdrop-blur border border-white/20">
              <h4 class="text-sm font-bold text-white">Core & Spine-Leaf Network Architecture</h4>
              <p class="text-xs text-blue-100 mt-1">100G/400G switching fabrics with redundant failover and zero-packet loss SLAs.</p>
            </div>
            <div class="p-4 rounded-xl bg-white/10 backdrop-blur border border-white/20">
              <h4 class="text-sm font-bold text-white">SAN/NAS Storage Virtualization</h4>
              <p class="text-xs text-blue-100 mt-1">Multi-petabyte all-flash arrays configured for VMware vSphere and Proxmox clusters.</p>
            </div>
          </div>

          <router-link
            to="/services"
            class="block text-center bg-white text-[#042562] hover:bg-blue-50 font-bold text-xs uppercase tracking-wider py-3 rounded-lg transition-colors shadow"
          >
            Explore All Engineering Services
          </router-link>
        </div>
      </div>
    </section>

    <!-- 8. Contact Consultation CTA Banner -->
    <section class="bg-[#042562] text-white py-16 border-t border-navy-800">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6">
        <h2 class="text-2xl sm:text-3xl font-black">
          Need a Custom Datacenter Bill of Materials (BOM)?
        </h2>
        <p class="text-sm text-blue-200 max-w-2xl mx-auto leading-relaxed">
          Our senior enterprise architects prepare formal quotation documents with guaranteed price protection, quantity tier discounts, and delivery timelines.
        </p>
        <div class="flex flex-wrap items-center justify-center gap-4 pt-2">
          <router-link
            to="/contact"
            class="inline-flex items-center gap-2 bg-[#E20913] hover:bg-[#be0710] text-white px-7 py-3 rounded-lg text-xs font-bold uppercase tracking-wider shadow-lg transition-all"
          >
            <Phone class="w-4 h-4" />
            <span>Speak with a Solutions Architect</span>
          </router-link>
          <router-link
            to="/cart"
            class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white border border-white/30 px-7 py-3 rounded-lg text-xs font-bold uppercase tracking-wider transition-all"
          >
            <FileSpreadsheet class="w-4 h-4 text-blue-300" />
            <span>Open Quotation Basket</span>
          </router-link>
        </div>
      </div>
    </section>

    <!-- Quick View Modal -->
    <QuickViewModal
      :is-open="quickViewOpen"
      :product="selectedQuickProduct"
      @close="quickViewOpen = false"
    />
  </PublicLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import PublicLayout from '@/layouts/PublicLayout.vue';
import ProductCard from '@/components/catalog/ProductCard.vue';
import QuickViewModal from '@/components/common/QuickViewModal.vue';
import catalogService from '@/services/catalogService';
import { configService } from '@/services/configService';
import {
  ArrowRight,
  FileSpreadsheet,
  Search,
  Server,
  Network,
  HardDrive,
  Zap,
  Shield,
  Monitor,
  Cpu,
  Layers,
  Cable,
  Award,
  ShieldCheck,
  Truck,
  Users,
  Phone,
} from 'lucide-vue-next';

const router = useRouter();

const categories = ref([]);
const brands = ref([]);
const featuredProducts = ref([]);
const recentProducts = ref([]);

// Dynamic CMS & Merchandising State
const banners = ref([]);
const currentBannerIndex = ref(0);
const merchandisingSettings = ref({});
let bannerTimer = null;

const activeBanner = computed(() => {
  if (banners.value.length === 0) return null;
  return banners.value[currentBannerIndex.value] || banners.value[0];
});

const heroSearch = ref('');
const selectedCategoryFilter = ref('');

const quickViewOpen = ref(false);
const selectedQuickProduct = ref(null);

const getCategoryIcon = (slug) => {
  switch (slug) {
    case 'servers-and-workstations': return Server;
    case 'networking-equipment': return Network;
    case 'storage-solutions': return HardDrive;
    case 'power-solutions': return Zap;
    case 'security-solutions': return Shield;
    case 'audio-visual-solutions': return Monitor;
    case 'computers-and-accessories': return Cpu;
    case 'data-center-infrastructure': return Layers;
    case 'it-accessories': return Cable;
    default: return Server;
  }
};

const executeSearch = () => {
  const query = {};
  if (heroSearch.value.trim()) query.search = heroSearch.value.trim();
  if (selectedCategoryFilter.value) query.category = selectedCategoryFilter.value;
  router.push({ path: '/catalog', query });
};

const navigateToCategory = (slug) => {
  router.push({ path: '/catalog', query: { category: slug } });
};

const navigateToBrand = (slug) => {
  router.push({ path: '/catalog', query: { brand: slug } });
};

const openQuickView = (product) => {
  selectedQuickProduct.value = product;
  quickViewOpen.value = true;
};

const startBannerRotation = () => {
  if (bannerTimer) clearInterval(bannerTimer);
  if (banners.value.length > 1) {
    bannerTimer = setInterval(() => {
      currentBannerIndex.value = (currentBannerIndex.value + 1) % banners.value.length;
    }, 7000);
  }
};

onMounted(async () => {
  try {
    const [catsRes, brandsRes, productsRes, bannersRes, settingsRes] = await Promise.all([
      catalogService.getCategories(),
      catalogService.getBrands(),
      catalogService.getProducts({ per_page: 12 }),
      configService.getPublicBanners().catch(() => ({ data: [] })),
      configService.getPublicSettings().catch(() => ({ data: {} })),
    ]);

    categories.value = catsRes.data || [];
    brands.value = brandsRes.data || [];

    const all = productsRes.data || [];
    featuredProducts.value = all.filter(p => p.is_featured).slice(0, 4);
    if (featuredProducts.value.length === 0) {
      featuredProducts.value = all.slice(0, 4);
    }
    recentProducts.value = all.slice(0, 4);

    // Apply CMS Banners & Merchandising
    if (bannersRes && bannersRes.data && bannersRes.data.length > 0) {
      banners.value = bannersRes.data;
      startBannerRotation();
    }
    if (settingsRes && settingsRes.data && settingsRes.data.merchandising) {
      merchandisingSettings.value = settingsRes.data.merchandising;
    }
  } catch (err) {
    console.error('Failed to load showroom data', err);
  }
});

onUnmounted(() => {
  if (bannerTimer) clearInterval(bannerTimer);
});
</script>
