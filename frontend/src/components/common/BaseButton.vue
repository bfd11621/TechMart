<template>
  <button
    :type="type"
    :disabled="disabled || loading"
    :class="[
      'inline-flex items-center justify-center font-medium transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 select-none',
      sizeClasses[size],
      variantClasses[variant],
      (disabled || loading) ? 'opacity-60 cursor-not-allowed' : 'active:scale-[0.98]',
      customClass
    ]"
    @click="$emit('click', $event)"
  >
    <svg
      v-if="loading"
      class="animate-spin -ml-1 mr-2 h-4 w-4 text-current"
      fill="none"
      viewBox="0 0 24 24"
    >
      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>
    <slot name="prefix"></slot>
    <span><slot></slot></span>
    <slot name="suffix"></slot>
  </button>
</template>

<script setup>
defineProps({
  type: {
    type: String,
    default: 'button',
  },
  variant: {
    type: String,
    default: 'navy', // 'navy', 'blue', 'red', 'outline', 'ghost', 'secondary'
  },
  size: {
    type: String,
    default: 'md', // 'sm', 'md', 'lg'
  },
  loading: {
    type: Boolean,
    default: false,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  customClass: {
    type: String,
    default: '',
  },
});

defineEmits(['click']);

const sizeClasses = {
  sm: 'px-3 py-1.5 text-xs rounded-md gap-1.5',
  md: 'px-4 py-2 text-sm rounded-md gap-2',
  lg: 'px-6 py-2.5 text-base rounded-lg gap-2.5',
};

const variantClasses = {
  navy: 'bg-[#042562] hover:bg-[#021840] text-white focus:ring-[#042562]/50 shadow-sm',
  blue: 'bg-[#1865C0] hover:bg-[#104b91] text-white focus:ring-[#1865C0]/50 shadow-sm',
  red: 'bg-[#E20913] hover:bg-[#be0710] text-white focus:ring-[#E20913]/50 shadow-sm',
  outline: 'border border-[#CBD5E1] bg-white text-[#172033] hover:bg-[#F8FAFC] focus:ring-[#1865C0]/30',
  ghost: 'text-[#64748B] hover:text-[#172033] hover:bg-[#F1F5F9] focus:ring-slate-300',
  secondary: 'bg-[#E2E8F0] hover:bg-[#CBD5E1] text-[#172033] focus:ring-slate-400',
};
</script>
