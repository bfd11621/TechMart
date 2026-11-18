<template>
  <PublicLayout>
    <section class="mx-auto max-w-7xl px-4 py-12">
      <h1 class="text-3xl font-black text-[#042562]">Hardware categories</h1>
      <div class="mt-8 space-y-6">
        <article v-for="category in categories" :key="category.id" class="rounded-xl border border-slate-200 bg-white p-6">
          <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
              <h2 class="text-lg font-black text-[#042562]">{{ category.name }}</h2>
              <p v-if="category.description" class="mt-1 text-sm text-slate-500">{{ category.description }}</p>
            </div>
            <Link :href="`/catalog?category=${category.slug}`" class="text-xs font-bold uppercase tracking-wider text-[#1865C0]">
              {{ category.products_count || 0 }} products
            </Link>
          </div>
          <div v-if="category.children?.length" class="mt-4 flex flex-wrap gap-2">
            <Link
              v-for="child in category.children"
              :key="child.id"
              :href="`/catalog?category=${child.slug}`"
              class="rounded-full border border-slate-200 bg-[#F4F6FA] px-3 py-1 text-xs font-semibold text-[#042562]"
            >
              {{ child.name }}
            </Link>
          </div>
        </article>
      </div>
    </section>
  </PublicLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

defineProps({
  categories: { type: Array, default: () => [] },
});
</script>
