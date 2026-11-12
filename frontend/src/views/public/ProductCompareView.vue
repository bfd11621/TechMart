<template>
  <PublicLayout>
    <!-- Header Banner -->
    <div class="bg-[#042562] text-white py-10 border-b border-navy-800">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
          <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-blue-300 uppercase tracking-wider mb-1">
              <router-link to="/" class="hover:text-white">Home</router-link>
              <span>/</span>
              <router-link to="/catalog" class="hover:text-white">Catalog</router-link>
              <span>/</span>
              <span class="text-white">Hardware Comparison</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black">Technical Specifications Comparison</h1>
          </div>

          <div class="flex items-center gap-3">
            <button
              v-if="compare.count > 0"
              @click="compare.clearCompare"
              class="text-xs text-red-300 hover:text-white font-semibold transition-colors"
            >
              Clear All ({{ compare.count }})
            </button>
            <router-link
              to="/catalog"
              class="inline-flex items-center gap-1.5 bg-[#1865C0] hover:bg-blue-600 text-white text-xs font-bold uppercase tracking-wider px-4 py-2 rounded-lg transition-colors"
            >
              <Plus class="w-3.5 h-3.5" />
              <span>Add More Models</span>
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div
      v-if="compare.count === 0"
      class="max-w-3xl mx-auto px-4 py-20 text-center space-y-4"
    >
      <div class="w-16 h-16 rounded-full bg-blue-50 text-[#042562] mx-auto flex items-center justify-center">
        <Scale class="w-8 h-8" />
      </div>
      <h2 class="text-2xl font-black text-[#042562]">No Products in Comparison</h2>
      <p class="text-xs text-slate-500 max-w-sm mx-auto">
        Select up to 4 IT hardware models from our catalog to compare their technical architectures, power ratings, and port counts side-by-side.
      </p>
      <router-link
        to="/catalog"
        class="inline-flex items-center gap-2 bg-[#042562] hover:bg-[#063485] text-white text-xs font-bold uppercase tracking-wider px-6 py-2.5 rounded-lg transition-colors"
      >
        <span>Browse Hardware Catalog</span>
      </router-link>
    </div>

    <!-- Comparison Matrix Table -->
    <div v-else class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 overflow-x-auto">
      <div class="min-w-[800px] bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <!-- Top Products Header Row -->
        <div class="grid grid-cols-5 divide-x divide-slate-200 border-b border-slate-200 bg-[#F8FAFC]">
          <!-- Top Left Cell -->
          <div class="p-5 flex flex-col justify-between">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">
              Hardware Models ({{ compare.count }})
            </span>
            <div class="text-[11px] text-slate-400">
              Comparing technical parameters side-by-side
            </div>
          </div>

          <!-- Product Columns (Up to 4) -->
          <div
            v-for="product in compare.items"
            :key="product.id"
            class="p-5 flex flex-col justify-between relative space-y-3 bg-white"
          >
            <!-- Remove from compare -->
            <button
              @click="compare.removeCompare(product.id)"
              class="absolute top-3 right-3 text-slate-400 hover:text-[#E20913] transition-colors"
              title="Remove from comparison"
            >
              <X class="w-4 h-4" />
            </button>

            <!-- Image & Brand -->
            <div class="flex flex-col items-center text-center">
              <div class="w-24 h-24 p-2 bg-[#F4F6FA] rounded-xl border border-slate-200 flex items-center justify-center mb-3">
                <img
                  :src="product.primary_image_url || '/images/products/placeholder.png'"
                  :alt="product.name"
                  class="max-h-full max-w-full object-contain"
                />
              </div>
              <span class="text-[10px] font-bold uppercase text-[#1865C0] tracking-wider">
                {{ product.brand?.name || 'OEM' }}
              </span>
              <router-link
                :to="'/product/' + (product.slug || product.id)"
                class="text-xs font-black text-[#042562] hover:text-[#1865C0] line-clamp-2 mt-1"
              >
                {{ product.name }}
              </router-link>
              <span class="text-[10px] font-mono text-slate-500 mt-0.5">
                {{ product.model_number || product.sku }}
              </span>
            </div>

            <!-- Action Button -->
            <button
              @click="addToCart(product)"
              class="w-full inline-flex items-center justify-center gap-1.5 bg-[#042562] hover:bg-[#063485] text-white py-2 px-3 rounded-lg text-xs font-bold uppercase tracking-wider transition-colors shadow-sm"
            >
              <FileSpreadsheet class="w-3.5 h-3.5 text-blue-200" />
              <span>Add to RFQ</span>
            </button>
          </div>

          <!-- Fill Remaining Blank Columns if < 4 -->
          <div
            v-for="emptySlot in (compare.maxItems - compare.count)"
            :key="'slot-' + emptySlot"
            class="p-5 flex flex-col items-center justify-center text-center bg-slate-50/50 text-slate-400 border-dashed"
          >
            <router-link
              to="/catalog"
              class="text-xs font-bold text-[#1865C0] hover:underline flex flex-col items-center gap-2"
            >
              <div class="w-10 h-10 rounded-full border-2 border-dashed border-slate-300 flex items-center justify-center">
                <Plus class="w-5 h-5 text-slate-400" />
              </div>
              <span>Add Model to Slot</span>
            </router-link>
          </div>
        </div>

        <!-- Section 1: General Attributes -->
        <div class="bg-blue-50/70 px-5 py-2.5 text-xs font-black uppercase tracking-wider text-[#042562] border-b border-slate-200">
          General Architecture
        </div>
        <div class="divide-y divide-slate-100">
          <!-- Category -->
          <div class="grid grid-cols-5 divide-x divide-slate-100 text-xs">
            <div class="p-3.5 font-bold text-slate-700 bg-[#F8FAFC]">Category</div>
            <div v-for="p in compare.items" :key="'cat-' + p.id" class="p-3.5 text-slate-800">
              {{ p.category?.name || 'Hardware' }}
            </div>
            <div v-for="s in (compare.maxItems - compare.count)" :key="'e-cat-' + s" class="p-3.5 bg-slate-50/40"></div>
          </div>

          <!-- SKU -->
          <div class="grid grid-cols-5 divide-x divide-slate-100 text-xs">
            <div class="p-3.5 font-bold text-slate-700 bg-[#F8FAFC]">Unique SKU</div>
            <div v-for="p in compare.items" :key="'sku-' + p.id" class="p-3.5 font-mono text-slate-800">
              {{ p.sku }}
            </div>
            <div v-for="s in (compare.maxItems - compare.count)" :key="'e-sku-' + s" class="p-3.5 bg-slate-50/40"></div>
          </div>

          <!-- Availability -->
          <div class="grid grid-cols-5 divide-x divide-slate-100 text-xs">
            <div class="p-3.5 font-bold text-slate-700 bg-[#F8FAFC]">Stock Availability</div>
            <div v-for="p in compare.items" :key="'avail-' + p.id" class="p-3.5">
              <span
                class="font-bold text-xs"
                :class="p.availability_status === 'in_stock' ? 'text-[#16A34A]' : 'text-[#F59E0B]'"
              >
                {{ p.availability_status === 'in_stock' ? 'In Stock (Central Hub)' : 'Pre-Order' }}
              </span>
            </div>
            <div v-for="s in (compare.maxItems - compare.count)" :key="'e-av-' + s" class="p-3.5 bg-slate-50/40"></div>
          </div>
        </div>

        <!-- Section 2: Key Features Comparison -->
        <div class="bg-blue-50/70 px-5 py-2.5 text-xs font-black uppercase tracking-wider text-[#042562] border-y border-slate-200">
          Architecture Highlights
        </div>
        <div class="grid grid-cols-5 divide-x divide-slate-100 text-xs">
          <div class="p-3.5 font-bold text-slate-700 bg-[#F8FAFC]">Key Features</div>
          <div v-for="p in compare.items" :key="'feats-' + p.id" class="p-3.5 space-y-1.5">
            <div
              v-for="(f, i) in (p.key_features || []).slice(0, 4)"
              :key="i"
              class="flex items-start gap-1.5 text-[11px] text-slate-700"
            >
              <Check class="w-3 h-3 text-[#16A34A] flex-shrink-0 mt-0.5" />
              <span>{{ f }}</span>
            </div>
          </div>
          <div v-for="s in (compare.maxItems - compare.count)" :key="'e-feat-' + s" class="p-3.5 bg-slate-50/40"></div>
        </div>

        <!-- Section 3: Technical Specifications Matrix -->
        <div class="bg-blue-50/70 px-5 py-2.5 text-xs font-black uppercase tracking-wider text-[#042562] border-y border-slate-200">
          Technical Parameters
        </div>
        <div class="divide-y divide-slate-100">
          <div
            v-for="specName in commonSpecNames"
            :key="specName"
            class="grid grid-cols-5 divide-x divide-slate-100 text-xs"
          >
            <div class="p-3.5 font-bold text-slate-700 bg-[#F8FAFC]">{{ specName }}</div>
            <div
              v-for="p in compare.items"
              :key="'spec-' + p.id + '-' + specName"
              class="p-3.5 font-medium text-slate-800"
            >
              {{ getProductSpecValue(p, specName) }}
            </div>
            <div v-for="s in (compare.maxItems - compare.count)" :key="'e-sp-' + s" class="p-3.5 bg-slate-50/40"></div>
          </div>
        </div>
      </div>
    </div>
  </PublicLayout>
</template>

<script setup>
import { computed } from 'vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { useCompareStore } from '@/stores/compareStore';
import { useCartStore } from '@/stores/cartStore';
import { useNotificationStore } from '@/stores/notificationStore';
import {
  Scale,
  Plus,
  X,
  FileSpreadsheet,
  Check,
} from 'lucide-vue-next';

const compare = useCompareStore();
const cart = useCartStore();
const notify = useNotificationStore();

const addToCart = (product) => {
  cart.addItem(product, 1);
  notify.success(`"${product.name}" added to quotation cart.`);
};

// Aggregate unique spec names across all compared products
const commonSpecNames = computed(() => {
  const set = new Set();
  for (const p of compare.items) {
    if (p.specifications) {
      for (const s of p.specifications) {
        if (s.name) set.add(s.name);
      }
    }
  }
  return Array.from(set);
});

const getProductSpecValue = (product, specName) => {
  if (!product.specifications) return '—';
  const match = product.specifications.find((s) => s.name === specName);
  if (!match) return '—';
  return match.unit ? `${match.value} ${match.unit}` : match.value;
};
</script>
