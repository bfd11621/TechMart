<template>
  <PublicLayout>
    <section class="mx-auto max-w-7xl px-4 py-12">
      <h1 class="text-3xl font-black text-[#042562]">Authorized brands</h1>
      <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <Link
          v-for="brand in brands"
          :key="brand.id"
          :href="`/catalog?brand=${brand.slug}`"
          class="rounded-xl border border-slate-200 bg-white p-5 shadow-card"
        >
          <div class="text-lg font-black text-[#042562]">{{ brand.name }}</div>
          <div class="mt-1 text-xs text-slate-500">{{ brand.products_count || 0 }} published products</div>
          <p v-if="brand.description" class="mt-3 text-sm text-slate-600">{{ brand.description }}</p>
        </Link>
      </div>
    </section>
  </PublicLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

defineProps({
  brands: { type: Array, default: () => [] },
});
</script>
