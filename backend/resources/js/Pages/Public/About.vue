<template>
  <PublicLayout>
    <section class="mx-auto max-w-3xl px-4 py-14">
      <h1 class="text-3xl font-black text-[#042562]">About iRACK IT Solution</h1>
      <p class="mt-4 text-sm leading-relaxed text-slate-600">
        iRACK IT Solution supplies and integrates datacenter hardware: rack servers, managed switching, enterprise storage, power protection, and server cabinets. The showroom lists only products that are published in the catalog, with prices and stock taken from the live inventory.
      </p>
      <p class="mt-4 text-sm leading-relaxed text-slate-600">
        Sales quotations start in the quotation cart. A submitted request is stored as an RFQ for the sales team. Staff continue to manage orders, inventory, and accounts from the staff portal.
      </p>
      <Link href="/contact" class="mt-6 inline-block text-sm font-bold text-[#1865C0]">Talk to technical sales</Link>
    </section>
  </PublicLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
</script>
