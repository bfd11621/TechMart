<template>
  <div class="min-h-screen flex flex-col bg-[#F4F6FA] text-[#172033] font-sans">
    <!-- Top Announcement & Quick Contact Bar -->
    <header class="bg-[#021840] text-slate-300 text-xs py-2 px-4 border-b border-slate-800">
      <div class="max-w-7xl mx-auto flex items-center justify-between">
        <div class="flex items-center space-x-6">
          <span class="flex items-center gap-1.5 text-slate-300">
            <ShieldCheck class="w-3.5 h-3.5 text-[#1865C0]" />
            <span class="font-medium">Official Enterprise IT Hardware & Datacenter Solutions</span>
          </span>
          <span class="hidden md:inline text-slate-600">|</span>
          <span class="hidden md:flex items-center gap-1.5 text-slate-300">
            <Building2 class="w-3.5 h-3.5 text-slate-400" />
            <span>3 Regional Fulfillment Hubs (HQ, Downtown, Airport)</span>
          </span>
        </div>
        <div class="flex items-center space-x-4">
          <a :href="`tel:${companyInfo.phone || '+15550192831'}`" class="hover:text-white flex items-center gap-1.5 transition-colors">
            <Phone class="w-3 h-3 text-[#E20913]" />
            <span class="font-semibold">{{ companyInfo.phone || '+1 (555) 019-2831' }}</span>
          </a>
          <span class="text-slate-600">•</span>
          <router-link to="/admin/login" class="text-slate-300 hover:text-white flex items-center gap-1 transition-colors">
            <Lock class="w-3 h-3 text-slate-400" />
            <span class="font-medium">Staff Portal</span>
          </router-link>
        </div>
      </div>
    </header>

    <!-- Main Navigation Bar -->
    <nav class="bg-white sticky top-0 z-40 border-b border-slate-200 shadow-xs">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20 gap-4">
          <!-- Brand Logo with official iRACK TECH-MART logo -->
          <router-link to="/" class="flex items-center group flex-shrink-0">
            <AppLogo variant="dark" size="md" layout="horizontal" />
          </router-link>

          <!-- Search Bar (Desktop) -->
          <div class="hidden md:flex flex-1 max-w-md mx-4">
            <form @submit.prevent="handleHeaderSearch" class="relative w-full">
              <input
                type="text"
                v-model="searchQuery"
                placeholder="Search server, switch, rack SKU or model..."
                class="w-full bg-[#F4F6FA] text-slate-800 placeholder-slate-400 text-xs rounded-lg pl-9 pr-12 py-2.5 border border-slate-300 focus:outline-none focus:border-[#1865C0] focus:bg-white focus:ring-1 focus:ring-[#1865C0] transition-colors"
              />
              <Search class="w-4 h-4 text-slate-400 absolute left-3 top-3" />
              <button
                type="submit"
                class="absolute right-1.5 top-1.5 bg-[#042562] hover:bg-[#1865C0] text-white px-2.5 py-1 rounded text-[11px] font-bold uppercase transition-colors"
              >
                Go
              </button>
            </form>
          </div>

          <!-- Navigation Links (Desktop) -->
          <div class="hidden xl:flex items-center space-x-6 text-xs font-bold uppercase tracking-wider">
            <router-link
              to="/"
              class="text-slate-700 hover:text-[#1865C0] transition-colors py-2 border-b-2 border-transparent"
              active-class="!text-[#042562] !border-[#E20913]"
            >
              Home
            </router-link>
            <router-link
              to="/catalog"
              class="text-slate-700 hover:text-[#1865C0] transition-colors py-2 border-b-2 border-transparent"
              active-class="!text-[#042562] !border-[#E20913]"
            >
              Shop Catalog
            </router-link>
            <router-link
              to="/categories"
              class="text-slate-700 hover:text-[#1865C0] transition-colors py-2 border-b-2 border-transparent"
              active-class="!text-[#042562] !border-[#E20913]"
            >
              Categories
            </router-link>
            <router-link
              to="/brands"
              class="text-slate-700 hover:text-[#1865C0] transition-colors py-2 border-b-2 border-transparent"
              active-class="!text-[#042562] !border-[#E20913]"
            >
              Brands
            </router-link>
            <router-link
              to="/services"
              class="text-slate-700 hover:text-[#1865C0] transition-colors py-2 border-b-2 border-transparent"
              active-class="!text-[#042562] !border-[#E20913]"
            >
              Solutions
            </router-link>
            <router-link
              to="/about"
              class="text-slate-700 hover:text-[#1865C0] transition-colors py-2 border-b-2 border-transparent"
              active-class="!text-[#042562] !border-[#E20913]"
            >
              About
            </router-link>
            <router-link
              to="/contact"
              class="text-slate-700 hover:text-[#1865C0] transition-colors py-2 border-b-2 border-transparent"
              active-class="!text-[#042562] !border-[#E20913]"
            >
              Contact
            </router-link>
          </div>

          <!-- Actions: Compare & Quotation Cart -->
          <div class="flex items-center space-x-3">
            <!-- Compare Trigger -->
            <button
              v-if="compare.count > 0"
              @click="compare.isDrawerOpen = !compare.isDrawerOpen"
              class="relative bg-[#F4F6FA] hover:bg-slate-200 text-[#042562] p-2.5 rounded-lg border border-slate-300 transition-colors shadow-xs"
              title="Compare Models"
            >
              <Scale class="w-4 h-4 text-[#1865C0]" />
              <span class="absolute -top-1.5 -right-1.5 bg-[#1865C0] text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center">
                {{ compare.count }}
              </span>
            </button>

            <!-- RFQ Cart Button -->
            <router-link
              to="/cart"
              class="relative bg-[#042562] hover:bg-[#063485] text-white px-3.5 py-2.5 rounded-lg border border-[#042562] flex items-center gap-2.5 transition-colors shadow-xs group"
            >
              <FileSpreadsheet class="w-4 h-4 text-blue-200 group-hover:scale-110 transition-transform" />
              <span class="text-xs font-bold uppercase tracking-wider hidden sm:inline">Quotation Cart</span>
              <span
                v-if="cart.uniqueItemCount > 0"
                class="bg-[#E20913] text-white text-[10px] font-black px-1.5 py-0.5 rounded-full"
              >
                {{ cart.uniqueItemCount }}
              </span>
            </router-link>

            <!-- Mobile Hamburger Toggle -->
            <button
              @click="mobileMenuOpen = !mobileMenuOpen"
              class="xl:hidden text-slate-700 hover:text-[#042562] p-2 rounded-lg bg-[#F4F6FA] border border-slate-300 hover:bg-slate-200 transition-colors"
              aria-label="Toggle navigation drawer"
            >
              <Menu class="w-5 h-5" />
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Mobile Navigation Drawer Backdrop Overlay -->
    <transition
      enter-active-class="transition-opacity duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="mobileMenuOpen"
        class="fixed inset-0 bg-black/60 backdrop-blur-xs z-50 xl:hidden"
        @click="mobileMenuOpen = false"
      ></div>
    </transition>

    <!-- Mobile Navigation Drawer Panel -->
    <div
      :class="[
        'fixed top-0 bottom-0 left-0 w-80 max-w-[85vw] z-50 flex flex-col shadow-2xl transition-transform duration-300 ease-in-out xl:hidden',
        mobileMenuOpen ? 'translate-x-0' : '-translate-x-full'
      ]"
    >
      <!-- TOP SECTION: Clean White / Light Background for Crisp Logo Visibility -->
      <div class="bg-white border-b border-slate-200 px-5 py-4 flex items-center justify-between flex-shrink-0 shadow-xs">
        <router-link to="/" @click="mobileMenuOpen = false" class="flex items-center">
          <AppLogo variant="dark" size="sm" layout="horizontal" />
        </router-link>
        <button
          type="button"
          @click="mobileMenuOpen = false"
          class="p-1.5 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors"
          aria-label="Close menu"
        >
          <X class="w-5 h-5" />
        </button>
      </div>

      <!-- LOWER SIDEBAR BODY: Corporate Primary Blue with Clean Contrasting Text -->
      <div class="bg-[#042562] text-white flex-1 flex flex-col justify-between overflow-y-auto p-5">
        <div class="space-y-4">
          <!-- Mobile Search Input inside Blue Sidebar -->
          <form @submit.prevent="handleHeaderSearch" class="relative w-full">
            <input
              type="text"
              v-model="searchQuery"
              placeholder="Search IT hardware..."
              class="w-full bg-[#021840] text-white placeholder-slate-400 text-xs rounded-lg pl-9 pr-4 py-2.5 border border-blue-900 focus:outline-none focus:border-[#1865C0] transition-colors"
            />
            <Search class="w-4 h-4 text-slate-400 absolute left-3 top-3" />
          </form>

          <!-- Navigation Links -->
          <nav class="space-y-1 text-xs font-bold uppercase tracking-wider">
            <router-link
              to="/"
              @click="mobileMenuOpen = false"
              class="flex items-center justify-between px-3.5 py-2.5 rounded-lg text-slate-200 hover:text-white hover:bg-white/10 transition-colors"
              active-class="bg-[#1865C0] text-white"
            >
              <span>Home</span>
              <ArrowRight class="w-3.5 h-3.5 opacity-60" />
            </router-link>
            <router-link
              to="/catalog"
              @click="mobileMenuOpen = false"
              class="flex items-center justify-between px-3.5 py-2.5 rounded-lg text-slate-200 hover:text-white hover:bg-white/10 transition-colors"
              active-class="bg-[#1865C0] text-white"
            >
              <span>Shop Catalog</span>
              <ArrowRight class="w-3.5 h-3.5 opacity-60" />
            </router-link>
            <router-link
              to="/categories"
              @click="mobileMenuOpen = false"
              class="flex items-center justify-between px-3.5 py-2.5 rounded-lg text-slate-200 hover:text-white hover:bg-white/10 transition-colors"
              active-class="bg-[#1865C0] text-white"
            >
              <span>Categories</span>
              <ArrowRight class="w-3.5 h-3.5 opacity-60" />
            </router-link>
            <router-link
              to="/brands"
              @click="mobileMenuOpen = false"
              class="flex items-center justify-between px-3.5 py-2.5 rounded-lg text-slate-200 hover:text-white hover:bg-white/10 transition-colors"
              active-class="bg-[#1865C0] text-white"
            >
              <span>Brands</span>
              <ArrowRight class="w-3.5 h-3.5 opacity-60" />
            </router-link>
            <router-link
              to="/services"
              @click="mobileMenuOpen = false"
              class="flex items-center justify-between px-3.5 py-2.5 rounded-lg text-slate-200 hover:text-white hover:bg-white/10 transition-colors"
              active-class="bg-[#1865C0] text-white"
            >
              <span>Solutions</span>
              <ArrowRight class="w-3.5 h-3.5 opacity-60" />
            </router-link>
            <router-link
              to="/about"
              @click="mobileMenuOpen = false"
              class="flex items-center justify-between px-3.5 py-2.5 rounded-lg text-slate-200 hover:text-white hover:bg-white/10 transition-colors"
              active-class="bg-[#1865C0] text-white"
            >
              <span>About Us</span>
              <ArrowRight class="w-3.5 h-3.5 opacity-60" />
            </router-link>
            <router-link
              to="/contact"
              @click="mobileMenuOpen = false"
              class="flex items-center justify-between px-3.5 py-2.5 rounded-lg text-slate-200 hover:text-white hover:bg-white/10 transition-colors"
              active-class="bg-[#1865C0] text-white"
            >
              <span>Contact</span>
              <ArrowRight class="w-3.5 h-3.5 opacity-60" />
            </router-link>
            <router-link
              to="/cart"
              @click="mobileMenuOpen = false"
              class="flex items-center justify-between px-3.5 py-2.5 rounded-lg bg-[#E20913] hover:bg-[#be0710] text-white transition-colors mt-2"
            >
              <span class="flex items-center gap-2">
                <FileSpreadsheet class="w-4 h-4" />
                <span>Quotation Cart</span>
              </span>
              <span v-if="cart.uniqueItemCount > 0" class="bg-white text-[#E20913] text-[10px] font-black px-1.5 py-0.5 rounded-full">
                {{ cart.uniqueItemCount }}
              </span>
            </router-link>
          </nav>
        </div>

        <!-- Drawer Bottom Quick Contact & Staff Login -->
        <div class="pt-6 border-t border-blue-900/60 text-xs text-slate-300 space-y-2">
          <div class="flex items-center gap-2">
            <Phone class="w-3.5 h-3.5 text-[#E20913]" />
            <span>{{ companyInfo.phone || '+251 94 642 8560' }}</span>
          </div>
          <div class="flex items-center gap-2">
            <Lock class="w-3.5 h-3.5 text-blue-300" />
            <router-link to="/admin/login" @click="mobileMenuOpen = false" class="hover:text-white underline">
              Staff Portal Login
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Page Content Body -->
    <main class="flex-1">
      <slot />
    </main>

    <!-- Persistent Floating Product Compare Drawer -->
    <CompareDrawer />

    <!-- Professional Enterprise Footer -->
    <footer class="bg-[#042562] text-slate-300 pt-16 pb-8 border-t border-navy-800">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 pb-12 border-b border-navy-700/60">
          <!-- Col 1: Brand & Overview -->
          <div class="lg:col-span-2 space-y-4">
            <router-link to="/">
              <AppLogo variant="light" size="md" layout="horizontal" />
            </router-link>
            <p class="text-xs text-slate-300 leading-relaxed max-w-sm">
              iRACK IT Solution is a premier distributor and turnkey integrator of high-density server racks, enterprise compute nodes, managed campus switching, SAN/NAS storage, and continuous online power protection.
            </p>
            <div class="flex items-center gap-3 pt-2">
              <span class="inline-flex items-center gap-1.5 text-xs text-emerald-400 bg-emerald-950/60 border border-emerald-800/80 px-2.5 py-1 rounded font-semibold">
                <CheckCircle2 class="w-3.5 h-3.5" /> ISO 9001 Certified Supplier
              </span>
              <span class="inline-flex items-center gap-1.5 text-xs text-blue-300 bg-blue-950/60 border border-blue-800/80 px-2.5 py-1 rounded font-semibold">
                <ShieldCheck class="w-3.5 h-3.5 text-[#1865C0]" /> Tier-1 Warranty
              </span>
            </div>
          </div>

          <!-- Col 2: Showroom Solutions -->
          <div>
            <h4 class="text-xs font-black uppercase tracking-wider text-white mb-4 border-b border-blue-900 pb-2">
              Hardware Solutions
            </h4>
            <ul class="space-y-2.5 text-xs">
              <li>
                <router-link to="/catalog?category=servers-and-workstations" class="hover:text-white transition-colors">
                  Rack & Tower Servers
                </router-link>
              </li>
              <li>
                <router-link to="/catalog?category=networking-equipment" class="hover:text-white transition-colors">
                  Core & Managed Switches
                </router-link>
              </li>
              <li>
                <router-link to="/catalog?category=storage-solutions" class="hover:text-white transition-colors">
                  Enterprise SAN & NAS Storage
                </router-link>
              </li>
              <li>
                <router-link to="/catalog?category=power-solutions" class="hover:text-white transition-colors">
                  Online Double-Conversion UPS
                </router-link>
              </li>
              <li>
                <router-link to="/catalog?category=data-center-infrastructure" class="hover:text-white transition-colors">
                  42U/48U Server Cabinets
                </router-link>
              </li>
              <li>
                <router-link to="/catalog?category=security-solutions" class="hover:text-white transition-colors">
                  Next-Gen Firewalls
                </router-link>
              </li>
            </ul>
          </div>

          <!-- Col 3: Company & Information -->
          <div>
            <h4 class="text-xs font-black uppercase tracking-wider text-white mb-4 border-b border-blue-900 pb-2">
              Company & Portals
            </h4>
            <ul class="space-y-2.5 text-xs">
              <li>
                <router-link to="/about" class="hover:text-white transition-colors">
                  About iRACK IT Solution
                </router-link>
              </li>
              <li>
                <router-link to="/services" class="hover:text-white transition-colors">
                  Turnkey Deployment Services
                </router-link>
              </li>
              <li>
                <router-link to="/brands" class="hover:text-white transition-colors">
                  Authorized Vendor Partners
                </router-link>
              </li>
              <li>
                <router-link to="/contact" class="hover:text-white transition-colors">
                  Contact Technical Sales
                </router-link>
              </li>
              <li>
                <router-link to="/cart" class="text-blue-300 hover:text-white font-semibold transition-colors">
                  Request for Quotation (RFQ)
                </router-link>
              </li>
              <li>
                <router-link to="/admin/login" class="text-slate-400 hover:text-white transition-colors">
                  Administrative Staff Login
                </router-link>
              </li>
            </ul>
          </div>

          <!-- Col 4: Regional Distribution Hubs -->
          <div>
            <h4 class="text-xs font-black uppercase tracking-wider text-white mb-4 border-b border-blue-900 pb-2">
              Distribution Network
            </h4>
            <div class="space-y-3 text-xs text-slate-300">
              <div>
                <p class="font-bold text-white text-[11px]">Central Headquarters & Showroom</p>
                <p class="text-slate-400 text-[11px]">3rd Floor, Wastna Commercial Building, General Wingate St, Addis Ababa</p>
                <p class="text-[11px] text-blue-300">dagimw@iracktech.com • +251 94 642 8560</p>
              </div>
              <div>
                <p class="font-bold text-white text-[11px]">Downtown Commercial Center</p>
                <p class="text-slate-400 text-[11px]">Enterprise Tower, Financial District</p>
              </div>
              <div>
                <p class="font-bold text-white text-[11px]">Airport Logistics Terminal</p>
                <p class="text-slate-400 text-[11px]">Air Cargo Parkway, Logistics Bay 4</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Bottom Legal & Copyright -->
        <div class="pt-8 flex flex-col md:flex-row items-center justify-between text-xs text-slate-400 gap-4">
          <p>© {{ new Date().getFullYear() }} {{ companyInfo.name || 'iRACK IT Solution' }}. All rights reserved. Registered Enterprise IT Supplier.</p>
          <div class="flex items-center space-x-6 text-[11px]">
            <router-link to="/privacy" class="hover:text-white transition-colors">Privacy Policy</router-link>
            <router-link to="/terms" class="hover:text-white transition-colors">Terms & Quotation Conditions</router-link>
            <router-link to="/contact" class="hover:text-white transition-colors">Branch Locations</router-link>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useCartStore } from '@/stores/cartStore';
import { useCompareStore } from '@/stores/compareStore';
import { configService } from '@/services/configService';
import AppLogo from '@/components/common/AppLogo.vue';
import CompareDrawer from '@/components/common/CompareDrawer.vue';
import {
  ShieldCheck,
  Building2,
  Phone,
  Lock,
  Search,
  FileSpreadsheet,
  Scale,
  Menu,
  X,
  CheckCircle2,
  ArrowRight,
} from 'lucide-vue-next';

const router = useRouter();
const cart = useCartStore();
const compare = useCompareStore();

const searchQuery = ref('');
const mobileMenuOpen = ref(false);
const companyInfo = ref({});
const navigationSettings = ref({});

const handleHeaderSearch = () => {
  if (searchQuery.value.trim()) {
    router.push({
      path: '/catalog',
      query: { search: searchQuery.value.trim() },
    });
    mobileMenuOpen.value = false;
  }
};

onMounted(async () => {
  try {
    const res = await configService.getPublicSettings();
    if (res && res.data) {
      if (res.data.company) companyInfo.value = res.data.company;
      if (res.data.navigation) navigationSettings.value = res.data.navigation;
    }
  } catch (err) {
    // Graceful fallback to default brand constants
  }
});
</script>
