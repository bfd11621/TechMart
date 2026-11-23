<template>
  <PublicLayout>
    <section class="mx-auto grid max-w-7xl gap-8 px-4 py-12 lg:grid-cols-5">
      <div class="lg:col-span-3">
        <h1 class="text-3xl font-black text-[#042562]">Quotation cart</h1>
        <div v-if="items.length" class="mt-6 space-y-4">
          <article v-for="item in items" :key="item.product.id" class="flex items-center justify-between gap-4 rounded-xl border border-slate-200 bg-white p-4">
            <div class="flex min-w-0 items-center gap-4">
              <div class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-slate-200 bg-[#F4F6FA]">
                <img
                  v-if="item.product.primary_image_url"
                  :src="item.product.primary_image_url"
                  :alt="item.product.name"
                  class="max-h-full max-w-full object-contain"
                />
                <span v-else class="text-sm font-black text-[#042562]">iR</span>
              </div>
              <div class="min-w-0">
                <div v-if="item.product.brand?.name" class="text-[10px] font-bold uppercase tracking-wider text-[#1865C0]">
                  {{ item.product.brand.name }}
                </div>
                <Link :href="`/product/${item.product.slug}`" class="block font-bold text-[#042562]">{{ item.product.name }}</Link>
                <div class="text-xs text-slate-500">SKU {{ item.product.sku || '—' }} · {{ money(item.product.base_price) }} each</div>
              </div>
            </div>
            <div class="flex items-center gap-3">
              <input
                :value="item.quantity"
                type="number"
                min="1"
                class="w-20 rounded border border-slate-300 px-2 py-1 text-sm"
                @change="cart.updateQuantity(item.product.id, $event.target.value)"
              />
              <button type="button" class="text-xs font-bold text-[#E20913]" @click="cart.removeItem(item.product.id)">Remove</button>
            </div>
          </article>
        </div>
        <p v-else class="mt-6 rounded-xl border border-dashed border-slate-300 bg-white p-8 text-sm text-slate-500">
          The cart is empty. Add a product from the catalog.
        </p>
      </div>

      <form class="space-y-3 rounded-xl border border-slate-200 bg-white p-5 lg:col-span-2" @submit.prevent="submit">
        <h2 class="text-lg font-black text-[#042562]">Request a quotation</h2>
        <input v-model="form.customer_name" required placeholder="Full name" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" />
        <input v-model="form.customer_email" required type="email" placeholder="Email" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" />
        <input v-model="form.customer_phone" required placeholder="Phone" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" />
        <input v-model="form.company_name" placeholder="Company" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" />
        <textarea v-model="form.customer_notes" rows="4" placeholder="Notes for the sales team" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></textarea>
        <p v-if="message" class="text-sm" :class="ok ? 'text-emerald-700' : 'text-red-600'">{{ message }}</p>
        <button type="submit" :disabled="sending || !items.length" class="w-full rounded-lg bg-[#042562] py-3 text-xs font-bold uppercase tracking-wider text-white disabled:opacity-50">
          {{ sending ? 'Sending...' : 'Submit quotation request' }}
        </button>
      </form>
    </section>
  </PublicLayout>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { useQuotationCart } from '@/composables/useQuotationCart';
import { money } from '@/composables/format';

const cart = useQuotationCart();
const items = cart.items;
const sending = ref(false);
const message = ref('');
const ok = ref(false);
const form = reactive({
  customer_name: '',
  customer_email: '',
  customer_phone: '',
  company_name: '',
  customer_notes: '',
});

async function submit() {
  sending.value = true;
  message.value = '';
  try {
    const response = await fetch('/api/v1/public/rfqs', {
      method: 'POST',
      headers: { Accept: 'application/json', 'Content-Type': 'application/json' },
      body: JSON.stringify({
        ...form,
        items: items.value.map((item) => ({
          product_id: item.product.id,
          quantity: item.quantity,
          notes: item.notes || null,
        })),
      }),
    });
    const body = await response.json();
    ok.value = response.ok;
    message.value = body.message || (response.ok ? 'Quotation request received.' : 'The request could not be saved.');
    if (response.ok) {
      cart.clearCart();
    }
  } catch {
    ok.value = false;
    message.value = 'The API could not be reached.';
  } finally {
    sending.value = false;
  }
}
</script>
