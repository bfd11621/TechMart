<template>
  <AdminLayout>
    <div class="space-y-6">
      <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
          <router-link to="/admin/rfqs" class="text-xs font-bold text-[#1865C0] hover:text-[#042562]">
            ← RFQ Inquiries
          </router-link>
          <h2 class="text-xl font-black text-[#042562] tracking-tight mt-1">Prepare Formal Quotation</h2>
          <p class="text-xs text-slate-500 mt-0.5">
            Linked inquiry {{ form.rfq_number || '—' }}. Quote {{ form.quotation_number || 'number is assigned when you save' }}.
          </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
          <button type="button" class="px-3 py-2 rounded-lg border border-slate-200 bg-white text-xs font-bold text-[#042562]" @click="exportCsv">
            Export CSV
          </button>
          <button type="button" class="px-3 py-2 rounded-lg border border-slate-200 bg-white text-xs font-bold text-[#042562] disabled:opacity-50" :disabled="exporting" @click="exportPdf">
            {{ exporting ? 'Exporting...' : 'Export PDF' }}
          </button>
          <button type="button" class="px-3 py-2 rounded-lg border border-slate-200 bg-white text-xs font-bold text-[#042562]" @click="openGmail">
            Open in Gmail
          </button>
          <button type="button" class="px-3 py-2 rounded-lg border border-slate-200 bg-white text-xs font-bold text-[#042562]" @click="openOutlook">
            Open in Outlook
          </button>
          <button type="button" class="px-3 py-2 rounded-lg bg-[#1865C0] text-white text-xs font-bold" @click="sendOpen = true">
            Send Quotation via Email
          </button>
          <button type="button" class="px-3 py-2 rounded-lg bg-[#042562] text-white text-xs font-bold disabled:opacity-50" :disabled="saving" @click="saveQuotation">
            {{ saving ? 'Saving...' : (quotationId ? 'Update Quotation' : 'Save Quotation') }}
          </button>
        </div>
      </div>

      <p class="text-[11px] text-slate-500">
        Gmail and Outlook open a draft only. Download the PDF first, then attach it in the mail window.
      </p>

      <div v-if="loading" class="corporate-card p-8 text-sm text-slate-500">Loading inquiry...</div>

      <form v-else class="space-y-6" @submit.prevent="saveQuotation">
        <section class="corporate-card p-6">
          <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 border-b border-slate-100 pb-4">
            <div>
              <div class="text-2xl font-black text-[#042562] tracking-tight">iRACK <span class="text-[#E20913]">TECH-MART</span></div>
              <p class="text-xs text-slate-500 mt-1 max-w-md">3rd Floor, Wastna Commercial Building, General Wingate St, Addis Ababa</p>
            </div>
            <div class="text-xs text-slate-600 sm:text-right">
              <div><span class="font-bold text-[#042562]">Issue date</span> {{ issueDate }}</div>
              <div><span class="font-bold text-[#042562]">RFQ</span> {{ form.rfq_number }}</div>
              <div><span class="font-bold text-[#042562]">Quote</span> {{ form.quotation_number || 'Pending save' }}</div>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
            <label class="text-xs font-semibold text-[#172033]">
              Validity / expiry
              <input v-model="form.valid_until" type="date" class="mt-1.5 w-full py-2 px-3 text-xs rounded-md border border-slate-300" />
            </label>
            <label class="text-xs font-semibold text-[#172033]">
              Currency
              <select v-model="form.currency" class="mt-1.5 w-full py-2 px-3 text-xs rounded-md border border-slate-300 bg-white">
                <option value="ETB">ETB</option>
                <option value="USD">USD</option>
                <option value="SAR">SAR</option>
              </select>
            </label>
            <label class="text-xs font-semibold text-[#172033]">
              VAT / tax %
              <input v-model.number="form.tax_rate" type="number" min="0" max="100" step="0.01" class="mt-1.5 w-full py-2 px-3 text-xs rounded-md border border-slate-300" />
            </label>
          </div>
        </section>

        <section class="corporate-card p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
          <label class="text-xs font-semibold text-[#172033]">Client name
            <input v-model="form.customer_name" required class="mt-1.5 w-full py-2 px-3 text-xs rounded-md border border-slate-300" />
          </label>
          <label class="text-xs font-semibold text-[#172033]">Company
            <input v-model="form.company_name" class="mt-1.5 w-full py-2 px-3 text-xs rounded-md border border-slate-300" />
          </label>
          <label class="text-xs font-semibold text-[#172033]">Email
            <input v-model="form.customer_email" type="email" required class="mt-1.5 w-full py-2 px-3 text-xs rounded-md border border-slate-300" />
          </label>
          <label class="text-xs font-semibold text-[#172033]">Phone
            <input v-model="form.customer_phone" class="mt-1.5 w-full py-2 px-3 text-xs rounded-md border border-slate-300" />
          </label>
          <label class="sm:col-span-2 text-xs font-semibold text-[#172033]">Billing address
            <textarea v-model="form.billing_address" rows="2" class="mt-1.5 w-full py-2 px-3 text-xs rounded-md border border-slate-300"></textarea>
          </label>
        </section>

        <section class="corporate-card overflow-hidden">
          <div class="p-4 border-b border-slate-100 space-y-3">
            <div class="flex items-center justify-between gap-3">
              <h3 class="text-sm font-black text-[#042562]">Itemized hardware</h3>
              <span class="text-[11px] font-semibold text-slate-500">{{ catalog.length }} catalog products</span>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_minmax(0,1.4fr)_auto] gap-2">
              <input
                v-model="catalogQuery"
                type="search"
                placeholder="Search name, SKU, or model"
                class="w-full py-2 px-3 text-xs rounded-md border border-slate-300"
                @input="onCatalogSearch"
              />
              <select v-model="productToAdd" class="w-full py-2 px-3 text-xs rounded-md border border-slate-300 bg-white">
                <option value="">
                  {{ catalogLoading ? 'Loading the product catalog...' : (catalog.length ? 'Choose a catalog product' : 'No products match this search') }}
                </option>
                <option v-for="product in catalog" :key="product.id" :value="String(product.id)">
                  {{ product.sku }} — {{ product.name }}<template v-if="product.brand?.name"> · {{ product.brand.name }}</template> · {{ Number(product.base_price || 0).toLocaleString() }}
                </option>
              </select>
              <button type="button" class="px-4 py-2 rounded-lg bg-[#042562] text-white text-xs font-bold disabled:opacity-50" :disabled="!productToAdd" @click="addCatalogItem">
                Add line
              </button>
            </div>
            <p v-if="catalogError" class="text-[11px] font-semibold text-red-600">{{ catalogError }}</p>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full text-xs">
              <thead class="bg-slate-50 text-[#042562]">
                <tr>
                  <th class="text-left p-3">Description</th>
                  <th class="text-right p-3 w-24">Qty</th>
                  <th class="text-right p-3 w-32">Unit price ({{ form.currency }})</th>
                  <th class="text-right p-3 w-24">Disc %</th>
                  <th class="text-right p-3 w-32">Line total ({{ form.currency }})</th>
                  <th class="p-3 w-16"></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, index) in form.items" :key="index" class="border-t border-slate-100 align-top">
                  <td class="p-3">
                    <div class="font-bold text-[#172033]">{{ item.product_name }}</div>
                    <div class="text-[10px] text-slate-400 font-mono">{{ item.product_sku }}</div>
                    <textarea v-model="item.notes" rows="2" maxlength="255" placeholder="Specs or configuration notes" class="mt-2 w-full py-1.5 px-2 rounded border border-slate-200"></textarea>
                  </td>
                  <td class="p-3"><input v-model.number="item.quantity" type="number" min="1" class="w-full py-1.5 px-2 rounded border border-slate-200 text-right" /></td>
                  <td class="p-3"><input v-model.number="item.unit_price" type="number" min="0" step="0.01" class="w-full py-1.5 px-2 rounded border border-slate-200 text-right" /></td>
                  <td class="p-3"><input v-model.number="item.discount_percent" type="number" min="0" max="100" step="0.01" class="w-full py-1.5 px-2 rounded border border-slate-200 text-right" /></td>
                  <td class="p-3 text-right font-bold text-[#042562]">{{ money(lineTotal(item)) }}</td>
                  <td class="p-3 text-right">
                    <button type="button" class="text-red-600 font-bold" @click="form.items.splice(index, 1)">Remove</button>
                  </td>
                </tr>
                <tr v-if="!form.items.length">
                  <td colspan="6" class="p-6 text-center text-slate-400">This inquiry has no line items yet.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>

        <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <div class="corporate-card p-6 space-y-4">
            <label class="block text-xs font-semibold text-[#172033]">Terms, payment, delivery, and warranty
              <textarea v-model="form.terms_conditions" rows="8" class="mt-1.5 w-full py-2 px-3 text-xs rounded-md border border-slate-300"></textarea>
            </label>
            <label class="block text-xs font-semibold text-[#172033]">Admin notes
              <textarea v-model="form.notes" rows="3" class="mt-1.5 w-full py-2 px-3 text-xs rounded-md border border-slate-300"></textarea>
            </label>
          </div>
          <div class="corporate-card p-6 space-y-3 text-sm">
            <div class="flex justify-between"><span class="text-slate-500">Subtotal</span><strong>{{ money(subtotal) }}</strong></div>
            <label class="flex items-center justify-between gap-3 text-xs font-semibold text-[#172033]">
              Quotation discount ({{ form.currency }})
              <input v-model.number="form.discount_value" type="number" min="0" step="0.01" class="w-32 py-1.5 px-2 rounded border border-slate-200 text-right" />
            </label>
            <div class="flex justify-between"><span class="text-slate-500">VAT {{ form.tax_rate || 0 }}%</span><strong>{{ money(taxAmount) }}</strong></div>
            <div class="flex justify-between border-t border-slate-100 pt-3 text-base font-black text-[#042562]">
              <span>Grand total</span><span>{{ money(grandTotal) }}</span>
            </div>
          </div>
        </section>
      </form>

      <div v-if="sendOpen" class="fixed inset-0 z-50 bg-slate-900/40 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6 space-y-4">
          <h3 class="text-base font-black text-[#042562]">Send Quotation via Email</h3>
          <label class="block text-xs font-semibold">To
            <input v-model="mail.email" type="email" class="mt-1.5 w-full py-2 px-3 text-xs rounded-md border border-slate-300" />
          </label>
          <label class="block text-xs font-semibold">Subject
            <input v-model="mail.subject" class="mt-1.5 w-full py-2 px-3 text-xs rounded-md border border-slate-300" />
          </label>
          <label class="block text-xs font-semibold">Cover message
            <textarea v-model="mail.message" rows="5" class="mt-1.5 w-full py-2 px-3 text-xs rounded-md border border-slate-300"></textarea>
          </label>
          <div class="flex justify-end gap-2">
            <button type="button" class="px-3 py-2 text-xs font-bold text-slate-600" @click="sendOpen = false">Cancel</button>
            <button type="button" class="px-3 py-2 rounded-lg bg-[#042562] text-white text-xs font-bold disabled:opacity-50" :disabled="sending" @click="sendEmail">
              {{ sending ? 'Sending...' : 'Send with PDF' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import { useRoute } from 'vue-router';
import AdminLayout from '@/layouts/AdminLayout.vue';
import rfqService from '@/services/rfqService';
import { quotationService } from '@/services/quotationService';
import { adminProductService } from '@/services/adminProductService';
import { useNotificationStore } from '@/stores/notificationStore';

const route = useRoute();
const notify = useNotificationStore();

const loading = ref(true);
const saving = ref(false);
const sending = ref(false);
const exporting = ref(false);
const sendOpen = ref(false);
const quotationId = ref(null);
const catalog = ref([]);
const catalogQuery = ref('');
const catalogLoading = ref(false);
const catalogError = ref('');
const productToAdd = ref('');
let catalogTimer = null;

const defaultTerms = [
  '1. Price validity: quoted prices stay open for 15 days from the issue date.',
  '2. Payment terms: 50% advance on order confirmation, 50% on delivery.',
  '3. Delivery: stocked items ship from the selected branch. Built-to-order hardware needs extra lead time.',
  '4. Warranty: genuine OEM hardware under the manufacturer SLA named on each line.',
].join('\n');

const plusDays = (days) => {
  const date = new Date();
  date.setDate(date.getDate() + days);
  return date.toISOString().slice(0, 10);
};

const form = reactive({
  rfq_number: '',
  rfq_request_id: null,
  store_id: null,
  quotation_number: '',
  valid_until: plusDays(15),
  currency: 'ETB',
  tax_rate: 15,
  customer_name: '',
  company_name: '',
  customer_email: '',
  customer_phone: '',
  billing_address: '',
  discount_value: 0,
  terms_conditions: defaultTerms,
  notes: '',
  items: [],
});

const mail = reactive({
  email: '',
  subject: '',
  message: 'Please find the formal iRACK TECH-MART quotation attached as a PDF.',
});

const issueDate = computed(() => new Date().toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }));

const asList = (value) => {
  if (Array.isArray(value)) return value;
  if (Array.isArray(value?.data)) return value.data;
  return [];
};

const lineTotal = (item) => {
  const qty = Number(item.quantity) || 0;
  const price = Number(item.unit_price) || 0;
  const discount = Math.min(100, Math.max(0, Number(item.discount_percent) || 0));
  return Math.round(qty * price * (1 - discount / 100) * 100) / 100;
};

const subtotal = computed(() => form.items.reduce((sum, item) => sum + lineTotal(item), 0));
const discountAmount = computed(() => Math.min(subtotal.value, Math.max(0, Number(form.discount_value) || 0)));
const taxAmount = computed(() => Math.round((subtotal.value - discountAmount.value) * ((Number(form.tax_rate) || 0) / 100) * 100) / 100);
const grandTotal = computed(() => Math.round((subtotal.value - discountAmount.value + taxAmount.value) * 100) / 100);

const money = (amount) => `${form.currency} ${Number(amount || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

const mapQuoteItem = (item) => ({
  product_id: item.product_id,
  product_name: item.product_name || item.product?.name || 'Hardware item',
  product_sku: item.product_sku || item.product?.sku || '',
  quantity: Number(item.quantity) || 1,
  unit_price: Number(item.unit_price || item.target_price || 0),
  discount_percent: Number(item.discount_percent) || 0,
  notes: item.notes || '',
});

const applyQuotation = (quote) => {
  quotationId.value = quote.id;
  form.quotation_number = quote.quotation_number;
  form.customer_name = quote.customer_name || form.customer_name;
  form.customer_email = quote.customer_email || form.customer_email;
  form.customer_phone = quote.customer_phone || '';
  form.company_name = quote.company_name || '';
  form.billing_address = quote.billing_address || '';
  form.valid_until = quote.valid_until || form.valid_until;
  form.currency = quote.currency || 'ETB';
  form.tax_rate = Number(quote.tax_rate ?? 15);
  form.discount_value = Number(quote.discount_value || 0);
  form.terms_conditions = quote.terms_conditions || form.terms_conditions;
  form.notes = quote.notes || '';
  form.store_id = quote.store_id || form.store_id;
  form.items = asList(quote.items).map(mapQuoteItem);
  mail.email = form.customer_email;
  mail.subject = `Quotation ${quote.quotation_number} from iRACK TECH-MART`;
};

const loadInquiry = async () => {
  loading.value = true;
  try {
    const response = await rfqService.getAdminRfq(route.params.rfqId);
    const rfq = response.data;
    form.rfq_request_id = rfq.id;
    form.rfq_number = rfq.rfq_number;
    form.store_id = rfq.preferred_store?.id || null;
    form.customer_name = rfq.customer_name || '';
    form.customer_email = rfq.customer_email || '';
    form.customer_phone = rfq.customer_phone || '';
    form.company_name = rfq.company_name || '';
    form.billing_address = [rfq.address, rfq.city].filter(Boolean).join(', ');
    form.notes = rfq.admin_notes || '';
    form.items = asList(rfq.items).map(mapQuoteItem);
    mail.email = form.customer_email;
    mail.subject = `Quotation for ${rfq.rfq_number} from iRACK TECH-MART`;

    if (rfq.quotation?.id) {
      const existing = await quotationService.getQuotation(rfq.quotation.id);
      applyQuotation(existing.data);
    }
  } catch (error) {
    notify.error(error.response?.data?.message || 'Could not load this RFQ.');
  } finally {
    loading.value = false;
  }
};

const loadCatalog = async (search = '') => {
  catalogLoading.value = true;
  catalogError.value = '';
  try {
    const collected = [];
    let page = 1;
    let lastPage = 1;
    do {
      const response = await adminProductService.getProducts({
        search: search || undefined,
        per_page: 100,
        page,
      });
      const rows = Array.isArray(response?.data) ? response.data : [];
      collected.push(...rows);
      lastPage = Number(response?.meta?.pagination?.last_page || 1);
      page += 1;
    } while (page <= lastPage && page <= 20);

    catalog.value = collected
      .filter((product) => product?.id && product?.name)
      .sort((left, right) => String(left.name).localeCompare(String(right.name)));
    if (!catalog.value.some((product) => String(product.id) === String(productToAdd.value))) {
      productToAdd.value = '';
    }
  } catch (error) {
    catalog.value = [];
    catalogError.value = error.response?.data?.message || 'The product catalog could not be loaded.';
  } finally {
    catalogLoading.value = false;
  }
};

const onCatalogSearch = () => {
  clearTimeout(catalogTimer);
  catalogTimer = setTimeout(() => loadCatalog(catalogQuery.value.trim()), 250);
};

const addCatalogItem = () => {
  const product = catalog.value.find((row) => String(row.id) === String(productToAdd.value));
  if (!product) return;
  form.items.push({
    product_id: product.id,
    product_name: product.name,
    product_sku: product.sku,
    quantity: 1,
    unit_price: Number(product.base_price) || 0,
    discount_percent: 0,
    notes: '',
  });
  productToAdd.value = '';
};

const payload = () => ({
  rfq_request_id: form.rfq_request_id,
  store_id: form.store_id,
  customer_name: form.customer_name,
  customer_email: form.customer_email,
  customer_phone: form.customer_phone,
  company_name: form.company_name,
  billing_address: form.billing_address,
  valid_until: form.valid_until,
  currency: form.currency,
  discount_type: 'fixed',
  discount_value: Number(form.discount_value) || 0,
  tax_rate: Number(form.tax_rate) || 0,
  terms_conditions: form.terms_conditions,
  notes: form.notes,
  items: form.items.map((item) => ({
    product_id: item.product_id,
    quantity: Math.max(1, Number(item.quantity) || 1),
    unit_price: Number(item.unit_price) || 0,
    discount_percent: Number(item.discount_percent) || 0,
    notes: (item.notes || '').slice(0, 255),
  })),
});

const saveQuotation = async () => {
  if (!form.items.length) {
    notify.error('Add at least one hardware line before saving.');
    return false;
  }
  saving.value = true;
  try {
    const response = quotationId.value
      ? await quotationService.updateQuotation(quotationId.value, payload())
      : await quotationService.createQuotation(payload());
    applyQuotation(response.data);
    notify.success(response.message || 'Quotation saved.');
    return true;
  } catch (error) {
    notify.error(error.response?.data?.message || 'The quotation could not be saved.');
    return false;
  } finally {
    saving.value = false;
  }
};

const exportPdf = async () => {
  exporting.value = true;
  try {
    if (!quotationId.value) {
      const saved = await saveQuotation();
      if (!saved || !quotationId.value) return;
    }
    await quotationService.downloadPdf(quotationId.value, form.quotation_number);
    notify.success('Quotation PDF downloaded.');
  } catch (error) {
    notify.error(error.response?.data?.message || error.message || 'PDF export failed.');
  } finally {
    exporting.value = false;
  }
};

const exportCsv = () => {
  const header = ['SKU', 'Description', 'Notes', 'Quantity', 'Unit Price', 'Discount %', 'Line Total'];
  const rows = form.items.map((item) => [
    item.product_sku,
    item.product_name,
    item.notes,
    item.quantity,
    item.unit_price,
    item.discount_percent,
    lineTotal(item),
  ]);
  rows.push([]);
  rows.push(['', '', '', '', 'Subtotal', '', subtotal.value]);
  rows.push(['', '', '', '', 'Discount', '', discountAmount.value]);
  rows.push(['', '', '', '', `VAT ${form.tax_rate}%`, '', taxAmount.value]);
  rows.push(['', '', '', '', 'Grand Total', form.currency, grandTotal.value]);
  const csv = [header, ...rows]
    .map((row) => row.map((cell) => `"${String(cell ?? '').replaceAll('"', '""')}"`).join(','))
    .join('\n');
  const blob = new Blob([`\uFEFF${csv}`], { type: 'text/csv;charset=utf-8;' });
  const link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  link.download = `iRACK-Quotation-${form.quotation_number || form.rfq_number || 'draft'}.csv`;
  link.click();
  URL.revokeObjectURL(link.href);
};

const mailBody = () => [
  mail.message,
  '',
  `Quotation: ${form.quotation_number || 'not saved yet'}`,
  `RFQ: ${form.rfq_number}`,
  `Total: ${money(grandTotal.value)}`,
  '',
  'The formal PDF is downloaded from iRACK and attached separately.',
].join('\n');

const openGmail = () => {
  const url = `https://mail.google.com/mail/?view=cm&fs=1&to=${encodeURIComponent(form.customer_email || '')}&su=${encodeURIComponent(mail.subject)}&body=${encodeURIComponent(mailBody())}`;
  window.open(url, '_blank', 'noopener');
};

const openOutlook = () => {
  window.location.href = `mailto:${form.customer_email || ''}?subject=${encodeURIComponent(mail.subject)}&body=${encodeURIComponent(mailBody())}`;
};

const sendEmail = async () => {
  sending.value = true;
  try {
    const saved = await saveQuotation();
    if (!saved || !quotationId.value) return;
    const response = await quotationService.sendToClient(quotationId.value, {
      email: mail.email || form.customer_email,
      subject: mail.subject,
      message: mail.message,
    });
    if (response.data) applyQuotation(response.data);
    notify.success(response.message || 'Quotation emailed.');
    sendOpen.value = false;
  } catch (error) {
    notify.error(error.response?.data?.message || 'The quotation email could not be sent.');
  } finally {
    sending.value = false;
  }
};

onMounted(() => {
  loadInquiry();
  loadCatalog();
});
</script>
