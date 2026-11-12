<template>
  <BaseModal
    :model-value="modelValue"
    @update:model-value="$emit('update:modelValue', $event)"
    title="Inter-Store Stock Transfer"
    subtitle="Atomic rebalancing of hardware inventory between regional fulfillment branches."
    max-width="2xl"
  >
    <form @submit.prevent="handleSubmit" class="space-y-4">
      <!-- Source and Destination Stores Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-[#042562] mb-1">
            Originating Source Branch *
          </label>
          <select
            v-model="form.source_store_id"
            required
            class="w-full text-xs font-medium rounded-lg border border-slate-300 px-3 py-2.5 focus:border-[#1865C0] focus:ring-1 focus:ring-[#1865C0] bg-white text-slate-800"
          >
            <option :value="null" disabled>Select origin store...</option>
            <option v-for="store in stores" :key="store.id" :value="store.id">
              {{ store.name }} ({{ store.code }})
            </option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-[#042562] mb-1">
            Receiving Destination Branch *
          </label>
          <select
            v-model="form.destination_store_id"
            required
            class="w-full text-xs font-medium rounded-lg border border-slate-300 px-3 py-2.5 focus:border-[#1865C0] focus:ring-1 focus:ring-[#1865C0] bg-white text-slate-800"
          >
            <option :value="null" disabled>Select destination store...</option>
            <option
              v-for="store in stores"
              :key="store.id"
              :value="store.id"
              :disabled="store.id === form.source_store_id"
            >
              {{ store.name }} ({{ store.code }}) {{ store.id === form.source_store_id ? '(Origin)' : '' }}
            </option>
          </select>
        </div>
      </div>

      <!-- Product Selection -->
      <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-[#042562] mb-1">
          Hardware Product to Transfer *
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

      <!-- Source Availability Pill -->
      <div
        class="p-3 rounded-lg border flex items-center justify-between text-xs"
        :class="sourceAvailableStock > 0 ? 'bg-blue-50/70 border-blue-200 text-blue-900' : 'bg-red-50 border-red-200 text-red-800'"
      >
        <div class="flex items-center gap-2">
          <ArrowRightLeft class="w-4 h-4 text-[#1865C0]" />
          <span>Available at Originating Branch ({{ originStoreName }}):</span>
        </div>
        <div class="font-extrabold text-sm">
          <span :class="sourceAvailableStock > 0 ? 'text-[#042562]' : 'text-red-600'">
            {{ sourceAvailableStock }} units available
          </span>
        </div>
      </div>

      <!-- Quantity & Reference -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-[#042562] mb-1">
            Units to Transfer *
          </label>
          <input
            type="number"
            v-model.number="form.quantity"
            :max="sourceAvailableStock"
            min="1"
            required
            placeholder="e.g. 2"
            class="w-full text-xs font-bold rounded-lg border border-slate-300 px-3 py-2.5 focus:border-[#1865C0] focus:ring-1 focus:ring-[#1865C0] bg-white text-slate-900"
          />
        </div>

        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-[#042562] mb-1">
            Transfer Reference Code
          </label>
          <input
            type="text"
            v-model="form.transfer_reference"
            placeholder="e.g. TRF-2026-HQ-DT-01"
            class="w-full text-xs font-medium rounded-lg border border-slate-300 px-3 py-2.5 focus:border-[#1865C0] focus:ring-1 focus:ring-[#1865C0] bg-white text-slate-900"
          />
        </div>
      </div>

      <!-- Transfer Notes / Rationale -->
      <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-[#042562] mb-1">
          Transfer Logistics Notes & Courier Rationale
        </label>
        <textarea
          v-model="form.transfer_notes"
          rows="2"
          placeholder="Rebalancing for pending project deployment; dispatched via internal logistics van..."
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
          :disabled="loading || sourceAvailableStock <= 0 || form.quantity > sourceAvailableStock || form.source_store_id === form.destination_store_id"
          class="px-5 py-2 rounded-lg bg-[#1865C0] hover:bg-blue-700 text-white text-xs font-bold flex items-center gap-2 shadow-sm transition-colors disabled:opacity-50"
        >
          <ArrowRightLeft class="w-4 h-4" />
          <span>{{ loading ? 'Executing Transfer...' : 'Execute Atomic Transfer' }}</span>
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
import { ArrowRightLeft } from 'lucide-vue-next';

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

const emit = defineEmits(['update:modelValue', 'transferred']);
const notify = useNotificationStore();

const loading = ref(false);
const error = ref('');

const form = reactive({
  source_store_id: null,
  destination_store_id: null,
  product_id: null,
  quantity: 1,
  transfer_reference: '',
  transfer_notes: '',
});

const originStoreName = computed(() => {
  const store = props.stores.find((s) => s.id === form.source_store_id);
  return store ? store.name : 'Selected Store';
});

const sourceAvailableStock = computed(() => {
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
      // Select different destination
      const otherStore = props.stores.find((s) => s.id !== form.source_store_id);
      form.destination_store_id = otherStore ? otherStore.id : null;
      form.product_id = props.preselectedProductId || (props.products[0]?.id ?? null);
      form.quantity = 1;
      form.transfer_reference = '';
      form.transfer_notes = '';
    }
  }
);

const handleSubmit = async () => {
  if (form.source_store_id === form.destination_store_id) {
    error.value = 'Source and Destination stores cannot be identical.';
    return;
  }

  if (form.quantity > sourceAvailableStock.value) {
    error.value = `Cannot transfer more than available origin stock (${sourceAvailableStock.value} units).`;
    return;
  }

  loading.value = true;
  error.value = '';

  try {
    const res = await inventoryService.transferStock({
      source_store_id: form.source_store_id,
      destination_store_id: form.destination_store_id,
      product_id: form.product_id,
      quantity: form.quantity,
      transfer_reference: form.transfer_reference,
      transfer_notes: form.transfer_notes,
    });

    notify.success(res.message || 'Inter-store transfer executed atomically.');
    emit('update:modelValue', false);
    emit('transferred');
  } catch (err) {
    console.error('Transfer stock failed:', err);
    error.value = err.response?.data?.message || err.message || 'Failed to transfer stock.';
  } finally {
    loading.value = false;
  }
};
</script>
