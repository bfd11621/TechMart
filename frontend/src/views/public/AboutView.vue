<template>
  <PublicLayout>
    <!-- Header Banner -->
    <div class="bg-[#042562] text-white py-16 border-b border-navy-800">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-2 text-xs font-semibold text-blue-300 uppercase tracking-wider mb-3">
          <router-link to="/" class="hover:text-white">Home</router-link>
          <span>/</span>
          <span class="text-white">Corporate Profile</span>
        </div>
        <h1 class="text-3xl sm:text-4xl font-black">{{ contentData.title || 'About iRACK IT Solution' }}</h1>
        <p class="text-sm sm:text-base text-blue-200 mt-2 max-w-2xl leading-relaxed">
          {{ contentData.subtitle || 'Architecting, manufacturing, and deploying mission-critical IT infrastructure for hyperscale datacenters, cloud service providers, and global enterprises.' }}
        </p>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-20">
      <!-- Section 1: Corporate Mission & Architecture -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
        <div class="space-y-6">
          <span class="text-xs font-bold tracking-widest text-[#E20913] uppercase">Engineering Excellence</span>
          <h2 class="text-2xl sm:text-3xl font-black text-[#042562] leading-tight">
            Pioneering High-Density Server Racks & Enterprise Hardware Distribution
          </h2>
          <div v-if="contentData.content" class="text-sm text-slate-600 leading-relaxed space-y-4 whitespace-pre-line">
            {{ contentData.content }}
          </div>
          <div v-else class="space-y-4 text-sm text-slate-600 leading-relaxed">
            <p>
              Founded with a commitment to engineering precision, iRACK IT Solution bridges the gap between tier-1 hardware manufacturers and rapid enterprise deployment. We operate three specialized logistics hubs providing verified multi-store stock, atomic inventory tracking, and full architectural bill-of-materials (BOM) design.
            </p>
            <p>
              Whether fitting out a greenfield Tier IV data center or upgrading enterprise core switching, our certified systems architects guarantee genuine OEM hardware, complete technical datasheets, and predictable SLAs.
            </p>
          </div>
          <div class="pt-2 flex items-center gap-4">
            <router-link
              to="/catalog"
              class="inline-flex items-center gap-2 bg-[#042562] hover:bg-[#063485] text-white text-xs font-bold uppercase tracking-wider px-6 py-3 rounded-lg shadow transition-colors"
            >
              <span>Explore Hardware Showroom</span>
              <ArrowRight class="w-4 h-4 text-blue-300" />
            </router-link>
            <router-link
              to="/contact"
              class="inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold uppercase tracking-wider px-6 py-3 rounded-lg transition-colors"
            >
              <span>Contact Engineering</span>
            </router-link>
          </div>
        </div>

        <!-- Metrics Dashboard Card -->
        <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-md grid grid-cols-2 gap-6">
          <div class="p-4 rounded-xl bg-[#F4F6FA] border border-slate-200">
            <div class="text-3xl font-black text-[#042562]">500+</div>
            <div class="text-xs font-bold text-slate-800 mt-1">Enterprise Datacenters</div>
            <div class="text-[11px] text-slate-500">Equipped with iRACK enclosures</div>
          </div>
          <div class="p-4 rounded-xl bg-[#F4F6FA] border border-slate-200">
            <div class="text-3xl font-black text-[#1865C0]">15,000+</div>
            <div class="text-xs font-bold text-slate-800 mt-1">Server Cabinets Deployed</div>
            <div class="text-[11px] text-slate-500">42U & 48U high-density configurations</div>
          </div>
          <div class="p-4 rounded-xl bg-[#F4F6FA] border border-slate-200">
            <div class="text-3xl font-black text-[#E20913]">3 Hubs</div>
            <div class="text-xs font-bold text-slate-800 mt-1">Fulfillment Centers</div>
            <div class="text-[11px] text-slate-500">Central HQ, Downtown, Airport Terminal</div>
          </div>
          <div class="p-4 rounded-xl bg-[#F4F6FA] border border-slate-200">
            <div class="text-3xl font-black text-[#16A34A]">99.999%</div>
            <div class="text-xs font-bold text-slate-800 mt-1">Hardware Reliability</div>
            <div class="text-[11px] text-slate-500">Strict factory pre-testing & burn-in</div>
          </div>
        </div>
      </div>

      <!-- Section 2: Enterprise Certifications & Vendor Ecosystem -->
      <div class="bg-[#F8FAFC] rounded-2xl border border-slate-200 p-8 md:p-12 space-y-8">
        <div class="text-center max-w-xl mx-auto">
          <span class="text-xs font-bold tracking-widest text-[#1865C0] uppercase">Accreditations & Compliance</span>
          <h3 class="text-2xl font-black text-[#042562] mt-1">Certified Enterprise Standards</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
          <div class="bg-white p-6 rounded-xl border border-slate-200 text-center space-y-2">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-[#042562] flex items-center justify-center mx-auto">
              <ShieldCheck class="w-6 h-6" />
            </div>
            <h4 class="text-xs font-black text-[#042562]">ISO 9001:2015</h4>
            <p class="text-[11px] text-slate-500">Certified Quality Management System across supply chain and testing.</p>
          </div>

          <div class="bg-white p-6 rounded-xl border border-slate-200 text-center space-y-2">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-[#042562] flex items-center justify-center mx-auto">
              <Award class="w-6 h-6" />
            </div>
            <h4 class="text-xs font-black text-[#042562]">Tier III/IV Design</h4>
            <p class="text-[11px] text-slate-500">Compliant with EIA-310-E and TIA-942 datacenter infrastructure standards.</p>
          </div>

          <div class="bg-white p-6 rounded-xl border border-slate-200 text-center space-y-2">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-[#042562] flex items-center justify-center mx-auto">
              <CheckCircle2 class="w-6 h-6" />
            </div>
            <h4 class="text-xs font-black text-[#042562]">Cisco Premier Partner</h4>
            <p class="text-[11px] text-slate-500">Authorized enterprise routing, switching, and collaboration architects.</p>
          </div>

          <div class="bg-white p-6 rounded-xl border border-slate-200 text-center space-y-2">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-[#042562] flex items-center justify-center mx-auto">
              <Building2 class="w-6 h-6" />
            </div>
            <h4 class="text-xs font-black text-[#042562]">Dell Titanium Partner</h4>
            <p class="text-[11px] text-slate-500">Direct volume allocations on PowerEdge compute and PowerStore arrays.</p>
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
import {
  ArrowRight,
  ShieldCheck,
  Award,
  CheckCircle2,
  Building2,
} from 'lucide-vue-next';

const contentData = ref({});

onMounted(async () => {
  try {
    const res = await configService.getPublicContent('about-us');
    if (res && res.data) {
      contentData.value = res.data;
    }
  } catch (err) {
    // Keep fallback
  }
});
</script>
