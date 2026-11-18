<template>
  <PublicLayout>
    <section class="mx-auto max-w-7xl px-4 py-14">
      <h1 class="text-3xl font-black text-[#042562]">Turnkey IT services</h1>
      <div class="mt-8 grid gap-4 md:grid-cols-2">
        <article v-for="service in services" :key="service.title" class="rounded-xl border border-slate-200 bg-white p-6">
          <h2 class="text-lg font-black text-[#042562]">{{ service.title }}</h2>
          <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ service.body }}</p>
        </article>
      </div>
      <Link href="/contact" class="mt-8 inline-block rounded-lg bg-[#042562] px-5 py-3 text-xs font-bold uppercase tracking-wider text-white">
        Request a consultation
      </Link>
    </section>
  </PublicLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

const services = [
  { title: 'Rack and compute design', body: 'Bill of materials for servers, switching, and storage sized to the room and the workload.' },
  { title: 'Power and cooling', body: 'UPS, PDU, and cabinet planning so the rack can be installed without a second site visit.' },
  { title: 'Multi-store fulfillment', body: 'Stock is tracked per branch, so a quotation can be filled from the hub that actually has the units.' },
  { title: 'Deployment support', body: 'Installation, labeling, and handover documentation after the hardware arrives.' },
];
</script>
