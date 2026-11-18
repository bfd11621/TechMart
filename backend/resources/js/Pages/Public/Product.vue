<template>
  <PublicLayout>
    <section class="mx-auto grid max-w-7xl gap-10 px-4 py-12 lg:grid-cols-2">
      <div class="flex min-h-[320px] items-center justify-center rounded-2xl border border-slate-200 bg-white p-8">
        <img v-if="image" :src="image" :alt="product.name" class="max-h-80 object-contain" />
        <div v-else class="flex h-40 w-40 items-center justify-center rounded-xl bg-[#042562] text-4xl font-black text-white">iR</div>
      </div>
      <div>
        <div class="text-xs font-bold uppercase tracking-wider text-[#1865C0]">
          {{ product.brand?.name }} · {{ product.category?.name }}
        </div>
        <h1 class="mt-2 text-3xl font-black text-[#042562]">{{ product.name }}</h1>
        <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ product.short_description }}</p>
        <div class="mt-6 text-2xl font-black text-[#042562]">{{ money(product.base_price) }}</div>
        <div class="mt-1 text-sm text-slate-500">SKU {{ product.sku }} · {{ product.available_stock || 0 }} available</div>
        <div class="mt-6 flex flex-wrap gap-3">
          <button type="button" class="rounded-lg bg-[#042562] px-5 py-3 text-xs font-bold uppercase tracking-wider text-white" @click="cart.addItem(product)">
            Add to quotation cart
          </button>
          <button type="button" class="rounded-lg border border-slate-300 px-5 py-3 text-xs font-bold uppercase tracking-wider" @click="compare.toggle(product)">
            {{ compare.isInCompare(product.id) ? 'Remove from compare' : 'Compare' }}
          </button>
          <a v-if="product.datasheet_url" :href="product.datasheet_url" class="rounded-lg border border-slate-300 px-5 py-3 text-xs font-bold uppercase tracking-wider" target="_blank">
            Datasheet
          </a>
        </div>
      </div>
    </section>

    <section v-if="product.full_description" class="mx-auto max-w-7xl px-4 pb-8">
      <h2 class="text-xl font-black text-[#042562]">Description</h2>
      <p class="mt-3 max-w-3xl whitespace-pre-line text-sm leading-relaxed text-slate-600">{{ product.full_description }}</p>
    </section>

    <section v-if="product.specifications?.length" class="mx-auto max-w-7xl px-4 pb-12">
      <h2 class="text-xl font-black text-[#042562]">Specifications</h2>
      <div class="mt-4 overflow-hidden rounded-xl border border-slate-200 bg-white">
        <div v-for="spec in product.specifications" :key="spec.id" class="grid grid-cols-1 border-b border-slate-100 px-4 py-3 text-sm last:border-0 sm:grid-cols-3">
          <div class="font-semibold text-slate-500">{{ spec.group }}</div>
          <div class="font-bold text-[#042562]">{{ spec.name }}</div>
          <div>{{ spec.value }}</div>
        </div>
      </div>
    </section>

    <section v-if="related.length" class="mx-auto max-w-7xl px-4 pb-16">
      <h2 class="mb-6 text-xl font-black text-[#042562]">Related models</h2>
      <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <ProductCard v-for="item in related" :key="item.id" :product="item" />
      </div>
    </section>
  </PublicLayout>
</template>

<script setup>
import { computed } from 'vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';
import { useQuotationCart } from '@/composables/useQuotationCart';
import { useCompare } from '@/composables/useCompare';
import { money } from '@/composables/format';

const props = defineProps({
  product: { type: Object, required: true },
  related: { type: Array, default: () => [] },
});

const cart = useQuotationCart();
const compare = useCompare();
const image = computed(() => props.product.primary_image || props.product.images?.[0]?.url || null);
</script>
