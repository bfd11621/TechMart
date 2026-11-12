<template>
  <PublicLayout>
    <!-- Header Banner -->
    <div class="bg-[#042562] text-white py-12 border-b border-navy-800">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-2 text-xs font-semibold text-blue-300 uppercase tracking-wider mb-2">
          <router-link to="/" class="hover:text-white">Home</router-link>
          <span>/</span>
          <span class="text-white">Legal & Compliance</span>
        </div>
        <h1 class="text-3xl font-black">{{ contentData.title || 'Corporate Privacy Policy' }}</h1>
        <p class="text-xs text-blue-200 mt-1">
          {{ contentData.subtitle || 'Effective Date: January 1, 2026 • iRACK IT Solution B2B Data Protection Standard' }}
        </p>
      </div>
    </div>

    <!-- Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
      <div class="bg-white rounded-2xl border border-slate-200 p-8 sm:p-12 shadow-sm space-y-8 text-xs sm:text-sm text-slate-700 leading-relaxed">
        <!-- Dynamic Content from Admin CMS -->
        <div v-if="contentData.content" class="whitespace-pre-line text-sm text-slate-700 leading-relaxed space-y-4">
          {{ contentData.content }}
        </div>

        <div v-else class="space-y-8">
          <div>
            <h2 class="text-base font-black text-[#042562] uppercase tracking-wider mb-2">
              1. Commitment to Enterprise Data Privacy
            </h2>
            <p>
              iRACK IT Solution ("iRACK", "we", "our") respects the confidential nature of enterprise procurement. This Privacy Policy outlines how we collect, process, and safeguard commercial information provided via our online product showroom, request for quotation (RFQ) tools, and administrative customer portals.
            </p>
          </div>

          <div>
            <h2 class="text-base font-black text-[#042562] uppercase tracking-wider mb-2">
              2. Scope of Commercial Data Collected
            </h2>
            <p>We collect information strictly necessary to prepare formal B2B engineering quotations and facilitate logistics delivery:</p>
            <ul class="list-disc pl-5 mt-2 space-y-1">
              <li><strong>Company Contact Coordinates:</strong> Corporate name, business email addresses, direct phone numbers, and physical fulfillment site addresses.</li>
              <li><strong>Technical Infrastructure Specifications:</strong> Equipment models, form factors, rack heights, electrical requirements, and architecture notes submitted in RFQs.</li>
              <li><strong>Audit & Security Telemetry:</strong> IP addresses, browser agents, access timestamps, and session tokens utilized to secure administrative portals.</li>
            </ul>
          </div>

          <div>
            <h2 class="text-base font-black text-[#042562] uppercase tracking-wider mb-2">
              3. Business Purpose & Data Utilization
            </h2>
            <p>Commercial data collected through our public website is utilized exclusively for:</p>
            <ul class="list-disc pl-5 mt-2 space-y-1">
              <li>Generating official corporate Quotation Documents with price-protection locks.</li>
              <li>Verifying multi-store stock allocations across our 3 regional fulfillment centers.</li>
              <li>Scheduling on-site engineering assessments, delivery windows, and certified installations.</li>
              <li>Managing post-sales hardware warranties, firmware advisories, and RMA processes.</li>
            </ul>
          </div>

          <div>
            <h2 class="text-base font-black text-[#042562] uppercase tracking-wider mb-2">
              4. Absolute Non-Disclosure & Third-Party Protections
            </h2>
            <p>
              iRACK IT Solution never sells, rents, or monetizes client procurement information. Technical data is shared only with authorized Tier-1 OEMs (e.g., Cisco, Dell Technologies, HPE, APC) when registering serial warranties or securing enterprise project deal-registration pricing on behalf of the client.
            </p>
          </div>

          <div>
            <h2 class="text-base font-black text-[#042562] uppercase tracking-wider mb-2">
              5. Data Protection Officer & Inquiries
            </h2>
            <p>
              For inquiries regarding compliance or enterprise data retention records, please contact our legal team at <strong class="text-[#042562]">legal@irack.com</strong> or write to:
            </p>
            <div class="mt-2 p-3 bg-[#F4F6FA] rounded-lg border border-slate-200 font-mono text-xs">
              iRACK IT Solution — Compliance Office<br />
              1000 Technology Parkway, Silicon Valley Gateway<br />
              San Jose, CA 95110, United States
            </div>
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
    const res = await configService.getPublicContent('privacy-policy');
    if (res && res.data) {
      contentData.value = res.data;
    }
  } catch (err) {
    // Keep fallback
  }
});
</script>
