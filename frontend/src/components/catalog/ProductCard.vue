<template>
  <!-- Grid View Card -->
  <div
    v-if="viewMode === 'grid'"
    class="corporate-card bg-white rounded-xl border border-slate-200 overflow-hidden flex flex-col justify-between hover:shadow-card-hover transition-all duration-200 group relative"
  >
    <!-- Card Header / Media -->
    <div class="relative bg-[#F8FAFC] p-6 flex items-center justify-center min-h-[220px] border-b border-slate-100 overflow-hidden">
      <!-- Badges: Featured & Compact Expanding Availability Pill -->
      <div class="absolute top-3 left-3 flex flex-col items-start gap-1.5 z-10 pointer-events-auto">
        <!-- Featured Badge (Minimal, compact inline-flex) -->
        <span
          v-if="product.is_featured"
          class="inline-flex items-center w-auto self-start bg-[#E20913] text-white text-[10px] font-black uppercase px-2 py-0.5 rounded shadow-xs tracking-wider"
        >
          Featured
        </span>

        <!-- Availability Badge: Compact Status Dot that smoothly expands on card hover -->
        <div
          :class="[
            'inline-flex items-center rounded-full shadow-xs border transition-all duration-300 ease-in-out overflow-hidden',
            'h-5 px-1.5 max-w-[20px] group-hover:max-w-[130px] group-hover:px-2 group-hover:gap-1.5',
            product.availability_status === 'in_stock'
              ? 'bg-emerald-50/95 border-emerald-200/90 text-emerald-700'
              : 'bg-amber-50/95 border-amber-200/90 text-amber-700'
          ]"
          :title="product.availability_label || (product.availability_status === 'in_stock' ? 'In Stock' : 'Pre-Order')"
        >
          <span
            class="w-1.5 h-1.5 rounded-full flex-shrink-0"
            :class="product.availability_status === 'in_stock' ? 'bg-emerald-500 animate-pulse' : 'bg-amber-500'"
          ></span>
          <span
            class="text-[10px] font-bold whitespace-nowrap overflow-hidden opacity-0 group-hover:opacity-100 transition-opacity duration-300 select-none leading-none"
          >
            {{ product.availability_label || (product.availability_status === 'in_stock' ? 'In Stock' : 'Pre-Order') }}
          </span>
        </div>
      </div>

      <!-- Quick Action Buttons (Hover overlay) -->
      <div class="absolute top-3 right-3 flex flex-col gap-2 z-10 opacity-0 group-hover:opacity-100 transition-opacity">
        <button
          type="button"
          @click.stop="$emit('quick-view', product)"
          class="w-8 h-8 rounded-full bg-white/90 backdrop-blur border border-slate-300 text-slate-700 hover:text-[#1865C0] hover:border-[#1865C0] shadow flex items-center justify-center transition-colors"
          title="Quick Technical Preview"
        >
          <Eye class="w-4 h-4" />
        </button>
        <button
          type="button"
          @click.stop="compare.toggleCompare(product)"
          :class="[
            'w-8 h-8 rounded-full border shadow flex items-center justify-center transition-colors',
            compare.isInCompare(product.id)
              ? 'bg-[#042562] text-white border-[#042562]'
              : 'bg-white/90 backdrop-blur border-slate-300 text-slate-700 hover:text-[#042562]',
          ]"
          :title="compare.isInCompare(product.id) ? 'Remove from Comparison' : 'Add to Compare'"
        >
          <Scale class="w-4 h-4" />
        </button>
      </div>

      <!-- Product Image -->
      <router-link
        :to="'/product/' + (product.slug || product.id)"
        class="w-full h-full flex items-center justify-center"
      >
        <img
          :src="product.primary_image_url || '/images/products/placeholder.png'"
          :alt="product.name"
          class="max-h-40 max-w-full object-contain group-hover:scale-105 transition-transform duration-300"
          loading="lazy"
        />
      </router-link>
    </div>

    <!-- Card Body -->
    <div class="p-5 flex-1 flex flex-col justify-between">
      <div>
        <!-- Brand & Model Row -->
        <div class="flex items-center justify-between text-xs mb-1.5">
          <span class="font-extrabold uppercase text-[#1865C0] tracking-wider text-[11px]">
            {{ product.brand?.name || 'IT Hardware' }}
          </span>
          <span class="text-[11px] font-mono text-slate-500 font-medium">
            {{ product.model_number || product.sku }}
          </span>
        </div>

        <!-- Product Name Link -->
        <router-link
          :to="'/product/' + (product.slug || product.id)"
          class="block font-bold text-[#042562] text-sm leading-snug hover:text-[#1865C0] transition-colors line-clamp-2"
        >
          {{ product.name }}
        </router-link>

        <!-- Category Tag -->
        <span class="inline-block text-[11px] text-slate-500 font-medium mt-1">
          {{ product.category?.name || 'Datacenter' }}
        </span>

        <!-- Key Features Snippet -->
        <ul
          v-if="product.key_features && product.key_features.length"
          class="mt-3 space-y-1 text-[11px] text-slate-600 border-t border-slate-100 pt-2.5"
        >
          <li
            v-for="(f, i) in product.key_features.slice(0, 2)"
            :key="i"
            class="flex items-start gap-1.5 truncate"
          >
            <Check class="w-3.5 h-3.5 text-[#16A34A] flex-shrink-0 mt-0.5" />
            <span class="truncate">{{ f }}</span>
          </li>
        </ul>
      </div>

      <!-- Card Footer Actions -->
      <div class="mt-4 pt-3 border-t border-slate-100 flex items-center gap-2">
        <button
          @click="addToCart"
          class="flex-1 inline-flex items-center justify-center gap-1.5 bg-[#042562] hover:bg-[#063485] text-white py-2 px-3 rounded-lg text-xs font-bold uppercase tracking-wider transition-colors shadow-sm"
        >
          <FileSpreadsheet class="w-3.5 h-3.5 text-blue-200" />
          <span>Add to RFQ</span>
        </button>

        <router-link
          :to="'/product/' + (product.slug || product.id)"
          class="p-2 text-slate-500 hover:text-[#1865C0] hover:bg-slate-100 rounded-lg transition-colors"
          title="View Technical Specs"
        >
          <ArrowRight class="w-4 h-4" />
        </router-link>
      </div>
    </div>
  </div>

  <!-- List View Card -->
  <div
    v-else
    class="corporate-card bg-white rounded-xl border border-slate-200 overflow-hidden flex flex-col md:flex-row hover:shadow-card-hover transition-all duration-200 group"
  >
    <!-- Image Column -->
    <div class="relative bg-[#F8FAFC] p-6 md:w-64 flex items-center justify-center border-b md:border-b-0 md:border-r border-slate-100 flex-shrink-0">
      <span
        v-if="product.is_featured"
        class="absolute top-3 left-3 inline-flex items-center w-auto bg-[#E20913] text-white text-[10px] font-black uppercase px-2 py-0.5 rounded shadow-xs tracking-wider z-10"
      >
        Featured
      </span>

      <router-link
        :to="'/product/' + (product.slug || product.id)"
        class="w-full h-full flex items-center justify-center"
      >
        <img
          :src="product.primary_image_url || '/images/products/placeholder.png'"
          :alt="product.name"
          class="max-h-36 max-w-full object-contain group-hover:scale-105 transition-transform duration-300"
          loading="lazy"
        />
      </router-link>
    </div>

    <!-- Information Column -->
    <div class="p-6 flex-1 flex flex-col justify-between">
      <div>
        <div class="flex flex-wrap items-center justify-between gap-2 mb-1.5">
          <div class="flex items-center gap-2">
            <span class="font-extrabold uppercase text-[#1865C0] tracking-wider text-xs">
              {{ product.brand?.name || 'IT Hardware' }}
            </span>
            <span class="text-slate-300">•</span>
            <span class="text-xs font-mono text-slate-500">
              SKU: <strong class="text-slate-700">{{ product.sku }}</strong>
            </span>
            <span class="text-slate-300">•</span>
            <span class="text-xs font-mono text-slate-500">
              Model: <strong class="text-slate-700">{{ product.model_number || 'N/A' }}</strong>
            </span>
          </div>

          <span
            :class="[
              'text-xs font-bold px-2.5 py-0.5 rounded-full flex items-center gap-1',
              product.availability_status === 'in_stock'
                ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
                : 'bg-amber-50 text-amber-700 border border-amber-200',
            ]"
          >
            <span
              class="w-2 h-2 rounded-full"
              :class="product.availability_status === 'in_stock' ? 'bg-emerald-500' : 'bg-amber-500'"
            ></span>
            {{ product.availability_label || (product.availability_status === 'in_stock' ? 'In Stock' : 'Pre-Order') }}
          </span>
        </div>

        <router-link
          :to="'/product/' + (product.slug || product.id)"
          class="text-lg font-black text-[#042562] hover:text-[#1865C0] transition-colors leading-snug"
        >
          {{ product.name }}
        </router-link>

        <p class="text-xs text-slate-600 mt-2 leading-relaxed line-clamp-2">
          {{ product.short_description }}
        </p>

        <!-- Key Architecture Highlights -->
        <div v-if="product.key_features && product.key_features.length" class="mt-3 flex flex-wrap gap-x-4 gap-y-1">
          <div
            v-for="(f, i) in product.key_features.slice(0, 3)"
            :key="i"
            class="flex items-center gap-1.5 text-xs text-slate-700"
          >
            <Check class="w-3.5 h-3.5 text-[#16A34A] flex-shrink-0" />
            <span>{{ f }}</span>
          </div>
        </div>
      </div>

      <!-- Action Footer -->
      <div class="mt-4 pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
          <button
            type="button"
            @click.stop="$emit('quick-view', product)"
            class="text-xs font-semibold text-slate-600 hover:text-[#1865C0] flex items-center gap-1"
          >
            <Eye class="w-3.5 h-3.5" />
            <span>Quick Preview</span>
          </button>
          <span class="text-slate-300">|</span>
          <button
            type="button"
            @click.stop="compare.toggleCompare(product)"
            class="text-xs font-semibold flex items-center gap-1"
            :class="compare.isInCompare(product.id) ? 'text-[#042562] font-bold' : 'text-slate-600 hover:text-[#042562]'"
          >
            <Scale class="w-3.5 h-3.5 text-[#1865C0]" />
            <span>{{ compare.isInCompare(product.id) ? 'Remove Compare' : 'Add to Compare' }}</span>
          </button>
        </div>

        <div class="flex items-center gap-3">
          <router-link
            :to="'/product/' + (product.slug || product.id)"
            class="text-xs font-bold text-[#1865C0] hover:text-[#042562] px-3 py-2 rounded-lg hover:bg-slate-50 transition-colors flex items-center gap-1"
          >
            <span>Datasheet & Specs</span>
            <ArrowRight class="w-3.5 h-3.5" />
          </router-link>

          <button
            @click="addToCart"
            class="inline-flex items-center gap-2 bg-[#042562] hover:bg-[#063485] text-white py-2 px-5 rounded-lg text-xs font-bold uppercase tracking-wider transition-colors shadow-sm"
          >
            <FileSpreadsheet class="w-4 h-4 text-blue-200" />
            <span>Add to RFQ</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useCartStore } from '@/stores/cartStore';
import { useCompareStore } from '@/stores/compareStore';
import { useNotificationStore } from '@/stores/notificationStore';
import {
  Check,
  Eye,
  Scale,
  FileSpreadsheet,
  ArrowRight,
} from 'lucide-vue-next';

const props = defineProps({
  product: {
    type: Object,
    required: true,
  },
  viewMode: {
    type: String,
    default: 'grid', // 'grid' | 'list'
  },
});

defineEmits(['quick-view']);

const cart = useCartStore();
const compare = useCompareStore();
const notify = useNotificationStore();

const addToCart = () => {
  cart.addItem(props.product, 1);
  notify.success(`"${props.product.name}" added to quotation cart.`);
};
</script>
