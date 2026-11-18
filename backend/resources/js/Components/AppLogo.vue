<template>
  <div :class="['inline-flex items-center select-none', layoutClasses, customClass]">
    <!-- Monogram Symbol (iR) -->
    <div :class="['relative flex-shrink-0 flex items-center justify-center', symbolSizeClasses]">
      <svg
        viewBox="0 0 200 220"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
        class="w-full h-full drop-shadow-sm transition-transform duration-200 group-hover:scale-105"
      >
        <!-- Red Dot for "i" -->
        <circle cx="50" cy="34" r="20" fill="#E20913" />

        <!-- Vertical Navy Stem for "i" -->
        <rect
          x="32"
          y="68"
          width="36"
          height="136"
          rx="4"
          :fill="isDarkVariant ? '#FFFFFF' : '#042562'"
        />

        <!-- Corporate Blue Curved Loop for "R" -->
        <path
          d="M68 68H126C156 68 172 84 172 106C172 128 156 144 126 144H68V68ZM68 98V114H118C132 114 142 111 142 106C142 101 132 98 118 98H68Z"
          fill-rule="evenodd"
          clip-rule="evenodd"
          fill="#1865C0"
        />

        <!-- Navy Diagonal Leg for "R" -->
        <path
          d="M94 124L164 204H122L68 138V124H94Z"
          :fill="isDarkVariant ? '#FFFFFF' : '#042562'"
        />
      </svg>
    </div>

    <!-- Wordmark / Typography (iRACK TECH-MART) -->
    <div v-if="!iconOnly" :class="['flex flex-col', textAlignmentClasses]">
      <!-- iRACK line -->
      <div class="flex items-baseline leading-none tracking-tight font-black font-sans">
        <!-- Red stylized 'i' -->
        <span
          class="text-[#E20913] font-serif font-black inline-block transform -translate-y-0.5"
          :class="irackTextSize"
        >
          i
        </span>
        <!-- RACK in Navy / White -->
        <span
          class="font-black uppercase tracking-tight font-sans"
          :class="[isDarkVariant ? 'text-white' : 'text-[#042562]', irackTextSize]"
        >
          RACK
        </span>
      </div>

      <!-- TECH-MART line -->
      <div
        class="font-extrabold uppercase tracking-[0.28em] text-[0.62em] leading-tight font-sans transition-colors"
        :class="isDarkVariant ? 'text-blue-200' : 'text-[#042562]'"
      >
        TECH-MART
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  variant: {
    type: String,
    default: 'dark', // 'dark' = for light backgrounds (navy text), 'light' = for dark backgrounds (white text)
    validator: (v) => ['dark', 'light'].includes(v),
  },
  layout: {
    type: String,
    default: 'horizontal', // 'horizontal', 'stacked'
    validator: (v) => ['horizontal', 'stacked'].includes(v),
  },
  size: {
    type: String,
    default: 'md', // 'sm', 'md', 'lg', 'xl'
    validator: (v) => ['sm', 'md', 'lg', 'xl'].includes(v),
  },
  iconOnly: {
    type: Boolean,
    default: false,
  },
  customClass: {
    type: String,
    default: '',
  },
});

const isDarkVariant = computed(() => props.variant === 'light');

const layoutClasses = computed(() => {
  if (props.layout === 'stacked') {
    return 'flex-col gap-2 items-center text-center';
  }
  return 'flex-row gap-3 items-center';
});

const textAlignmentClasses = computed(() => {
  if (props.layout === 'stacked') {
    return 'items-center text-center';
  }
  return 'items-start justify-center';
});

const symbolSizeClasses = computed(() => {
  switch (props.size) {
    case 'sm':
      return 'w-8 h-9';
    case 'lg':
      return 'w-14 h-16';
    case 'xl':
      return 'w-20 h-24';
    case 'md':
    default:
      return 'w-10 h-11';
  }
});

const irackTextSize = computed(() => {
  switch (props.size) {
    case 'sm':
      return 'text-lg';
    case 'lg':
      return 'text-3xl';
    case 'xl':
      return 'text-4xl';
    case 'md':
    default:
      return 'text-2xl';
  }
});
</script>
