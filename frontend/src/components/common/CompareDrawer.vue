<template>
  <div
    v-if="compare.count > 0"
    class="fixed bottom-0 left-0 right-0 z-40 bg-white border-t-2 border-[#1865C0] shadow-2xl transition-all duration-300 transform"
    :class="compare.isDrawerOpen ? 'translate-y-0' : 'translate-y-[calc(100%-44px)]'"
  >
    <!-- Drawer Toggle Header -->
    <div
      class="bg-[#042562] text-white px-4 py-2.5 flex items-center justify-between cursor-pointer select-none"
      @click="compare.isDrawerOpen = !compare.isDrawerOpen"
    >
      <div class="flex items-center gap-3">
        <Scale class="w-4 h-4 text-[#1865C0]" />
        <span class="text-xs font-bold tracking-wider uppercase">
          Compare Products ({{ compare.count }} of {{ compare.maxItems }})
        </span>
        <span class="text-[11px] text-blue-200 hidden sm:inline">
          — Select up to 4 models for side-by-side technical specs
        </span>
      </div>

      <div class="flex items-center gap-4">
        <button
          v-if="compare.isDrawerOpen"
          @click.stop="compare.clearCompare"
          class="text-xs text-red-300 hover:text-white transition-colors"
        >
          Clear All
        </button>
        <button class="text-slate-300 hover:text-white p-1">
          <ChevronUp
            class="w-4 h-4 transition-transform duration-200"
            :class="{ 'rotate-180': compare.isDrawerOpen }"
          />
        </button>
      </div>
    </div>

    <!-- Drawer Content -->
    <div v-show="compare.isDrawerOpen" class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8">
      <div class="flex flex-col md:flex-row items-center justify-between gap-4">
        <!-- Selected Items Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 w-full md:w-auto flex-1">
          <div
            v-for="product in compare.items"
            :key="product.id"
            class="relative flex items-center gap-3 p-2.5 rounded-lg border border-slate-200 bg-[#F4F6FA] hover:border-blue-300 transition-colors"
          >
            <button
              @click="compare.removeCompare(product.id)"
              class="absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full bg-slate-700 hover:bg-[#E20913] text-white flex items-center justify-center text-xs shadow transition-colors"
              title="Remove"
            >
              <X class="w-3 h-3" />
            </button>
            <div class="w-12 h-12 bg-white rounded border border-slate-200 flex-shrink-0 flex items-center justify-center p-1">
              <img
                :src="product.primary_image_url || '/images/products/placeholder.png'"
                :alt="product.name"
                class="max-h-full max-w-full object-contain"
              />
            </div>
            <div class="min-w-0 flex-1">
              <span class="text-[10px] font-bold text-[#1865C0] block truncate">
                {{ product.brand?.name || 'IT Hardware' }}
              </span>
              <p class="text-xs font-semibold text-[#172033] truncate">
                {{ product.name }}
              </p>
              <span class="text-[10px] font-mono text-slate-500 block truncate">
                {{ product.model_number || product.sku }}
              </span>
            </div>
          </div>

          <!-- Empty Slots -->
          <div
            v-for="i in (compare.maxItems - compare.count)"
            :key="'empty-' + i"
            class="hidden sm:flex items-center justify-center border border-dashed border-slate-300 rounded-lg p-2.5 text-center text-slate-400 bg-white"
          >
            <span class="text-xs font-medium">+ Add to Compare</span>
          </div>
        </div>

        <!-- Action Button -->
        <div class="flex items-center gap-3 w-full md:w-auto justify-end">
          <router-link
            to="/compare"
            class="w-full md:w-auto inline-flex items-center justify-center gap-2 bg-[#042562] hover:bg-[#063485] text-white text-xs font-bold uppercase tracking-wider px-5 py-3 rounded-lg shadow-md transition-colors"
            @click="compare.isDrawerOpen = false"
          >
            <Scale class="w-4 h-4 text-[#1865C0]" />
            <span>Compare Now</span>
            <ArrowRight class="w-3.5 h-3.5" />
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useCompareStore } from '@/stores/compareStore';
import { Scale, X, ChevronUp, ArrowRight } from 'lucide-vue-next';

const compare = useCompareStore();
</script>
