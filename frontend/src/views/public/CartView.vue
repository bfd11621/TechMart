<template>
  <PublicLayout>
    <!-- Header Banner -->
    <div class="bg-[#042562] text-white py-12 border-b border-navy-800">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
          <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-blue-300 uppercase tracking-wider mb-2">
              <router-link to="/" class="hover:text-white">Home</router-link>
              <span>/</span>
              <router-link to="/catalog" class="hover:text-white">Catalog</router-link>
              <span>/</span>
              <span class="text-white">Quotation Basket</span>
            </div>
            <h1 class="text-3xl font-black">Request for Quotation (RFQ) Basket</h1>
          </div>
          <div class="text-xs text-blue-200">
            <span class="font-bold text-white">{{ cart.itemCount }} units</span> across
            <span class="font-bold text-white">{{ cart.uniqueItemCount }} models</span> selected
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content Workspace -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <!-- Submission Confirmation Screen (Shown after successful submission) -->
      <div
        v-if="submissionSuccess"
        class="bg-white rounded-2xl border border-slate-200 p-8 sm:p-12 shadow-lg max-w-3xl mx-auto text-center space-y-6"
      >
        <div class="w-20 h-20 rounded-full bg-emerald-50 text-[#16A34A] mx-auto flex items-center justify-center ring-8 ring-emerald-100">
          <CheckCircle2 class="w-10 h-10" />
        </div>

        <div>
          <span class="text-xs font-bold uppercase tracking-widest text-[#1865C0]">Quotation Request Dispatched</span>
          <h2 class="text-2xl sm:text-3xl font-black text-[#042562] mt-1">
            RFQ Submitted Successfully
          </h2>
          <p class="text-xs sm:text-sm text-slate-600 mt-2 max-w-lg mx-auto">
            Your equipment bill-of-materials inquiry has been logged in our multi-store fulfillment network. A dedicated enterprise systems architect is preparing your formal quotation document.
          </p>
        </div>

        <!-- RFQ Reference Number Box -->
        <div class="p-5 rounded-xl bg-[#F0FDF4] border border-emerald-300 max-w-md mx-auto space-y-1">
          <span class="text-xs font-bold text-emerald-800 uppercase tracking-wider block">
            Unique RFQ Reference Number
          </span>
          <span class="text-2xl font-black font-mono text-[#042562] block tracking-wider">
            {{ submissionSuccess.rfq_number }}
          </span>
          <span class="text-[11px] text-emerald-700 block">
            Confirmation email dispatched to: <strong>{{ submissionSuccess.customer_email }}</strong>
          </span>
        </div>

        <!-- Next Steps Roadmap -->
        <div class="text-left bg-[#F8FAFC] rounded-xl border border-slate-200 p-6 space-y-4">
          <h4 class="text-xs font-black uppercase tracking-wider text-[#042562]">
            Fulfillment Workflow & Next Steps
          </h4>
          <div class="space-y-3 text-xs text-slate-700">
            <div class="flex items-start gap-3">
              <span class="w-6 h-6 rounded-full bg-[#042562] text-white flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">1</span>
              <div>
                <strong class="text-[#042562]">Inventory & BOM Verification (Underway):</strong>
                <p class="text-slate-500 text-[11px]">Our team checks real-time physical stock across Central HQ, Downtown, and Airport logistics centers.</p>
              </div>
            </div>
            <div class="flex items-start gap-3">
              <span class="w-6 h-6 rounded-full bg-[#1865C0] text-white flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">2</span>
              <div>
                <strong class="text-[#042562]">Formal Quotation Document with 30-Day Price Lock:</strong>
                <p class="text-slate-500 text-[11px]">You will receive an official PDF quotation containing firm pricing, delivery lead times, and payment terms.</p>
              </div>
            </div>
            <div class="flex items-start gap-3">
              <span class="w-6 h-6 rounded-full bg-[#E20913] text-white flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">3</span>
              <div>
                <strong class="text-[#042562]">Conversion to Confirmed Sales Order:</strong>
                <p class="text-slate-500 text-[11px]">Upon your approval of the quotation, an authorized administrator registers the confirmed order and schedules delivery.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="pt-4 flex flex-wrap items-center justify-center gap-4">
          <button
            @click="window.print()"
            class="inline-flex items-center gap-2 bg-[#F4F6FA] hover:bg-slate-200 text-slate-800 text-xs font-bold uppercase tracking-wider px-5 py-2.5 rounded-lg border border-slate-300 transition-colors"
          >
            <Printer class="w-4 h-4 text-slate-600" />
            <span>Print Acknowledgment</span>
          </button>

          <router-link
            to="/catalog"
            @click="resetSubmission"
            class="inline-flex items-center gap-2 bg-[#042562] hover:bg-[#063485] text-white text-xs font-bold uppercase tracking-wider px-6 py-2.5 rounded-lg transition-colors shadow"
          >
            <span>Return to Product Showroom</span>
            <ArrowRight class="w-4 h-4" />
          </router-link>
        </div>
      </div>

      <!-- Empty Cart State -->
      <div
        v-else-if="cart.uniqueItemCount === 0"
        class="bg-white rounded-2xl border border-slate-200 p-16 text-center space-y-5 max-w-2xl mx-auto shadow-sm"
      >
        <div class="w-16 h-16 rounded-full bg-blue-50 text-[#042562] mx-auto flex items-center justify-center">
          <FileSpreadsheet class="w-8 h-8 text-[#1865C0]" />
        </div>
        <div>
          <h2 class="text-2xl font-black text-[#042562]">Your Quotation Basket is Empty</h2>
          <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
            Browse our enterprise showroom to select datacenter servers, managed switches, storage NAS, or 42U rack enclosures for custom B2B pricing.
          </p>
        </div>
        <router-link
          to="/catalog"
          class="inline-flex items-center gap-2 bg-[#042562] hover:bg-[#063485] text-white text-xs font-bold uppercase tracking-wider px-6 py-3 rounded-lg transition-colors shadow"
        >
          <span>Explore Product Catalog</span>
          <ArrowRight class="w-4 h-4" />
        </router-link>
      </div>

      <!-- Active Cart & RFQ Form Grid -->
      <div v-else class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
        <!-- Left: Equipment Line Items Table (7 cols) -->
        <div class="lg:col-span-7 space-y-6">
          <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <!-- Table Header -->
            <div class="p-5 bg-[#F8FAFC] border-b border-slate-200 flex items-center justify-between">
              <div>
                <h3 class="text-sm font-black uppercase tracking-wider text-[#042562]">
                  Selected Equipment Line Items ({{ cart.uniqueItemCount }})
                </h3>
                <span class="text-[11px] text-slate-500">Review requested models and adjust quantities</span>
              </div>
              <button
                @click="confirmClearCart"
                class="text-xs text-red-600 hover:text-red-800 font-bold hover:underline transition-colors"
              >
                Clear Entire Basket
              </button>
            </div>

            <!-- Items List -->
            <div class="divide-y divide-slate-100 p-5 space-y-4">
              <div
                v-for="item in cart.items"
                :key="item.product.id"
                class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-4 first:pt-0"
              >
                <!-- Thumbnail & Name -->
                <div class="flex items-center gap-4 flex-1 min-w-0">
                  <div class="w-16 h-16 bg-[#F4F6FA] rounded-xl border border-slate-200 p-1 flex-shrink-0 flex items-center justify-center">
                    <img
                      :src="item.product.primary_image_url || '/images/products/placeholder.png'"
                      :alt="item.product.name"
                      class="max-h-full max-w-full object-contain"
                    />
                  </div>
                  <div class="min-w-0 flex-1">
                    <span class="text-[10px] font-bold uppercase text-[#1865C0] tracking-wider block">
                      {{ item.product.brand?.name || 'IT Hardware' }}
                    </span>
                    <router-link
                      :to="'/product/' + (item.product.slug || item.product.id)"
                      class="text-xs font-black text-[#042562] hover:text-[#1865C0] transition-colors leading-snug line-clamp-1 block"
                    >
                      {{ item.product.name }}
                    </router-link>
                    <span class="text-[10px] font-mono text-slate-500 block">
                      Model: <strong>{{ item.product.model_number || 'N/A' }}</strong> • SKU: {{ item.product.sku }}
                    </span>
                  </div>
                </div>

                <!-- Quantity Stepper & Remove Action -->
                <div class="flex items-center gap-4 self-end sm:self-center">
                  <!-- Stepper -->
                  <div class="flex items-center border border-slate-300 rounded-lg overflow-hidden bg-white">
                    <button
                      type="button"
                      @click="cart.updateQuantity(item.product.id, item.quantity - 1)"
                      class="px-2.5 py-1 text-slate-600 hover:bg-slate-100 font-bold text-xs"
                    >
                      -
                    </button>
                    <input
                      type="number"
                      :value="item.quantity"
                      @input="cart.updateQuantity(item.product.id, Number($event.target.value))"
                      min="1"
                      class="w-12 text-center text-xs font-bold text-[#172033] focus:outline-none py-1"
                    />
                    <button
                      type="button"
                      @click="cart.updateQuantity(item.product.id, item.quantity + 1)"
                      class="px-2.5 py-1 text-slate-600 hover:bg-slate-100 font-bold text-xs"
                    >
                      +
                    </button>
                  </div>

                  <!-- Remove Button -->
                  <button
                    @click="cart.removeItem(item.product.id)"
                    class="p-2 text-slate-400 hover:text-[#E20913] hover:bg-red-50 rounded-lg transition-colors"
                    title="Remove item"
                  >
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </div>
            </div>

            <!-- Bottom Action Row -->
            <div class="p-4 bg-[#F8FAFC] border-t border-slate-200 flex items-center justify-between">
              <router-link
                to="/catalog"
                class="inline-flex items-center gap-1.5 text-xs font-bold text-[#1865C0] hover:text-[#042562] transition-colors"
              >
                <ArrowLeft class="w-3.5 h-3.5" />
                <span>Add More Models from Catalog</span>
              </router-link>
              <span class="text-xs font-bold text-slate-700">
                Total Units: <strong class="text-[#042562]">{{ cart.itemCount }}</strong>
              </span>
            </div>
          </div>

          <!-- B2B Procurement Policy Notice Card -->
          <div class="bg-blue-50/80 border border-blue-200 rounded-xl p-5 space-y-2 text-xs">
            <div class="flex items-center gap-2 text-[#042562] font-black uppercase tracking-wider text-[11px]">
              <ShieldCheck class="w-4 h-4 text-[#1865C0]" />
              <span>Enterprise Procurement Policy (No Online Payment)</span>
            </div>
            <p class="text-slate-700 leading-relaxed text-[11px]">
              Submitting an RFQ does not charge your organization or constitute a binding sale. Our commercial sales desk validates inventory across our 3 regional hubs and issues a formal <strong>Quotation Document with 30-day price protection</strong>. Revenue is recorded strictly upon client purchase order confirmation.
            </p>
          </div>
        </div>

        <!-- Right: Professional RFQ Submission Form (5 cols) -->
        <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-6">
          <div class="border-b border-slate-100 pb-4">
            <span class="text-[11px] font-bold text-[#E20913] uppercase tracking-wider">Step 2 of 2</span>
            <h3 class="text-xl font-black text-[#042562] mt-0.5">Commercial Inquiry Details</h3>
            <p class="text-xs text-slate-500 mt-1">
              Provide corporate details for formal quotation issuance.
            </p>
          </div>

          <!-- Validation Error Alert -->
          <div
            v-if="errorMessage"
            class="p-3 bg-red-50 border border-red-200 rounded-lg text-xs text-[#DC2626] flex items-start gap-2"
          >
            <AlertCircle class="w-4 h-4 flex-shrink-0 mt-0.5" />
            <span>{{ errorMessage }}</span>
          </div>

          <form @submit.prevent="submitRfq" class="space-y-4">
            <!-- Full Name -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">
                Full Name <span class="text-[#E20913]">*</span>
              </label>
              <input
                type="text"
                v-model="form.customer_name"
                required
                placeholder="e.g. David Sterling"
                class="w-full bg-[#F4F6FA] text-xs text-[#172033] rounded-lg px-3 py-2.5 border border-slate-300 focus:outline-none focus:border-[#1865C0]"
              />
            </div>

            <!-- Organization / Company Name -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">
                Organization / Company Name <span class="text-[#E20913]">*</span>
              </label>
              <input
                type="text"
                v-model="form.company_name"
                required
                placeholder="e.g. Apex Cloud Solutions Inc."
                class="w-full bg-[#F4F6FA] text-xs text-[#172033] rounded-lg px-3 py-2.5 border border-slate-300 focus:outline-none focus:border-[#1865C0]"
              />
            </div>

            <!-- Corporate Email & Phone -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">
                  Corporate Email <span class="text-[#E20913]">*</span>
                </label>
                <input
                  type="email"
                  v-model="form.customer_email"
                  required
                  placeholder="name@company.com"
                  class="w-full bg-[#F4F6FA] text-xs text-[#172033] rounded-lg px-3 py-2.5 border border-slate-300 focus:outline-none focus:border-[#1865C0]"
                />
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">
                  Direct Phone <span class="text-[#E20913]">*</span>
                </label>
                <input
                  type="tel"
                  v-model="form.customer_phone"
                  required
                  placeholder="+1 (555) 000-0000"
                  class="w-full bg-[#F4F6FA] text-xs text-[#172033] rounded-lg px-3 py-2.5 border border-slate-300 focus:outline-none focus:border-[#1865C0]"
                />
              </div>
            </div>

            <!-- Location / Delivery Site -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">
                Project Location / Delivery Site Address <span class="text-[#E20913]">*</span>
              </label>
              <input
                type="text"
                v-model="form.location"
                required
                placeholder="e.g. 100 Silicon Parkway, Building C, San Jose, CA"
                class="w-full bg-[#F4F6FA] text-xs text-[#172033] rounded-lg px-3 py-2.5 border border-slate-300 focus:outline-none focus:border-[#1865C0]"
              />
            </div>

            <!-- Preferred Fulfillment Hub -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">
                Preferred Regional Fulfillment Hub
              </label>
              <select
                v-model="form.preferred_store_id"
                class="w-full bg-[#F4F6FA] text-xs text-[#172033] rounded-lg px-3 py-2.5 border border-slate-300 focus:outline-none focus:border-[#1865C0]"
              >
                <option :value="null">Automatic Routing (Nearest Live Hub)</option>
                <option :value="1">Central Distribution Hub (HQ) — San Jose, CA</option>
                <option :value="2">Downtown Commercial Center — Financial District</option>
                <option :value="3">Airport Logistics Terminal — Air Cargo Bay</option>
              </select>
            </div>

            <!-- Additional Comments / Engineering Requirements -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">
                Additional Technical Requirements / Tender Notes
              </label>
              <textarea
                v-model="form.customer_notes"
                rows="3"
                placeholder="Specific transceiver types, redundant power requirements, custom firmware, or target delivery date..."
                class="w-full bg-[#F4F6FA] text-xs text-[#172033] rounded-lg p-3 border border-slate-300 focus:outline-none focus:border-[#1865C0]"
              ></textarea>
            </div>

            <!-- Submit Button (With duplicate protection) -->
            <button
              type="submit"
              :disabled="submitting || cart.uniqueItemCount === 0"
              class="w-full inline-flex items-center justify-center gap-2 bg-[#E20913] hover:bg-[#be0710] text-white py-3 px-6 rounded-lg font-bold text-xs uppercase tracking-wider shadow-md transition-all transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
            >
              <FileSpreadsheet class="w-4 h-4" />
              <span>{{ submitting ? 'Generating RFQ Reference...' : 'Submit Request for Quotation' }}</span>
            </button>
          </form>
        </div>
      </div>
    </div>
  </PublicLayout>
</template>

<script setup>
import { reactive, ref } from 'vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { useCartStore } from '@/stores/cartStore';
import { useNotificationStore } from '@/stores/notificationStore';
import rfqService from '@/services/rfqService';
import {
  FileSpreadsheet,
  Trash2,
  ArrowRight,
  ArrowLeft,
  CheckCircle2,
  Printer,
  ShieldCheck,
  AlertCircle,
} from 'lucide-vue-next';

const cart = useCartStore();
const notify = useNotificationStore();

const submitting = ref(false);
const errorMessage = ref('');
const submissionSuccess = ref(null);

const form = reactive({
  customer_name: '',
  company_name: '',
  customer_email: '',
  customer_phone: '',
  location: '',
  preferred_store_id: null,
  customer_notes: '',
});

const confirmClearCart = () => {
  if (confirm('Are you sure you want to clear all items from your quotation basket?')) {
    cart.clearCart();
    notify.info('Quotation basket cleared.');
  }
};

const submitRfq = async () => {
  if (cart.uniqueItemCount === 0) {
    notify.error('Your quotation basket is empty.');
    return;
  }

  submitting.value = true;
  errorMessage.value = '';

  try {
    const payload = {
      customer_name: form.customer_name,
      company_name: form.company_name,
      customer_email: form.customer_email,
      customer_phone: form.customer_phone,
      location: form.location,
      preferred_store_id: form.preferred_store_id,
      customer_notes: form.customer_notes,
      items: cart.items.map((i) => ({
        product_id: i.product.id,
        quantity: i.quantity,
        target_price: i.targetPrice || null,
        notes: i.notes || null,
      })),
    };

    const res = await rfqService.submitRfq(payload);

    // Save successful submission result
    submissionSuccess.value = {
      rfq_number: res.data?.rfq_number || 'RFQ-2026-CONFIRMED',
      customer_email: form.customer_email,
      customer_name: form.customer_name,
    };

    // Clear cart upon successful submission
    cart.clearCart();
    notify.success('Your Request for Quotation has been submitted.');
    window.scrollTo({ top: 120, behavior: 'smooth' });
  } catch (err) {
    console.error('RFQ submission error', err);
    if (err.response?.data?.message) {
      errorMessage.value = err.response.data.message;
    } else {
      errorMessage.value = 'Failed to submit quotation request. Please verify your details.';
    }
  } finally {
    submitting.value = false;
  }
};

const resetSubmission = () => {
  submissionSuccess.value = null;
  form.customer_name = '';
  form.company_name = '';
  form.customer_email = '';
  form.customer_phone = '';
  form.location = '';
  form.customer_notes = '';
};
</script>
