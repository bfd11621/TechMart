<template>
  <div id="app-root" class="min-h-screen text-[#172033]">
    <router-view />
    <!-- Global Enterprise Toast Notifications -->
    <BaseToast />
  </div>
</template>

<script setup>
import BaseToast from '@/components/common/BaseToast.vue';
</script>
