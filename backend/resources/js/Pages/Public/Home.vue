<template>
  <PublicLayout>
    <section class="relative overflow-hidden bg-gradient-to-r from-[#042562] via-[#063485] to-[#1865C0] py-16 text-white">
      <div class="relative z-10 mx-auto grid max-w-7xl items-center gap-10 px-4 lg:grid-cols-12">
        <div class="space-y-6 lg:col-span-7">
          <div class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3.5 py-1 text-xs font-bold uppercase tracking-wider text-blue-100">
            <span class="h-2 w-2 rounded-full bg-[#E20913]"></span>
            Enterprise datacenter solutions
          </div>
          <h1 class="text-4xl font-black leading-tight lg:text-5xl">Next-generation server racks, compute nodes, and optical networks</h1>
          <p class="max-w-xl text-sm leading-relaxed text-blue-100">
            Equip your site with published hardware from the iRACK catalog. Prices, stock, and specifications come from the live database.
          </p>
          <div class="flex flex-wrap gap-3">
            <Link href="/catalog" class="rounded-lg bg-[#E20913] px-6 py-3 text-xs font-bold uppercase tracking-wider text-white">Browse the showroom</Link>
            <Link href="/cart" class="rounded-lg border border-white/30 bg-white/10 px-6 py-3 text-xs font-bold uppercase tracking-wider">Quotation cart</Link>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-3 rounded-2xl bg-white p-6 text-[#172033] lg:col-span-5">
          <div class="rounded-xl border border-slate-200 bg-[#F4F6FA] p-4">
            <div class="text-2xl font-black text-[#042562]">{{ counts.categories }}</div>
            <div class="text-xs font-bold">Hardware categories</div>
          </div>
          <div class="rounded-xl border border-slate-200 bg-[#F4F6FA] p-4">
            <div class="text-2xl font-black text-[#1865C0]">{{ counts.products }}</div>
            <div class="text-xs font-bold">Published products</div>
          </div>
          <div class="rounded-xl border border-slate-200 bg-[#F4F6FA] p-4">
            <div class="text-2xl font-black text-[#E20913]">{{ counts.brands }}</div>
            <div class="text-xs font-bold">Active brands</div>
          </div>
          <div class="rounded-xl border border-slate-200 bg-[#F4F6FA] p-4">
            <div class="text-2xl font-black text-[#042562]">3</div>
            <div class="text-xs font-bold">Fulfillment hubs</div>
          </div>
        </div>
      </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-14">
      <div class="mb-8 flex items-end justify-between">
        <div>
          <div class="text-xs font-bold uppercase tracking-widest text-[#1865C0]">Showroom classifications</div>
          <h2 class="text-3xl font-extrabold text-[#042562]">Hardware categories</h2>
        </div>
        <Link href="/categories" class="text-xs font-bold text-[#1865C0]">View all</Link>
      </div>
      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <Link
          v-for="category in categories"
          :key="category.id"
          :href="`/catalog?category=${category.slug}`"
          class="rounded-xl border border-slate-200 bg-white p-5 shadow-card hover:border-[#1865C0]"
        >
          <div class="text-sm font-black text-[#042562]">{{ category.name }}</div>
          <div class="mt-1 text-xs text-slate-500">{{ category.products_count || 0 }} published models</div>
        </Link>
      </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 pb-16">
      <div class="mb-8 flex items-end justify-between">
        <h2 class="text-3xl font-extrabold text-[#042562]">Catalog highlights</h2>
        <Link href="/catalog" class="text-xs font-bold text-[#1865C0]">Open catalog</Link>
      </div>
      <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <ProductCard v-for="product in products" :key="product.id" :product="product" />
      </div>
    </section>
  </PublicLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';

defineProps({
  categories: { type: Array, default: () => [] },
  products: { type: Array, default: () => [] },
  counts: { type: Object, default: () => ({ categories: 0, products: 0, brands: 0 }) },
});
</script>
