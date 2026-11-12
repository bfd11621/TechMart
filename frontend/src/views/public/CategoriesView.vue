<template>
  <PublicLayout>
    <!-- Page Header Banner -->
    <div class="bg-[#042562] text-white py-12 border-b border-navy-800">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
          <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-blue-300 uppercase tracking-wider mb-2">
              <router-link to="/" class="hover:text-white">Home</router-link>
              <span>/</span>
              <span class="text-white">Hardware Classifications</span>
            </div>
            <h1 class="text-3xl font-black">Datacenter Hardware Categories</h1>
            <p class="text-sm text-blue-200 mt-1 max-w-xl">
              Browse our comprehensive 9-tier enterprise IT hardware directory, from dual-socket compute servers to high-density optical fiber accessories.
            </p>
          </div>

          <!-- Category Quick Filter Input -->
          <div class="relative w-full md:w-72">
            <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" />
            <input
              type="text"
              v-model="filterText"
              placeholder="Filter categories or subcategories..."
              class="w-full bg-[#021840] text-white placeholder-slate-400 text-xs rounded-lg pl-9 pr-4 py-2.5 border border-blue-900 focus:outline-none focus:border-[#1865C0]"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Main Categories Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <!-- Loading State -->
      <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div v-for="i in 6" :key="i" class="h-64 rounded-xl bg-slate-200 animate-pulse"></div>
      </div>

      <!-- Categories Grid -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div
          v-for="cat in filteredCategories"
          :key="cat.id"
          class="corporate-card bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-card-hover transition-all duration-200 flex flex-col justify-between"
        >
          <!-- Card Header Banner -->
          <div class="p-6 bg-[#F8FAFC] border-b border-slate-100 flex items-start justify-between">
            <div class="flex items-center gap-4">
              <div class="w-14 h-14 rounded-xl bg-[#042562] text-white flex items-center justify-center flex-shrink-0 shadow-md">
                <component :is="getCategoryIcon(cat.slug)" class="w-7 h-7 text-blue-300" />
              </div>
              <div>
                <h2 class="text-lg font-black text-[#042562] leading-snug">
                  {{ cat.name }}
                </h2>
                <span class="inline-block text-xs font-semibold text-[#1865C0] mt-0.5">
                  {{ cat.products_count || '0' }} models in catalog
                </span>
              </div>
            </div>
          </div>

          <!-- Category Description & Subcategories -->
          <div class="p-6 flex-1 flex flex-col justify-between">
            <div>
              <p class="text-xs text-slate-600 leading-relaxed mb-5">
                {{ cat.description }}
              </p>

              <!-- Subcategories List -->
              <div v-if="cat.children && cat.children.length">
                <h4 class="text-[11px] font-black uppercase tracking-wider text-[#042562] mb-2.5">
                  Subcategories & Architecture Types
                </h4>
                <div class="space-y-1.5">
                  <router-link
                    v-for="sub in cat.children"
                    :key="sub.id"
                    :to="{ path: '/catalog', query: { category: sub.slug } }"
                    class="flex items-center justify-between p-2 rounded-lg hover:bg-[#F4F6FA] text-xs font-medium text-slate-700 hover:text-[#1865C0] transition-colors border border-transparent hover:border-slate-200 group"
                  >
                    <span class="flex items-center gap-2">
                      <ChevronRight class="w-3.5 h-3.5 text-slate-400 group-hover:text-[#1865C0] group-hover:translate-x-0.5 transition-transform" />
                      <span>{{ sub.name }}</span>
                    </span>
                    <span class="text-[10px] text-slate-400 font-mono">
                      {{ sub.products_count || '1+' }}
                    </span>
                  </router-link>
                </div>
              </div>
            </div>

            <!-- Card Bottom Action -->
            <div class="mt-6 pt-4 border-t border-slate-100">
              <router-link
                :to="{ path: '/catalog', query: { category: cat.slug } }"
                class="w-full inline-flex items-center justify-center gap-2 bg-[#042562] hover:bg-[#063485] text-white py-2.5 rounded-lg text-xs font-bold uppercase tracking-wider transition-colors shadow-sm"
              >
                <span>Browse All {{ cat.name }}</span>
                <ArrowRight class="w-3.5 h-3.5" />
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </PublicLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import catalogService from '@/services/catalogService';
import {
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
  ChevronRight,
  ArrowRight,
} from 'lucide-vue-next';

const categories = ref([]);
const loading = ref(true);
const filterText = ref('');

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

const filteredCategories = computed(() => {
  if (!filterText.value.trim()) return categories.value;
  const q = filterText.value.toLowerCase();
  return categories.value.filter(
    (c) =>
      c.name.toLowerCase().includes(q) ||
      (c.description && c.description.toLowerCase().includes(q)) ||
      (c.children && c.children.some((sub) => sub.name.toLowerCase().includes(q)))
  );
});

onMounted(async () => {
  loading.value = true;
  try {
    const res = await catalogService.getCategories();
    categories.value = res.data || [];
  } catch (err) {
    console.error('Failed to load categories', err);
  } finally {
    loading.value = false;
  }
});
</script>
