<template>
  <BaseModal
    :model-value="modelValue"
    @update:model-value="$emit('update:modelValue', $event)"
    title="Stock Adjustment — Physical Audit Reconciliation"
    subtitle="Reconcile recorded inventory balances against physical warehouse cycle counts with mandatory compliance rationale."
    max-width="2xl"
  >
    <form @submit.prevent="handleSubmit" class="space-y-4">
      <!-- Store Selection -->
      <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-[#042562] mb-1">
          Store Branch *
        </label>
        <select
          v-model="form.store_id"
          required
          class="w-full text-xs font-medium rounded-lg border border-slate-300 px-3 py-2.5 focus:border-[#1865C0] focus:ring-1 focus:ring-[#1865C0] bg-white text-slate-800"
        >
          <option :value="null" disabled>Select branch location...</option>
          <option v-for="store in stores" :key="store.id" :value="store.id">
            {{ store.name }} ({{ store.code }})
          </option>
        </select>
      </div>

      <!-- Product Selection -->
      <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-[#042562] mb-1">
          Hardware Product *
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

      <!-- Current Balance vs. New Balance & Delta Comparison Box -->
      <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 grid grid-cols-3 gap-4 text-center">
        <div>
          <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Current Balance</span>
          <span class="text-xl font-black text-[#042562] block mt-1">{{ currentQuantity }} units</span>
        </div>
        <div>
          <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">New Verified Count</span>
          <input
            type="number"
            v-model.number="form.new_quantity"
            min="0"
            required
            class="w-24 text-center font-black text-lg rounded-lg border border-slate-300 py-1 bg-white focus:border-[#1865C0] focus:ring-1 focus:ring-[#1865C0] mx-auto block mt-1"
          />
        </div>
        <div>
          <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Variance (Delta)</span>
          <span
            class="text-xl font-black block mt-1"
            :class="delta > 0 ? 'text-[#16A34A]' : (delta < 0 ? 'text-[#DC2626]' : 'text-slate-500')"
          >
            {{ delta > 0 ? `+${delta}` : delta }} units
          </span>
        </div>
      </div>

      <!-- Mandatory Adjustment Reason -->
      <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-[#042562] mb-1">
          Adjustment Reason (Mandatory for Audit Compliance) *
        </label>
        <select
          v-model="reasonPreset"
          @change="onPresetChange"
          class="w-full text-xs font-medium rounded-lg border border-slate-300 px-3 py-2.5 focus:border-[#1865C0] focus:ring-1 focus:ring-[#1865C0] bg-white text-slate-800 mb-2"
        >
          <option value="Physical Inventory Cycle Count Discrepancy">Physical Inventory Cycle Count Discrepancy</option>
          <option value="Damaged in Warehouse During Handling">Damaged in Warehouse During Handling</option>
          <option value="Found Unrecorded Batch in Bay/Aisle">Found Unrecorded Batch in Bay/Aisle</option>
          <option value="RMA Restocking Discrepancy">RMA Restocking Discrepancy</option>
          <option value="Initial Physical Baseline Setup">Initial Physical Baseline Setup</option>
          <option value="Custom">Other Custom Reason (Type below)...</option>
        </select>

        <input
          type="text"
          v-model="form.adjustment_reason"
          required
          minlength="5"
          placeholder="Detailed explanation for audit log..."
          class="w-full text-xs font-medium rounded-lg border border-slate-300 px-3 py-2.5 focus:border-[#1865C0] focus:ring-1 focus:ring-[#1865C0] bg-white text-slate-900"
        />
      </div>

      <!-- Audit Reference / Sheet Number -->
      <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-[#042562] mb-1">
          Audit Sheet / Work Order Reference
        </label>
        <input
          type="text"
          v-model="form.external_reference"
          placeholder="e.g. AUDIT-2026-Q4-SHEET-12"
          class="w-full text-xs font-medium rounded-lg border border-slate-300 px-3 py-2.5 focus:border-[#1865C0] focus:ring-1 focus:ring-[#1865C0] bg-white text-slate-900"
        />
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
          :disabled="loading || delta === 0 || !form.adjustment_reason || form.adjustment_reason.length < 5"
          class="px-5 py-2 rounded-lg bg-[#F59E0B] hover:bg-amber-600 text-slate-900 text-xs font-bold flex items-center gap-2 shadow-sm transition-colors disabled:opacity-50"
        >
          <Scale class="w-4 h-4" />
          <span>{{ loading ? 'Reconciling...' : 'Confirm Audit Adjustment' }}</span>
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
import { Scale } from 'lucide-vue-next';

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

const emit = defineEmits(['update:modelValue', 'adjusted']);
const notify = useNotificationStore();

const loading = ref(false);
const error = ref('');
const reasonPreset = ref('Physical Inventory Cycle Count Discrepancy');

const form = reactive({
  store_id: null,
  product_id: null,
  new_quantity: 0,
  adjustment_reason: 'Physical Inventory Cycle Count Discrepancy',
  external_reference: '',
});

const currentQuantity = computed(() => {
  if (!form.store_id || !form.product_id) return 0;
  const match = props.inventories.find(
    (i) => i.store_id === form.store_id && i.product_id === form.product_id
  );
  return match ? match.quantity : 0;
});

const delta = computed(() => {
  return Number(form.new_quantity) - currentQuantity.value;
});

const onPresetChange = () => {
  if (reasonPreset.value !== 'Custom') {
    form.adjustment_reason = reasonPreset.value;
  } else {
    form.adjustment_reason = '';
  }
};

watch(
  () => props.modelValue,
  (open) => {
    if (open) {
      error.value = '';
      form.store_id = props.preselectedStoreId || (props.stores[0]?.id ?? null);
      form.product_id = props.preselectedProductId || (props.products[0]?.id ?? null);
      form.external_reference = '';
      reasonPreset.value = 'Physical Inventory Cycle Count Discrepancy';
      form.adjustment_reason = reasonPreset.value;

      // sync new_quantity to current
      setTimeout(() => {
        form.new_quantity = currentQuantity.value;
      }, 50);
    }
  }
);

watch(
  [() => form.store_id, () => form.product_id],
  () => {
    form.new_quantity = currentQuantity.value;
  }
);

const handleSubmit = async () => {
  if (delta.value === 0) {
    error.value = 'New verified quantity is identical to existing balance. No adjustment needed.';
    return;
  }

  if (!form.adjustment_reason || form.adjustment_reason.trim().length < 5) {
    error.value = 'A detailed adjustment reason (at least 5 characters) is required for audit trail compliance.';
    return;
  }

  loading.value = true;
  error.value = '';

  try {
    const res = await inventoryService.adjustStock({
      store_id: form.store_id,
      product_id: form.product_id,
      new_quantity: form.new_quantity,
      adjustment_reason: form.adjustment_reason,
      external_reference: form.external_reference,
    });

    notify.success(res.message || 'Inventory balance successfully adjusted.');
    emit('update:modelValue', false);
    emit('adjusted');
  } catch (err) {
    console.error('Adjust stock failed:', err);
    error.value = err.response?.data?.message || err.message || 'Failed to adjust stock.';
  } finally {
    loading.value = false;
  }
};
</script>
