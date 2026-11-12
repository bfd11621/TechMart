<template>
  <PublicLayout>
    <!-- Header Banner -->
    <div class="bg-[#042562] text-white py-12 border-b border-navy-800">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-2 text-xs font-semibold text-blue-300 uppercase tracking-wider mb-2">
          <router-link to="/" class="hover:text-white">Home</router-link>
          <span>/</span>
          <span class="text-white">Commercial Agreements</span>
        </div>
        <h1 class="text-3xl font-black">{{ contentData.title || 'Terms of Procurement & Quotation Conditions' }}</h1>
        <p class="text-xs text-blue-200 mt-1">
          {{ contentData.subtitle || 'Effective Date: January 1, 2026 • Enterprise IT Infrastructure Supply Agreement' }}
        </p>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
      <div class="bg-white rounded-2xl border border-slate-200 p-8 sm:p-12 shadow-sm space-y-8 text-xs sm:text-sm text-slate-700 leading-relaxed">
        <!-- Dynamic Content from Admin CMS -->
        <div v-if="contentData.content" class="whitespace-pre-line text-sm text-slate-700 leading-relaxed space-y-4">
          {{ contentData.content }}
        </div>

        <div v-else class="space-y-8">
          <!-- Critical Rule 1 & 2 Alert -->
          <div class="p-4 bg-blue-50 border-l-4 border-[#1865C0] rounded-r-xl space-y-1">
            <h3 class="text-xs font-black uppercase tracking-wider text-[#042562]">
              Mandatory Business Policy: Request for Quotation (RFQ) Status
            </h3>
            <p class="text-xs text-slate-700">
              A Request for Quotation (RFQ) submitted through our public showroom is an inquiry for commercial pricing and does not constitute a confirmed or binding sale. RFQ submissions never incur charges or represent financial commitments until formally approved and converted into an authorized Sales Order.
            </p>
          </div>

          <div>
            <h2 class="text-base font-black text-[#042562] uppercase tracking-wider mb-2">
              1. Quotation Preparation & Price Locks
            </h2>
            <p>
              Upon receipt of an RFQ basket, our commercial sales desk issues a formal, numbered Quotation Document. Standard quotations carry a guaranteed price-protection validity of <strong>30 calendar days</strong>, during which quoted hardware prices and volume tier rebates remain fixed against currency and component market fluctuations.
            </p>
          </div>

          <div>
            <h2 class="text-base font-black text-[#042562] uppercase tracking-wider mb-2">
              2. Sales Order Confirmation & Revenue Recognition
            </h2>
            <p>
              In accordance with enterprise accounting governance, commercial revenue is recognized strictly when an authorized iRACK administrator registers or confirms a formal Sales Order following client purchase order (PO) receipt or approved credit agreement.
            </p>
          </div>

          <div>
            <h2 class="text-base font-black text-[#042562] uppercase tracking-wider mb-2">
              3. Multi-Store Inventory Allocation & Dispatch
            </h2>
            <p>
              Confirmed orders are fulfilled through our multi-store fulfillment network (Central Distribution Hub, Downtown Commercial Center, or Airport Logistics Terminal). Quantities are deducted atomically from store inventory at the exact moment of order confirmation to prevent overselling.
            </p>
          </div>

          <div>
            <h2 class="text-base font-black text-[#042562] uppercase tracking-wider mb-2">
              4. Manufacturer OEM Warranties & Support
            </h2>
            <p>
              All equipment supplied by iRACK IT Solution includes authentic manufacturer factory warranties with direct serial verification. Server enclosures manufactured by iRACK feature a standard 5-year chassis warranty against structural defects.
            </p>
          </div>

          <div>
            <h2 class="text-base font-black text-[#042562] uppercase tracking-wider mb-2">
              5. Return Merchandise Authorization (RMA) Policy
            </h2>
            <p>
              Hardware defects covered under manufacturer warranty are eligible for advance replacement RMA through our 24/7 technical desk. Requests must reference the original Invoice or Sales Order number.
            </p>
          </div>
        </div>
      </div>
    </div>
  </PublicLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { configService } from '@/services/configService';

const contentData = ref({});

onMounted(async () => {
  try {
    const res = await configService.getPublicContent('terms-conditions');
    if (res && res.data) {
      contentData.value = res.data;
    }
  } catch (err) {
    // Keep fallback
  }
});
</script>
