<template>
  <AdminLayout>
    <div class="space-y-6">
      <!-- Top Title and Primary Actions -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2">
            <h1 class="text-2xl font-black text-[#042562] tracking-tight">Multi-Store Inventory Management</h1>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-blue-100 text-[#042562]">
              Live Physical Network
            </span>
          </div>
          <p class="text-xs text-slate-500 mt-1">
            Centralized visibility across regional fulfillment hubs, atomic transfers, stock receiving, and compliance cycle adjustments.
          </p>
        </div>

        <!-- Inventory Transaction Action Buttons -->
        <div class="flex items-center flex-wrap gap-2">
          <button
            @click="openReceivingModal()"
            class="px-3.5 py-2 rounded-lg bg-[#16A34A] hover:bg-green-700 text-white text-xs font-bold flex items-center gap-1.5 shadow-sm transition-colors"
            title="Log received shipment from supplier"
          >
            <ArrowDownToLine class="w-4 h-4" />
            <span>Receive Stock</span>
          </button>

          <button
            @click="openIssuingModal()"
            class="px-3.5 py-2 rounded-lg bg-[#DC2626] hover:bg-red-700 text-white text-xs font-bold flex items-center gap-1.5 shadow-sm transition-colors"
            title="Issue equipment for deployment, RMA or scrap"
          >
            <ArrowUpRight class="w-4 h-4" />
            <span>Issue Stock</span>
          </button>

          <button
            @click="openTransferModal()"
            class="px-3.5 py-2 rounded-lg bg-[#1865C0] hover:bg-blue-700 text-white text-xs font-bold flex items-center gap-1.5 shadow-sm transition-colors"
            title="Inter-store transfer between branches"
          >
            <ArrowRightLeft class="w-4 h-4" />
            <span>Transfer Stock</span>
          </button>

          <button
            @click="openAdjustmentModal()"
            class="px-3.5 py-2 rounded-lg bg-[#F59E0B] hover:bg-amber-600 text-slate-900 text-xs font-bold flex items-center gap-1.5 shadow-sm transition-colors"
            title="Physical audit cycle count adjustment"
          >
            <Scale class="w-4 h-4" />
            <span>Adjust Stock</span>
          </button>
        </div>
      </div>

      <!-- Executive Dashboard Metric Cards -->
      <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Total Products -->
        <div class="corporate-card p-4">
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Catalog Hardware</span>
          <div class="flex items-baseline justify-between mt-1">
            <span class="text-2xl font-black text-[#042562]">{{ metrics.total_products || 5 }}</span>
            <span class="text-xs text-blue-600 font-semibold">Models</span>
          </div>
        </div>

        <!-- Total Units in Stock -->
        <div class="corporate-card p-4">
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Physical Units</span>
          <div class="flex items-baseline justify-between mt-1">
            <span class="text-2xl font-black text-[#042562]">{{ metrics.total_stock_quantity || 113 }}</span>
            <span class="text-xs text-slate-500 font-semibold">On-Hand</span>
          </div>
        </div>

        <!-- Total Stock Valuation ($) -->
        <div class="corporate-card p-4">
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Stock Valuation (Cost)</span>
          <div class="flex items-baseline justify-between mt-1">
            <span class="text-xl sm:text-2xl font-black text-[#042562]">
              ${{ formatCurrency(metrics.total_stock_value || 371250) }}
            </span>
            <span class="text-[11px] text-emerald-600 font-bold">USD</span>
          </div>
        </div>

        <!-- Low Stock Alerts -->
        <div class="corporate-card p-4">
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Low Stock Alerts</span>
          <div class="flex items-baseline justify-between mt-1">
            <span class="text-2xl font-black text-[#F59E0B]">{{ metrics.low_stock_count || 1 }}</span>
            <span class="text-[11px] font-bold px-1.5 py-0.5 rounded bg-amber-50 text-amber-700">Warning</span>
          </div>
        </div>

        <!-- Out of Stock -->
        <div class="corporate-card p-4 col-span-2 lg:col-span-1">
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Out of Stock</span>
          <div class="flex items-baseline justify-between mt-1">
            <span class="text-2xl font-black text-[#DC2626]">{{ metrics.out_of_stock_count || 1 }}</span>
            <span class="text-[11px] font-bold px-1.5 py-0.5 rounded bg-red-50 text-red-700">Critical</span>
          </div>
        </div>
      </div>

      <!-- Store-Wise Stock Distribution Network Card -->
      <div class="corporate-card p-5">
        <div class="flex items-center justify-between mb-3">
          <div class="flex items-center gap-2">
            <Building2 class="w-4 h-4 text-[#1865C0]" />
            <h3 class="text-xs font-bold uppercase tracking-wider text-[#042562]">
              Regional Distribution Hubs — Stock Balances & Valuation
            </h3>
          </div>
          <span class="text-xs text-slate-400 font-medium">3 active fulfillment centers</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div
            v-for="st in metrics.store_distribution"
            :key="st.store_id"
            @click="selectStoreFilter(st.store_id)"
            class="p-4 rounded-xl border transition-all cursor-pointer"
            :class="selectedStoreId === st.store_id ? 'border-[#1865C0] bg-blue-50/50 shadow-sm' : 'border-slate-200 hover:border-slate-300 bg-white'"
          >
            <div class="flex items-center justify-between mb-2">
              <span class="text-xs font-extrabold text-[#042562] truncate">{{ st.store_name }}</span>
              <span
                v-if="st.is_primary"
                class="px-1.5 py-0.5 rounded text-[9px] font-black uppercase tracking-wider bg-amber-100 text-amber-800"
              >
                HQ
              </span>
            </div>
            <div class="flex items-baseline justify-between text-xs">
              <span class="text-slate-500">Units on Hand:</span>
              <span class="font-black text-sm text-[#042562]">{{ st.total_quantity }} units</span>
            </div>
            <div class="flex items-baseline justify-between text-xs mt-1">
              <span class="text-slate-500">Valuation:</span>
              <span class="font-bold text-slate-800">${{ formatCurrency(st.total_valuation) }}</span>
            </div>

            <!-- Visual Bar -->
            <div class="w-full h-1.5 bg-slate-100 rounded-full mt-3 overflow-hidden">
              <div
                class="h-full rounded-full transition-all duration-500"
                :class="st.is_primary ? 'bg-[#042562]' : 'bg-[#1865C0]'"
                :style="{ width: `${Math.min(100, (st.total_quantity / (metrics.total_stock_quantity || 1)) * 100)}%` }"
              ></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Navigation Views: Master Inventory vs. Movements Audit vs. Reports -->
      <div class="flex items-center justify-between border-b border-slate-200">
        <div class="flex items-center gap-6">
          <button
            @click="activeTab = 'inventory'"
            class="py-3 text-xs font-extrabold uppercase tracking-wider transition-colors relative"
            :class="activeTab === 'inventory' ? 'text-[#042562] border-b-2 border-[#1865C0]' : 'text-slate-400 hover:text-slate-600'"
          >
            <span class="flex items-center gap-2">
              <Boxes class="w-4 h-4" />
              Store Inventory Records ({{ filteredInventories.length }})
            </span>
          </button>

          <button
            @click="activeTab = 'movements'"
            class="py-3 text-xs font-extrabold uppercase tracking-wider transition-colors relative"
            :class="activeTab === 'movements' ? 'text-[#042562] border-b-2 border-[#1865C0]' : 'text-slate-400 hover:text-slate-600'"
          >
            <span class="flex items-center gap-2">
              <History class="w-4 h-4" />
              Stock Movement History Log
            </span>
          </button>

          <button
            @click="activeTab = 'reports'"
            class="py-3 text-xs font-extrabold uppercase tracking-wider transition-colors relative"
            :class="activeTab === 'reports' ? 'text-[#042562] border-b-2 border-[#1865C0]' : 'text-slate-400 hover:text-slate-600'"
          >
            <span class="flex items-center gap-2">
              <FileSpreadsheet class="w-4 h-4" />
              Aging Analysis & Reports
            </span>
          </button>
        </div>

        <button
          @click="loadData"
          class="p-2 text-slate-400 hover:text-[#042562] rounded-lg hover:bg-slate-100 transition-colors"
          title="Refresh Inventory"
        >
          <RefreshCw class="w-4 h-4" :class="{ 'animate-spin': loading }" />
        </button>
      </div>

      <!-- TAB 1: STORE INVENTORY DATA TABLE -->
      <div v-if="activeTab === 'inventory'" class="space-y-4">
        <!-- Toolbar: Store Selector, Search & Filters -->
        <div class="corporate-card p-4">
          <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <!-- Search -->
            <div class="relative sm:col-span-2">
              <Search class="w-4 h-4 text-slate-400 absolute left-3 top-3" />
              <input
                type="text"
                v-model="searchQuery"
                @input="handleSearchDebounced"
                placeholder="Search product model, SKU, or shelf location..."
                class="w-full text-xs rounded-lg pl-9 pr-4 py-2.5 border border-slate-300 focus:border-[#1865C0] focus:ring-1 focus:ring-[#1865C0] bg-white"
              />
            </div>

            <!-- Store Selector Filter -->
            <div>
              <select
                v-model="selectedStoreId"
                @change="loadInventory"
                class="w-full text-xs font-medium rounded-lg border border-slate-300 px-3 py-2.5 focus:border-[#1865C0] focus:ring-1 focus:ring-[#1865C0] bg-white text-slate-800"
              >
                <option :value="null">All Regional Hubs (HQ Consolidated)</option>
                <option v-for="store in stores" :key="store.id" :value="store.id">
                  {{ store.name }}
                </option>
              </select>
            </div>

            <!-- Status Filter -->
            <div>
              <select
                v-model="statusFilter"
                @change="loadInventory"
                class="w-full text-xs font-medium rounded-lg border border-slate-300 px-3 py-2.5 focus:border-[#1865C0] focus:ring-1 focus:ring-[#1865C0] bg-white text-slate-800"
              >
                <option value="">All Stock Statuses</option>
                <option value="in_stock">In Stock (Healthy)</option>
                <option value="low_stock">Low Stock Warning</option>
                <option value="out_of_stock">Out of Stock</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Inventory Table -->
        <div class="corporate-card overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-[10px] font-black uppercase tracking-wider text-slate-500">
                  <th class="py-3 px-4">Product Details</th>
                  <th class="py-3 px-4">Store Hub</th>
                  <th class="py-3 px-4">Shelf / Bay</th>
                  <th class="py-3 px-4 text-center">Available Stock</th>
                  <th class="py-3 px-4 text-center">Status</th>
                  <th class="py-3 px-4 text-right">Valuation (Cost)</th>
                  <th class="py-3 px-4">Last Movement</th>
                  <th class="py-3 px-4 text-right">Quick Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 text-xs">
                <tr
                  v-for="item in filteredInventories"
                  :key="item.id"
                  class="hover:bg-blue-50/30 transition-colors"
                >
                  <!-- Product Thumbnail & Details -->
                  <td class="py-3 px-4">
                    <div class="flex items-center gap-3">
                      <img
                        :src="item.primary_image_url || 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=100&auto=format&fit=crop&q=80'"
                        :alt="item.product_name"
                        class="w-10 h-10 rounded-lg object-cover border border-slate-200 flex-shrink-0"
                      />
                      <div class="min-w-0">
                        <span class="font-extrabold text-[#042562] block truncate max-w-xs sm:max-w-md">
                          {{ item.product_name }}
                        </span>
                        <div class="flex items-center gap-2 mt-0.5 text-[10px] text-slate-400">
                          <span class="font-mono font-semibold">{{ item.sku }}</span>
                          <span>•</span>
                          <span>{{ item.brand_name }}</span>
                        </div>
                      </div>
                    </div>
                  </td>

                  <!-- Store Hub -->
                  <td class="py-3 px-4">
                    <span class="font-semibold text-slate-800 block">{{ item.store_name }}</span>
                    <span class="text-[10px] font-mono text-slate-400">{{ item.store_code }}</span>
                  </td>

                  <!-- Shelf Location -->
                  <td class="py-3 px-4">
                    <span class="font-mono text-slate-600 bg-slate-100 px-2 py-0.5 rounded text-[11px]">
                      {{ item.shelf_location || 'Unassigned' }}
                    </span>
                  </td>

                  <!-- Available Stock (with reserved breakdown) -->
                  <td class="py-3 px-4 text-center">
                    <span class="font-black text-sm text-[#042562] block">
                      {{ item.available_quantity }}
                    </span>
                    <span class="text-[10px] text-slate-400 block" v-if="item.reserved_quantity > 0">
                      ({{ item.reserved_quantity }} reserved)
                    </span>
                    <span class="text-[10px] text-slate-400 block" v-else>
                      Total on hand: {{ item.quantity }}
                    </span>
                  </td>

                  <!-- Status Badge -->
                  <td class="py-3 px-4 text-center">
                    <span
                      class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider inline-block"
                      :class="{
                        'bg-emerald-50 text-[#16A34A] border border-emerald-200': item.stock_status === 'IN_STOCK',
                        'bg-amber-50 text-[#F59E0B] border border-amber-200': item.stock_status === 'LOW_STOCK',
                        'bg-red-50 text-[#DC2626] border border-red-200': item.stock_status === 'OUT_OF_STOCK',
                      }"
                    >
                      {{ item.stock_status_label }}
                    </span>
                  </td>

                  <!-- Valuation -->
                  <td class="py-3 px-4 text-right font-mono font-bold text-slate-800">
                    ${{ formatCurrency(item.stock_valuation) }}
                  </td>

                  <!-- Last Movement Date & Aging -->
                  <td class="py-3 px-4 text-slate-500">
                    <span class="block">{{ item.formatted_last_movement }}</span>
                    <span class="text-[10px] text-slate-400 font-medium block">
                      {{ item.aging_days }} day{{ item.aging_days === 1 ? '' : 's' }} ago
                    </span>
                  </td>

                  <!-- Actions -->
                  <td class="py-3 px-4 text-right">
                    <div class="flex items-center justify-end gap-1">
                      <button
                        @click="openAdjustmentModal(item)"
                        class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded"
                        title="Quick Cycle Adjustment"
                      >
                        <Scale class="w-4 h-4" />
                      </button>

                      <button
                        @click="openTransferModal(item)"
                        class="p-1.5 text-slate-400 hover:text-[#1865C0] hover:bg-blue-50 rounded"
                        title="Inter-Store Transfer"
                      >
                        <ArrowRightLeft class="w-4 h-4" />
                      </button>

                      <button
                        @click="openDetailDrawer(item)"
                        class="p-1.5 text-slate-400 hover:text-[#042562] hover:bg-slate-100 rounded"
                        title="Inspect Multi-Store Breakdown"
                      >
                        <Eye class="w-4 h-4" />
                      </button>
                    </div>
                  </td>
                </tr>

                <!-- Empty State -->
                <tr v-if="filteredInventories.length === 0">
                  <td colspan="8" class="text-center py-12 text-slate-400">
                    <Boxes class="w-8 h-8 mx-auto mb-2 opacity-40" />
                    <p class="font-bold">No inventory records matching active filters.</p>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- TAB 2: STOCK MOVEMENT HISTORY AUDIT -->
      <div v-if="activeTab === 'movements'" class="space-y-4">
        <div class="corporate-card p-4">
          <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3 w-full sm:w-auto">
              <select
                v-model="movementTypeFilter"
                @change="loadMovements"
                class="text-xs font-medium rounded-lg border border-slate-300 px-3 py-2 bg-white text-slate-800"
              >
                <option value="">All Movement Types</option>
                <option value="RECEIVE">Stock Receiving (Inward)</option>
                <option value="ISSUE">Stock Issuing (Outward)</option>
                <option value="TRANSFER_OUT">Inter-Store Transfers</option>
                <option value="ADJUSTMENT">Audit Adjustments</option>
              </select>

              <select
                v-model="selectedStoreId"
                @change="loadMovements"
                class="text-xs font-medium rounded-lg border border-slate-300 px-3 py-2 bg-white text-slate-800"
              >
                <option :value="null">All Hub Locations</option>
                <option v-for="store in stores" :key="store.id" :value="store.id">
                  {{ store.name }}
                </option>
              </select>
            </div>

            <span class="text-xs text-slate-500 font-medium">
              Showing {{ movements.length }} logged transactions
            </span>
          </div>
        </div>

        <div class="corporate-card overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-[10px] font-black uppercase tracking-wider text-slate-500">
                  <th class="py-3 px-4">Ref Number</th>
                  <th class="py-3 px-4">Type</th>
                  <th class="py-3 px-4">Hardware Line Item</th>
                  <th class="py-3 px-4">Source / Destination</th>
                  <th class="py-3 px-4 text-center">Quantity Delta</th>
                  <th class="py-3 px-4">Reason / Notes</th>
                  <th class="py-3 px-4">Authorized Staff</th>
                  <th class="py-3 px-4">Timestamp</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 text-xs">
                <tr v-for="mov in movements" :key="mov.id" class="hover:bg-slate-50/50">
                  <td class="py-3 px-4 font-mono font-bold text-[#042562]">
                    {{ mov.movement_number }}
                  </td>
                  <td class="py-3 px-4">
                    <span
                      class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase tracking-wider"
                      :class="{
                        'bg-emerald-50 text-emerald-700 border border-emerald-200': mov.badge_variant === 'success',
                        'bg-red-50 text-red-700 border border-red-200': mov.badge_variant === 'danger',
                        'bg-blue-50 text-blue-700 border border-blue-200': mov.badge_variant === 'info',
                        'bg-amber-50 text-amber-700 border border-amber-200': mov.badge_variant === 'warning',
                      }"
                    >
                      {{ mov.type_label }}
                    </span>
                  </td>
                  <td class="py-3 px-4">
                    <span class="font-extrabold text-slate-800 block">
                      {{ mov.items?.[0]?.product_name || 'Multi-Item Batch' }}
                    </span>
                    <span class="text-[10px] font-mono text-slate-400">
                      {{ mov.items?.[0]?.sku || '' }}
                    </span>
                  </td>
                  <td class="py-3 px-4">
                    <div v-if="mov.source_store && mov.destination_store">
                      <span class="font-semibold text-slate-700">{{ mov.source_store.code }}</span>
                      <span class="text-slate-400 mx-1">→</span>
                      <span class="font-semibold text-[#1865C0]">{{ mov.destination_store.code }}</span>
                    </div>
                    <div v-else-if="mov.destination_store">
                      <span class="text-emerald-700 font-semibold">{{ mov.destination_store.name }}</span>
                    </div>
                    <div v-else-if="mov.source_store">
                      <span class="text-slate-700 font-semibold">{{ mov.source_store.name }}</span>
                    </div>
                  </td>
                  <td class="py-3 px-4 text-center font-black text-sm">
                    <span
                      :class="{
                        'text-[#16A34A]': mov.direction === 'IN',
                        'text-[#DC2626]': mov.direction === 'OUT',
                        'text-amber-600': mov.direction === 'ADJUST',
                      }"
                    >
                      {{ mov.direction === 'IN' ? `+${mov.total_quantity}` : (mov.direction === 'OUT' ? `-${mov.total_quantity}` : mov.total_quantity) }}
                    </span>
                  </td>
                  <td class="py-3 px-4 max-w-xs truncate text-slate-600">
                    <span class="font-medium block truncate">{{ mov.reason }}</span>
                    <span class="text-[10px] text-slate-400 truncate block">{{ mov.notes }}</span>
                  </td>
                  <td class="py-3 px-4 text-slate-700">
                    {{ mov.user?.name || 'Automated System' }}
                  </td>
                  <td class="py-3 px-4 text-slate-500 whitespace-nowrap">
                    {{ mov.formatted_created_at }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- TAB 3: AGING ANALYSIS & REPORTS -->
      <div v-if="activeTab === 'reports'" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div class="corporate-card p-4 border-l-4 border-l-emerald-500">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">0 - 30 Days</span>
            <span class="text-xl font-black text-emerald-700 block mt-1">Fast Moving Stock</span>
            <span class="text-xs text-slate-500 mt-1 block">Active showroom inventory with recent sales / transfer velocity.</span>
          </div>

          <div class="corporate-card p-4 border-l-4 border-l-blue-500">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">31 - 60 Days</span>
            <span class="text-xl font-black text-blue-700 block mt-1">Normal Moving</span>
            <span class="text-xs text-slate-500 mt-1 block">Healthy turnover cycle for enterprise compute & switches.</span>
          </div>

          <div class="corporate-card p-4 border-l-4 border-l-amber-500">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">61 - 90 Days</span>
            <span class="text-xl font-black text-amber-700 block mt-1">Slow Moving Stock</span>
            <span class="text-xs text-slate-500 mt-1 block">Recommend highlighting in customer quotation proposals.</span>
          </div>

          <div class="corporate-card p-4 border-l-4 border-l-red-500">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">90+ Days</span>
            <span class="text-xl font-black text-red-700 block mt-1">Stagnant Stock</span>
            <span class="text-xs text-slate-500 mt-1 block">Zero movement logged in 3 months; review storage footprint.</span>
          </div>
        </div>

        <!-- Current Stock Valuation Summary Table -->
        <div class="corporate-card p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-extrabold text-[#042562] uppercase tracking-wider">
              Consolidated Stock Valuation Ledger
            </h3>
            <button
              @click="printReport"
              class="px-3 py-1.5 rounded-lg border border-slate-300 text-xs font-bold text-slate-700 hover:bg-slate-50 flex items-center gap-1.5"
            >
              <Printer class="w-3.5 h-3.5 text-slate-500" />
              <span>Print Audit Sheet</span>
            </button>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
              <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-[10px] font-black uppercase text-slate-500">
                  <th class="p-3">Model SKU</th>
                  <th class="p-3">Equipment Name</th>
                  <th class="p-3">Store Branch</th>
                  <th class="p-3 text-right">Units</th>
                  <th class="p-3 text-right">Unit Cost</th>
                  <th class="p-3 text-right">Total Valuation</th>
                  <th class="p-3">Age Bracket</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 font-mono">
                <tr v-for="item in filteredInventories" :key="item.id">
                  <td class="p-3 font-bold text-[#042562]">{{ item.sku }}</td>
                  <td class="p-3 font-sans font-semibold text-slate-800">{{ item.product_name }}</td>
                  <td class="p-3 font-sans text-slate-600">{{ item.store_code }}</td>
                  <td class="p-3 text-right font-black">{{ item.quantity }}</td>
                  <td class="p-3 text-right text-slate-600">${{ formatCurrency(item.cost_price) }}</td>
                  <td class="p-3 text-right font-black text-[#042562]">${{ formatCurrency(item.stock_valuation) }}</td>
                  <td class="p-3 font-sans">
                    <span
                      class="px-2 py-0.5 rounded text-[10px] font-bold"
                      :class="item.aging_days <= 30 ? 'bg-emerald-50 text-emerald-800' : (item.aging_days <= 60 ? 'bg-blue-50 text-blue-800' : 'bg-amber-50 text-amber-800')"
                    >
                      {{ item.aging_days }} days
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Modals -->
    <StockReceivingModal
      v-model="receivingModalOpen"
      :stores="stores"
      :products="products"
      :preselected-store-id="modalStoreId"
      :preselected-product-id="modalProductId"
      @received="loadData"
    />

    <StockIssuingModal
      v-model="issuingModalOpen"
      :stores="stores"
      :products="products"
      :inventories="inventories"
      :preselected-store-id="modalStoreId"
      :preselected-product-id="modalProductId"
      @issued="loadData"
    />

    <StockTransferModal
      v-model="transferModalOpen"
      :stores="stores"
      :products="products"
      :inventories="inventories"
      :preselected-store-id="modalStoreId"
      :preselected-product-id="modalProductId"
      @transferred="loadData"
    />

    <StockAdjustmentModal
      v-model="adjustmentModalOpen"
      :stores="stores"
      :products="products"
      :inventories="inventories"
      :preselected-store-id="modalStoreId"
      :preselected-product-id="modalProductId"
      @adjusted="loadData"
    />

    <!-- Multi-Store Detail Slide-Over Drawer -->
    <div
      v-if="detailDrawerOpen"
      class="fixed inset-0 z-50 overflow-hidden bg-black/40 backdrop-blur-sm flex justify-end transition-opacity"
      @click.self="detailDrawerOpen = false"
    >
      <div class="w-full max-w-lg bg-white h-full shadow-2xl p-6 overflow-y-auto space-y-6">
        <div class="flex items-center justify-between border-b pb-4">
          <div>
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Multi-Store Analysis</span>
            <h3 class="text-lg font-black text-[#042562] mt-0.5">{{ selectedItem?.product_name }}</h3>
          </div>
          <button @click="detailDrawerOpen = false" class="text-slate-400 hover:text-slate-600 p-1">
            <X class="w-5 h-5" />
          </button>
        </div>

        <!-- Product Snapshot -->
        <div class="flex items-center gap-4 p-4 rounded-xl bg-slate-50 border">
          <img
            :src="selectedItem?.primary_image_url || 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=120&auto=format&fit=crop&q=80'"
            class="w-16 h-16 rounded-lg object-cover border"
          />
          <div>
            <span class="font-mono text-xs font-bold text-[#1865C0]">{{ selectedItem?.sku }}</span>
            <p class="text-xs text-slate-600 font-medium">Model: {{ selectedItem?.model_number }}</p>
            <p class="text-xs text-slate-600 font-medium">OEM Cost: ${{ formatCurrency(selectedItem?.cost_price) }}</p>
          </div>
        </div>

        <!-- Store Distribution Breakdown Table for this Product -->
        <div>
          <h4 class="text-xs font-bold uppercase tracking-wider text-[#042562] mb-3">
            Stock Distribution Across All 3 Branches
          </h4>
          <div class="border rounded-xl overflow-hidden text-xs">
            <table class="w-full text-left">
              <thead class="bg-slate-50 text-[10px] uppercase text-slate-500 font-black">
                <tr>
                  <th class="p-3">Store Branch</th>
                  <th class="p-3">Shelf</th>
                  <th class="p-3 text-right">Available</th>
                  <th class="p-3 text-right">Total</th>
                </tr>
              </thead>
              <tbody class="divide-y font-medium">
                <tr
                  v-for="st in stores"
                  :key="st.id"
                  class="hover:bg-slate-50"
                  :class="{ 'bg-blue-50/50 font-bold': st.id === selectedItem?.store_id }"
                >
                  <td class="p-3">
                    <span class="font-bold text-[#042562]">{{ st.name }}</span>
                    <span v-if="st.id === selectedItem?.store_id" class="text-[10px] text-blue-600 ml-1.5">(Selected)</span>
                  </td>
                  <td class="p-3 font-mono text-slate-500">
                    {{ getProductStockForStore(st.id)?.shelf_location || 'Unassigned' }}
                  </td>
                  <td class="p-3 text-right font-black text-emerald-700">
                    {{ getProductStockForStore(st.id)?.available_quantity || 0 }}
                  </td>
                  <td class="p-3 text-right font-black text-[#042562]">
                    {{ getProductStockForStore(st.id)?.quantity || 0 }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Quick Actions for this item -->
        <div class="pt-4 border-t flex items-center gap-3">
          <button
            @click="openTransferModal(selectedItem)"
            class="flex-1 py-2.5 rounded-lg bg-[#1865C0] hover:bg-blue-700 text-white text-xs font-bold flex items-center justify-center gap-2"
          >
            <ArrowRightLeft class="w-4 h-4" />
            <span>Transfer Item</span>
          </button>
          <button
            @click="openAdjustmentModal(selectedItem)"
            class="flex-1 py-2.5 rounded-lg bg-[#F59E0B] hover:bg-amber-600 text-slate-900 text-xs font-bold flex items-center justify-center gap-2"
          >
            <Scale class="w-4 h-4" />
            <span>Cycle Adjust</span>
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import inventoryService from '@/services/inventoryService';
import StockReceivingModal from '@/components/inventory/StockReceivingModal.vue';
import StockIssuingModal from '@/components/inventory/StockIssuingModal.vue';
import StockTransferModal from '@/components/inventory/StockTransferModal.vue';
import StockAdjustmentModal from '@/components/inventory/StockAdjustmentModal.vue';
import {
  Boxes,
  Building2,
  ArrowDownToLine,
  ArrowUpRight,
  ArrowRightLeft,
  Scale,
  History,
  FileSpreadsheet,
  RefreshCw,
  Search,
  Eye,
  Printer,
  X,
} from 'lucide-vue-next';

const activeTab = ref('inventory');
const loading = ref(false);

const inventories = ref([]);
const movements = ref([]);
const metrics = ref({
  total_products: 5,
  total_stock_quantity: 113,
  total_stock_value: 371250,
  in_stock_count: 6,
  low_stock_count: 1,
  out_of_stock_count: 1,
  store_distribution: [],
});

const stores = ref([
  { id: 1, name: 'iRACK Central Distribution Hub (HQ)', code: 'ST-HQ-01', is_primary: true },
  { id: 2, name: 'iRACK Commercial Sales Center', code: 'ST-DOWNTOWN-02', is_primary: false },
  { id: 3, name: 'iRACK North Regional Depot', code: 'ST-NORTH-03', is_primary: false },
]);

const products = ref([
  { id: 1, name: 'Dell PowerEdge R760 2U Enterprise Server', sku: 'DEL-PE-R760-01', cost_price: 4150.00, brand_name: 'Dell Technologies' },
  { id: 2, name: 'Cisco Catalyst 9300 48-Port PoE+ Layer 3 Switch', sku: 'CSC-C9300-48P', cost_price: 2800.00, brand_name: 'Cisco Systems' },
  { id: 3, name: 'Synology FlashStation FS3410 24-Bay All-Flash NAS', sku: 'SYN-FS-3410-01', cost_price: 4900.00, brand_name: 'Synology Inc.' },
  { id: 4, name: 'APC Smart-UPS On-Line 10kVA 208V/230V Double Conversion', sku: 'APC-SRT-10KXLI', cost_price: 3650.00, brand_name: 'APC by Schneider' },
  { id: 5, name: 'iRACK NetShelter Enterprise 42U Server Cabinet Rack', sku: 'IRK-NET-42U812', cost_price: 1350.00, brand_name: 'iRACK Custom' },
]);

const searchQuery = ref('');
const selectedStoreId = ref(null);
const statusFilter = ref('');
const movementTypeFilter = ref('');

// Modals State
const receivingModalOpen = ref(false);
const issuingModalOpen = ref(false);
const transferModalOpen = ref(false);
const adjustmentModalOpen = ref(false);
const detailDrawerOpen = ref(false);

const modalStoreId = ref(null);
const modalProductId = ref(null);
const selectedItem = ref(null);

const filteredInventories = computed(() => {
  let list = [...inventories.value];

  if (selectedStoreId.value) {
    list = list.filter((i) => i.store_id === selectedStoreId.value);
  }

  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase();
    list = list.filter(
      (i) =>
        i.product_name.toLowerCase().includes(q) ||
        i.sku.toLowerCase().includes(q) ||
        (i.shelf_location && i.shelf_location.toLowerCase().includes(q))
    );
  }

  if (statusFilter.value) {
    list = list.filter((i) => i.stock_status.toLowerCase() === statusFilter.value.toLowerCase());
  }

  return list;
});

const formatCurrency = (val) => {
  if (val === null || val === undefined) return '0.00';
  return Number(val).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};

const selectStoreFilter = (storeId) => {
  if (selectedStoreId.value === storeId) {
    selectedStoreId.value = null;
  } else {
    selectedStoreId.value = storeId;
  }
  loadInventory();
};

const handleSearchDebounced = () => {
  // client-side filtering handles search reactively
};

const openReceivingModal = (item = null) => {
  modalStoreId.value = item ? item.store_id : selectedStoreId.value;
  modalProductId.value = item ? item.product_id : null;
  receivingModalOpen.value = true;
};

const openIssuingModal = (item = null) => {
  modalStoreId.value = item ? item.store_id : selectedStoreId.value;
  modalProductId.value = item ? item.product_id : null;
  issuingModalOpen.value = true;
};

const openTransferModal = (item = null) => {
  modalStoreId.value = item ? item.store_id : selectedStoreId.value;
  modalProductId.value = item ? item.product_id : null;
  transferModalOpen.value = true;
};

const openAdjustmentModal = (item = null) => {
  modalStoreId.value = item ? item.store_id : selectedStoreId.value;
  modalProductId.value = item ? item.product_id : null;
  adjustmentModalOpen.value = true;
};

const openDetailDrawer = (item) => {
  selectedItem.value = item;
  detailDrawerOpen.value = true;
};

const getProductStockForStore = (storeId) => {
  if (!selectedItem.value) return null;
  return inventories.value.find(
    (i) => i.store_id === storeId && i.product_id === selectedItem.value.product_id
  );
};

const printReport = () => {
  window.print();
};

const loadData = async () => {
  loading.value = true;
  await Promise.all([loadInventory(), loadDashboard(), loadMovements()]);
  loading.value = false;
};

const loadInventory = async () => {
  try {
    const params = {};
    if (selectedStoreId.value) params.store_id = selectedStoreId.value;
    if (statusFilter.value) params.status = statusFilter.value;

    const res = await inventoryService.getInventory(params);
    inventories.value = res.data || [];
  } catch (err) {
    console.error('Failed to load inventory:', err);
  }
};

const loadDashboard = async () => {
  try {
    const res = await inventoryService.getDashboard(selectedStoreId.value);
    if (res.data) {
      metrics.value = res.data;
    }
  } catch (err) {
    console.error('Failed to load inventory dashboard:', err);
  }
};

const loadMovements = async () => {
  try {
    const params = {};
    if (movementTypeFilter.value) params.type = movementTypeFilter.value;
    if (selectedStoreId.value) params.store_id = selectedStoreId.value;

    const res = await inventoryService.getMovements(params);
    movements.value = res.data || [];
  } catch (err) {
    console.error('Failed to load movements:', err);
  }
};

onMounted(() => {
  loadData();
});
</script>
