<template>
  <BaseModal
    :model-value="modelValue"
    @update:model-value="$emit('update:modelValue', $event)"
    title="Stock Receiving — Goods Inward"
    subtitle="Log incoming hardware shipments from OEM suppliers and update warehouse inventory balance."
    max-width="2xl"
  >
    <form @submit.prevent="handleSubmit" class="space-y-4">
      <!-- Destination Store Selection -->
      <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-[#042562] mb-1">
          Destination Store / Fulfillment Hub *
        </label>
        <select
          v-model="form.destination_store_id"
          required
          class="w-full text-xs font-medium rounded-lg border border-slate-300 px-3 py-2.5 focus:border-[#1865C0] focus:ring-1 focus:ring-[#1865C0] bg-white text-slate-800"
        >
          <option :value="null" disabled>Select receiving destination branch...</option>
          <option v-for="store in stores" :key="store.id" :value="store.id">
            {{ store.name }} ({{ store.code }}) {{ store.is_primary ? '— Central HQ' : '' }}
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
          <option :value="null" disabled>Select equipment model...</option>
          <option v-for="prod in products" :key="prod.id" :value="prod.id">
            [{{ prod.sku }}] {{ prod.name }} — {{ prod.brand_name || 'OEM' }}
          </option>
        </select>
      </div>

      <!-- Quantity, Unit Cost & Shelf Location -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-[#042562] mb-1">
            Quantity Received *
          </label>
          <input
            type="number"
            v-model.number="form.quantity"
            min="1"
            required
            placeholder="e.g. 10"
            class="w-full text-xs font-bold rounded-lg border border-slate-300 px-3 py-2.5 focus:border-[#1865C0] focus:ring-1 focus:ring-[#1865C0] bg-white text-slate-900"
          />
        </div>

        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-[#042562] mb-1">
            Unit Cost ($ USD)
          </label>
          <input
            type="number"
            step="0.01"
            v-model.number="form.unit_cost"
            min="0"
            placeholder="e.g. 4150.00"
            class="w-full text-xs font-medium rounded-lg border border-slate-300 px-3 py-2.5 focus:border-[#1865C0] focus:ring-1 focus:ring-[#1865C0] bg-white text-slate-900"
          />
        </div>

        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-[#042562] mb-1">
            Shelf / Bay Location
          </label>
          <input
            type="text"
            v-model="form.shelf_location"
            placeholder="e.g. AISLE-A-BAY-1"
            class="w-full text-xs font-medium rounded-lg border border-slate-300 px-3 py-2.5 focus:border-[#1865C0] focus:ring-1 focus:ring-[#1865C0] bg-white text-slate-900"
          />
        </div>
      </div>

      <!-- Supplier & Purchase Order Reference -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-[#042562] mb-1">
            Supplier / OEM Partner
          </label>
          <input
            type="text"
            v-model="form.supplier"
            placeholder="e.g. Dell Technologies Logistics"
            class="w-full text-xs font-medium rounded-lg border border-slate-300 px-3 py-2.5 focus:border-[#1865C0] focus:ring-1 focus:ring-[#1865C0] bg-white text-slate-900"
          />
        </div>

        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-[#042562] mb-1">
            Purchase Order / Packing Slip #
          </label>
          <input
            type="text"
            v-model="form.purchase_reference"
            placeholder="e.g. PO-2026-90412"
            class="w-full text-xs font-medium rounded-lg border border-slate-300 px-3 py-2.5 focus:border-[#1865C0] focus:ring-1 focus:ring-[#1865C0] bg-white text-slate-900"
          />
        </div>
      </div>

      <!-- Remarks / Inspection Notes -->
      <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-[#042562] mb-1">
          Inspection Remarks & Receiving Notes
        </label>
        <textarea
          v-model="form.remarks"
          rows="2"
          placeholder="Physical inspection verified; factory seals intact, serial numbers checked..."
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
          :disabled="loading"
          class="px-5 py-2 rounded-lg bg-[#16A34A] hover:bg-green-700 text-white text-xs font-bold flex items-center gap-2 shadow-sm transition-colors disabled:opacity-50"
        >
          <ArrowDownToLine class="w-4 h-4" />
          <span>{{ loading ? 'Receiving Stock...' : 'Confirm Stock Receiving' }}</span>
        </button>
      </div>
    </form>
  </BaseModal>
</template>

<script setup>
import { reactive, ref, watch } from 'vue';
import BaseModal from '@/components/common/BaseModal.vue';
import inventoryService from '@/services/inventoryService';
import { useNotificationStore } from '@/stores/notificationStore';
import { ArrowDownToLine } from 'lucide-vue-next';

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
  preselectedStoreId: {
    type: Number,
    default: null,
  },
  preselectedProductId: {
    type: Number,
    default: null,
  },
});

const emit = defineEmits(['update:modelValue', 'received']);
const notify = useNotificationStore();

const loading = ref(false);
const error = ref('');

const form = reactive({
  destination_store_id: null,
  product_id: null,
  quantity: 1,
  unit_cost: null,
  supplier: '',
  purchase_reference: '',
  shelf_location: '',
  remarks: '',
});

watch(
  () => props.modelValue,
  (open) => {
    if (open) {
      error.value = '';
      form.destination_store_id = props.preselectedStoreId || (props.stores[0]?.id ?? null);
      form.product_id = props.preselectedProductId || (props.products[0]?.id ?? null);
      form.quantity = 1;
      form.supplier = '';
      form.purchase_reference = '';
      form.remarks = '';

      if (form.product_id) {
        const prod = props.products.find((p) => p.id === form.product_id);
        if (prod) form.unit_cost = prod.cost_price;
      }
    }
  }
);

watch(
  () => form.product_id,
  (newId) => {
    if (newId) {
      const prod = props.products.find((p) => p.id === newId);
      if (prod && prod.cost_price) {
        form.unit_cost = prod.cost_price;
      }
    }
  }
);

const handleSubmit = async () => {
  loading.value = true;
  error.value = '';

  try {
    const res = await inventoryService.receiveStock({
      destination_store_id: form.destination_store_id,
      product_id: form.product_id,
      quantity: form.quantity,
      unit_cost: form.unit_cost,
      supplier: form.supplier,
      purchase_reference: form.purchase_reference,
      shelf_location: form.shelf_location,
      remarks: form.remarks,
    });

    notify.success(res.message || 'Stock successfully received and credited.');
    emit('update:modelValue', false);
    emit('received');
  } catch (err) {
    console.error('Receive stock failed:', err);
    error.value = err.response?.data?.message || err.message || 'Failed to record stock receiving.';
  } finally {
    loading.value = false;
  }
};
</script>
