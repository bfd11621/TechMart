<template>
  <AdminLayout>
    <div class="space-y-6">
      <!-- Page Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-black text-[#042562] tracking-tight">
            Sales & Orders Management
          </h1>
          <p class="text-xs text-slate-500 mt-0.5">
            Confirmed commercial orders, multi-store stock allocations, payment receipts, and recognized enterprise sales revenue.
          </p>
        </div>

        <div class="flex items-center gap-3">
          <button
            @click="openDirectSaleModal"
            class="inline-flex items-center gap-2 bg-[#042562] hover:bg-[#1865C0] text-white text-xs font-bold uppercase tracking-wider px-4 py-2.5 rounded-lg transition-colors shadow-sm"
          >
            <Plus class="w-4 h-4" />
            <span>Register Direct Sale</span>
          </button>
        </div>
      </div>

      <!-- Recognized Revenue KPIs Row (Revenue Segregation Enforced) -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
          <div class="flex items-center justify-between">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Recognized Revenue</span>
            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-[#16A34A] flex items-center justify-center">
              <BadgeDollarSign class="w-4 h-4" />
            </div>
          </div>
          <div class="text-2xl font-black text-[#16A34A] mt-2">
            {{ formatCurrency(metrics.total_sales || 0) }}
          </div>
          <span class="text-[10px] text-slate-500">Confirmed orders strictly recognized</span>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
          <div class="flex items-center justify-between">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Collected Payments</span>
            <div class="w-8 h-8 rounded-lg bg-blue-50 text-[#1865C0] flex items-center justify-center">
              <CheckCircle2 class="w-4 h-4" />
            </div>
          </div>
          <div class="text-2xl font-black text-[#042562] mt-2">
            {{ formatCurrency(metrics.total_collected || 0) }}
          </div>
          <span class="text-[10px] text-slate-500">Total cash & wire transfers cleared</span>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
          <div class="flex items-center justify-between">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Outstanding Receivables</span>
            <div class="w-8 h-8 rounded-lg bg-amber-50 text-[#F59E0B] flex items-center justify-center">
              <Clock class="w-4 h-4" />
            </div>
          </div>
          <div class="text-2xl font-black text-[#F59E0B] mt-2">
            {{ formatCurrency(metrics.outstanding_receivables || 0) }}
          </div>
          <span class="text-[10px] text-amber-700 font-semibold">Balance due from active clients</span>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
          <div class="flex items-center justify-between">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Physical Units Sold</span>
            <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-700 flex items-center justify-center">
              <Boxes class="w-4 h-4" />
            </div>
          </div>
          <div class="text-2xl font-black text-[#042562] mt-2">
            {{ metrics.total_products_sold || 0 }} Units
          </div>
          <span class="text-[10px] text-slate-500">Dispatched from regional branches</span>
        </div>
      </div>

      <!-- Filters, Status Tabs & Search Toolbar -->
      <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm space-y-4">
        <!-- Status Tabs -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs border-b border-slate-100">
          <button
            v-for="tab in orderStatusTabs"
            :key="tab.value"
            @change="fetchOrders"
            @click="setOrderStatusTab(tab.value)"
            :class="[
              'px-3.5 py-2 font-bold rounded-lg transition-colors flex items-center gap-1.5 whitespace-nowrap',
              activeOrderStatus === tab.value
                ? 'bg-[#042562] text-white shadow-sm'
                : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'
            ]"
          >
            <span>{{ tab.label }}</span>
            <span
              :class="[
                'text-[10px] px-1.5 py-0.2 rounded-full font-bold',
                activeOrderStatus === tab.value ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700'
              ]"
            >
              {{ counts[tab.value] ?? 0 }}
            </span>
          </button>
        </div>

        <!-- Search & Control Row -->
        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
          <div class="relative w-full md:w-96">
            <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" />
            <input
              type="text"
              v-model="searchQuery"
              @input="debounceSearch"
              placeholder="Search by SO #, customer, quotation ref, company..."
              class="w-full bg-[#F4F6FA] text-xs text-[#172033] rounded-lg pl-9 pr-4 py-2.5 border border-slate-300 focus:outline-none focus:border-[#1865C0]"
            />
          </div>

          <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <!-- Payment Status Filter -->
            <select
              v-model="selectedPaymentStatus"
              @change="fetchOrders"
              class="bg-[#F4F6FA] text-xs font-semibold text-[#172033] rounded-lg px-3 py-2 border border-slate-300 focus:outline-none focus:border-[#1865C0]"
            >
              <option value="all">All Payment Statuses</option>
              <option value="UNPAID">Unpaid ({{ counts.unpaid || 0 }})</option>
              <option value="PARTIAL">Partially Paid ({{ counts.partial || 0 }})</option>
              <option value="PAID">Fully Paid ({{ counts.paid || 0 }})</option>
            </select>

            <!-- Store Selector -->
            <select
              v-model="selectedStore"
              @change="fetchOrders"
              class="bg-[#F4F6FA] text-xs font-semibold text-[#172033] rounded-lg px-3 py-2 border border-slate-300 focus:outline-none focus:border-[#1865C0]"
            >
              <option value="">All Fulfillment Stores</option>
              <option value="1">Central Distribution Hub (HQ)</option>
              <option value="2">North Logistics Facility</option>
              <option value="3">West Coast Regional Store</option>
            </select>

            <button
              @click="fetchOrders"
              class="p-2.5 text-slate-500 hover:text-[#042562] bg-[#F4F6FA] border border-slate-300 rounded-lg hover:bg-slate-100 transition-colors"
              title="Refresh Orders"
            >
              <RefreshCw :class="['w-4 h-4', { 'animate-spin': loading }]" />
            </button>
          </div>
        </div>
      </div>

      <!-- Orders Data Table -->
      <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div v-if="loading && orders.length === 0" class="p-12 text-center text-slate-500">
          <RefreshCw class="w-6 h-6 animate-spin mx-auto text-[#1865C0] mb-2" />
          <p class="text-xs">Loading commercial sales orders...</p>
        </div>

        <div v-else-if="orders.length === 0" class="p-12 text-center text-slate-500">
          <ShoppingCart class="w-10 h-10 mx-auto text-slate-300 mb-3" />
          <p class="text-sm font-semibold text-slate-700">No Sales Orders Found</p>
          <p class="text-xs text-slate-500 mt-1">There are no confirmed sales orders matching the current filter criteria.</p>
        </div>

        <div v-else class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-[#F4F6FA] text-[11px] font-bold text-slate-600 uppercase tracking-wider border-b border-slate-200">
                <th class="py-3 px-4">Sales Order Ref</th>
                <th class="py-3 px-4">Customer & Organization</th>
                <th class="py-3 px-4">Fulfillment Hub</th>
                <th class="py-3 px-4 text-center">BOM Units</th>
                <th class="py-3 px-4 text-right">Order Total (SAR)</th>
                <th class="py-3 px-4 text-right">Paid / Balance Due</th>
                <th class="py-3 px-4 text-center">Payment</th>
                <th class="py-3 px-4 text-center">Order Status</th>
                <th class="py-3 px-4 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs">
              <tr
                v-for="order in orders"
                :key="order.id"
                class="hover:bg-blue-50/40 transition-colors"
              >
                <!-- Order Number & Origin Quotation -->
                <td class="py-3.5 px-4">
                  <div class="font-bold text-[#042562] flex items-center gap-1.5">
                    <BadgeDollarSign class="w-3.5 h-3.5 text-[#1865C0] flex-shrink-0" />
                    <span>{{ order.order_number }}</span>
                  </div>
                  <div v-if="order.quotation_number" class="text-[10px] text-slate-500 mt-0.5 flex items-center gap-1">
                    <span>From Proposal:</span>
                    <span class="font-semibold text-purple-700">{{ order.quotation_number }}</span>
                  </div>
                  <div v-else class="text-[10px] text-slate-400 mt-0.5">
                    Direct Point of Sale
                  </div>
                </td>

                <!-- Customer & Organization -->
                <td class="py-3.5 px-4">
                  <div class="font-semibold text-slate-900">{{ order.customer_name }}</div>
                  <div class="text-[11px] text-slate-500 truncate max-w-[200px]">
                    {{ order.company_name || order.customer_email }}
                  </div>
                </td>

                <!-- Fulfillment Store -->
                <td class="py-3.5 px-4 text-slate-600">
                  <div class="flex items-center gap-1 text-[11px]">
                    <Building class="w-3.5 h-3.5 text-slate-400" />
                    <span class="truncate max-w-[140px]">{{ order.store_name || 'Central Hub (HQ)' }}</span>
                  </div>
                </td>

                <!-- Units -->
                <td class="py-3.5 px-4 text-center">
                  <span class="font-bold text-slate-800">{{ order.total_quantity || order.items_count }}</span>
                  <span class="text-[10px] text-slate-400 block">({{ order.items_count }} items)</span>
                </td>

                <!-- Total Amount -->
                <td class="py-3.5 px-4 text-right">
                  <div class="font-black text-[#042562] text-sm">
                    {{ formatCurrency(order.total_amount) }}
                  </div>
                  <div class="text-[10px] text-slate-400">
                    Inc. 15% VAT
                  </div>
                </td>

                <!-- Paid / Balance Due -->
                <td class="py-3.5 px-4 text-right">
                  <div class="font-bold text-emerald-700 text-xs">
                    {{ formatCurrency(order.paid_amount) }}
                  </div>
                  <div v-if="order.balance_due > 0" class="text-[10px] text-red-600 font-bold">
                    Due: {{ formatCurrency(order.balance_due) }}
                  </div>
                  <div v-else class="text-[10px] text-slate-400">
                    Settled in Full
                  </div>
                </td>

                <!-- Payment Status Badge -->
                <td class="py-3.5 px-4 text-center">
                  <span
                    :class="[
                      'inline-block px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider',
                      paymentBadgeClass(order.payment_status)
                    ]"
                  >
                    {{ order.payment_status_label || order.payment_status }}
                  </span>
                </td>

                <!-- Order Status Badge -->
                <td class="py-3.5 px-4 text-center">
                  <span
                    :class="[
                      'inline-block px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider',
                      orderBadgeClass(order.status)
                    ]"
                  >
                    {{ order.status_label || order.status }}
                  </span>
                </td>

                <!-- Actions -->
                <td class="py-3.5 px-4 text-right">
                  <div class="flex items-center justify-end gap-1.5">
                    <!-- Record Payment Button -->
                    <button
                      v-if="order.balance_due > 0"
                      @click="openPaymentModal(order)"
                      class="inline-flex items-center gap-1 bg-[#1865C0] hover:bg-[#042562] text-white text-[11px] font-bold px-2.5 py-1.5 rounded-lg shadow-sm transition-colors"
                      title="Record Payment Receipt"
                    >
                      <CreditCard class="w-3.5 h-3.5" />
                      <span>Pay</span>
                    </button>

                    <!-- Order Details Drawer -->
                    <button
                      @click="openOrderDetailDrawer(order)"
                      class="p-1.5 text-slate-600 hover:text-[#042562] hover:bg-slate-100 rounded-lg transition-colors"
                      title="View Detailed Order Ledger"
                    >
                      <Eye class="w-4 h-4" />
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Slide-Over Drawer: Order Ledger & Payment History -->
      <div
        v-if="detailDrawerOpen && selectedOrder"
        class="fixed inset-0 z-50 overflow-hidden bg-navy-950/50 backdrop-blur-sm flex justify-end"
        @click.self="detailDrawerOpen = false"
      >
        <div class="w-full max-w-2xl bg-white h-full shadow-2xl flex flex-col transform transition-transform duration-300">
          <!-- Drawer Header -->
          <div class="p-6 bg-[#042562] text-white flex items-center justify-between border-b border-navy-800">
            <div>
              <div class="flex items-center gap-2">
                <BadgeDollarSign class="w-5 h-5 text-[#1865C0]" />
                <h2 class="text-lg font-black tracking-tight">{{ selectedOrder.order_number }}</h2>
                <span
                  :class="[
                    'px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider',
                    orderBadgeClass(selectedOrder.status)
                  ]"
                >
                  {{ selectedOrder.status_label || selectedOrder.status }}
                </span>
              </div>
              <p class="text-xs text-blue-200 mt-1">
                Confirmed on {{ selectedOrder.formatted_confirmed_at || selectedOrder.confirmed_at }} &bull; Branch: {{ selectedOrder.store_name }}
              </p>
            </div>
            <button
              @click="detailDrawerOpen = false"
              class="text-blue-200 hover:text-white p-1 rounded-lg transition-colors"
            >
              <X class="w-5 h-5" />
            </button>
          </div>

          <!-- Drawer Body -->
          <div class="flex-1 overflow-y-auto p-6 space-y-6">
            <!-- Client & Delivery Information -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div class="bg-[#F4F6FA] p-4 rounded-xl border border-slate-200">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Customer & Billing</span>
                <p class="text-sm font-bold text-[#042562] mt-1">{{ selectedOrder.customer_name }}</p>
                <p class="text-xs text-slate-600 font-semibold">{{ selectedOrder.company_name }}</p>
                <p class="text-xs text-slate-500 mt-1">{{ selectedOrder.customer_email }}</p>
                <p class="text-xs text-slate-500">{{ selectedOrder.customer_phone }}</p>
                <p class="text-xs text-slate-600 mt-1">{{ selectedOrder.billing_address }}</p>
              </div>

              <div class="bg-[#F4F6FA] p-4 rounded-xl border border-slate-200">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Shipping Destination</span>
                <p class="text-xs text-slate-700 mt-1 leading-relaxed">{{ selectedOrder.shipping_address || 'Customer site dispatch' }}</p>
                <div class="mt-3 pt-2 border-t border-slate-200 text-xs">
                  <span class="text-[10px] text-slate-400 block uppercase">Payment Status:</span>
                  <span :class="['font-bold inline-block px-2 py-0.5 rounded text-[10px] mt-0.5', paymentBadgeClass(selectedOrder.payment_status)]">
                    {{ selectedOrder.payment_status_label || selectedOrder.payment_status }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Bill of Materials (BOM) Line Items -->
            <div>
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Allocated Hardware Equipment</h3>
                <span class="text-xs text-slate-400">{{ selectedOrder.items?.length || 0 }} Items</span>
              </div>

              <div class="border border-slate-200 rounded-xl overflow-hidden">
                <table class="w-full text-left border-collapse text-xs">
                  <thead class="bg-[#F4F6FA] text-[10px] font-bold text-slate-600 uppercase border-b border-slate-200">
                    <tr>
                      <th class="py-2.5 px-3">Product / Model</th>
                      <th class="py-2.5 px-3 text-center">Qty</th>
                      <th class="py-2.5 px-3 text-right">Unit Price</th>
                      <th class="py-2.5 px-3 text-right">Total (SAR)</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-100">
                    <tr v-for="item in selectedOrder.items" :key="item.id" class="hover:bg-slate-50">
                      <td class="py-3 px-3">
                        <div class="font-bold text-[#042562]">{{ item.product_name }}</div>
                        <div class="text-[10px] text-slate-500 flex items-center gap-2 mt-0.5">
                          <span>Model: {{ item.product_model }}</span>
                          <span>&bull;</span>
                          <span>SKU: {{ item.product_sku }}</span>
                        </div>
                      </td>
                      <td class="py-3 px-3 text-center font-bold">{{ item.quantity }}</td>
                      <td class="py-3 px-3 text-right text-slate-600">{{ formatCurrency(item.unit_price) }}</td>
                      <td class="py-3 px-3 text-right font-bold text-[#042562]">{{ formatCurrency(item.total_price) }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Financial Settlement Ledger Card -->
            <div class="bg-[#F4F6FA] p-4 rounded-xl border border-slate-200 space-y-2 text-xs">
              <div class="flex justify-between text-slate-600">
                <span>Gross Order Value</span>
                <span class="font-semibold">{{ formatCurrency(selectedOrder.subtotal) }}</span>
              </div>
              <div v-if="selectedOrder.discount_amount > 0" class="flex justify-between text-emerald-700 font-semibold">
                <span>Discounts Applied</span>
                <span>- {{ formatCurrency(selectedOrder.discount_amount) }}</span>
              </div>
              <div class="flex justify-between text-slate-600">
                <span>Value Added Tax (15% VAT)</span>
                <span class="font-semibold">{{ formatCurrency(selectedOrder.tax_amount) }}</span>
              </div>
              <div class="pt-2 border-t border-slate-300 flex justify-between text-sm font-black text-[#042562]">
                <span>Total Order Value (SAR)</span>
                <span>{{ formatCurrency(selectedOrder.total_amount) }}</span>
              </div>
              <div class="flex justify-between text-emerald-700 font-bold">
                <span>Total Collected to Date</span>
                <span>{{ formatCurrency(selectedOrder.paid_amount) }}</span>
              </div>
              <div
                v-if="selectedOrder.balance_due > 0"
                class="flex justify-between text-red-600 font-black text-sm pt-1 border-t border-red-200"
              >
                <span>Outstanding Receivables</span>
                <span>{{ formatCurrency(selectedOrder.balance_due) }}</span>
              </div>
            </div>

            <!-- Payment Transaction Receipts -->
            <div>
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Official Payment Receipts</h3>
                <button
                  v-if="selectedOrder.balance_due > 0"
                  @click="openPaymentModal(selectedOrder)"
                  class="text-xs font-bold text-[#1865C0] hover:text-[#042562] flex items-center gap-1"
                >
                  <Plus class="w-3.5 h-3.5" />
                  <span>Record Payment Receipt</span>
                </button>
              </div>

              <div v-if="!selectedOrder.payments || selectedOrder.payments.length === 0" class="p-6 bg-slate-50 border border-slate-200 rounded-xl text-center text-xs text-slate-500">
                No payment transactions recorded yet. Balance due: {{ formatCurrency(selectedOrder.balance_due) }}.
              </div>

              <div v-else class="space-y-2">
                <div
                  v-for="pay in selectedOrder.payments"
                  :key="pay.id"
                  class="bg-white border border-slate-200 rounded-xl p-3 flex items-center justify-between shadow-sm text-xs"
                >
                  <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-[#16A34A] flex items-center justify-center font-bold">
                      <Receipt class="w-4 h-4" />
                    </div>
                    <div>
                      <div class="font-bold text-[#042562]">{{ pay.receipt_number }}</div>
                      <div class="text-[10px] text-slate-400 mt-0.5">
                        {{ pay.payment_method }} &bull; Ref: {{ pay.payment_reference || 'N/A' }} &bull; {{ pay.formatted_payment_date || pay.payment_date }}
                      </div>
                    </div>
                  </div>
                  <div class="text-right">
                    <span class="font-black text-emerald-700 text-sm block">+ {{ formatCurrency(pay.amount) }}</span>
                    <span v-if="pay.received_by" class="text-[9px] text-slate-400">By: {{ pay.received_by.name }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Drawer Footer Actions -->
          <div class="p-4 bg-[#F4F6FA] border-t border-slate-200 flex items-center justify-between">
            <span class="text-xs text-slate-500">
              Internal Ledger ID: #{{ selectedOrder.id }}
            </span>

            <button
              v-if="selectedOrder.balance_due > 0"
              @click="openPaymentModal(selectedOrder)"
              class="inline-flex items-center gap-2 bg-[#1865C0] hover:bg-[#042562] text-white px-5 py-2 rounded-lg text-xs font-bold transition-colors shadow-sm"
            >
              <CreditCard class="w-4 h-4" />
              <span>Record Payment Receipt</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Modal: Record Payment -->
      <div
        v-if="paymentModalOpen && paymentOrder"
        class="fixed inset-0 z-50 overflow-y-auto bg-navy-950/60 backdrop-blur-sm flex items-center justify-center p-4"
      >
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden border border-slate-200">
          <div class="bg-[#042562] text-white p-5 flex items-center justify-between">
            <div class="flex items-center gap-2">
              <CreditCard class="w-5 h-5 text-[#1865C0]" />
              <h3 class="font-black text-base">Record Payment Receipt</h3>
            </div>
            <button @click="paymentModalOpen = false" class="text-blue-200 hover:text-white">
              <X class="w-5 h-5" />
            </button>
          </div>

          <form @submit.prevent="handleRecordPayment" class="p-6 space-y-4 text-xs">
            <!-- Order Balance Banner -->
            <div class="bg-[#F4F6FA] p-3.5 rounded-xl border border-slate-200 space-y-1">
              <div class="flex justify-between">
                <span class="text-slate-500">Sales Order:</span>
                <span class="font-bold text-[#042562]">{{ paymentOrder.order_number }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-slate-500">Client:</span>
                <span class="font-semibold text-slate-800">{{ paymentOrder.customer_name }}</span>
              </div>
              <div class="flex justify-between text-sm font-black text-red-600 pt-1.5 border-t border-slate-300">
                <span>Outstanding Balance:</span>
                <span>{{ formatCurrency(paymentOrder.balance_due) }}</span>
              </div>
            </div>

            <!-- Amount Input -->
            <div>
              <label class="block font-bold text-slate-700 mb-1">Payment Amount (SAR) *</label>
              <input
                v-model.number="paymentForm.amount"
                type="number"
                step="0.01"
                min="0.01"
                :max="paymentOrder.balance_due"
                required
                class="w-full bg-[#F4F6FA] text-xs font-bold text-[#042562] rounded-lg px-3 py-2.5 border border-slate-300 focus:outline-none focus:border-[#1865C0]"
              />
            </div>

            <!-- Payment Method -->
            <div>
              <label class="block font-bold text-slate-700 mb-1">Payment Method *</label>
              <select
                v-model="paymentForm.payment_method"
                required
                class="w-full bg-[#F4F6FA] text-xs font-semibold rounded-lg px-3 py-2.5 border border-slate-300 focus:outline-none"
              >
                <option value="BANK_TRANSFER">Bank Wire Transfer</option>
                <option value="CHEQUE">Corporate Cheque</option>
                <option value="CREDIT_CARD">Credit Card / POS Terminal</option>
                <option value="CASH">Counter Cash</option>
                <option value="LETTER_OF_CREDIT">Letter of Credit (LC)</option>
              </select>
            </div>

            <!-- Payment Reference -->
            <div>
              <label class="block font-bold text-slate-700 mb-1">Bank Reference / Cheque #</label>
              <input
                v-model="paymentForm.payment_reference"
                type="text"
                placeholder="e.g. SNB-WIRE-202611-1029"
                class="w-full bg-[#F4F6FA] text-xs rounded-lg px-3 py-2 border border-slate-300 focus:outline-none"
              />
            </div>

            <!-- Payment Notes -->
            <div>
              <label class="block font-bold text-slate-700 mb-1">Internal Notes</label>
              <textarea
                v-model="paymentForm.notes"
                rows="2"
                placeholder="Confirmation remarks..."
                class="w-full bg-[#F4F6FA] text-xs rounded-lg p-2.5 border border-slate-300 focus:outline-none"
              ></textarea>
            </div>

            <!-- Modal Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200">
              <button
                type="button"
                @click="paymentModalOpen = false"
                class="px-4 py-2 border border-slate-300 text-slate-600 rounded-lg font-bold hover:bg-slate-100"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="submitting"
                class="px-5 py-2 bg-[#1865C0] hover:bg-[#042562] text-white rounded-lg font-bold uppercase tracking-wider transition-colors disabled:opacity-50"
              >
                {{ submitting ? 'Issuing Receipt...' : 'Generate Receipt & Settle' }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Modal: Register Direct Sale -->
      <div
        v-if="directSaleModalOpen"
        class="fixed inset-0 z-50 overflow-y-auto bg-navy-950/60 backdrop-blur-sm flex items-center justify-center p-4"
      >
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full overflow-hidden border border-slate-200 my-8">
          <div class="bg-[#042562] text-white p-5 flex items-center justify-between">
            <div class="flex items-center gap-2">
              <ShoppingCart class="w-5 h-5 text-[#1865C0]" />
              <h3 class="font-black text-base">Register Direct Walk-In / Phone Sale</h3>
            </div>
            <button @click="directSaleModalOpen = false" class="text-blue-200 hover:text-white">
              <X class="w-5 h-5" />
            </button>
          </div>

          <form @submit.prevent="handleCreateDirectSale" class="p-6 space-y-4 text-xs">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block font-bold text-slate-700 mb-1">Customer / Organization *</label>
                <input
                  v-model="directSaleForm.customer_name"
                  type="text"
                  required
                  placeholder="e.g. Riyadh Digital Labs"
                  class="w-full bg-[#F4F6FA] text-xs rounded-lg px-3 py-2 border border-slate-300"
                />
              </div>

              <div>
                <label class="block font-bold text-slate-700 mb-1">Fulfillment Store Branch *</label>
                <select
                  v-model="directSaleForm.store_id"
                  required
                  class="w-full bg-[#F4F6FA] text-xs rounded-lg px-3 py-2 border border-slate-300"
                >
                  <option :value="1">Central Distribution Hub (HQ)</option>
                  <option :value="2">North Logistics Facility</option>
                  <option :value="3">West Coast Regional Store</option>
                </select>
              </div>
            </div>

            <!-- Hardware Item Selection -->
            <div>
              <label class="block font-bold text-slate-700 mb-1">Equipment Model *</label>
              <select
                v-model="directSaleForm.selected_product_id"
                required
                class="w-full bg-[#F4F6FA] text-xs rounded-lg px-3 py-2 border border-slate-300"
              >
                <option :value="1">Dell PowerEdge R760 2U Rack Server (SAR 5,200.00)</option>
                <option :value="2">Cisco Catalyst 9300X-48HX Switch (SAR 5,200.00)</option>
                <option :value="3">NetApp AFF A250 NVMe Array (SAR 9,250.00)</option>
                <option :value="4">Schneider APC Smart-UPS Ultra 5kW (SAR 6,000.00)</option>
              </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block font-bold text-slate-700 mb-1">Quantity Units *</label>
                <input
                  v-model.number="directSaleForm.quantity"
                  type="number"
                  min="1"
                  required
                  class="w-full bg-[#F4F6FA] text-xs rounded-lg px-3 py-2 border border-slate-300"
                />
              </div>

              <div>
                <label class="block font-bold text-slate-700 mb-1">Initial Cash / POS Settlement</label>
                <input
                  v-model.number="directSaleForm.initial_payment_amount"
                  type="number"
                  min="0"
                  step="0.01"
                  placeholder="0.00"
                  class="w-full bg-[#F4F6FA] text-xs rounded-lg px-3 py-2 border border-slate-300"
                />
              </div>
            </div>

            <!-- Warning Notice -->
            <div class="p-3 bg-blue-50 border border-blue-200 rounded-xl text-blue-900 text-[11px]">
              Direct sale will immediately confirm the sales order, deduct physical stock balance from the store, and generate transaction receipts.
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200">
              <button
                type="button"
                @click="directSaleModalOpen = false"
                class="px-4 py-2 border border-slate-300 text-slate-600 rounded-lg font-bold"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="submitting"
                class="px-5 py-2 bg-[#042562] hover:bg-[#1865C0] text-white rounded-lg font-bold uppercase tracking-wider transition-colors disabled:opacity-50"
              >
                {{ submitting ? 'Confirming...' : 'Confirm Sale & Deduct Stock' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { salesOrderService } from '@/services/salesOrderService';
import { useNotificationStore } from '@/stores/notificationStore';
import {
  BadgeDollarSign,
  Boxes,
  CheckCircle2,
  Clock,
  Building,
  Search,
  RefreshCw,
  Plus,
  CreditCard,
  ShoppingCart,
  Receipt,
  Eye,
  X
} from 'lucide-vue-next';

const notify = useNotificationStore();

const loading = ref(false);
const submitting = ref(false);
const orders = ref([]);
const counts = ref({});
const metrics = ref({
  total_sales: 0,
  total_collected: 0,
  outstanding_receivables: 0,
  total_products_sold: 0,
});

const activeOrderStatus = ref('all');
const selectedPaymentStatus = ref('all');
const searchQuery = ref('');
const selectedStore = ref('');

const detailDrawerOpen = ref(false);
const selectedOrder = ref(null);

const paymentModalOpen = ref(false);
const paymentOrder = ref(null);
const paymentForm = ref({
  amount: 0,
  payment_method: 'BANK_TRANSFER',
  payment_reference: '',
  notes: '',
});

const directSaleModalOpen = ref(false);
const directSaleForm = ref({
  customer_name: '',
  store_id: 1,
  selected_product_id: 1,
  quantity: 1,
  initial_payment_amount: 0,
});

const orderStatusTabs = [
  { label: 'All Orders', value: 'all' },
  { label: 'Confirmed', value: 'confirmed' },
  { label: 'Processing', value: 'processing' },
  { label: 'Completed', value: 'completed' },
  { label: 'Cancelled', value: 'cancelled' },
];

function formatCurrency(val) {
  if (val === null || val === undefined) return 'SAR 0.00';
  return `SAR ${Number(val).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
}

function paymentBadgeClass(status) {
  switch (String(status).toUpperCase()) {
    case 'PAID':
      return 'bg-emerald-100 text-emerald-800 border border-emerald-300';
    case 'PARTIAL':
      return 'bg-amber-100 text-amber-800 border border-amber-300';
    case 'UNPAID':
      return 'bg-red-100 text-red-800 border border-red-300';
    default:
      return 'bg-slate-100 text-slate-700 border border-slate-300';
  }
}

function orderBadgeClass(status) {
  switch (String(status).toUpperCase()) {
    case 'CONFIRMED':
      return 'bg-blue-100 text-[#042562] border border-blue-300';
    case 'PROCESSING':
      return 'bg-amber-100 text-amber-800 border border-amber-300';
    case 'COMPLETED':
      return 'bg-emerald-100 text-emerald-800 border border-emerald-300';
    case 'CANCELLED':
      return 'bg-red-100 text-red-800 border border-red-300';
    default:
      return 'bg-slate-100 text-slate-700 border border-slate-300';
  }
}

async function fetchOrders() {
  loading.value = true;
  try {
    const params = {
      status: activeOrderStatus.value,
      payment_status: selectedPaymentStatus.value !== 'all' ? selectedPaymentStatus.value : undefined,
      search: searchQuery.value,
      store_id: selectedStore.value || undefined,
    };
    const [ordersRes, metricsRes] = await Promise.all([
      salesOrderService.getOrders(params),
      salesOrderService.getDashboardMetrics(selectedStore.value || null),
    ]);

    orders.value = ordersRes.data || [];
    if (ordersRes.meta) {
      counts.value = ordersRes.meta.counts || {};
    }
    if (metricsRes.data) {
      metrics.value = metricsRes.data;
    }
  } catch (err) {
    notify.error(err.message || 'Failed to load sales orders');
  } finally {
    loading.value = false;
  }
}

let debounceTimer = null;
function debounceSearch() {
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(() => {
    fetchOrders();
  }, 350);
}

function setOrderStatusTab(tab) {
  activeOrderStatus.value = tab;
  fetchOrders();
}

function openOrderDetailDrawer(order) {
  selectedOrder.value = order;
  detailDrawerOpen.value = true;
}

function openPaymentModal(order) {
  paymentOrder.value = order;
  paymentForm.value = {
    amount: order.balance_due,
    payment_method: 'BANK_TRANSFER',
    payment_reference: '',
    notes: '',
  };
  paymentModalOpen.value = true;
}

async function handleRecordPayment() {
  if (!paymentOrder.value) return;
  submitting.value = true;
  try {
    const res = await salesOrderService.recordPayment(paymentOrder.value.id, paymentForm.value);
    notify.success(res.message || 'Payment receipt generated successfully');
    paymentModalOpen.value = false;
    
    // Update active order drawer if open
    if (selectedOrder.value?.id === paymentOrder.value.id) {
      selectedOrder.value.paid_amount = (selectedOrder.value.paid_amount || 0) + paymentForm.value.amount;
      selectedOrder.value.balance_due = Math.max(0, selectedOrder.value.total_amount - selectedOrder.value.paid_amount);
      selectedOrder.value.payments = selectedOrder.value.payments || [];
      selectedOrder.value.payments.unshift(res.data);
    }
    await fetchOrders();
  } catch (err) {
    notify.error(err.message || 'Failed to record payment');
  } finally {
    submitting.value = false;
  }
}

function openDirectSaleModal() {
  directSaleModalOpen.value = true;
}

async function handleCreateDirectSale() {
  submitting.value = true;
  try {
    const prices = { 1: 5200.00, 2: 5200.00, 3: 9250.00, 4: 6000.00 };
    const price = prices[directSaleForm.value.selected_product_id] || 1000.00;

    const payload = {
      store_id: directSaleForm.value.store_id,
      customer_name: directSaleForm.value.customer_name,
      items: [
        {
          product_id: directSaleForm.value.selected_product_id,
          quantity: directSaleForm.value.quantity,
          unit_price: price,
        }
      ],
      initial_payment_amount: directSaleForm.value.initial_payment_amount,
      payment_method: 'CASH',
    };

    const res = await salesOrderService.createDirectSale(payload);
    notify.success(res.message || 'Direct sales order registered successfully');
    directSaleModalOpen.value = false;
    await fetchOrders();
  } catch (err) {
    notify.error(err.message || 'Failed to create direct sale');
  } finally {
    submitting.value = false;
  }
}

onMounted(() => {
  fetchOrders();
});
</script>
