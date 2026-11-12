<template>
  <AdminLayout>
    <div class="space-y-6">
      <!-- Executive Welcome & Branch Banner -->
      <div class="bg-gradient-to-r from-[#042562] to-[#1865C0] rounded-xl p-6 text-white shadow-md flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-white/10 text-[11px] font-semibold tracking-wider text-blue-200 uppercase mb-2">
            <ShieldCheck class="w-3.5 h-3.5 text-blue-300" />
            iRACK Enterprise Administration &bull; Management Dashboard
          </span>
          <h2 class="text-xl sm:text-2xl font-black tracking-tight">
            Welcome back, {{ auth.user?.name || 'Alexander Vance' }}
          </h2>
          <p class="text-xs text-blue-100 mt-1 max-w-xl">
            Currently overseeing multi-store operations for <strong class="text-white">{{ auth.currentStore?.name || 'All Regional Branches' }}</strong>.
          </p>
        </div>

        <div class="flex items-center gap-3">
          <router-link
            to="/admin/reports"
            class="px-4 py-2 rounded-lg bg-white text-[#042562] hover:bg-slate-100 text-xs font-bold uppercase tracking-wider shadow-sm transition-colors flex items-center gap-1.5"
          >
            <BarChart3 class="w-3.5 h-3.5" />
            <span>View Analytics & Reports</span>
          </router-link>
          <router-link
            to="/admin/inventory"
            class="px-4 py-2 rounded-lg bg-[#E20913] hover:bg-[#be0710] text-white text-xs font-bold uppercase tracking-wider shadow-sm transition-colors"
          >
            Manage Stock
          </router-link>
        </div>
      </div>

      <!-- Management Dashboard KPI Grid (Requirement 1: 12 Key Metrics) -->
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">
        <!-- Metric 1: Registered Products -->
        <div class="bg-white rounded-xl border border-slate-200 p-3.5 shadow-sm">
          <div class="flex items-center justify-between text-slate-400">
            <span class="text-[10px] font-bold uppercase tracking-wider text-[#64748B]">Products</span>
            <Layers class="w-3.5 h-3.5 text-[#1865C0]" />
          </div>
          <div class="text-xl font-black text-[#042562] mt-1.5">{{ metrics.total_products || 0 }}</div>
          <span class="text-[9px] text-slate-400">Registered Models</span>
        </div>

        <!-- Metric 2: Categories -->
        <div class="bg-white rounded-xl border border-slate-200 p-3.5 shadow-sm">
          <div class="flex items-center justify-between text-slate-400">
            <span class="text-[10px] font-bold uppercase tracking-wider text-[#64748B]">Categories</span>
            <FolderTree class="w-3.5 h-3.5 text-[#1865C0]" />
          </div>
          <div class="text-xl font-black text-[#042562] mt-1.5">{{ metrics.total_categories || 0 }}</div>
          <span class="text-[9px] text-slate-400">Taxonomy Groups</span>
        </div>

        <!-- Metric 3: Active Stores -->
        <div class="bg-white rounded-xl border border-slate-200 p-3.5 shadow-sm">
          <div class="flex items-center justify-between text-slate-400">
            <span class="text-[10px] font-bold uppercase tracking-wider text-[#64748B]">Stores</span>
            <Building class="w-3.5 h-3.5 text-blue-600" />
          </div>
          <div class="text-xl font-black text-[#042562] mt-1.5">{{ metrics.active_stores || 3 }}</div>
          <span class="text-[9px] text-emerald-600 font-semibold">Active Branches</span>
        </div>

        <!-- Metric 4: Stock Quantity -->
        <div class="bg-white rounded-xl border border-slate-200 p-3.5 shadow-sm">
          <div class="flex items-center justify-between text-slate-400">
            <span class="text-[10px] font-bold uppercase tracking-wider text-[#64748B]">Total Stock</span>
            <Boxes class="w-3.5 h-3.5 text-[#042562]" />
          </div>
          <div class="text-xl font-black text-[#042562] mt-1.5">{{ metrics.current_stock_quantity || 0 }}</div>
          <span class="text-[9px] text-slate-400">Units On-Hand</span>
        </div>

        <!-- Metric 5: Total Inventory Value -->
        <div class="bg-white rounded-xl border border-slate-200 p-3.5 shadow-sm">
          <div class="flex items-center justify-between text-slate-400">
            <span class="text-[10px] font-bold uppercase tracking-wider text-[#64748B]">Inventory Value</span>
            <BadgeDollarSign class="w-3.5 h-3.5 text-emerald-600" />
          </div>
          <div class="text-lg font-black text-emerald-700 mt-1.5 truncate">{{ formatCurrencyShort(metrics.total_inventory_value || 0) }}</div>
          <span class="text-[9px] text-slate-400">Cost Valuation</span>
        </div>

        <!-- Metric 6: Low Stock Products -->
        <div class="bg-white rounded-xl border border-slate-200 p-3.5 shadow-sm">
          <div class="flex items-center justify-between text-slate-400">
            <span class="text-[10px] font-bold uppercase tracking-wider text-[#64748B]">Low Stock</span>
            <AlertTriangle class="w-3.5 h-3.5 text-amber-500" />
          </div>
          <div class="text-xl font-black text-amber-600 mt-1.5">{{ metrics.low_stock_products || 0 }}</div>
          <span class="text-[9px] text-amber-700 font-semibold">Under Threshold</span>
        </div>

        <!-- Metric 7: Out of Stock -->
        <div class="bg-white rounded-xl border border-slate-200 p-3.5 shadow-sm">
          <div class="flex items-center justify-between text-slate-400">
            <span class="text-[10px] font-bold uppercase tracking-wider text-[#64748B]">Out of Stock</span>
            <AlertCircle class="w-3.5 h-3.5 text-red-500" />
          </div>
          <div class="text-xl font-black text-red-600 mt-1.5">{{ metrics.out_of_stock_products || 0 }}</div>
          <span class="text-[9px] text-red-600 font-semibold">Depleted Items</span>
        </div>

        <!-- Metric 8: Total Confirmed Revenue -->
        <div class="bg-white rounded-xl border border-slate-200 p-3.5 shadow-sm">
          <div class="flex items-center justify-between text-slate-400">
            <span class="text-[10px] font-bold uppercase tracking-wider text-[#64748B]">Sales Revenue</span>
            <TrendingUp class="w-3.5 h-3.5 text-[#16A34A]" />
          </div>
          <div class="text-lg font-black text-[#16A34A] mt-1.5 truncate">{{ formatCurrencyShort(metrics.total_confirmed_revenue || 0) }}</div>
          <span class="text-[9px] text-slate-400">Confirmed Orders</span>
        </div>

        <!-- Metric 9: Periodic Sales (Daily/Weekly/Monthly) -->
        <div class="bg-white rounded-xl border border-slate-200 p-3.5 shadow-sm">
          <div class="flex items-center justify-between text-slate-400">
            <span class="text-[10px] font-bold uppercase tracking-wider text-[#64748B]">Monthly Sales</span>
            <Clock class="w-3.5 h-3.5 text-indigo-600" />
          </div>
          <div class="text-lg font-black text-indigo-900 mt-1.5 truncate">{{ formatCurrencyShort(metrics.monthly_sales || 0) }}</div>
          <span class="text-[9px] text-slate-400">MTD Recognized</span>
        </div>

        <!-- Metric 10: Total RFQs -->
        <div class="bg-white rounded-xl border border-slate-200 p-3.5 shadow-sm">
          <div class="flex items-center justify-between text-slate-400">
            <span class="text-[10px] font-bold uppercase tracking-wider text-[#64748B]">Total RFQs</span>
            <FileQuestion class="w-3.5 h-3.5 text-amber-500" />
          </div>
          <div class="text-xl font-black text-amber-600 mt-1.5">{{ metrics.total_rfqs || 0 }}</div>
          <span class="text-[9px] text-slate-400">Client Inquiries</span>
        </div>

        <!-- Metric 11: Pending Quotations -->
        <div class="bg-white rounded-xl border border-slate-200 p-3.5 shadow-sm">
          <div class="flex items-center justify-between text-slate-400">
            <span class="text-[10px] font-bold uppercase tracking-wider text-[#64748B]">Pending Quotes</span>
            <FileText class="w-3.5 h-3.5 text-[#1865C0]" />
          </div>
          <div class="text-xl font-black text-[#1865C0] mt-1.5">{{ metrics.pending_quotations || 0 }}</div>
          <span class="text-[9px] text-slate-400">Awaiting Decision</span>
        </div>

        <!-- Metric 12: Completed Sales Orders -->
        <div class="bg-white rounded-xl border border-slate-200 p-3.5 shadow-sm">
          <div class="flex items-center justify-between text-slate-400">
            <span class="text-[10px] font-bold uppercase tracking-wider text-[#64748B]">Fulfilled Orders</span>
            <CheckCircle2 class="w-3.5 h-3.5 text-emerald-600" />
          </div>
          <div class="text-xl font-black text-emerald-700 mt-1.5">{{ metrics.completed_sales_orders || 0 }}</div>
          <span class="text-[9px] text-slate-400">Completed & Delivered</span>
        </div>
      </div>

      <!-- Main Operational Split: Store Branches & Quick Actions -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Store Branch Inventory Allocation -->
        <div class="lg:col-span-2 corporate-card p-6">
          <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div>
              <h3 class="text-sm font-bold text-[#042562] uppercase tracking-wider">Regional Stores Stock Allocation</h3>
              <p class="text-xs text-slate-500 mt-0.5">Real-time distribution across active physical stores</p>
            </div>
            <router-link to="/admin/inventory" class="text-xs font-semibold text-[#1865C0] hover:underline flex items-center gap-1">
              <span>View Stock Ledger</span>
              <ChevronRight class="w-3.5 h-3.5" />
            </router-link>
          </div>

          <div class="mt-5 space-y-4">
            <div
              v-for="store in storesOverview"
              :key="store.code"
              class="p-4 rounded-lg bg-[#F4F6FA] border border-slate-200 flex items-center justify-between"
            >
              <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-md bg-[#042562] text-white flex items-center justify-center font-bold text-xs">
                  {{ store.code.split('-')[1] }}
                </div>
                <div>
                  <h4 class="text-xs font-bold text-[#172033]">{{ store.name }}</h4>
                  <p class="text-[11px] text-slate-500 font-mono">{{ store.code }} &bull; {{ store.city }}</p>
                </div>
              </div>

              <div class="flex items-center gap-6 text-right">
                <div>
                  <span class="text-[10px] uppercase font-bold text-slate-400 block">Inventory</span>
                  <span class="text-sm font-extrabold text-[#042562]">{{ store.units }} Units</span>
                </div>
                <div>
                  <span class="text-[10px] uppercase font-bold text-slate-400 block">Alerts</span>
                  <span
                    :class="[
                      'text-xs font-bold px-2 py-0.5 rounded',
                      store.lowStock > 0 ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800'
                    ]"
                  >
                    {{ store.lowStock > 0 ? `${store.lowStock} Low Stock` : 'Optimal' }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Workflows & System Health -->
        <div class="space-y-6">
          <div class="corporate-card p-6">
            <h3 class="text-sm font-bold text-[#042562] uppercase tracking-wider mb-4">Quick Workflows</h3>
            <div class="space-y-2.5">
              <router-link
                to="/admin/reports"
                class="w-full flex items-center justify-between p-3 rounded-lg border border-slate-200 hover:border-[#1865C0] hover:bg-blue-50/50 transition-all text-xs font-semibold text-[#172033]"
              >
                <div class="flex items-center gap-2.5">
                  <BarChart3 class="w-4 h-4 text-[#1865C0]" />
                  <span>Interactive Sales Analytics</span>
                </div>
                <ChevronRight class="w-3.5 h-3.5 text-slate-400" />
              </router-link>

              <router-link
                to="/admin/quotations"
                class="w-full flex items-center justify-between p-3 rounded-lg border border-slate-200 hover:border-[#1865C0] hover:bg-blue-50/50 transition-all text-xs font-semibold text-[#172033]"
              >
                <div class="flex items-center gap-2.5">
                  <FilePlus2 class="w-4 h-4 text-[#E20913]" />
                  <span>Prepare Formal Quotation</span>
                </div>
                <ChevronRight class="w-3.5 h-3.5 text-slate-400" />
              </router-link>

              <router-link
                to="/admin/sales-orders"
                class="w-full flex items-center justify-between p-3 rounded-lg border border-slate-200 hover:border-[#1865C0] hover:bg-blue-50/50 transition-all text-xs font-semibold text-[#172033]"
              >
                <div class="flex items-center gap-2.5">
                  <BadgeDollarSign class="w-4 h-4 text-[#16A34A]" />
                  <span>Confirmed Sales Orders</span>
                </div>
                <ChevronRight class="w-3.5 h-3.5 text-slate-400" />
              </router-link>
            </div>
          </div>

          <!-- System Diagnostics Card -->
          <div class="corporate-card p-6 bg-slate-50/70">
            <h3 class="text-xs font-bold text-[#042562] uppercase tracking-wider mb-3">System Health</h3>
            <div class="space-y-2 text-xs">
              <div class="flex items-center justify-between py-1.5 border-b border-slate-200">
                <span class="text-slate-500">API Service</span>
                <span class="font-semibold text-emerald-700 flex items-center gap-1">
                  <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active (v1.0.0)
                </span>
              </div>
              <div class="flex items-center justify-between py-1.5 border-b border-slate-200">
                <span class="text-slate-500">Database Engine</span>
                <span class="font-semibold text-[#042562]">MySQL 8.0 (3NF)</span>
              </div>
              <div class="flex items-center justify-between py-1.5">
                <span class="text-slate-500">Sanctum Token Auth</span>
                <span class="font-semibold text-emerald-700">Enforced</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { useAuthStore } from '@/stores/authStore';
import { analyticsService } from '@/services/analyticsService';
import {
  ShieldCheck,
  Boxes,
  FileQuestion,
  FileText,
  BadgeDollarSign,
  ChevronRight,
  BarChart3,
  FilePlus2,
  Layers,
  FolderTree,
  Building,
  AlertTriangle,
  AlertCircle,
  TrendingUp,
  Clock,
  CheckCircle2
} from 'lucide-vue-next';

const auth = useAuthStore();
const metrics = ref({});

function formatCurrencyShort(val) {
  if (!val) return 'SAR 0';
  if (val >= 1000000) return `SAR ${(val / 1000000).toFixed(1)}M`;
  if (val >= 1000) return `SAR ${(val / 1000).toFixed(1)}k`;
  return `SAR ${Number(val).toLocaleString()}`;
}

const storesOverview = [
  {
    code: 'ST-HQ-01',
    name: 'iRACK Central Distribution Hub (HQ)',
    city: 'Riyadh',
    units: 84,
    lowStock: 0,
  },
  {
    code: 'ST-NOR-02',
    name: 'iRACK North Logistics Facility',
    city: 'Dammam',
    units: 58,
    lowStock: 1,
  },
  {
    code: 'ST-WST-03',
    name: 'iRACK West Coast Regional Store',
    city: 'Jeddah',
    units: 36,
    lowStock: 0,
  },
];

async function loadDashboard() {
  try {
    const res = await analyticsService.getDashboardMetrics();
    metrics.value = res.data || {};
  } catch (err) {
    console.warn('Dashboard metrics fetch error:', err);
  }
}

onMounted(() => {
  loadDashboard();
});
</script>
