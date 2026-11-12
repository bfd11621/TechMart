<template>
  <div
    v-if="isOpen && product"
    class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 sm:p-6"
  >
    <!-- Backdrop -->
    <div
      class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
      @click="close"
    ></div>

    <!-- Modal Box -->
    <div
      class="relative bg-white rounded-2xl max-w-3xl w-full shadow-2xl border border-slate-200 overflow-hidden z-10 animate-in fade-in zoom-in-95 duration-200"
    >
      <!-- Header Bar -->
      <div class="bg-[#042562] text-white px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-2">
          <span class="text-xs font-bold uppercase tracking-widest text-[#1865C0] bg-white/10 px-2 py-0.5 rounded">
            Quick Technical Overview
          </span>
          <span class="text-xs text-slate-300 font-mono">SKU: {{ product.sku }}</span>
        </div>
        <button
          @click="close"
          class="text-slate-300 hover:text-white p-1 rounded-lg hover:bg-white/10 transition-colors"
        >
          <X class="w-5 h-5" />
        </button>
      </div>

      <!-- Content Grid -->
      <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Product Imagery & Badges -->
        <div class="flex flex-col items-center justify-center bg-[#F4F6FA] rounded-xl p-6 border border-slate-200">
          <div class="relative w-full aspect-square flex items-center justify-center">
            <img
              :src="product.primary_image_url || '/images/products/placeholder.png'"
              :alt="product.name"
              class="max-h-full max-w-full object-contain drop-shadow"
            />
            <span
              v-if="product.is_featured"
              class="absolute top-2 left-2 bg-[#E20913] text-white text-[10px] font-black uppercase px-2 py-0.5 rounded tracking-wider shadow"
            >
              Enterprise Pick
            </span>
          </div>

          <!-- Availability & Brand Pill -->
          <div class="w-full flex items-center justify-between mt-4 pt-3 border-t border-slate-200">
            <span class="text-xs font-bold text-[#042562]">
              {{ product.brand?.name || 'Manufacturer Certified' }}
            </span>
            <span
              :class="[
                'inline-flex items-center gap-1 text-xs font-bold px-2.5 py-0.5 rounded-full',
                product.availability_status === 'in_stock'
                  ? 'bg-emerald-100 text-emerald-800'
                  : 'bg-amber-100 text-amber-800',
              ]"
            >
              <CheckCircle2 v-if="product.availability_status === 'in_stock'" class="w-3.5 h-3.5" />
              <Clock v-else class="w-3.5 h-3.5" />
              {{ product.availability_label || (product.availability_status === 'in_stock' ? 'In Stock' : 'Pre-Order') }}
            </span>
          </div>
        </div>

        <!-- Specifications & Actions -->
        <div class="flex flex-col justify-between space-y-5">
          <div>
            <h3 class="text-xl font-black text-[#042562] leading-snug">
              {{ product.name }}
            </h3>
            <p class="text-xs font-mono text-slate-500 mt-1">
              Model: <span class="font-bold text-[#172033]">{{ product.model_number || 'N/A' }}</span>
              <span class="mx-2">•</span>
              Category: <span class="text-[#1865C0] font-semibold">{{ product.category?.name || 'Hardware' }}</span>
            </p>

            <p class="text-sm text-slate-600 mt-3 leading-relaxed">
              {{ product.short_description }}
            </p>

            <!-- Key Features Preview -->
            <div v-if="product.key_features && product.key_features.length" class="mt-4">
              <h4 class="text-xs font-bold uppercase tracking-wider text-[#042562] mb-2">
                Key Architecture Features
              </h4>
              <ul class="space-y-1.5 text-xs text-slate-700">
                <li
                  v-for="(feature, idx) in product.key_features.slice(0, 4)"
                  :key="idx"
                  class="flex items-start gap-2"
                >
                  <Check class="w-3.5 h-3.5 text-[#16A34A] flex-shrink-0 mt-0.5" />
                  <span>{{ feature }}</span>
                </li>
              </ul>
            </div>
          </div>

          <!-- Quantity Stepper & Actions -->
          <div class="pt-4 border-t border-slate-100 space-y-3">
            <div class="flex items-center gap-3">
              <div class="flex items-center border border-slate-300 rounded-lg overflow-hidden bg-white">
                <button
                  type="button"
                  @click="qty = Math.max(1, qty - 1)"
                  class="px-3 py-2 text-slate-600 hover:bg-slate-100 transition-colors font-bold"
                >
                  -
                </button>
                <input
                  type="number"
                  v-model.number="qty"
                  min="1"
                  class="w-14 text-center font-bold text-sm text-[#172033] focus:outline-none py-2"
                />
                <button
                  type="button"
                  @click="qty++"
                  class="px-3 py-2 text-slate-600 hover:bg-slate-100 transition-colors font-bold"
                >
                  +
                </button>
              </div>

              <button
                @click="addToCart"
                class="flex-1 inline-flex items-center justify-center gap-2 bg-[#E20913] hover:bg-[#be0710] text-white px-5 py-2.5 rounded-lg text-xs font-bold uppercase tracking-wider shadow transition-all transform hover:-translate-y-0.5"
              >
                <FileSpreadsheet class="w-4 h-4" />
                <span>Add to RFQ Cart</span>
              </button>
            </div>

            <div class="flex items-center justify-between text-xs pt-1">
              <router-link
                :to="'/product/' + (product.slug || product.id)"
                @click="close"
                class="text-[#1865C0] hover:text-[#042562] font-semibold flex items-center gap-1"
              >
                <span>View Full Technical Datasheet</span>
                <ArrowRight class="w-3.5 h-3.5" />
              </router-link>

              <button
                @click="toggleCompare"
                class="text-slate-600 hover:text-[#042562] font-semibold flex items-center gap-1"
              >
                <Scale class="w-3.5 h-3.5 text-[#1865C0]" />
                <span>{{ compare.isInCompare(product.id) ? 'Remove Compare' : 'Compare Model' }}</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useCartStore } from '@/stores/cartStore';
import { useCompareStore } from '@/stores/compareStore';
import { useNotificationStore } from '@/stores/notificationStore';
import {
  X,
  CheckCircle2,
  Clock,
  Check,
  FileSpreadsheet,
  ArrowRight,
  Scale,
} from 'lucide-vue-next';

const props = defineProps({
  isOpen: Boolean,
  product: Object,
});

const emit = defineEmits(['close']);

const cart = useCartStore();
const compare = useCompareStore();
const notify = useNotificationStore();

const qty = ref(1);

const close = () => {
  qty.value = 1;
  emit('close');
};

const addToCart = () => {
  if (!props.product) return;
  cart.addItem(props.product, qty.value);
  notify.success(`${qty.value}x "${props.product.name}" added to quotation cart.`);
  close();
};

const toggleCompare = () => {
  if (!props.product) return;
  compare.toggleCompare(props.product);
};
</script>
