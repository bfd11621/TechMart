<template>
  <AdminLayout>
    <div class="space-y-6">
      <!-- Page Header & Action Controls -->
      <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-black text-[#042562] tracking-tight">
            Business Intelligence & Reports
          </h1>
          <p class="text-xs text-slate-500 mt-0.5">
            Interactive sales analytics, ECharts visualizations, stock velocity, and downloadable branded PDF/CSV reports.
          </p>
        </div>

        <!-- Export Buttons Toolbar -->
        <div class="flex flex-wrap items-center gap-2">
          <div class="relative inline-block text-left">
            <button
              @click="toggleExportMenu"
              class="inline-flex items-center gap-2 bg-[#042562] hover:bg-[#1865C0] text-white text-xs font-bold uppercase tracking-wider px-4 py-2.5 rounded-lg transition-colors shadow-sm"
            >
              <Download class="w-4 h-4" />
              <span>Export Audit Reports</span>
              <ChevronDown class="w-3.5 h-3.5" />
            </button>

            <!-- Dropdown Menu -->
            <div
              v-if="exportMenuOpen"
              class="origin-top-right absolute right-0 mt-2 w-64 rounded-xl shadow-xl bg-white ring-1 ring-black ring-opacity-5 divide-y divide-slate-100 z-50 text-xs font-semibold"
            >
              <div class="p-2 space-y-1">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider px-2 block py-1">Sales Reports</span>
                <button
                  @click="exportSalesPdf"
                  class="w-full text-left px-3 py-2 rounded-lg hover:bg-slate-100 text-slate-700 flex items-center gap-2"
                >
                  <FileText class="w-4 h-4 text-[#1865C0]" />
                  <span>Sales Performance Report (PDF)</span>
                </button>
                <button
                  @click="exportSalesCsv"
                  class="w-full text-left px-3 py-2 rounded-lg hover:bg-slate-100 text-slate-700 flex items-center gap-2"
                >
                  <FileSpreadsheet class="w-4 h-4 text-emerald-600" />
                  <span>Sales Transactions Ledger (CSV)</span>
                </button>
              </div>

              <div class="p-2 space-y-1">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider px-2 block py-1">Inventory Reports</span>
                <button
                  @click="exportInventoryPdf"
                  class="w-full text-left px-3 py-2 rounded-lg hover:bg-slate-100 text-slate-700 flex items-center gap-2"
                >
                  <FileText class="w-4 h-4 text-[#1865C0]" />
                  <span>Inventory Valuation Audit (PDF)</span>
                </button>
                <button
                  @click="exportInventoryCsv"
                  class="w-full text-left px-3 py-2 rounded-lg hover:bg-slate-100 text-slate-700 flex items-center gap-2"
                >
                  <FileSpreadsheet class="w-4 h-4 text-emerald-600" />
                  <span>Store Stock Balances (CSV)</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Filter Horizon Bar -->
      <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <!-- Period Preset Selector -->
        <div class="flex items-center gap-1 overflow-x-auto w-full md:w-auto text-xs">
          <button
            v-for="p in periodPresets"
            :key="p.value"
            @click="setPeriod(p.value)"
            :class="[
              'px-3 py-1.5 font-bold rounded-lg transition-colors whitespace-nowrap',
              selectedPeriod === p.value
                ? 'bg-[#042562] text-white shadow-sm'
                : 'text-slate-600 hover:bg-slate-100'
            ]"
          >
            {{ p.label }}
          </button>
        </div>

        <!-- Custom Date Range & Store Scope -->
        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
          <div v-if="selectedPeriod === 'custom'" class="flex items-center gap-2">
            <input
              type="date"
              v-model="customFromDate"
              class="bg-[#F4F6FA] text-xs font-semibold rounded-lg px-2.5 py-1.5 border border-slate-300"
            />
            <span class="text-xs text-slate-400">to</span>
            <input
              type="date"
              v-model="customToDate"
              class="bg-[#F4F6FA] text-xs font-semibold rounded-lg px-2.5 py-1.5 border border-slate-300"
            />
            <button
              @click="applyCustomDate"
              class="bg-[#1865C0] hover:bg-[#042562] text-white px-3 py-1.5 rounded-lg text-xs font-bold"
            >
              Apply
            </button>
          </div>

          <select
            v-model="selectedStore"
            @change="fetchAnalyticsData"
            class="bg-[#F4F6FA] text-xs font-semibold text-[#172033] rounded-lg px-3 py-2 border border-slate-300 focus:outline-none focus:border-[#1865C0]"
          >
            <option value="">All Regional Stores (HQ Consolidated)</option>
            <option value="1">Central Distribution Hub (HQ)</option>
            <option value="2">North Logistics Facility</option>
            <option value="3">West Coast Regional Store</option>
          </select>

          <button
            @click="fetchAnalyticsData"
            class="p-2 text-slate-500 hover:text-[#042562] bg-[#F4F6FA] border border-slate-300 rounded-lg hover:bg-slate-100 transition-colors"
            title="Refresh Analytics"
          >
            <RefreshCw :class="['w-4 h-4', { 'animate-spin': loading }]" />
          </button>
        </div>
      </div>

      <!-- Navigation Tabs -->
      <div class="flex items-center gap-2 border-b border-slate-200 text-xs font-bold">
        <button
          v-for="tab in viewTabs"
          :key="tab.value"
          @click="activeTab = tab.value"
          :class="[
            'pb-3 px-3 transition-colors border-b-2 flex items-center gap-1.5 whitespace-nowrap',
            activeTab === tab.value
              ? 'border-[#042562] text-[#042562]'
              : 'border-transparent text-slate-500 hover:text-slate-800'
          ]"
        >
          <component :is="tab.icon" class="w-4 h-4" />
          <span>{{ tab.label }}</span>
        </button>
      </div>

      <!-- Tab 1: Sales Analytics & Interactive ECharts -->
      <div v-show="activeTab === 'sales'" class="space-y-6">
        <!-- Recognized Revenue KPI Cards Row -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <div class="flex items-center justify-between">
              <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Recognized Revenue</span>
              <div class="w-8 h-8 rounded-lg bg-emerald-50 text-[#16A34A] flex items-center justify-center">
                <BadgeDollarSign class="w-4 h-4" />
              </div>
            </div>
            <div class="text-2xl font-black text-[#16A34A] mt-2">
              {{ formatCurrency(salesData.summary?.total_revenue || 0) }}
            </div>
            <div class="flex items-center gap-1.5 mt-1">
              <span :class="['text-xs font-bold', salesData.summary?.growth_percent >= 0 ? 'text-emerald-600' : 'text-red-600']">
                {{ salesData.summary?.growth_percent >= 0 ? '+' : '' }}{{ salesData.summary?.growth_percent || 0 }}%
              </span>
              <span class="text-[10px] text-slate-400">vs previous period</span>
            </div>
          </div>

          <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <div class="flex items-center justify-between">
              <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Confirmed Orders</span>
              <div class="w-8 h-8 rounded-lg bg-blue-50 text-[#1865C0] flex items-center justify-center">
                <ShoppingCart class="w-4 h-4" />
              </div>
            </div>
            <div class="text-2xl font-black text-[#042562] mt-2">
              {{ salesData.summary?.total_orders || 0 }} Orders
            </div>
            <span class="text-[10px] text-slate-500">Confirmed and processed</span>
          </div>

          <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <div class="flex items-center justify-between">
              <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Settled Payments</span>
              <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-700 flex items-center justify-center">
                <CheckCircle2 class="w-4 h-4" />
              </div>
            </div>
            <div class="text-2xl font-black text-[#042562] mt-2">
              {{ formatCurrency(salesData.summary?.total_collected || 0) }}
            </div>
            <span class="text-[10px] text-slate-500">Collected advance and final</span>
          </div>

          <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <div class="flex items-center justify-between">
              <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Outstanding Receivables</span>
              <div class="w-8 h-8 rounded-lg bg-amber-50 text-[#F59E0B] flex items-center justify-center">
                <Clock class="w-4 h-4" />
              </div>
            </div>
            <div class="text-2xl font-black text-[#F59E0B] mt-2">
              {{ formatCurrency(salesData.summary?.outstanding_receivables || 0) }}
            </div>
            <span class="text-[10px] text-amber-700 font-semibold">Balance due from clients</span>
          </div>
        </div>

        <!-- ECharts 1: Revenue Timeline Trend -->
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
          <div class="flex items-center justify-between mb-4">
            <div>
              <h3 class="text-sm font-black text-[#042562]">Sales Revenue Trajectory Over Time</h3>
              <p class="text-[11px] text-slate-500">Chronological recognized revenue (SAR) & confirmed order frequency</p>
            </div>
            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-blue-50 text-[#1865C0] border border-blue-200">
              Interactive ECharts
            </span>
          </div>
          <!-- Chart Container -->
          <div ref="trendChartRef" class="w-full h-80"></div>
        </div>

        <!-- Categorical Breakdown Grid (ECharts 2 & ECharts 3) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- ECharts 2: Doughnut by Category -->
          <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
              <div>
                <h3 class="text-sm font-black text-[#042562]">Sales by Hardware Category</h3>
                <p class="text-[11px] text-slate-500">Percentage share of total recognized revenue</p>
              </div>
            </div>
            <div ref="categoryChartRef" class="w-full h-72"></div>
          </div>

          <!-- ECharts 3: Bar Chart by Brand -->
          <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
              <div>
                <h3 class="text-sm font-black text-[#042562]">Sales by Manufacturer Brand</h3>
                <p class="text-[11px] text-slate-500">Revenue generation across hardware partners</p>
              </div>
            </div>
            <div ref="brandChartRef" class="w-full h-72"></div>
          </div>
        </div>

        <!-- Store Revenue Share Progress Bars -->
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
          <h3 class="text-sm font-black text-[#042562] mb-3">Store Branch Revenue Distribution</h3>
          <div class="space-y-4">
            <div v-for="st in salesData.by_store" :key="st.id" class="space-y-1">
              <div class="flex justify-between text-xs">
                <span class="font-bold text-slate-800">{{ st.name }} ({{ st.code }})</span>
                <span class="font-bold text-[#042562]">{{ formatCurrency(st.revenue) }} ({{ st.percentage }}%)</span>
              </div>
              <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                <div
                  class="bg-[#1865C0] h-2.5 rounded-full transition-all duration-500"
                  :style="{ width: `${st.percentage}%` }"
                ></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Tab 2: Best-Selling Products Rankings -->
      <div v-show="activeTab === 'bestsellers'" class="space-y-6">
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm flex items-center justify-between">
          <div>
            <h3 class="text-sm font-black text-[#042562]">Top Performing Hardware Models</h3>
            <p class="text-[11px] text-slate-500">Rankings based on physical units dispatched and revenue recognized</p>
          </div>
          <div class="flex items-center gap-1.5 text-xs">
            <button
              @click="bestSellerSort = 'quantity'"
              :class="[
                'px-3 py-1.5 rounded-lg font-bold',
                bestSellerSort === 'quantity' ? 'bg-[#042562] text-white' : 'bg-slate-100 text-slate-600'
              ]"
            >
              Highest Quantity
            </button>
            <button
              @click="bestSellerSort = 'revenue'"
              :class="[
                'px-3 py-1.5 rounded-lg font-bold',
                bestSellerSort === 'revenue' ? 'bg-[#042562] text-white' : 'bg-slate-100 text-slate-600'
              ]"
            >
              Highest Revenue
            </button>
          </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
          <table class="w-full text-left border-collapse text-xs">
            <thead class="bg-[#F4F6FA] text-[10px] font-bold text-slate-600 uppercase border-b border-slate-200">
              <tr>
                <th class="py-3 px-4">Rank</th>
                <th class="py-3 px-4">Hardware Product & Model</th>
                <th class="py-3 px-4">Category</th>
                <th class="py-3 px-4">Brand</th>
                <th class="py-3 px-4 text-center">Units Sold</th>
                <th class="py-3 px-4 text-right">Avg Unit Price</th>
                <th class="py-3 px-4 text-right">Total Revenue (SAR)</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr
                v-for="(item, idx) in activeBestSellers"
                :key="item.id"
                class="hover:bg-slate-50 transition-colors"
              >
                <td class="py-3 px-4 font-black text-slate-400">#{{ idx + 1 }}</td>
                <td class="py-3 px-4">
                  <div class="font-bold text-[#042562]">{{ item.name }}</div>
                  <div class="text-[10px] text-slate-500 mt-0.5">
                    Model: {{ item.model_number }} &bull; SKU: {{ item.sku }}
                  </div>
                </td>
                <td class="py-3 px-4 text-slate-600">{{ item.category_name }}</td>
                <td class="py-3 px-4 font-semibold text-slate-700">{{ item.brand_name }}</td>
                <td class="py-3 px-4 text-center font-bold text-[#042562]">{{ item.total_quantity_sold }}</td>
                <td class="py-3 px-4 text-right text-slate-600">{{ formatCurrency(item.average_unit_price) }}</td>
                <td class="py-3 px-4 text-right font-black text-[#16A34A]">{{ formatCurrency(item.total_revenue_generated) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Tab 3: Slow-Moving & Stagnant Stock (Capital Risk) -->
      <div v-show="activeTab === 'slowmoving'" class="space-y-6">
        <!-- Control & Capital Risk Summary -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm md:col-span-2 flex items-center justify-between">
            <div>
              <h3 class="text-sm font-black text-[#042562]">Slow-Moving & Stagnant Stock Detection</h3>
              <p class="text-[11px] text-slate-500">Identifies hardware with low turnover or zero sales to prevent warehouse capital lockup.</p>
            </div>
            <div class="flex items-center gap-2 text-xs">
              <span class="font-bold text-slate-600">Threshold:</span>
              <select
                v-model="slowMovingThreshold"
                @change="fetchSlowMovingData"
                class="bg-[#F4F6FA] text-xs font-bold text-[#042562] rounded-lg px-2.5 py-1.5 border border-slate-300"
              >
                <option :value="30">30 Days</option>
                <option :value="60">60 Days</option>
                <option :value="90">90 Days</option>
                <option :value="180">180 Days</option>
              </select>
            </div>
          </div>

          <div class="bg-white rounded-xl border border-red-200 p-4 shadow-sm bg-red-50/30">
            <span class="text-[10px] font-bold text-red-700 uppercase tracking-wider block">Tied-Up Working Capital</span>
            <div class="text-2xl font-black text-red-600 mt-1">
              {{ formatCurrency(slowMovingData.total_tied_capital || 0) }}
            </div>
            <span class="text-[10px] text-slate-500">{{ slowMovingData.total_slow_moving_items || 0 }} SKUs requiring clearance action</span>
          </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
          <table class="w-full text-left border-collapse text-xs">
            <thead class="bg-[#F4F6FA] text-[10px] font-bold text-slate-600 uppercase border-b border-slate-200">
              <tr>
                <th class="py-3 px-4">Hardware SKU & Model</th>
                <th class="py-3 px-4">Store Location</th>
                <th class="py-3 px-4 text-center">On-Hand Stock</th>
                <th class="py-3 px-4 text-center">Stock Age</th>
                <th class="py-3 px-4 text-center">Last Confirmed Sale</th>
                <th class="py-3 px-4 text-right">Tied Capital (SAR)</th>
                <th class="py-3 px-4 text-center">Risk Level</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr
                v-for="item in slowMovingData.items"
                :key="item.product_id"
                class="hover:bg-slate-50 transition-colors"
              >
                <td class="py-3 px-4">
                  <div class="font-bold text-[#042562]">{{ item.name }}</div>
                  <div class="text-[10px] text-slate-500 mt-0.5">
                    Model: {{ item.model_number }} &bull; SKU: {{ item.sku }}
                  </div>
                </td>
                <td class="py-3 px-4 text-slate-700">{{ item.store_name }}</td>
                <td class="py-3 px-4 text-center font-bold">{{ item.on_hand_quantity }}</td>
                <td class="py-3 px-4 text-center font-medium">{{ item.stock_age_days }} days</td>
                <td class="py-3 px-4 text-center">
                  <span v-if="item.days_since_last_sale !== null" class="font-medium text-slate-700">
                    {{ item.days_since_last_sale }} days ago
                  </span>
                  <span v-else class="text-[10px] font-bold text-purple-700 uppercase">
                    Never Sold
                  </span>
                </td>
                <td class="py-3 px-4 text-right font-black text-red-600">
                  {{ formatCurrency(item.tied_up_capital) }}
                </td>
                <td class="py-3 px-4 text-center">
                  <span
                    :class="[
                      'inline-block px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase',
                      riskBadgeClass(item.risk_level)
                    ]"
                  >
                    {{ item.risk_level.replace('_', ' ') }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Tab 4: Store-Based Performance Matrix -->
      <div v-show="activeTab === 'stores'" class="space-y-6">
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
          <h3 class="text-sm font-black text-[#042562]">Multi-Store Branch Performance Matrix</h3>
          <p class="text-[11px] text-slate-500">Side-by-side revenue recognition, stock valuation, and top models across branches.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
          <div
            v-for="st in storeComparisonData"
            :key="st.store_id"
            class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm space-y-4"
          >
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
              <div>
                <h4 class="font-bold text-sm text-[#042562]">{{ st.store_name }}</h4>
                <span class="text-[10px] text-slate-400 font-mono">{{ st.store_code }}</span>
              </div>
              <span
                v-if="st.is_primary"
                class="px-2 py-0.5 rounded bg-blue-100 text-blue-800 text-[10px] font-bold"
              >
                HQ Primary
              </span>
            </div>

            <div class="space-y-2 text-xs">
              <div class="flex justify-between">
                <span class="text-slate-500">Confirmed Revenue:</span>
                <span class="font-black text-[#16A34A]">{{ formatCurrency(st.confirmed_revenue) }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-slate-500">Confirmed Orders:</span>
                <span class="font-bold text-slate-800">{{ st.confirmed_orders_count }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-slate-500">Units Dispatched:</span>
                <span class="font-bold text-slate-800">{{ st.units_sold }} units</span>
              </div>
              <div class="flex justify-between">
                <span class="text-slate-500">On-Hand Inventory:</span>
                <span class="font-semibold text-slate-800">{{ st.stock_quantity }} units</span>
              </div>
              <div class="flex justify-between">
                <span class="text-slate-500">Stock Valuation:</span>
                <span class="font-bold text-[#042562]">{{ formatCurrency(st.stock_valuation) }}</span>
              </div>
            </div>

            <div class="pt-3 border-t border-slate-100 text-xs">
              <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Top Branch Hardware Model:</span>
              <p class="font-bold text-[#042562] mt-0.5 truncate">{{ st.top_product_name }}</p>
              <span class="text-[10px] text-emerald-700 font-semibold">{{ formatCurrency(st.top_product_revenue) }} revenue</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Tab 5: Historical Reports Archive -->
      <div v-show="activeTab === 'history'" class="space-y-6">
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm flex items-center justify-between">
          <div>
            <h3 class="text-sm font-black text-[#042562]">Generated Audit Reports Ledger</h3>
            <p class="text-[11px] text-slate-500">Archived PDF and CSV export records with compliance verification.</p>
          </div>
          <button
            @click="fetchReportHistory"
            class="p-2 text-slate-500 hover:text-[#042562] bg-[#F4F6FA] border border-slate-300 rounded-lg"
          >
            <RefreshCw :class="['w-4 h-4', { 'animate-spin': loading }]" />
          </button>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
          <table class="w-full text-left border-collapse text-xs">
            <thead class="bg-[#F4F6FA] text-[10px] font-bold text-slate-600 uppercase border-b border-slate-200">
              <tr>
                <th class="py-3 px-4">Report Reference</th>
                <th class="py-3 px-4">Title & Classification</th>
                <th class="py-3 px-4">Period Scope</th>
                <th class="py-3 px-4">Store Branch</th>
                <th class="py-3 px-4 text-center">Format</th>
                <th class="py-3 px-4">Generated By / Date</th>
                <th class="py-3 px-4 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr
                v-for="rep in reportHistory"
                :key="rep.id"
                class="hover:bg-slate-50 transition-colors"
              >
                <td class="py-3 px-4 font-bold text-[#042562] font-mono">{{ rep.report_number }}</td>
                <td class="py-3 px-4">
                  <div class="font-semibold text-slate-900">{{ rep.title }}</div>
                  <span class="text-[10px] text-slate-400 uppercase font-bold">{{ rep.type }}</span>
                </td>
                <td class="py-3 px-4 text-slate-600">{{ rep.period_label || 'Current Snapshot' }}</td>
                <td class="py-3 px-4 text-slate-600">{{ rep.store ? rep.store.name : 'Consolidated HQ' }}</td>
                <td class="py-3 px-4 text-center">
                  <span
                    :class="[
                      'px-2 py-0.5 rounded text-[10px] font-bold',
                      rep.format === 'PDF' ? 'bg-red-100 text-red-800' : 'bg-emerald-100 text-emerald-800'
                    ]"
                  >
                    {{ rep.format }}
                  </span>
                </td>
                <td class="py-3 px-4 text-slate-500">
                  {{ rep.generated_by ? rep.generated_by.name : 'System' }}<br>
                  <span class="text-[10px]">{{ new Date(rep.created_at).toLocaleDateString() }}</span>
                </td>
                <td class="py-3 px-4 text-right">
                  <button
                    @click="reDownloadReport(rep)"
                    class="p-1.5 text-slate-600 hover:text-[#042562] hover:bg-slate-100 rounded-lg transition-colors"
                    title="Download Report File"
                  >
                    <Download class="w-4 h-4" />
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick, watch } from 'vue';
import * as echarts from 'echarts';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { analyticsService } from '@/services/analyticsService';
import { reportService } from '@/services/reportService';
import { useNotificationStore } from '@/stores/notificationStore';
import {
  TrendingUp,
  BarChart3,
  Award,
  AlertTriangle,
  Building,
  History,
  Download,
  ChevronDown,
  FileText,
  FileSpreadsheet,
  RefreshCw,
  BadgeDollarSign,
  ShoppingCart,
  CheckCircle2,
  Clock
} from 'lucide-vue-next';

const notify = useNotificationStore();

const loading = ref(false);
const exportMenuOpen = ref(false);
const activeTab = ref('sales');

const selectedPeriod = ref('this_month');
const customFromDate = ref('');
const customToDate = ref('');
const selectedStore = ref('');

const periodPresets = [
  { label: 'Today', value: 'today' },
  { label: 'This Week', value: 'this_week' },
  { label: 'This Month', value: 'this_month' },
  { label: 'This Quarter', value: 'this_quarter' },
  { label: 'This Year', value: 'this_year' },
  { label: 'Custom Range', value: 'custom' },
];

const viewTabs = [
  { label: 'Sales Analytics', value: 'sales', icon: BarChart3 },
  { label: 'Best-Selling Models', value: 'bestsellers', icon: Award },
  { label: 'Slow-Moving Stock', value: 'slowmoving', icon: AlertTriangle },
  { label: 'Store Comparisons', value: 'stores', icon: Building },
  { label: 'Report History', value: 'history', icon: History },
];

// Data State
const salesData = ref({});
const bestSellersData = ref({ top_by_quantity: [], top_by_revenue: [] });
const bestSellerSort = ref('quantity');
const slowMovingThreshold = ref(60);
const slowMovingData = ref({ items: [] });
const storeComparisonData = ref([]);
const reportHistory = ref([]);

// ECharts References & Instances
const trendChartRef = ref(null);
const categoryChartRef = ref(null);
const brandChartRef = ref(null);

let trendChartInstance = null;
let categoryChartInstance = null;
let brandChartInstance = null;

const activeBestSellers = computed(() => {
  return bestSellerSort.value === 'quantity'
    ? bestSellersData.value.top_by_quantity
    : bestSellersData.value.top_by_revenue;
});

function formatCurrency(val) {
  if (val === null || val === undefined) return 'SAR 0.00';
  return `SAR ${Number(val).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
}

function riskBadgeClass(level) {
  switch (level) {
    case 'CRITICAL_DEAD_STOCK':
      return 'bg-red-100 text-red-800 border border-red-300';
    case 'HIGH_STAGNANT':
      return 'bg-amber-100 text-amber-800 border border-amber-300';
    default:
      return 'bg-blue-100 text-blue-800 border border-blue-300';
  }
}

function toggleExportMenu() {
  exportMenuOpen.value = !exportMenuOpen.value;
}

function setPeriod(p) {
  selectedPeriod.value = p;
  if (p !== 'custom') {
    fetchAnalyticsData();
  }
}

function applyCustomDate() {
  if (!customFromDate.value || !customToDate.value) {
    notify.error('Please select both from and to dates');
    return;
  }
  fetchAnalyticsData();
}

async function fetchAnalyticsData() {
  loading.value = true;
  try {
    const params = {
      period: selectedPeriod.value,
      from_date: selectedPeriod.value === 'custom' ? customFromDate.value : undefined,
      to_date: selectedPeriod.value === 'custom' ? customToDate.value : undefined,
      store_id: selectedStore.value || undefined,
    };

    const [salesRes, bestRes, slowRes, storeRes] = await Promise.all([
      analyticsService.getSalesAnalytics(params),
      analyticsService.getBestSellers(params),
      analyticsService.getSlowMoving({
        store_id: selectedStore.value || undefined,
        days_threshold: slowMovingThreshold.value,
      }),
      analyticsService.getStoreComparison(),
    ]);

    salesData.value = salesRes.data || {};
    bestSellersData.value = bestRes.data || { top_by_quantity: [], top_by_revenue: [] };
    slowMovingData.value = slowRes.data || { items: [] };
    storeComparisonData.value = storeRes.data || [];

    await nextTick();
    renderCharts();
  } catch (err) {
    notify.error(err.message || 'Failed to load analytics data');
  } finally {
    loading.value = false;
  }
}

async function fetchSlowMovingData() {
  try {
    const res = await analyticsService.getSlowMoving({
      store_id: selectedStore.value || undefined,
      days_threshold: slowMovingThreshold.value,
    });
    slowMovingData.value = res.data || { items: [] };
  } catch (err) {
    notify.error(err.message || 'Failed to update slow moving products');
  }
}

async function fetchReportHistory() {
  try {
    const res = await reportService.getReportHistory();
    reportHistory.value = res.data || [];
  } catch (err) {
    notify.error(err.message || 'Failed to load report history');
  }
}

function renderCharts() {
  renderTrendChart();
  renderCategoryChart();
  renderBrandChart();
}

function renderTrendChart() {
  if (!trendChartRef.value) return;
  if (!trendChartInstance) {
    trendChartInstance = echarts.init(trendChartRef.value);
  }

  const trendData = salesData.value.trend || [];
  const periods = trendData.map(d => d.period);
  const revenues = trendData.map(d => d.revenue);
  const orders = trendData.map(d => d.orders_count);

  const option = {
    tooltip: {
      trigger: 'axis',
      axisPointer: { type: 'cross' },
      formatter: function (params) {
        let res = `<div style="font-weight:bold;margin-bottom:4px;">${params[0].name}</div>`;
        params.forEach(item => {
          if (item.seriesName === 'Revenue') {
            res += `<div>${item.marker} ${item.seriesName}: <strong>SAR ${item.value.toLocaleString()}</strong></div>`;
          } else {
            res += `<div>${item.marker} ${item.seriesName}: <strong>${item.value} Orders</strong></div>`;
          }
        });
        return res;
      },
    },
    legend: {
      data: ['Revenue', 'Orders Count'],
      bottom: 0,
    },
    grid: {
      left: '3%',
      right: '4%',
      bottom: '10%',
      top: '8%',
      containLabel: true,
    },
    xAxis: {
      type: 'category',
      data: periods,
      boundaryGap: false,
      axisLine: { lineStyle: { color: '#CBD5E1' } },
      axisLabel: { color: '#64748B', fontSize: 10 },
    },
    yAxis: [
      {
        type: 'value',
        name: 'Revenue (SAR)',
        axisLabel: {
          formatter: val => `${val / 1000}k`,
          color: '#64748B',
          fontSize: 10,
        },
        splitLine: { lineStyle: { color: '#F1F5F9' } },
      },
      {
        type: 'value',
        name: 'Orders',
        minInterval: 1,
        axisLabel: { color: '#64748B', fontSize: 10 },
        splitLine: { show: false },
      },
    ],
    series: [
      {
        name: 'Revenue',
        type: 'line',
        smooth: true,
        data: revenues,
        itemStyle: { color: '#042562' },
        lineStyle: { width: 3 },
        areaStyle: {
          color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
            { offset: 0, color: 'rgba(4, 37, 98, 0.25)' },
            { offset: 1, color: 'rgba(4, 37, 98, 0.01)' },
          ]),
        },
      },
      {
        name: 'Orders Count',
        type: 'bar',
        yAxisIndex: 1,
        data: orders,
        itemStyle: { color: '#1865C0', borderRadius: [4, 4, 0, 0] },
        barWidth: '20%',
      },
    ],
  };

  trendChartInstance.setOption(option, true);
}

function renderCategoryChart() {
  if (!categoryChartRef.value) return;
  if (!categoryChartInstance) {
    categoryChartInstance = echarts.init(categoryChartRef.value);
  }

  const catData = (salesData.value.by_category || []).map(c => ({
    name: c.name,
    value: c.revenue,
  }));

  const option = {
    tooltip: {
      trigger: 'item',
      formatter: '{b}: SAR {c} ({d}%)',
    },
    legend: {
      orient: 'vertical',
      right: 10,
      top: 'center',
      textStyle: { fontSize: 11, color: '#475569' },
    },
    color: ['#042562', '#1865C0', '#16A34A', '#E20913', '#F59E0B', '#8B5CF6'],
    series: [
      {
        name: 'Revenue by Category',
        type: 'pie',
        radius: ['45%', '70%'],
        center: ['40%', '50%'],
        avoidLabelOverlap: false,
        label: { show: false },
        emphasis: {
          label: {
            show: true,
            fontSize: 12,
            fontWeight: 'bold',
          },
        },
        data: catData,
      },
    ],
  };

  categoryChartInstance.setOption(option, true);
}

function renderBrandChart() {
  if (!brandChartRef.value) return;
  if (!brandChartInstance) {
    brandChartInstance = echarts.init(brandChartRef.value);
  }

  const brandData = salesData.value.by_brand || [];
  const brandNames = brandData.map(b => b.name);
  const brandRevenues = brandData.map(b => b.revenue);

  const option = {
    tooltip: {
      trigger: 'axis',
      axisPointer: { type: 'shadow' },
      formatter: '{b}: SAR {c}',
    },
    grid: {
      left: '3%',
      right: '4%',
      bottom: '5%',
      top: '8%',
      containLabel: true,
    },
    xAxis: {
      type: 'value',
      axisLabel: {
        formatter: val => `${val / 1000}k`,
        color: '#64748B',
        fontSize: 10,
      },
      splitLine: { lineStyle: { color: '#F1F5F9' } },
    },
    yAxis: {
      type: 'category',
      data: brandNames,
      axisLabel: { color: '#475569', fontSize: 11 },
      axisLine: { lineStyle: { color: '#CBD5E1' } },
    },
    series: [
      {
        name: 'Brand Revenue',
        type: 'bar',
        data: brandRevenues,
        itemStyle: {
          color: new echarts.graphic.LinearGradient(1, 0, 0, 0, [
            { offset: 0, color: '#1865C0' },
            { offset: 1, color: '#042562' },
          ]),
          borderRadius: [0, 4, 4, 0],
        },
        barWidth: '40%',
      },
    ],
  };

  brandChartInstance.setOption(option, true);
}

function handleResize() {
  trendChartInstance?.resize();
  categoryChartInstance?.resize();
  brandChartInstance?.resize();
}

async function exportSalesPdf() {
  exportMenuOpen.value = false;
  notify.info('Compiling official Sales Performance PDF report...');
  try {
    await reportService.downloadSalesPdf({
      period: selectedPeriod.value,
      from_date: customFromDate.value || undefined,
      to_date: customToDate.value || undefined,
      store_id: selectedStore.value || undefined,
    });
    notify.success('Downloaded official Sales Report PDF');
  } catch (err) {
    notify.error('Failed to export sales PDF');
  }
}

async function exportSalesCsv() {
  exportMenuOpen.value = false;
  notify.info('Exporting sales transactions CSV ledger...');
  try {
    await reportService.downloadSalesCsv({
      period: selectedPeriod.value,
      from_date: customFromDate.value || undefined,
      to_date: customToDate.value || undefined,
      store_id: selectedStore.value || undefined,
    });
    notify.success('Exported sales CSV ledger');
  } catch (err) {
    notify.error('Failed to export sales CSV');
  }
}

async function exportInventoryPdf() {
  exportMenuOpen.value = false;
  notify.info('Compiling Multi-Store Inventory Audit PDF...');
  try {
    await reportService.downloadInventoryPdf({
      store_id: selectedStore.value || undefined,
      days_threshold: slowMovingThreshold.value,
    });
    notify.success('Downloaded Inventory Audit PDF');
  } catch (err) {
    notify.error('Failed to export inventory PDF');
  }
}

async function exportInventoryCsv() {
  exportMenuOpen.value = false;
  notify.info('Exporting multi-store inventory CSV ledger...');
  try {
    await reportService.downloadInventoryCsv({
      store_id: selectedStore.value || undefined,
    });
    notify.success('Exported inventory CSV ledger');
  } catch (err) {
    notify.error('Failed to export inventory CSV');
  }
}

function reDownloadReport(rep) {
  if (rep.type === 'SALES' && rep.format === 'PDF') {
    exportSalesPdf();
  } else if (rep.type === 'SALES' && rep.format === 'CSV') {
    exportSalesCsv();
  } else if (rep.type === 'INVENTORY' && rep.format === 'PDF') {
    exportInventoryPdf();
  } else {
    exportInventoryCsv();
  }
}

watch(activeTab, async (newTab) => {
  if (newTab === 'sales') {
    await nextTick();
    renderCharts();
  } else if (newTab === 'history') {
    fetchReportHistory();
  }
});

onMounted(() => {
  fetchAnalyticsData();
  window.addEventListener('resize', handleResize);
});

onUnmounted(() => {
  window.removeEventListener('resize', handleResize);
  trendChartInstance?.dispose();
  categoryChartInstance?.dispose();
  brandChartInstance?.dispose();
});
</script>
