<template>
  <PublicLayout>
    <div class="bg-[#042562] py-16 text-white">
      <div class="mx-auto max-w-7xl px-4">
        <div class="mb-3 flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-blue-300">
          <router-link to="/" class="hover:text-white">Home</router-link>
          <span>/</span>
          <span>Registration</span>
        </div>
        <h1 class="text-3xl font-black sm:text-4xl">Register your company</h1>
        <p class="mt-2 max-w-2xl text-sm text-blue-100">
          Tell the sales desk who you are and what you procure. We use this to prepare quotations and deliveries. It does not create a staff login.
        </p>
      </div>
    </div>

    <div class="mx-auto grid max-w-7xl gap-10 px-4 py-16 lg:grid-cols-2">
      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <CustomerRegistrationForm />
      </div>
      <div class="space-y-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 text-sm text-slate-600">
          <h2 class="text-lg font-black text-[#042562]">Office</h2>
          <p class="mt-2">3rd Floor, Wastna Commercial Building, General Wingate St, Addis Ababa</p>
          <p class="mt-2 font-bold text-[#042562]">+251 94 642 8560</p>
          <p class="font-bold text-[#1865C0]">dagimw@iracktech.com</p>
          <p class="mt-2 text-xs text-slate-500">Monday to Saturday, 8:30 AM – 6:00 PM EAT.</p>
        </div>
        <OfficeMap />
      </div>
    </div>
  </PublicLayout>
</template>

<script setup>
import PublicLayout from '@/layouts/PublicLayout.vue';
import OfficeMap from '@/components/public/OfficeMap.vue';
import CustomerRegistrationForm from '@/components/public/CustomerRegistrationForm.vue';
</script>
