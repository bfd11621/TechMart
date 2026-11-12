<template>
  <div class="min-h-screen bg-[#F4F6FA] flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <!-- Corporate Logo -->
      <div class="flex justify-center">
        <router-link to="/" class="flex flex-col items-center">
          <AppLogo variant="dark" size="lg" layout="stacked" />
          <span class="mt-2 text-[10px] px-2 py-0.5 rounded bg-[#042562] text-white uppercase font-black tracking-widest">
            Enterprise Security Gateway
          </span>
        </router-link>
      </div>
      <h2 class="mt-6 text-center text-xl font-extrabold text-[#042562]">
        Administrative Staff Sign In
      </h2>
      <p class="mt-1 text-center text-xs text-[#64748B]">
        Authorized personnel only. Multi-store inventory & sales records.
      </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-8 px-6 shadow-xl rounded-xl border border-slate-200 sm:px-10">
        <slot></slot>
      </div>

      <div class="mt-6 text-center">
        <router-link to="/" class="text-xs text-[#1865C0] hover:text-[#042562] font-semibold flex items-center justify-center gap-1">
          <span>← Back to Public Product Showroom</span>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import AppLogo from '@/components/common/AppLogo.vue';
</script>
