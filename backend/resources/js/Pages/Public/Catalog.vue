<template>
  <PublicLayout>
    <section class="border-b border-slate-200 bg-white">
      <div class="mx-auto max-w-7xl px-4 py-8">
        <h1 class="text-3xl font-black text-[#042562]">Hardware catalog</h1>
        <p class="mt-1 text-sm text-slate-500">{{ pagination.total }} published products</p>
        <form class="mt-6 grid gap-3 md:grid-cols-4" @submit.prevent="applyFilters">
          <input v-model="form.search" type="search" placeholder="Name, SKU, or model" class="rounded-lg border border-slate-300 px-3 py-2 text-sm" />
          <select v-model="form.category" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <option value="">All categories</option>
            <option v-for="category in categories" :key="category.id" :value="category.slug">{{ category.name }}</option>
          </select>
          <select v-model="form.brand" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <option value="">All brands</option>
            <option v-for="brand in brands" :key="brand.id" :value="brand.slug">{{ brand.name }}</option>
          </select>
          <select v-model="form.sort" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <option value="newest">Newest</option>
            <option value="price_asc">Price: low to high</option>
            <option value="price_desc">Price: high to low</option>
            <option value="name_asc">Name</option>
          </select>
          <button type="submit" class="rounded-lg bg-[#042562] px-4 py-2 text-xs font-bold uppercase tracking-wider text-white md:col-span-4 md:w-fit">
            Apply filters
          </button>
        </form>
      </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-10">
      <div v-if="products.length" class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <ProductCard v-for="product in products" :key="product.id" :product="product" />
      </div>
      <p v-else class="rounded-xl border border-dashed border-slate-300 bg-white p-10 text-center text-sm text-slate-500">
        No published products match these filters.
      </p>

      <div v-if="pagination.last_page > 1" class="mt-8 flex gap-2">
        <Link
          v-for="page in pagination.last_page"
          :key="page"
          :href="pageHref(page)"
          class="rounded border px-3 py-1 text-sm"
          :class="page === pagination.current_page ? 'border-[#042562] bg-[#042562] text-white' : 'border-slate-300 bg-white'"
        >
          {{ page }}
        </Link>
      </div>
    </section>
  </PublicLayout>
</template>

<script setup>
import { reactive } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';

const props = defineProps({
  products: { type: Array, default: () => [] },
  categories: { type: Array, default: () => [] },
  brands: { type: Array, default: () => [] },
  filters: { type: Object, default: () => ({}) },
  pagination: { type: Object, default: () => ({ current_page: 1, last_page: 1, total: 0 }) },
});

const form = reactive({
  search: props.filters.search || '',
  category: props.filters.category || '',
  brand: props.filters.brand || '',
  sort: props.filters.sort || 'newest',
});

function applyFilters() {
  router.get('/catalog', { ...form }, { preserveState: true, replace: true });
}

function pageHref(page) {
  const params = new URLSearchParams({ ...form, page: String(page) });
  return `/catalog?${params.toString()}`;
}
</script>
