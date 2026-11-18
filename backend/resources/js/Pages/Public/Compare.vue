<template>
  <PublicLayout>
    <section class="mx-auto max-w-7xl px-4 py-12">
      <h1 class="text-3xl font-black text-[#042562]">Compare models</h1>
      <div v-if="compare.items.length" class="mt-8 overflow-x-auto rounded-xl border border-slate-200 bg-white">
        <table class="min-w-full text-sm">
          <thead>
            <tr>
              <th class="p-4 text-left text-xs uppercase text-slate-500">Field</th>
              <th v-for="item in compare.items" :key="item.id" class="p-4 text-left">
                <Link :href="`/product/${item.slug}`" class="font-bold text-[#042562]">{{ item.name }}</Link>
              </th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="row in rows" :key="row.label" class="border-t border-slate-100">
              <td class="p-4 font-semibold text-slate-500">{{ row.label }}</td>
              <td v-for="item in compare.items" :key="item.id" class="p-4">{{ row.value(item) }}</td>
            </tr>
            <tr class="border-t border-slate-100">
              <td class="p-4"></td>
              <td v-for="item in compare.items" :key="item.id" class="p-4">
                <button type="button" class="text-xs font-bold text-[#E20913]" @click="compare.remove(item.id)">Remove</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <p v-else class="mt-6 text-sm text-slate-500">Choose Compare on a product page. You can keep up to four models.</p>
    </section>
  </PublicLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { useCompare } from '@/composables/useCompare';
import { money } from '@/composables/format';

const compare = useCompare();
const rows = [
  { label: 'SKU', value: (item) => item.sku },
  { label: 'Brand', value: (item) => item.brand || '—' },
  { label: 'Category', value: (item) => item.category || '—' },
  { label: 'Price', value: (item) => money(item.base_price) },
  { label: 'Stock', value: (item) => item.total_stock },
  { label: 'Warranty', value: (item) => item.warranty_info || '—' },
];
</script>
