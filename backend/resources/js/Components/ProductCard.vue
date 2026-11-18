<template>
  <article class="flex h-full flex-col overflow-hidden rounded-xl border border-slate-200 bg-white shadow-card transition hover:shadow-card-hover">
    <div class="relative flex min-h-[200px] items-center justify-center border-b border-slate-100 bg-[#F8FAFC] p-6">
      <span
        v-if="product.is_featured"
        class="absolute left-3 top-3 rounded bg-[#E20913] px-2 py-0.5 text-[10px] font-black uppercase tracking-wider text-white"
      >
        Featured
      </span>
      <Link :href="`/product/${product.slug}`" class="flex h-full w-full items-center justify-center">
        <img
          v-if="product.primary_image_url"
          :src="product.primary_image_url"
          :alt="product.name"
          class="max-h-36 max-w-full object-contain"
        />
        <div v-else class="flex h-28 w-28 items-center justify-center rounded-lg bg-[#042562] text-2xl font-black text-white">
          iR
        </div>
      </Link>
    </div>

    <div class="flex flex-1 flex-col gap-3 p-4">
      <div class="text-[11px] font-bold uppercase tracking-wider text-[#1865C0]">
        {{ product.brand?.name || 'iRACK' }} · {{ product.category?.name || 'Hardware' }}
      </div>
      <Link :href="`/product/${product.slug}`" class="text-sm font-bold leading-snug text-[#042562] hover:text-[#1865C0]">
        {{ product.name }}
      </Link>
      <div class="text-[11px] text-slate-500">SKU {{ product.sku }}</div>
      <div class="mt-auto flex items-end justify-between gap-3 pt-2">
        <div>
          <div class="text-sm font-black text-[#042562]">{{ money(product.base_price) }}</div>
          <div class="text-[11px] font-semibold text-slate-500">{{ stockLabel(product) }} · {{ product.total_stock || 0 }} units</div>
        </div>
        <button
          type="button"
          class="rounded-lg bg-[#042562] px-3 py-2 text-[11px] font-bold uppercase tracking-wider text-white hover:bg-[#1865C0]"
          @click="cart.addItem(product)"
        >
          Add
        </button>
      </div>
    </div>
  </article>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { useQuotationCart } from '@/composables/useQuotationCart';
import { money, stockLabel } from '@/composables/format';

defineProps({
  product: { type: Object, required: true },
});

const cart = useQuotationCart();
</script>
