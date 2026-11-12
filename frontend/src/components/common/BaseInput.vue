<template>
  <div class="w-full">
    <label v-if="label" :for="id" class="block text-xs font-semibold text-[#172033] uppercase tracking-wider mb-1.5">
      {{ label }}
      <span v-if="required" class="text-[#E20913]">*</span>
    </label>
    <div class="relative rounded-md shadow-sm">
      <div v-if="$slots.prefix" class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
        <slot name="prefix"></slot>
      </div>
      <input
        :id="id"
        :type="type"
        :value="modelValue"
        :placeholder="placeholder"
        :disabled="disabled"
        :required="required"
        @input="$emit('update:modelValue', $event.target.value)"
        :class="[
          'block w-full rounded-md border text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-[#1865C0]/30',
          $slots.prefix ? 'pl-9' : 'pl-3',
          $slots.suffix ? 'pr-9' : 'pr-3',
          'py-2',
          error
            ? 'border-[#DC2626] text-[#DC2626] focus:border-[#DC2626] focus:ring-[#DC2626]/20'
            : 'border-[#CBD5E1] text-[#172033] focus:border-[#1865C0] bg-white',
          disabled ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : ''
        ]"
      />
      <div v-if="$slots.suffix" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400">
        <slot name="suffix"></slot>
      </div>
    </div>
    <p v-if="error" class="mt-1 text-xs text-[#DC2626]">{{ error }}</p>
    <p v-else-if="hint" class="mt-1 text-xs text-slate-500">{{ hint }}</p>
  </div>
</template>

<script setup>
defineProps({
  modelValue: {
    type: [String, Number],
    default: '',
  },
  id: {
    type: String,
    default: () => 'input-' + Math.random().toString(36).substring(2, 9),
  },
  label: {
    type: String,
    default: '',
  },
  type: {
    type: String,
    default: 'text',
  },
  placeholder: {
    type: String,
    default: '',
  },
  error: {
    type: String,
    default: '',
  },
  hint: {
    type: String,
    default: '',
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  required: {
    type: Boolean,
    default: false,
  },
});

defineEmits(['update:modelValue']);
</script>
