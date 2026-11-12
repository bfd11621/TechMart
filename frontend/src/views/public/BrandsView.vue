<template>
  <PublicLayout>
    <!-- Header Banner -->
    <div class="bg-[#042562] text-white py-14 border-b border-navy-800">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-2 text-xs font-semibold text-blue-300 uppercase tracking-wider mb-2">
          <router-link to="/" class="hover:text-white">Home</router-link>
          <span>/</span>
          <span class="text-white">Authorized Partners</span>
        </div>
        <h1 class="text-3xl font-black">Authorized Hardware Manufacturers & Brands</h1>
        <p class="text-sm text-blue-200 mt-1 max-w-xl">
          iRACK IT Solution maintains direct authorized integration and distribution partnerships with the world's leading enterprise IT manufacturers.
        </p>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 space-y-12">
      <!-- Loading State -->
      <div v-if="loading" class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div v-for="i in 6" :key="i" class="h-48 bg-slate-200 rounded-xl animate-pulse"></div>
      </div>

      <!-- Brands Grid -->
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="brand in brands"
          :key="brand.id"
          class="corporate-card bg-white rounded-2xl border border-slate-200 p-6 hover:shadow-card-hover transition-all flex flex-col justify-between space-y-6"
        >
          <div class="space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
              <div class="w-12 h-12 rounded-xl bg-blue-50 text-[#042562] flex items-center justify-center font-black text-lg">
                <Award class="w-6 h-6 text-[#1865C0]" />
              </div>
              <span class="text-[10px] font-bold uppercase tracking-wider bg-blue-50 text-[#1865C0] px-2.5 py-1 rounded-full border border-blue-200">
                {{ brand.tier || 'Certified OEM Partner' }}
              </span>
            </div>

            <div>
              <h3 class="text-lg font-black text-[#042562]">{{ brand.name }}</h3>
              <p class="text-xs text-slate-500 mt-1">
                Full factory warranty, official technical datasheets, and direct firmware updates supported.
              </p>
            </div>

            <div class="flex items-center gap-2 text-xs font-semibold text-slate-600">
              <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
              <span>{{ brand.products_count || '12+' }} Models in Showroom Catalog</span>
            </div>
          </div>

          <!-- Bottom Action -->
          <div class="pt-4 border-t border-slate-100">
            <router-link
              :to="{ path: '/catalog', query: { brand: brand.slug } }"
              class="w-full inline-flex items-center justify-center gap-2 bg-[#042562] hover:bg-[#063485] text-white py-2.5 rounded-lg text-xs font-bold uppercase tracking-wider transition-colors shadow-sm"
            >
              <span>Browse {{ brand.name }} Hardware</span>
              <ArrowRight class="w-3.5 h-3.5" />
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </PublicLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import catalogService from '@/services/catalogService';
import { Award, ArrowRight } from 'lucide-vue-next';

const brands = ref([]);
const loading = ref(true);

onMounted(async () => {
  try {
    const res = await catalogService.getBrands();
    brands.value = res.data || [];
  } catch (err) {
    console.error(err);
  } finally {
    loading.value = false;
  }
});
</script>
