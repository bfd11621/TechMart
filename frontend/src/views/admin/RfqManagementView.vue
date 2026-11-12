<template>
  <AdminLayout>
    <div class="space-y-6">
      <!-- Page Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-black text-[#042562] tracking-tight">
            Quotation Requests (RFQs)
          </h1>
          <p class="text-xs text-slate-500 mt-0.5">
            Incoming customer bill-of-materials inquiries, multi-store stock checks, and quotation workflows.
          </p>
        </div>

        <div class="flex items-center gap-3">
          <router-link
            to="/cart"
            target="_blank"
            class="inline-flex items-center gap-1.5 bg-white text-slate-700 hover:text-[#042562] border border-slate-300 text-xs font-bold uppercase tracking-wider px-4 py-2 rounded-lg transition-colors shadow-sm"
          >
            <ExternalLink class="w-3.5 h-3.5 text-[#1865C0]" />
            <span>Public RFQ Basket</span>
          </router-link>
        </div>
      </div>

      <!-- Metrics Row -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
          <div class="flex items-center justify-between">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total RFQs</span>
            <div class="w-8 h-8 rounded-lg bg-blue-50 text-[#042562] flex items-center justify-center">
              <FileSpreadsheet class="w-4 h-4" />
            </div>
          </div>
          <div class="text-2xl font-black text-[#042562] mt-2">{{ totalCount }}</div>
          <span class="text-[10px] text-slate-500">Across all fulfillment hubs</span>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
          <div class="flex items-center justify-between">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">New Unreviewed</span>
            <div class="w-8 h-8 rounded-lg bg-red-50 text-[#E20913] flex items-center justify-center">
              <Clock class="w-4 h-4" />
            </div>
          </div>
          <div class="text-2xl font-black text-[#E20913] mt-2">{{ newCount }}</div>
          <span class="text-[10px] text-red-600 font-semibold">Requires solutions review</span>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
          <div class="flex items-center justify-between">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Under Review</span>
            <div class="w-8 h-8 rounded-lg bg-amber-50 text-[#F59E0B] flex items-center justify-center">
              <Layers class="w-4 h-4" />
            </div>
          </div>
          <div class="text-2xl font-black text-[#F59E0B] mt-2">{{ reviewCount }}</div>
          <span class="text-[10px] text-slate-500">BOM allocation in progress</span>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
          <div class="flex items-center justify-between">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Quoted / In Flight</span>
            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-[#16A34A] flex items-center justify-center">
              <CheckCircle2 class="w-4 h-4" />
            </div>
          </div>
          <div class="text-2xl font-black text-[#16A34A] mt-2">{{ quotedCount }}</div>
          <span class="text-[10px] text-slate-500">With 30-day price locks</span>
        </div>
      </div>

      <!-- Filters & Search Toolbar -->
      <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <!-- Search Input -->
        <div class="relative w-full md:w-80">
          <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" />
          <input
            type="text"
            v-model="searchQuery"
            @input="debounceSearch"
            placeholder="Search by RFQ #, customer, company..."
            class="w-full bg-[#F4F6FA] text-xs text-[#172033] rounded-lg pl-9 pr-4 py-2.5 border border-slate-300 focus:outline-none focus:border-[#1865C0]"
          />
        </div>

        <!-- Filter Controls -->
        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
          <!-- Status Selector -->
          <select
            v-model="selectedStatus"
            @change="fetchRfqs"
            class="bg-[#F4F6FA] text-xs font-semibold text-[#172033] rounded-lg px-3 py-2 border border-slate-300 focus:outline-none focus:border-[#1865C0]"
          >
            <option value="">All Statuses</option>
            <option value="NEW_REQUEST">New Request</option>
            <option value="UNDER_REVIEW">Under Review</option>
            <option value="QUOTATION_PREPARED">Quotation Prepared</option>
            <option value="SENT_TO_CUSTOMER">Sent to Customer</option>
            <option value="APPROVED">Approved</option>
            <option value="REJECTED">Rejected</option>
            <option value="CONVERTED_TO_ORDER">Converted to Sales Order</option>
            <option value="COMPLETED">Completed</option>
          </select>

          <!-- Branch Filter -->
          <select
            v-model="selectedStore"
            @change="fetchRfqs"
            class="bg-[#F4F6FA] text-xs font-semibold text-[#172033] rounded-lg px-3 py-2 border border-slate-300 focus:outline-none focus:border-[#1865C0]"
          >
            <option value="">All Distribution Hubs</option>
            <option value="1">Central Distribution Hub (HQ)</option>
            <option value="2">Downtown Commercial Center</option>
            <option value="3">Airport Logistics Terminal</option>
          </select>
        </div>
      </div>

      <!-- RFQ Data Table -->
      <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="bg-[#F8FAFC] text-slate-600 font-bold uppercase tracking-wider border-b border-slate-200">
              <tr>
                <th class="p-4">RFQ Reference</th>
                <th class="p-4">Submitted</th>
                <th class="p-4">Customer & Organization</th>
                <th class="p-4">Equipment Line Items</th>
                <th class="p-4">Hub Context</th>
                <th class="p-4">Status</th>
                <th class="p-4 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-if="loading">
                <td colspan="7" class="p-8 text-center text-slate-400">
                  <div class="inline-block animate-spin w-5 h-5 border-2 border-[#042562] border-t-transparent rounded-full mb-2"></div>
                  <div>Loading quotation requests...</div>
                </td>
              </tr>

              <tr v-else-if="rfqs.length === 0">
                <td colspan="7" class="p-12 text-center text-slate-500">
                  <FileSpreadsheet class="w-8 h-8 text-slate-400 mx-auto mb-2" />
                  <p class="font-bold">No Request for Quotation records found.</p>
                  <p class="text-[11px] text-slate-400">Try adjusting your search criteria or status filter.</p>
                </td>
              </tr>

              <tr
                v-for="rfq in rfqs"
                :key="rfq.id"
                class="hover:bg-[#F8FAFC] transition-colors cursor-pointer"
                @click="openDetails(rfq)"
              >
                <!-- RFQ Reference -->
                <td class="p-4 font-mono font-black text-[#042562]">
                  {{ rfq.rfq_number }}
                </td>

                <!-- Date -->
                <td class="p-4 text-slate-500 whitespace-nowrap">
                  {{ rfq.formatted_submitted_at || 'Recent' }}
                </td>

                <!-- Customer & Company -->
                <td class="p-4">
                  <div class="font-bold text-[#172033]">{{ rfq.customer_name }}</div>
                  <div class="text-[11px] text-slate-500 font-medium">{{ rfq.company_name || 'Individual Enterprise' }}</div>
                  <div class="text-[10px] text-blue-600">{{ rfq.customer_email }}</div>
                </td>

                <!-- Items & Quantity -->
                <td class="p-4">
                  <span class="font-bold text-[#042562]">{{ rfq.items_count || (rfq.items ? rfq.items.length : 1) }} models</span>
                  <span class="text-slate-400 text-[11px] block">
                    ({{ rfq.total_quantity || 1 }} total units)
                  </span>
                </td>

                <!-- Preferred Store -->
                <td class="p-4 text-slate-600">
                  {{ rfq.preferred_store?.name || 'Central Distribution Hub (HQ)' }}
                </td>

                <!-- Status Badge -->
                <td class="p-4">
                  <span
                    :class="[
                      'inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-0.5 rounded-full',
                      getStatusBadgeClass(rfq.status),
                    ]"
                  >
                    <span class="w-1.5 h-1.5 rounded-full" :class="getStatusDotClass(rfq.status)"></span>
                    {{ getStatusLabel(rfq.status) }}
                  </span>
                </td>

                <!-- Actions -->
                <td class="p-4 text-right" @click.stop>
                  <button
                    @click="openDetails(rfq)"
                    class="bg-[#F4F6FA] hover:bg-[#042562] hover:text-white text-[#042562] px-3 py-1.5 rounded-lg text-xs font-bold transition-colors shadow-sm"
                  >
                    Review BOM
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Slide-over / Modal Detailed Inspection Drawer -->
    <div
      v-if="selectedRfq"
      class="fixed inset-0 z-50 overflow-hidden flex justify-end"
    >
      <!-- Backdrop -->
      <div
        class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"
        @click="selectedRfq = null"
      ></div>

      <!-- Slide-over Drawer Panel -->
      <div class="relative bg-white w-full max-w-2xl h-full shadow-2xl flex flex-col z-10 animate-in slide-in-from-right duration-300">
        <!-- Drawer Header -->
        <div class="bg-[#042562] text-white p-6 flex items-center justify-between border-b border-navy-800">
          <div>
            <div class="flex items-center gap-2">
              <span class="text-xs font-bold uppercase tracking-widest text-[#1865C0] bg-white/10 px-2 py-0.5 rounded">
                BOM Inspection
              </span>
              <span class="font-mono text-xs text-blue-200">{{ selectedRfq.formatted_submitted_at }}</span>
            </div>
            <h2 class="text-xl font-black mt-1">{{ selectedRfq.rfq_number }}</h2>
          </div>

          <button
            @click="selectedRfq = null"
            class="text-slate-300 hover:text-white p-1 rounded-lg hover:bg-white/10 transition-colors"
          >
            <X class="w-6 h-6" />
          </button>
        </div>

        <!-- Drawer Scrollable Body -->
        <div class="flex-1 overflow-y-auto p-6 space-y-6">
          <!-- 1. Customer & Delivery Site Card -->
          <div class="bg-[#F8FAFC] rounded-xl border border-slate-200 p-4 space-y-3">
            <h4 class="text-xs font-black uppercase tracking-wider text-[#042562]">
              Customer & Organization Details
            </h4>
            <div class="grid grid-cols-2 gap-3 text-xs">
              <div>
                <span class="text-slate-500 block text-[11px]">Contact Person:</span>
                <strong class="text-slate-800">{{ selectedRfq.customer_name }}</strong>
              </div>
              <div>
                <span class="text-slate-500 block text-[11px]">Organization:</span>
                <strong class="text-slate-800">{{ selectedRfq.company_name || 'N/A' }}</strong>
              </div>
              <div>
                <span class="text-slate-500 block text-[11px]">Corporate Email:</span>
                <a :href="'mailto:' + selectedRfq.customer_email" class="text-[#1865C0] font-semibold">
                  {{ selectedRfq.customer_email }}
                </a>
              </div>
              <div>
                <span class="text-slate-500 block text-[11px]">Direct Telephone:</span>
                <strong class="text-slate-800">{{ selectedRfq.customer_phone }}</strong>
              </div>
              <div class="col-span-2">
                <span class="text-slate-500 block text-[11px]">Fulfillment Location / Site Address:</span>
                <strong class="text-slate-800">{{ selectedRfq.location || selectedRfq.address || 'Standard Delivery' }}</strong>
              </div>
            </div>

            <!-- Customer Notes -->
            <div v-if="selectedRfq.customer_notes" class="pt-2 border-t border-slate-200 text-xs">
              <span class="text-slate-500 font-semibold block text-[11px]">Customer Technical Requirements:</span>
              <p class="text-slate-700 italic mt-0.5">{{ selectedRfq.customer_notes }}</p>
            </div>
          </div>

          <!-- 2. Requested Hardware Items List -->
          <div>
            <h4 class="text-xs font-black uppercase tracking-wider text-[#042562] mb-3">
              Requested Hardware Line Items ({{ selectedRfq.items ? selectedRfq.items.length : 0 }})
            </h4>
            <div class="border border-slate-200 rounded-xl overflow-hidden divide-y divide-slate-100">
              <div
                v-for="item in selectedRfq.items"
                :key="item.id"
                class="p-3.5 flex items-center justify-between gap-3 text-xs hover:bg-[#F8FAFC]"
              >
                <div class="flex items-center gap-3 min-w-0 flex-1">
                  <div class="w-12 h-12 rounded bg-[#F4F6FA] border border-slate-200 p-1 flex-shrink-0 flex items-center justify-center">
                    <img
                      :src="item.primary_image_url || '/images/products/placeholder.png'"
                      :alt="item.product_name"
                      class="max-h-full max-w-full object-contain"
                    />
                  </div>
                  <div class="min-w-0 flex-1">
                    <span class="text-[10px] font-bold text-[#1865C0] uppercase block truncate">
                      {{ item.brand_name || 'Hardware' }}
                    </span>
                    <strong class="text-[#042562] block truncate">{{ item.product_name }}</strong>
                    <span class="text-[10px] font-mono text-slate-500 block">
                      Model: {{ item.model_number }} • SKU: {{ item.product_sku }}
                    </span>
                    <span v-if="item.notes" class="text-[10px] text-amber-700 font-medium block mt-0.5">
                      Note: {{ item.notes }}
                    </span>
                  </div>
                </div>

                <div class="text-right flex-shrink-0">
                  <span class="text-xs font-bold text-slate-500 block">Requested:</span>
                  <span class="text-sm font-black text-[#042562]">{{ item.quantity }} units</span>
                </div>
              </div>
            </div>
          </div>

          <!-- 3. Lifecycle Status Management Console -->
          <div class="bg-blue-50/70 rounded-xl border border-blue-200 p-5 space-y-4">
            <div class="flex items-center justify-between">
              <h4 class="text-xs font-black uppercase tracking-wider text-[#042562]">
                Lifecycle Status Transition
              </h4>
              <span
                :class="[
                  'text-xs font-bold px-2.5 py-0.5 rounded-full',
                  getStatusBadgeClass(selectedRfq.status),
                ]"
              >
                Current: {{ getStatusLabel(selectedRfq.status) }}
              </span>
            </div>

            <div class="space-y-3">
              <div>
                <label class="block text-[11px] font-bold text-slate-700 mb-1">
                  Change Status To:
                </label>
                <select
                  v-model="newStatus"
                  class="w-full bg-white text-xs font-bold text-[#172033] rounded-lg px-3 py-2 border border-slate-300 focus:outline-none focus:border-[#1865C0]"
                >
                  <option value="NEW_REQUEST">New Request</option>
                  <option value="UNDER_REVIEW">Under Review</option>
                  <option value="QUOTATION_PREPARED">Quotation Prepared</option>
                  <option value="SENT_TO_CUSTOMER">Sent to Customer</option>
                  <option value="APPROVED">Approved</option>
                  <option value="REJECTED">Rejected</option>
                  <option value="CONVERTED_TO_ORDER">Converted to Sales Order</option>
                  <option value="COMPLETED">Completed</option>
                </select>
              </div>

              <div>
                <label class="block text-[11px] font-bold text-slate-700 mb-1">
                  Transition Reason / Audit Note:
                </label>
                <input
                  type="text"
                  v-model="statusNote"
                  placeholder="e.g. Stock verified at Central HQ; BOM transferred to Quotation Desk"
                  class="w-full bg-white text-xs text-[#172033] rounded-lg px-3 py-2 border border-slate-300 focus:outline-none focus:border-[#1865C0]"
                />
              </div>

              <button
                @click="updateStatus"
                :disabled="updatingStatus || newStatus === selectedRfq.status"
                class="w-full bg-[#042562] hover:bg-[#063485] text-white text-xs font-bold uppercase tracking-wider py-2.5 rounded-lg transition-colors shadow-sm disabled:opacity-50"
              >
                <span>{{ updatingStatus ? 'Recording Transition...' : 'Update Status & Log Audit' }}</span>
              </button>
            </div>
          </div>

          <!-- 4. Complete Status History Timeline -->
          <div>
            <h4 class="text-xs font-black uppercase tracking-wider text-[#042562] mb-3">
              Status Change Audit Timeline
            </h4>
            <div class="border border-slate-200 rounded-xl p-4 space-y-3 bg-white">
              <div
                v-for="hist in (selectedRfq.status_histories || [])"
                :key="hist.id"
                class="flex items-start gap-3 text-xs pb-3 border-b border-slate-100 last:border-b-0 last:pb-0"
              >
                <div class="w-6 h-6 rounded-full bg-blue-100 text-[#1865C0] flex items-center justify-center font-bold text-[10px] flex-shrink-0 mt-0.5">
                  <Check class="w-3.5 h-3.5" />
                </div>
                <div class="flex-1 min-w-0">
                  <div class="flex items-center justify-between">
                    <strong class="text-[#042562]">{{ hist.status_label || hist.status }}</strong>
                    <span class="text-[10px] text-slate-400 font-mono">{{ hist.formatted_date }}</span>
                  </div>
                  <div class="text-[11px] text-slate-600">
                    By: <strong>{{ hist.user_name }}</strong>
                  </div>
                  <p v-if="hist.notes" class="text-[11px] text-slate-500 italic mt-0.5">
                    "{{ hist.notes }}"
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- 5. Internal Engineering Notes -->
          <div class="space-y-2">
            <h4 class="text-xs font-black uppercase tracking-wider text-[#042562]">
              Internal Engineering Notes
            </h4>
            <textarea
              v-model="adminNotes"
              rows="3"
              placeholder="Add confidential notes for staff and assigned sales managers..."
              class="w-full bg-[#F4F6FA] text-xs text-[#172033] rounded-lg p-3 border border-slate-300 focus:outline-none focus:border-[#1865C0]"
            ></textarea>
            <button
              @click="saveAdminNotes"
              :disabled="savingNotes"
              class="bg-slate-700 hover:bg-slate-800 text-white text-xs font-bold uppercase tracking-wider px-4 py-2 rounded-lg transition-colors"
            >
              <span>{{ savingNotes ? 'Saving...' : 'Save Internal Notes' }}</span>
            </button>
          </div>
        </div>

        <!-- Drawer Footer: Next Step CTA -->
        <div class="p-4 bg-[#F8FAFC] border-t border-slate-200 flex items-center justify-between gap-3">
          <div class="text-xs text-slate-500">
            Rule: RFQ never affects revenue until converted to confirmed order.
          </div>
          <button
            @click="prepareQuotation"
            class="inline-flex items-center gap-1.5 bg-[#1865C0] hover:bg-blue-700 text-white text-xs font-bold uppercase tracking-wider px-5 py-2.5 rounded-lg transition-colors shadow-sm"
          >
            <FileSpreadsheet class="w-4 h-4" />
            <span>Prepare Formal Quotation</span>
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import AdminLayout from '@/layouts/AdminLayout.vue';
import rfqService from '@/services/rfqService';
import { useNotificationStore } from '@/stores/notificationStore';
import {
  FileSpreadsheet,
  Clock,
  Layers,
  CheckCircle2,
  Search,
  ExternalLink,
  X,
  Check,
} from 'lucide-vue-next';

const router = useRouter();
const notify = useNotificationStore();

const loading = ref(true);
const rfqs = ref([]);
const searchQuery = ref('');
const selectedStatus = ref('');
const selectedStore = ref('');

const selectedRfq = ref(null);
const newStatus = ref('');
const statusNote = ref('');
const updatingStatus = ref(false);
const adminNotes = ref('');
const savingNotes = ref(false);

const totalCount = computed(() => rfqs.value.length);
const newCount = computed(() => rfqs.value.filter((r) => r.status === 'NEW_REQUEST').length);
const reviewCount = computed(() => rfqs.value.filter((r) => r.status === 'UNDER_REVIEW').length);
const quotedCount = computed(() =>
  rfqs.value.filter((r) => ['QUOTATION_PREPARED', 'SENT_TO_CUSTOMER'].includes(r.status)).length
);

const getStatusLabel = (status) => {
  const map = {
    NEW_REQUEST: 'New Request',
    UNDER_REVIEW: 'Under Review',
    QUOTATION_PREPARED: 'Quotation Prepared',
    SENT_TO_CUSTOMER: 'Sent to Customer',
    APPROVED: 'Approved',
    REJECTED: 'Rejected',
    CONVERTED_TO_ORDER: 'Converted to Order',
    COMPLETED: 'Completed',
  };
  return map[status] || status;
};

const getStatusBadgeClass = (status) => {
  switch (status) {
    case 'NEW_REQUEST':
      return 'bg-blue-100 text-blue-900 border border-blue-300';
    case 'UNDER_REVIEW':
      return 'bg-amber-100 text-amber-900 border border-amber-300';
    case 'QUOTATION_PREPARED':
      return 'bg-indigo-100 text-indigo-900 border border-indigo-300';
    case 'SENT_TO_CUSTOMER':
      return 'bg-sky-100 text-sky-900 border border-sky-300';
    case 'APPROVED':
      return 'bg-emerald-100 text-emerald-900 border border-emerald-300';
    case 'REJECTED':
      return 'bg-red-100 text-red-900 border border-red-300';
    case 'CONVERTED_TO_ORDER':
      return 'bg-purple-100 text-purple-900 border border-purple-300';
    case 'COMPLETED':
      return 'bg-slate-100 text-slate-900 border border-slate-300';
    default:
      return 'bg-slate-100 text-slate-800';
  }
};

const getStatusDotClass = (status) => {
  switch (status) {
    case 'NEW_REQUEST': return 'bg-blue-600';
    case 'UNDER_REVIEW': return 'bg-amber-600';
    case 'QUOTATION_PREPARED': return 'bg-indigo-600';
    case 'SENT_TO_CUSTOMER': return 'bg-sky-600';
    case 'APPROVED': return 'bg-emerald-600';
    case 'REJECTED': return 'bg-red-600';
    case 'CONVERTED_TO_ORDER': return 'bg-purple-600';
    case 'COMPLETED': return 'bg-slate-600';
    default: return 'bg-slate-400';
  }
};

let searchTimer = null;
const debounceSearch = () => {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => {
    fetchRfqs();
  }, 350);
};

const fetchRfqs = async () => {
  loading.value = true;
  try {
    const params = {
      search: searchQuery.value || undefined,
      status: selectedStatus.value || undefined,
      store_id: selectedStore.value || undefined,
    };
    const res = await rfqService.getAdminRfqs(params);
    rfqs.value = res.data || [];
  } catch (err) {
    console.error('Failed to load RFQs', err);
  } finally {
    loading.value = false;
  }
};

const openDetails = (rfq) => {
  selectedRfq.value = rfq;
  newStatus.value = rfq.status;
  statusNote.value = '';
  adminNotes.value = rfq.admin_notes || '';
};

const updateStatus = async () => {
  if (!selectedRfq.value || !newStatus.value) return;
  updatingStatus.value = true;
  try {
    const res = await rfqService.updateRfqStatus(selectedRfq.value.id, {
      status: newStatus.value,
      notes: statusNote.value || undefined,
    });
    notify.success(res.message || 'Status updated.');
    selectedRfq.value.status = newStatus.value;
    statusNote.value = '';
    fetchRfqs();
  } catch (err) {
    console.error(err);
    notify.error('Failed to update status.');
  } finally {
    updatingStatus.value = false;
  }
};

const saveAdminNotes = async () => {
  if (!selectedRfq.value) return;
  savingNotes.value = true;
  try {
    await rfqService.updateRfqNotes(selectedRfq.value.id, {
      admin_notes: adminNotes.value,
    });
    notify.success('Internal notes saved.');
    selectedRfq.value.admin_notes = adminNotes.value;
  } catch (err) {
    console.error(err);
    notify.error('Failed to save notes.');
  } finally {
    savingNotes.value = false;
  }
};

const prepareQuotation = () => {
  notify.info(`Navigating to Quotation Preparation for ${selectedRfq.value.rfq_number}...`);
  // Will link directly to Quotation preparation in Phase 7
};

onMounted(() => {
  fetchRfqs();
});
</script>
