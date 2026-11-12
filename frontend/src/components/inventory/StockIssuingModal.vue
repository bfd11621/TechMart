<template>
  <BaseModal
    :model-value="modelValue"
    @update:model-value="$emit('update:modelValue', $event)"
    title="Stock Issuing — Goods Outward"
    subtitle="Issue inventory from store for demo, RMA, deployment, or write-off with strict negative-stock protection."
    max-width="2xl"
  >
    <form @submit.prevent="handleSubmit" class="space-y-4">
      <!-- Source Store Selection -->
      <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-[#042562] mb-1">
          Source Store / Branch *
        </label>
        <select
          v-model="form.source_store_id"
          required
          class="w-full text-xs font-medium rounded-lg border border-slate-300 px-3 py-2.5 focus:border-[#1865C0] focus:ring-1 focus:ring-[#1865C0] bg-white text-slate-800"
        >
          <option :value="null" disabled>Select source branch...</option>
          <option v-for="store in stores" :key="store.id" :value="store.id">
            {{ store.name }} ({{ store.code }})
          </option>
        </select>
      </div>

      <!-- Product Selection -->
      <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-[#042562] mb-1">
          Hardware Product to Issue *
        </label>
        <select
          v-model="form.product_id"
          required
          class="w-full text-xs font-medium rounded-lg border border-slate-300 px-3 py-2.5 focus:border-[#1865C0] focus:ring-1 focus:ring-[#1865C0] bg-white text-slate-800"
        >
          <option :value="null" disabled>Select hardware model...</option>
          <option v-for="prod in products" :key="prod.id" :value="prod.id">
            [{{ prod.sku }}] {{ prod.name }}
          </option>
        </select>
      </div>

      <!-- Stock Availability Indicator Banner -->
      <div
        class="p-3 rounded-lg border flex items-center justify-between text-xs"
        :class="availableStock > 0 ? 'bg-blue-50/70 border-blue-200 text-blue-900' : 'bg-red-50 border-red-200 text-red-800'"
      >
        <div class="flex items-center gap-2">
          <Boxes class="w-4 h-4 text-[#1865C0]" />
          <span>Available on-hand at selected branch:</span>
        </div>
        <div class="font-extrabold text-sm">
          <span :class="availableStock > 0 ? 'text-[#042562]' : 'text-red-600'">
            {{ availableStock }} units
          </span>
        </div>
      </div>

      <!-- Issued Quantity & Reason -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-[#042562] mb-1">
            Quantity to Issue *
          </label>
          <input
            type="number"
            v-model.number="form.quantity"
            :max="availableStock"
            min="1"
            required
            placeholder="e.g. 1"
            class="w-full text-xs font-bold rounded-lg border border-slate-300 px-3 py-2.5 focus:border-[#1865C0] focus:ring-1 focus:ring-[#1865C0] bg-white text-slate-900"
          />
          <p v-if="form.quantity > availableStock" class="text-[11px] text-red-600 mt-1 font-semibold">
            Cannot issue more than available stock ({{ availableStock }} units max).
          </p>
        </div>

        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-[#042562] mb-1">
            Issue Reason *
          </label>
          <select
            v-model="form.issue_reason"
            required
            class="w-full text-xs font-medium rounded-lg border border-slate-300 px-3 py-2.5 focus:border-[#1865C0] focus:ring-1 focus:ring-[#1865C0] bg-white text-slate-800"
          >
            <option value="Customer Project Whitespace Deployment">Customer Project Whitespace Deployment</option>
            <option value="OEM RMA Defective Return">OEM RMA Defective Return</option>
            <option value="Internal Engineering Lab / Demo">Internal Engineering Lab / Demo</option>
            <option value="Warranty SLA Emergency Replacement">Warranty SLA Emergency Replacement</option>
            <option value="Damaged / Scrap Write-off">Damaged / Scrap Write-off</option>
            <option value="Consignment Staging">Consignment Staging</option>
          </select>
        </div>
      </div>

      <!-- Reference Number -->
      <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-[#042562] mb-1">
          Issue Reference / RMA Ticket #
        </label>
        <input
          type="text"
          v-model="form.reference_number"
          placeholder="e.g. RMA-2026-0981 or TKT-PROJ-44"
          class="w-full text-xs font-medium rounded-lg border border-slate-300 px-3 py-2.5 focus:border-[#1865C0] focus:ring-1 focus:ring-[#1865C0] bg-white text-slate-900"
        />
      </div>

      <!-- Notes -->
      <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-[#042562] mb-1">
          Internal Issuing Notes
        </label>
        <textarea
          v-model="form.notes"
          rows="2"
          placeholder="Authorized by engineering manager; serial numbers dispatched..."
          class="w-full text-xs rounded-lg border border-slate-300 p-2.5 focus:border-[#1865C0] focus:ring-1 focus:ring-[#1865C0] bg-white text-slate-800"
        ></textarea>
      </div>

      <!-- Error Alert -->
      <div v-if="error" class="p-3 rounded-lg bg-red-50 border border-red-200 text-xs text-red-700 font-medium">
        {{ error }}
      </div>

      <!-- Modal Actions -->
      <div class="pt-4 border-t border-slate-200 flex items-center justify-end gap-3">
        <button
          type="button"
          @click="$emit('update:modelValue', false)"
          class="px-4 py-2 rounded-lg border border-slate-300 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors"
        >
          Cancel
        </button>
        <button
          type="submit"
          :disabled="loading || availableStock <= 0 || form.quantity > availableStock"
          class="px-5 py-2 rounded-lg bg-[#DC2626] hover:bg-red-700 text-white text-xs font-bold flex items-center gap-2 shadow-sm transition-colors disabled:opacity-50"
        >
          <ArrowUpRight class="w-4 h-4" />
          <span>{{ loading ? 'Issuing Stock...' : 'Confirm Stock Issuance' }}</span>
        </button>
      </div>
    </form>
  </BaseModal>
</template>

<script setup>
import { computed, reactive, ref, watch } from 'vue';
import BaseModal from '@/components/common/BaseModal.vue';
import inventoryService from '@/services/inventoryService';
import { useNotificationStore } from '@/stores/notificationStore';
import { Boxes, ArrowUpRight } from 'lucide-vue-next';

const props = defineProps({
  modelValue: Boolean,
  stores: {
    type: Array,
    default: () => [],
  },
  products: {
    type: Array,
    default: () => [],
  },
  inventories: {
    type: Array,
    default: () => [],
  },
  preselectedStoreId: {
    type: Number,
    default: null,
  },
  preselectedProductId: {
    type: Number,
    default: null,
  },
});

const emit = defineEmits(['update:modelValue', 'issued']);
const notify = useNotificationStore();

const loading = ref(false);
const error = ref('');

const form = reactive({
  source_store_id: null,
  product_id: null,
  quantity: 1,
  issue_reason: 'Customer Project Whitespace Deployment',
  reference_number: '',
  notes: '',
});

// Compute available stock at selected branch
const availableStock = computed(() => {
  if (!form.source_store_id || !form.product_id) return 0;
  const match = props.inventories.find(
    (i) => i.store_id === form.source_store_id && i.product_id === form.product_id
  );
  return match ? match.available_quantity : 0;
});

watch(
  () => props.modelValue,
  (open) => {
    if (open) {
      error.value = '';
      form.source_store_id = props.preselectedStoreId || (props.stores[0]?.id ?? null);
      form.product_id = props.preselectedProductId || (props.products[0]?.id ?? null);
      form.quantity = 1;
      form.reference_number = '';
      form.notes = '';
    }
  }
);

const handleSubmit = async () => {
  if (form.quantity > availableStock.value) {
    error.value = `Cannot issue more than available stock (${availableStock.value} units).`;
    return;
  }

  loading.value = true;
  error.value = '';

  try {
    const res = await inventoryService.issueStock({
      source_store_id: form.source_store_id,
      product_id: form.product_id,
      quantity: form.quantity,
      issue_reason: form.issue_reason,
      reference_number: form.reference_number,
      notes: form.notes,
    });

    notify.success(res.message || 'Stock successfully issued.');
    emit('update:modelValue', false);
    emit('issued');
  } catch (err) {
    console.error('Issue stock failed:', err);
    error.value = err.response?.data?.message || err.message || 'Failed to issue stock.';
  } finally {
    loading.value = false;
  }
};
</script>
