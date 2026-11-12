<template>
  <div class="fixed bottom-5 right-5 z-50 flex flex-col space-y-3 max-w-md w-full pointer-events-none">
    <transition-group
      enter-active-class="transform ease-out duration-300 transition"
      enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
      enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
      leave-active-class="transition ease-in duration-200"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-for="notification in store.notifications"
        :key="notification.id"
        class="pointer-events-auto flex items-start p-4 rounded-lg shadow-lg border bg-white"
        :class="borderClasses[notification.type]"
      >
        <div class="flex-shrink-0 mr-3 mt-0.5">
          <component :is="iconComponents[notification.type]" :class="['w-5 h-5', iconClasses[notification.type]]" />
        </div>
        <div class="flex-1 mr-2 text-sm text-[#172033] font-normal leading-relaxed">
          {{ notification.message }}
        </div>
        <button
          @click="store.removeNotification(notification.id)"
          class="flex-shrink-0 text-slate-400 hover:text-slate-600 transition-colors p-1"
        >
          <X class="w-4 h-4" />
        </button>
      </div>
    </transition-group>
  </div>
</template>

<script setup>
import { useNotificationStore } from '@/stores/notificationStore';
import { CheckCircle2, AlertCircle, AlertTriangle, Info, X } from 'lucide-vue-next';

const store = useNotificationStore();

const iconComponents = {
  success: CheckCircle2,
  error: AlertCircle,
  warning: AlertTriangle,
  info: Info,
};

const iconClasses = {
  success: 'text-[#16A34A]',
  error: 'text-[#DC2626]',
  warning: 'text-[#F59E0B]',
  info: 'text-[#1865C0]',
};

const borderClasses = {
  success: 'border-[#16A34A]/30',
  error: 'border-[#DC2626]/30',
  warning: 'border-[#F59E0B]/30',
  info: 'border-[#1865C0]/30',
};
</script>
