<template>
  <AdminLayout>
    <div class="space-y-6">
      <!-- Page Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-black text-[#042562] tracking-tight">
            Quotation Management
          </h1>
          <p class="text-xs text-slate-500 mt-0.5">
            Commercial B2B proposals, pricing matrices, branded PDF documents, and conversion to confirmed sales orders.
          </p>
        </div>

        <div class="flex items-center gap-3">
          <button
            @click="openCreateModal"
            class="inline-flex items-center gap-2 bg-[#042562] hover:bg-[#1865C0] text-white text-xs font-bold uppercase tracking-wider px-4 py-2.5 rounded-lg transition-colors shadow-sm"
          >
            <Plus class="w-4 h-4" />
            <span>Create Formal Quotation</span>
          </button>
        </div>
      </div>

      <!-- Financial Pipeline Metrics Row -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
          <div class="flex items-center justify-between">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Active Pipeline</span>
            <div class="w-8 h-8 rounded-lg bg-blue-50 text-[#1865C0] flex items-center justify-center">
              <TrendingUp class="w-4 h-4" />
            </div>
          </div>
          <div class="text-2xl font-black text-[#042562] mt-2">
            {{ formatCurrency(pipelineVolume) }}
          </div>
          <span class="text-[10px] text-slate-500">Prepared, Sent & Approved proposals</span>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
          <div class="flex items-center justify-between">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Approved by Clients</span>
            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-[#16A34A] flex items-center justify-center">
              <CheckCircle2 class="w-4 h-4" />
            </div>
          </div>
          <div class="text-2xl font-black text-[#16A34A] mt-2">
            {{ formatCurrency(approvedVolume) }}
          </div>
          <span class="text-[10px] text-emerald-700 font-semibold">{{ counts.approved || 0 }} proposals ready for SO conversion</span>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
          <div class="flex items-center justify-between">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Awaiting Decision</span>
            <div class="w-8 h-8 rounded-lg bg-amber-50 text-[#F59E0B] flex items-center justify-center">
              <Clock class="w-4 h-4" />
            </div>
          </div>
          <div class="text-2xl font-black text-[#F59E0B] mt-2">
            {{ (counts.sent || 0) + (counts.prepared || 0) }}
          </div>
          <span class="text-[10px] text-slate-500">Active 30-day commercial price locks</span>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
          <div class="flex items-center justify-between">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Converted Orders</span>
            <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-700 flex items-center justify-center">
              <BadgeDollarSign class="w-4 h-4" />
            </div>
          </div>
          <div class="text-2xl font-black text-purple-900 mt-2">
            {{ counts.converted || 0 }}
          </div>
          <span class="text-[10px] text-purple-700 font-semibold">Active Sales Orders confirmed</span>
        </div>
      </div>

      <!-- Filter Tabs & Search Toolbar -->
      <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm space-y-4">
        <!-- Status Tabs -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs border-b border-slate-100">
          <button
            v-for="tab in statusTabs"
            :key="tab.value"
            @click="setTab(tab.value)"
            :class="[
              'px-3.5 py-2 font-bold rounded-lg transition-colors flex items-center gap-1.5 whitespace-nowrap',
              activeTab === tab.value
                ? 'bg-[#042562] text-white shadow-sm'
                : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'
            ]"
          >
            <span>{{ tab.label }}</span>
            <span
              :class="[
                'text-[10px] px-1.5 py-0.2 rounded-full font-bold',
                activeTab === tab.value ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700'
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
              placeholder="Search by QT #, customer, company, email..."
              class="w-full bg-[#F4F6FA] text-xs text-[#172033] rounded-lg pl-9 pr-4 py-2.5 border border-slate-300 focus:outline-none focus:border-[#1865C0]"
            />
          </div>

          <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <select
              v-model="selectedStore"
              @change="fetchQuotations"
              class="bg-[#F4F6FA] text-xs font-semibold text-[#172033] rounded-lg px-3 py-2 border border-slate-300 focus:outline-none focus:border-[#1865C0]"
            >
              <option value="">All Fulfillment Stores</option>
              <option value="1">Central Distribution Hub (HQ)</option>
              <option value="2">North Logistics Facility</option>
              <option value="3">West Coast Regional Store</option>
            </select>

            <button
              @click="fetchQuotations"
              class="p-2.5 text-slate-500 hover:text-[#042562] bg-[#F4F6FA] border border-slate-300 rounded-lg hover:bg-slate-100 transition-colors"
              title="Refresh Quotations"
            >
              <RefreshCw :class="['w-4 h-4', { 'animate-spin': loading }]" />
            </button>
          </div>
        </div>
      </div>

      <!-- Quotations Data Table -->
      <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div v-if="loading && quotations.length === 0" class="p-12 text-center text-slate-500">
          <RefreshCw class="w-6 h-6 animate-spin mx-auto text-[#1865C0] mb-2" />
          <p class="text-xs">Loading commercial quotations...</p>
        </div>

        <div v-else-if="quotations.length === 0" class="p-12 text-center text-slate-500">
          <FileText class="w-10 h-10 mx-auto text-slate-300 mb-3" />
          <p class="text-sm font-semibold text-slate-700">No Quotations Found</p>
          <p class="text-xs text-slate-500 mt-1">There are no commercial quotations matching your filter criteria.</p>
        </div>

        <div v-else class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-[#F4F6FA] text-[11px] font-bold text-slate-600 uppercase tracking-wider border-b border-slate-200">
                <th class="py-3 px-4">Quotation Ref</th>
                <th class="py-3 px-4">Client / Organization</th>
                <th class="py-3 px-4">Store Hub</th>
                <th class="py-3 px-4 text-center">BOM Units</th>
                <th class="py-3 px-4 text-right">Grand Total (SAR)</th>
                <th class="py-3 px-4">Validity</th>
                <th class="py-3 px-4 text-center">Status</th>
                <th class="py-3 px-4 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs">
              <tr
                v-for="quote in quotations"
                :key="quote.id"
                class="hover:bg-blue-50/40 transition-colors"
              >
                <!-- Quotation Ref -->
                <td class="py-3.5 px-4">
                  <div class="font-bold text-[#042562] flex items-center gap-1.5">
                    <FileText class="w-3.5 h-3.5 text-[#1865C0] flex-shrink-0" />
                    <span>{{ quote.quotation_number }}</span>
                  </div>
                  <div v-if="quote.rfq_number" class="text-[10px] text-slate-500 mt-0.5 flex items-center gap-1">
                    <span>From:</span>
                    <span class="font-semibold text-blue-600">{{ quote.rfq_number }}</span>
                  </div>
                  <div v-else class="text-[10px] text-slate-400 mt-0.5">
                    Direct Solution
                  </div>
                </td>

                <!-- Client / Organization -->
                <td class="py-3.5 px-4">
                  <div class="font-semibold text-slate-900">{{ quote.customer_name }}</div>
                  <div class="text-[11px] text-slate-500 truncate max-w-[200px]">
                    {{ quote.company_name || quote.customer_email }}
                  </div>
                </td>

                <!-- Store Hub -->
                <td class="py-3.5 px-4 text-slate-600">
                  <div class="flex items-center gap-1 text-[11px]">
                    <Building class="w-3.5 h-3.5 text-slate-400" />
                    <span class="truncate max-w-[140px]">{{ quote.store_name || 'Central Hub (HQ)' }}</span>
                  </div>
                </td>

                <!-- BOM Units -->
                <td class="py-3.5 px-4 text-center">
                  <span class="font-bold text-slate-800">{{ quote.total_quantity || quote.items_count }}</span>
                  <span class="text-[10px] text-slate-400 block">({{ quote.items_count }} items)</span>
                </td>

                <!-- Financial Total -->
                <td class="py-3.5 px-4 text-right">
                  <div class="font-black text-[#042562] text-sm">
                    {{ formatCurrency(quote.total_amount) }}
                  </div>
                  <div class="text-[10px] text-slate-400">
                    Inc. 15% VAT ({{ formatCurrency(quote.tax_amount) }})
                  </div>
                </td>

                <!-- Validity -->
                <td class="py-3.5 px-4">
                  <div :class="['text-xs font-semibold', quote.is_expired ? 'text-red-600' : 'text-slate-700']">
                    {{ quote.formatted_valid_until || quote.valid_until }}
                  </div>
                  <span v-if="quote.is_expired" class="text-[9px] font-bold text-red-600 uppercase block">Expired</span>
                  <span v-else class="text-[10px] text-slate-400 block">Price lock</span>
                </td>

                <!-- Status Badge -->
                <td class="py-3.5 px-4 text-center">
                  <span
                    :class="[
                      'inline-block px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider',
                      badgeVariantClass(quote.badge_variant || quote.status)
                    ]"
                  >
                    {{ quote.status_label || quote.status }}
                  </span>
                </td>

                <!-- Actions -->
                <td class="py-3.5 px-4 text-right">
                  <div class="flex items-center justify-end gap-1.5">
                    <!-- Convert CTA Button (Prominent when approved) -->
                    <button
                      v-if="quote.can_be_converted || quote.status === 'APPROVED'"
                      @click="openConvertModal(quote)"
                      class="inline-flex items-center gap-1 bg-[#16A34A] hover:bg-emerald-700 text-white text-[11px] font-bold px-2.5 py-1.5 rounded-lg shadow-sm transition-colors"
                      title="Convert Approved Proposal to Confirmed Sales Order"
                    >
                      <ShoppingCart class="w-3.5 h-3.5" />
                      <span>Convert to SO</span>
                    </button>

                    <!-- PDF Download -->
                    <button
                      @click="downloadPdf(quote)"
                      class="p-1.5 text-slate-600 hover:text-[#042562] hover:bg-slate-100 rounded-lg transition-colors"
                      title="Download Branded PDF Quotation"
                    >
                      <Download class="w-4 h-4" />
                    </button>

                    <!-- Status Change Controller -->
                    <button
                      @click="openStatusModal(quote)"
                      class="p-1.5 text-slate-600 hover:text-[#1865C0] hover:bg-slate-100 rounded-lg transition-colors"
                      title="Transition Quotation Status"
                    >
                      <SlidersHorizontal class="w-4 h-4" />
                    </button>

                    <!-- View Drawer -->
                    <button
                      @click="openDetailDrawer(quote)"
                      class="p-1.5 text-slate-600 hover:text-[#042562] hover:bg-slate-100 rounded-lg transition-colors"
                      title="View Proposal Details"
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

      <!-- Slide-Over Drawer: Quotation Details & Audit Timeline -->
      <div
        v-if="detailDrawerOpen && selectedQuotation"
        class="fixed inset-0 z-50 overflow-hidden bg-navy-950/50 backdrop-blur-sm flex justify-end"
        @click.self="detailDrawerOpen = false"
      >
        <div class="w-full max-w-2xl bg-white h-full shadow-2xl flex flex-col transform transition-transform duration-300">
          <!-- Drawer Header -->
          <div class="p-6 bg-[#042562] text-white flex items-center justify-between border-b border-navy-800">
            <div>
              <div class="flex items-center gap-2">
                <FileText class="w-5 h-5 text-[#1865C0]" />
                <h2 class="text-lg font-black tracking-tight">{{ selectedQuotation.quotation_number }}</h2>
                <span
                  :class="[
                    'px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider',
                    badgeVariantClass(selectedQuotation.badge_variant || selectedQuotation.status)
                  ]"
                >
                  {{ selectedQuotation.status_label || selectedQuotation.status }}
                </span>
              </div>
              <p class="text-xs text-blue-200 mt-1">
                Prepared on {{ selectedQuotation.formatted_created_at }} &bull; Valid until {{ selectedQuotation.formatted_valid_until }}
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
            <!-- Converted Alert if active order exists -->
            <div
              v-if="selectedQuotation.sales_order_number || selectedQuotation.status === 'CONVERTED'"
              class="p-4 bg-purple-50 border border-purple-200 rounded-xl flex items-center justify-between"
            >
              <div class="flex items-center gap-3">
                <BadgeDollarSign class="w-5 h-5 text-purple-700" />
                <div>
                  <h4 class="text-xs font-bold text-purple-900">Converted into Active Sales Order</h4>
                  <p class="text-[11px] text-purple-700">Order Reference: {{ selectedQuotation.sales_order_number || 'SO-2026-ACTIVE' }}</p>
                </div>
              </div>
              <router-link
                to="/admin/sales-orders"
                class="text-xs font-bold text-purple-800 hover:text-purple-950 underline"
              >
                View Sales Order
              </router-link>
            </div>

            <!-- Customer & Fulfillment Hub Card -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div class="bg-[#F4F6FA] p-4 rounded-xl border border-slate-200">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Customer Details</span>
                <p class="text-sm font-bold text-[#042562] mt-1">{{ selectedQuotation.customer_name }}</p>
                <p class="text-xs text-slate-600 font-semibold">{{ selectedQuotation.company_name }}</p>
                <p class="text-xs text-slate-500 mt-1">{{ selectedQuotation.customer_email }}</p>
                <p class="text-xs text-slate-500">{{ selectedQuotation.customer_phone }}</p>
                <p class="text-xs text-slate-600 mt-1">{{ selectedQuotation.billing_address }}</p>
              </div>

              <div class="bg-[#F4F6FA] p-4 rounded-xl border border-slate-200">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Fulfillment Branch & Staff</span>
                <p class="text-sm font-bold text-[#042562] mt-1">{{ selectedQuotation.store_name || 'Central Distribution Hub (HQ)' }}</p>
                <div class="mt-2 text-xs text-slate-600">
                  <span class="text-[10px] text-slate-400 block uppercase">Prepared By:</span>
                  <span class="font-semibold">{{ selectedQuotation.prepared_by?.name || 'Marcus Brody' }}</span>
                  <span class="text-slate-500 block text-[11px]">{{ selectedQuotation.prepared_by?.email || 'sales@irack.com' }}</span>
                </div>
              </div>
            </div>

            <!-- Bill of Materials (BOM) Line Items -->
            <div>
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Quoted Hardware Bill of Materials</h3>
                <span class="text-xs text-slate-400">{{ selectedQuotation.items?.length || 0 }} Line Items</span>
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
                    <tr v-for="item in selectedQuotation.items" :key="item.id" class="hover:bg-slate-50">
                      <td class="py-3 px-3">
                        <div class="font-bold text-[#042562]">{{ item.product_name }}</div>
                        <div class="text-[10px] text-slate-500 flex items-center gap-2 mt-0.5">
                          <span>Model: {{ item.product_model }}</span>
                          <span>&bull;</span>
                          <span>SKU: {{ item.product_sku }}</span>
                        </div>
                        <p v-if="item.notes" class="text-[10px] text-blue-700 italic mt-0.5">{{ item.notes }}</p>
                      </td>
                      <td class="py-3 px-3 text-center font-bold">{{ item.quantity }}</td>
                      <td class="py-3 px-3 text-right text-slate-600">{{ formatCurrency(item.unit_price) }}</td>
                      <td class="py-3 px-3 text-right font-bold text-[#042562]">{{ formatCurrency(item.total_price) }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Financial Breakdown Table -->
            <div class="bg-[#F4F6FA] p-4 rounded-xl border border-slate-200 space-y-2 text-xs">
              <div class="flex justify-between text-slate-600">
                <span>Gross Subtotal</span>
                <span class="font-semibold">{{ formatCurrency(selectedQuotation.subtotal) }}</span>
              </div>
              <div v-if="selectedQuotation.discount_amount > 0" class="flex justify-between text-emerald-700 font-semibold">
                <span>Commercial Discount</span>
                <span>- {{ formatCurrency(selectedQuotation.discount_amount) }}</span>
              </div>
              <div class="flex justify-between text-slate-600">
                <span>Value Added Tax (15% VAT)</span>
                <span class="font-semibold">{{ formatCurrency(selectedQuotation.tax_amount) }}</span>
              </div>
              <div class="pt-2 border-t border-slate-300 flex justify-between text-sm font-black text-[#042562]">
                <span>Grand Total (SAR)</span>
                <span>{{ formatCurrency(selectedQuotation.total_amount) }}</span>
              </div>
            </div>

            <!-- Commercial Terms -->
            <div v-if="selectedQuotation.terms_conditions" class="p-4 bg-slate-50 rounded-xl border border-slate-200">
              <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block mb-1">Commercial Terms & Warranty</span>
              <p class="text-xs text-slate-700 whitespace-pre-line">{{ selectedQuotation.terms_conditions }}</p>
            </div>

            <!-- Lifecycle Status History Timeline -->
            <div>
              <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">Audit Timeline & History</h3>
              <div class="space-y-3 pl-2 border-l-2 border-slate-200">
                <div
                  v-for="hist in selectedQuotation.status_histories"
                  :key="hist.id"
                  class="relative pl-4"
                >
                  <div class="w-2.5 h-2.5 rounded-full bg-[#1865C0] absolute -left-[21px] top-1.5 ring-4 ring-white"></div>
                  <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-slate-900">{{ hist.status_label || hist.status }}</span>
                    <span class="text-[10px] text-slate-400">&bull; {{ hist.formatted_created_at || hist.created_at }}</span>
                  </div>
                  <p class="text-xs text-slate-600 mt-0.5">{{ hist.notes }}</p>
                  <span v-if="hist.user" class="text-[10px] text-slate-400">By: {{ hist.user.name }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Drawer Footer Actions -->
          <div class="p-4 bg-[#F4F6FA] border-t border-slate-200 flex items-center justify-between">
            <button
              @click="downloadPdf(selectedQuotation)"
              class="inline-flex items-center gap-1.5 bg-white border border-slate-300 text-slate-700 hover:text-[#042562] hover:bg-slate-50 px-4 py-2 rounded-lg text-xs font-bold transition-colors"
            >
              <Download class="w-4 h-4 text-[#1865C0]" />
              <span>Download Branded PDF</span>
            </button>

            <button
              v-if="selectedQuotation.can_be_converted || selectedQuotation.status === 'APPROVED'"
              @click="openConvertModal(selectedQuotation)"
              class="inline-flex items-center gap-2 bg-[#16A34A] hover:bg-emerald-700 text-white px-5 py-2 rounded-lg text-xs font-bold transition-colors shadow-sm"
            >
              <ShoppingCart class="w-4 h-4" />
              <span>Convert to Confirmed Sales Order</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Modal: Create / Draft Formal Quotation -->
      <div
        v-if="createModalOpen"
        class="fixed inset-0 z-50 overflow-y-auto bg-navy-950/60 backdrop-blur-sm flex items-center justify-center p-4"
      >
        <div class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full overflow-hidden border border-slate-200 my-8">
          <!-- Modal Header -->
          <div class="bg-[#042562] text-white p-5 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
              <FilePlus class="w-5 h-5 text-[#1865C0]" />
              <h3 class="font-black text-base">Prepare Formal Quotation Proposal</h3>
            </div>
            <button @click="createModalOpen = false" class="text-blue-200 hover:text-white">
              <X class="w-5 h-5" />
            </button>
          </div>

          <!-- Modal Body Form -->
          <form @submit.prevent="handleCreateQuotation" class="p-6 space-y-5">
            <!-- Client Information -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Customer Full Name *</label>
                <input
                  v-model="newQuoteForm.customer_name"
                  type="text"
                  required
                  class="w-full bg-[#F4F6FA] text-xs text-[#172033] rounded-lg px-3 py-2 border border-slate-300 focus:outline-none focus:border-[#1865C0]"
                  placeholder="e.g. David Sterling"
                />
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Company / Organization *</label>
                <input
                  v-model="newQuoteForm.company_name"
                  type="text"
                  required
                  class="w-full bg-[#F4F6FA] text-xs text-[#172033] rounded-lg px-3 py-2 border border-slate-300 focus:outline-none focus:border-[#1865C0]"
                  placeholder="e.g. Apex Cloud Solutions Inc."
                />
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Customer Email *</label>
                <input
                  v-model="newQuoteForm.customer_email"
                  type="email"
                  required
                  class="w-full bg-[#F4F6FA] text-xs text-[#172033] rounded-lg px-3 py-2 border border-slate-300 focus:outline-none focus:border-[#1865C0]"
                  placeholder="name@organization.com"
                />
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Phone Number</label>
                <input
                  v-model="newQuoteForm.customer_phone"
                  type="text"
                  class="w-full bg-[#F4F6FA] text-xs text-[#172033] rounded-lg px-3 py-2 border border-slate-300 focus:outline-none focus:border-[#1865C0]"
                  placeholder="+966 50 000 0000"
                />
              </div>
            </div>

            <!-- Store & Validity -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Fulfillment Store Branch *</label>
                <select
                  v-model="newQuoteForm.store_id"
                  required
                  class="w-full bg-[#F4F6FA] text-xs text-[#172033] rounded-lg px-3 py-2 border border-slate-300 focus:outline-none focus:border-[#1865C0]"
                >
                  <option :value="1">Central Distribution Hub (HQ)</option>
                  <option :value="2">North Logistics Facility</option>
                  <option :value="3">West Coast Regional Store</option>
                </select>
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Price Validity Date *</label>
                <input
                  v-model="newQuoteForm.valid_until"
                  type="date"
                  required
                  class="w-full bg-[#F4F6FA] text-xs text-[#172033] rounded-lg px-3 py-2 border border-slate-300 focus:outline-none focus:border-[#1865C0]"
                />
              </div>
            </div>

            <!-- Bill of Materials (BOM) Editor -->
            <div>
              <div class="flex items-center justify-between mb-2">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Hardware Bill of Materials</label>
                <button
                  type="button"
                  @click="addBomItem"
                  class="text-xs font-bold text-[#1865C0] hover:text-[#042562] flex items-center gap-1"
                >
                  <Plus class="w-3.5 h-3.5" />
                  <span>Add Line Item</span>
                </button>
              </div>

              <div class="space-y-2 border border-slate-200 rounded-xl p-3 bg-slate-50">
                <div
                  v-for="(item, index) in newQuoteForm.items"
                  :key="index"
                  class="grid grid-cols-1 sm:grid-cols-12 gap-2 items-center bg-white p-2.5 rounded-lg border border-slate-200"
                >
                  <div class="sm:col-span-5">
                    <select
                      v-model="item.product_id"
                      required
                      @change="handleProductSelect(item)"
                      class="w-full bg-[#F4F6FA] text-xs rounded-lg px-2.5 py-1.5 border border-slate-300 focus:outline-none"
                    >
                      <option :value="1">Dell PowerEdge R760 2U Rack Server (Base: SAR 5,200)</option>
                      <option :value="2">Cisco Catalyst 9300X-48HX Switch (Base: SAR 5,200)</option>
                      <option :value="3">NetApp AFF A250 NVMe Array (Base: SAR 9,250)</option>
                      <option :value="4">Schneider APC Smart-UPS Ultra 5kW (Base: SAR 6,000)</option>
                    </select>
                  </div>

                  <div class="sm:col-span-2">
                    <input
                      v-model.number="item.quantity"
                      type="number"
                      min="1"
                      required
                      placeholder="Qty"
                      class="w-full bg-[#F4F6FA] text-xs text-center rounded-lg px-2 py-1.5 border border-slate-300"
                    />
                  </div>

                  <div class="sm:col-span-2">
                    <input
                      v-model.number="item.unit_price"
                      type="number"
                      step="0.01"
                      required
                      placeholder="Price"
                      class="w-full bg-[#F4F6FA] text-xs text-right rounded-lg px-2 py-1.5 border border-slate-300"
                    />
                  </div>

                  <div class="sm:col-span-2 text-right font-bold text-[#042562] text-xs">
                    {{ formatCurrency(item.quantity * item.unit_price) }}
                  </div>

                  <div class="sm:col-span-1 text-center">
                    <button
                      type="button"
                      v-if="newQuoteForm.items.length > 1"
                      @click="removeBomItem(index)"
                      class="text-red-500 hover:text-red-700 p-1"
                    >
                      <Trash2 class="w-4 h-4" />
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Financial Discounts & Totals Preview -->
            <div class="bg-[#F4F6FA] p-4 rounded-xl border border-slate-200 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs">
              <div class="flex items-center gap-3">
                <span class="font-bold text-slate-700">Commercial Discount:</span>
                <input
                  v-model.number="newQuoteForm.discount_value"
                  type="number"
                  min="0"
                  step="0.01"
                  placeholder="0.00 SAR"
                  class="w-28 bg-white text-xs rounded-lg px-2 py-1.5 border border-slate-300"
                />
              </div>

              <div class="text-right">
                <span class="text-slate-500 block">Total Payable (15% VAT Included):</span>
                <span class="text-base font-black text-[#042562]">{{ formatCurrency(computedCreateTotal) }}</span>
              </div>
            </div>

            <!-- Terms & Conditions -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">Commercial Terms & Warranty Clause</label>
              <textarea
                v-model="newQuoteForm.terms_conditions"
                rows="2"
                class="w-full bg-[#F4F6FA] text-xs text-[#172033] rounded-lg p-3 border border-slate-300 focus:outline-none"
              ></textarea>
            </div>

            <!-- Modal Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200">
              <button
                type="button"
                @click="createModalOpen = false"
                class="px-4 py-2 border border-slate-300 text-slate-600 rounded-lg text-xs font-bold hover:bg-slate-100"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="submitting"
                class="px-6 py-2 bg-[#042562] hover:bg-[#1865C0] text-white rounded-lg text-xs font-bold uppercase tracking-wider transition-colors disabled:opacity-50"
              >
                {{ submitting ? 'Drafting Proposal...' : 'Create Official Quotation' }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Modal: Convert Approved Quotation into Confirmed Sales Order -->
      <div
        v-if="convertModalOpen && convertingQuotation"
        class="fixed inset-0 z-50 overflow-y-auto bg-navy-950/60 backdrop-blur-sm flex items-center justify-center p-4"
      >
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden border border-slate-200">
          <!-- Modal Header -->
          <div class="bg-[#16A34A] text-white p-5 flex items-center justify-between">
            <div class="flex items-center gap-2">
              <ShoppingCart class="w-5 h-5" />
              <h3 class="font-black text-base">Convert Proposal to Sales Order</h3>
            </div>
            <button @click="convertModalOpen = false" class="text-white hover:text-emerald-100">
              <X class="w-5 h-5" />
            </button>
          </div>

          <!-- Convert Warning & Form -->
          <form @submit.prevent="handleConfirmConversion" class="p-6 space-y-4 text-xs">
            <!-- Warning Banner -->
            <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-900 space-y-1">
              <div class="font-bold flex items-center gap-1.5">
                <AlertCircle class="w-4 h-4 text-emerald-700 flex-shrink-0" />
                <span>Revenue Recognition & Inventory Deduction Rule</span>
              </div>
              <p class="text-[11px] text-emerald-800">
                Confirming this quotation will generate Sales Order <strong>SO-2026-XXXXX</strong>, allocate physical inventory, and recognize official sales revenue.
              </p>
            </div>

            <!-- Quotation Confirmation Summary -->
            <div class="bg-[#F4F6FA] p-3.5 rounded-xl border border-slate-200 space-y-1.5">
              <div class="flex justify-between">
                <span class="text-slate-500">Quotation Ref:</span>
                <span class="font-bold text-[#042562]">{{ convertingQuotation.quotation_number }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-slate-500">Client:</span>
                <span class="font-semibold text-slate-800">{{ convertingQuotation.customer_name }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-slate-500">Fulfillment Store:</span>
                <span class="font-semibold text-slate-800">{{ convertingQuotation.store_name }}</span>
              </div>
              <div class="flex justify-between text-sm font-black text-[#042562] pt-1.5 border-t border-slate-300">
                <span>Total Order Value:</span>
                <span>{{ formatCurrency(convertingQuotation.total_amount) }}</span>
              </div>
            </div>

            <!-- Shipping Destination -->
            <div>
              <label class="block font-bold text-slate-700 mb-1">Site / Shipping Address *</label>
              <textarea
                v-model="convertForm.shipping_address"
                rows="2"
                required
                class="w-full bg-[#F4F6FA] text-xs rounded-lg p-2.5 border border-slate-300 focus:outline-none"
                placeholder="Enterprise delivery facility address..."
              ></textarea>
            </div>

            <!-- Initial Payment / Advance Option -->
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-bold text-slate-700 mb-1">Advance Paid Amount (SAR)</label>
                <input
                  v-model.number="convertForm.initial_payment_amount"
                  type="number"
                  step="0.01"
                  min="0"
                  class="w-full bg-[#F4F6FA] text-xs rounded-lg px-3 py-2 border border-slate-300"
                  placeholder="0.00"
                />
              </div>

              <div>
                <label class="block font-bold text-slate-700 mb-1">Payment Method</label>
                <select
                  v-model="convertForm.payment_method"
                  class="w-full bg-[#F4F6FA] text-xs rounded-lg px-3 py-2 border border-slate-300"
                >
                  <option value="BANK_TRANSFER">Bank Wire Transfer</option>
                  <option value="CHEQUE">Corporate Cheque</option>
                  <option value="CREDIT_CARD">Credit Card / POS</option>
                  <option value="CASH">Counter Cash</option>
                </select>
              </div>
            </div>

            <div v-if="convertForm.initial_payment_amount > 0">
              <label class="block font-bold text-slate-700 mb-1">Payment Reference / Transaction ID</label>
              <input
                v-model="convertForm.payment_reference"
                type="text"
                class="w-full bg-[#F4F6FA] text-xs rounded-lg px-3 py-2 border border-slate-300"
                placeholder="e.g. WIRE-SNB-202611-9921"
              />
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200">
              <button
                type="button"
                @click="convertModalOpen = false"
                class="px-4 py-2 border border-slate-300 text-slate-600 rounded-lg font-bold hover:bg-slate-100"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="submitting"
                class="px-5 py-2 bg-[#16A34A] hover:bg-emerald-700 text-white rounded-lg font-bold uppercase tracking-wider transition-colors disabled:opacity-50"
              >
                {{ submitting ? 'Converting...' : 'Confirm Sales Order' }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Modal: Change Status -->
      <div
        v-if="statusModalOpen && statusQuotation"
        class="fixed inset-0 z-50 overflow-y-auto bg-navy-950/60 backdrop-blur-sm flex items-center justify-center p-4"
      >
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden border border-slate-200">
          <div class="bg-[#042562] text-white p-4 flex items-center justify-between">
            <h3 class="font-bold text-sm">Update Proposal Status</h3>
            <button @click="statusModalOpen = false" class="text-blue-200 hover:text-white">
              <X class="w-4 h-4" />
            </button>
          </div>

          <form @submit.prevent="handleUpdateStatus" class="p-5 space-y-4 text-xs">
            <div>
              <label class="block font-bold text-slate-700 mb-1">Quotation Lifecycle Status *</label>
              <select
                v-model="targetStatus"
                class="w-full bg-[#F4F6FA] text-xs rounded-lg px-3 py-2 border border-slate-300"
              >
                <option value="DRAFT">Draft</option>
                <option value="PREPARED">Prepared</option>
                <option value="SENT">Sent to Customer</option>
                <option value="APPROVED">Approved by Client</option>
                <option value="REJECTED">Rejected by Client</option>
                <option value="EXPIRED">Expired</option>
              </select>
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Audit Notes / Comments</label>
              <textarea
                v-model="statusNotes"
                rows="3"
                class="w-full bg-[#F4F6FA] text-xs rounded-lg p-2.5 border border-slate-300"
                placeholder="Reason for status change..."
              ></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-200">
              <button
                type="button"
                @click="statusModalOpen = false"
                class="px-3 py-1.5 border border-slate-300 text-slate-600 rounded-lg font-bold"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="submitting"
                class="px-4 py-1.5 bg-[#042562] hover:bg-[#1865C0] text-white rounded-lg font-bold"
              >
                {{ submitting ? 'Updating...' : 'Update Status' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { quotationService } from '@/services/quotationService';
import { salesOrderService } from '@/services/salesOrderService';
import { useNotificationStore } from '@/stores/notificationStore';
import {
  FileText,
  FilePlus,
  Plus,
  Search,
  RefreshCw,
  TrendingUp,
  CheckCircle2,
  Clock,
  BadgeDollarSign,
  Building,
  Eye,
  Download,
  SlidersHorizontal,
  ShoppingCart,
  Trash2,
  X,
  AlertCircle
} from 'lucide-vue-next';

const notify = useNotificationStore();

const loading = ref(false);
const submitting = ref(false);
const quotations = ref([]);
const counts = ref({});
const pipelineVolume = ref(0);
const approvedVolume = ref(0);

const activeTab = ref('all');
const searchQuery = ref('');
const selectedStore = ref('');

const detailDrawerOpen = ref(false);
const selectedQuotation = ref(null);

const createModalOpen = ref(false);
const convertModalOpen = ref(false);
const convertingQuotation = ref(null);

const statusModalOpen = ref(false);
const statusQuotation = ref(null);
const targetStatus = ref('');
const statusNotes = ref('');

const statusTabs = [
  { label: 'All Quotations', value: 'all' },
  { label: 'Draft', value: 'draft' },
  { label: 'Prepared', value: 'prepared' },
  { label: 'Sent', value: 'sent' },
  { label: 'Approved', value: 'approved' },
  { label: 'Converted', value: 'converted' },
  { label: 'Rejected', value: 'rejected' },
  { label: 'Expired', value: 'expired' },
];

const newQuoteForm = ref({
  customer_name: '',
  company_name: '',
  customer_email: '',
  customer_phone: '',
  billing_address: '',
  store_id: 1,
  valid_until: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
  discount_value: 0,
  tax_rate: 15,
  terms_conditions: 'Standard 30-day corporate price lock. Delivery within 7-10 business days.',
  items: [
    { product_id: 1, quantity: 1, unit_price: 5200.00 },
  ],
});

const convertForm = ref({
  shipping_address: '',
  initial_payment_amount: 0,
  payment_method: 'BANK_TRANSFER',
  payment_reference: '',
});

const computedCreateTotal = computed(() => {
  const subtotal = newQuoteForm.value.items.reduce((acc, it) => acc + (it.quantity * it.unit_price), 0);
  const discount = newQuoteForm.value.discount_value || 0;
  const taxable = Math.max(0, subtotal - discount);
  const vat = taxable * 0.15;
  return taxable + vat;
});

function formatCurrency(val) {
  if (val === null || val === undefined) return 'SAR 0.00';
  return `SAR ${Number(val).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
}

function badgeVariantClass(variant) {
  switch (String(variant).toLowerCase()) {
    case 'success':
    case 'approved':
      return 'bg-emerald-100 text-emerald-800 border border-emerald-300';
    case 'info':
    case 'sent':
      return 'bg-blue-100 text-blue-800 border border-blue-300';
    case 'warning':
    case 'prepared':
      return 'bg-amber-100 text-amber-800 border border-amber-300';
    case 'primary':
    case 'converted':
      return 'bg-purple-100 text-purple-800 border border-purple-300';
    case 'danger':
    case 'rejected':
      return 'bg-red-100 text-red-800 border border-red-300';
    default:
      return 'bg-slate-100 text-slate-700 border border-slate-300';
  }
}

async function fetchQuotations() {
  loading.value = true;
  try {
    const params = {
      status: activeTab.value,
      search: searchQuery.value,
      store_id: selectedStore.value || undefined,
    };
    const res = await quotationService.getQuotations(params);
    quotations.value = res.data || [];
    if (res.meta) {
      counts.value = res.meta.counts || {};
      pipelineVolume.value = res.meta.pipeline_volume || 0;
      approvedVolume.value = res.meta.approved_volume || 0;
    }
  } catch (err) {
    notify.error(err.message || 'Failed to load quotations');
  } finally {
    loading.value = false;
  }
}

let debounceTimer = null;
function debounceSearch() {
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(() => {
    fetchQuotations();
  }, 350);
}

function setTab(tab) {
  activeTab.value = tab;
  fetchQuotations();
}

function openDetailDrawer(quote) {
  selectedQuotation.value = quote;
  detailDrawerOpen.value = true;
}

function openCreateModal() {
  createModalOpen.value = true;
}

function addBomItem() {
  newQuoteForm.value.items.push({
    product_id: 1,
    quantity: 1,
    unit_price: 5200.00,
  });
}

function removeBomItem(index) {
  newQuoteForm.value.items.splice(index, 1);
}

function handleProductSelect(item) {
  const prices = {
    1: 5200.00,
    2: 5200.00,
    3: 9250.00,
    4: 6000.00,
  };
  item.unit_price = prices[item.product_id] || 1000.00;
}

async function handleCreateQuotation() {
  submitting.value = true;
  try {
    const payload = {
      ...newQuoteForm.value,
      status: 'PREPARED',
    };
    const res = await quotationService.createQuotation(payload);
    notify.success(res.message || 'Quotation created successfully');
    createModalOpen.value = false;
    await fetchQuotations();
  } catch (err) {
    notify.error(err.message || 'Failed to generate quotation');
  } finally {
    submitting.value = false;
  }
}

function openConvertModal(quote) {
  convertingQuotation.value = quote;
  convertForm.value = {
    shipping_address: quote.billing_address || `Dispatch from ${quote.store_name}`,
    initial_payment_amount: 0,
    payment_method: 'BANK_TRANSFER',
    payment_reference: '',
  };
  convertModalOpen.value = true;
}

async function handleConfirmConversion() {
  if (!convertingQuotation.value) return;
  submitting.value = true;
  try {
    const payload = {
      store_id: convertingQuotation.value.store_id,
      customer_name: convertingQuotation.value.customer_name,
      customer_email: convertingQuotation.value.customer_email,
      company_name: convertingQuotation.value.company_name,
      billing_address: convertingQuotation.value.billing_address,
      shipping_address: convertForm.value.shipping_address,
      total_amount: convertingQuotation.value.total_amount,
      subtotal: convertingQuotation.value.subtotal,
      discount_amount: convertingQuotation.value.discount_amount,
      tax_amount: convertingQuotation.value.tax_amount,
      initial_payment_amount: convertForm.value.initial_payment_amount,
      payment_method: convertForm.value.payment_method,
      payment_reference: convertForm.value.payment_reference,
      items: convertingQuotation.value.items || [],
    };

    const res = await salesOrderService.convertQuotation(convertingQuotation.value.id, payload);
    notify.success(res.message || 'Quotation converted into confirmed Sales Order!');
    convertModalOpen.value = false;
    detailDrawerOpen.value = false;
    await fetchQuotations();
  } catch (err) {
    notify.error(err.message || 'Failed to convert quotation');
  } finally {
    submitting.value = false;
  }
}

function openStatusModal(quote) {
  statusQuotation.value = quote;
  targetStatus.value = quote.status;
  statusNotes.value = '';
  statusModalOpen.value = true;
}

async function handleUpdateStatus() {
  if (!statusQuotation.value) return;
  submitting.value = true;
  try {
    await quotationService.updateStatus(statusQuotation.value.id, {
      status: targetStatus.value,
      notes: statusNotes.value,
    });
    notify.success(`Quotation updated to ${targetStatus.value}`);
    statusModalOpen.value = false;
    if (selectedQuotation.value?.id === statusQuotation.value.id) {
      selectedQuotation.value.status = targetStatus.value;
    }
    await fetchQuotations();
  } catch (err) {
    notify.error(err.message || 'Failed to transition status');
  } finally {
    submitting.value = false;
  }
}

async function downloadPdf(quote) {
  try {
    notify.info(`Generating branded PDF for #${quote.quotation_number}...`);
    await quotationService.downloadPdf(quote.id, quote.quotation_number);
    notify.success(`Downloaded Quotation PDF #${quote.quotation_number}`);
  } catch (err) {
    notify.error(err.message || 'Failed to stream PDF');
  }
}

onMounted(() => {
  fetchQuotations();
});
</script>
