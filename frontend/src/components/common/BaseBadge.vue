<template>
  <span
    :class="[
      'inline-flex items-center font-medium rounded-full',
      sizeClasses[size],
      variantClasses[variant]
    ]"
  >
    <span
      v-if="dot"
      :class="['w-1.5 h-1.5 rounded-full mr-1.5', dotClasses[variant]]"
    ></span>
    <slot></slot>
  </span>
</template>

<script setup>
defineProps({
  variant: {
    type: String,
    default: 'navy', // 'navy', 'blue', 'success', 'warning', 'error', 'gray'
  },
  size: {
    type: String,
    default: 'md', // 'sm', 'md'
  },
  dot: {
    type: Boolean,
    default: false,
  },
});

const sizeClasses = {
  sm: 'px-2 py-0.5 text-xs',
  md: 'px-2.5 py-1 text-xs',
};

const variantClasses = {
  navy: 'bg-[#042562]/10 text-[#042562] border border-[#042562]/20',
  blue: 'bg-[#1865C0]/10 text-[#1865C0] border border-[#1865C0]/20',
  success: 'bg-[#16A34A]/10 text-[#16A34A] border border-[#16A34A]/20',
  warning: 'bg-[#F59E0B]/10 text-[#D97706] border border-[#F59E0B]/20',
  error: 'bg-[#DC2626]/10 text-[#DC2626] border border-[#DC2626]/20',
  gray: 'bg-slate-100 text-slate-700 border border-slate-200',
};

const dotClasses = {
  navy: 'bg-[#042562]',
  blue: 'bg-[#1865C0]',
  success: 'bg-[#16A34A]',
  warning: 'bg-[#F59E0B]',
  error: 'bg-[#DC2626]',
  gray: 'bg-slate-400',
};
</script>
