<template>
  <PublicLayout>
    <!-- Header Banner -->
    <div class="bg-[#042562] text-white py-12 border-b border-navy-800">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-2 text-xs font-semibold text-blue-300 uppercase tracking-wider mb-2">
          <router-link to="/" class="hover:text-white">Home</router-link>
          <span>/</span>
          <span class="text-white">Contact & Fulfillment Centers</span>
        </div>
        <h1 class="text-3xl font-black">Contact Enterprise Technical Sales</h1>
        <p class="text-sm text-blue-200 mt-1 max-w-xl">
          Connect with our certified solution architects, schedule an on-site datacenter assessment, or request formal corporate pricing.
        </p>
      </div>
    </div>

    <!-- Main Content Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-16">
      <!-- 3 Regional Distribution Centers Cards -->
      <div>
        <div class="text-center max-w-xl mx-auto mb-10">
          <span class="text-xs font-bold tracking-widest text-[#1865C0] uppercase">Fulfillment Network</span>
          <h2 class="text-2xl font-black text-[#042562] mt-1">Our Regional Distribution Hubs</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <!-- Hub 1: Central HQ -->
          <div class="bg-white rounded-2xl border-2 border-[#1865C0] p-6 shadow-md relative overflow-hidden space-y-4">
            <span class="absolute top-0 right-0 bg-[#1865C0] text-white text-[10px] font-black uppercase px-3 py-1 rounded-bl-lg">
              Primary HQ & Main Showroom
            </span>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-[#042562] flex items-center justify-center">
              <Building2 class="w-6 h-6" />
            </div>
            <div>
              <h3 class="text-base font-black text-[#042562]">Central Distribution Hub & HQ</h3>
              <p class="text-xs text-slate-500 mt-1">Commercial Infrastructure & Executive Suite</p>
            </div>
            <div class="space-y-2 text-xs text-slate-700 pt-2 border-t border-slate-100">
              <div class="flex items-center gap-2">
                <MapPin class="w-3.5 h-3.5 text-[#E20913] flex-shrink-0" />
                <span>3rd Floor, Wastna Commercial Building, General Wingate St, Addis Ababa</span>
              </div>
              <div class="flex items-center gap-2">
                <Phone class="w-3.5 h-3.5 text-[#1865C0] flex-shrink-0" />
                <span>+251 94 642 8560</span>
              </div>
              <div class="flex items-center gap-2">
                <Mail class="w-3.5 h-3.5 text-[#1865C0] flex-shrink-0" />
                <span>dagimw@iracktech.com</span>
              </div>
              <div class="flex items-center gap-2">
                <Clock class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" />
                <span>Mon-Sat: 8:30 AM – 6:00 PM (EAT / UTC+3)</span>
              </div>
            </div>
          </div>

          <!-- Hub 2: Downtown -->
          <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-[#042562] flex items-center justify-center">
              <Building class="w-6 h-6" />
            </div>
            <div>
              <h3 class="text-base font-black text-[#042562]">Downtown Commercial Center</h3>
              <p class="text-xs text-slate-500 mt-1">Financial District Sales & Demo Suite</p>
            </div>
            <div class="space-y-2 text-xs text-slate-700 pt-2 border-t border-slate-100">
              <div class="flex items-center gap-2">
                <MapPin class="w-3.5 h-3.5 text-[#E20913]" />
                <span>450 Market Street, Suite 1200</span>
              </div>
              <div class="flex items-center gap-2">
                <Phone class="w-3.5 h-3.5 text-[#1865C0]" />
                <span>+1 (555) 019-2832</span>
              </div>
              <div class="flex items-center gap-2">
                <Mail class="w-3.5 h-3.5 text-[#1865C0]" />
                <span>commercial@irack.com</span>
              </div>
              <div class="flex items-center gap-2">
                <Clock class="w-3.5 h-3.5 text-slate-400" />
                <span>Mon-Fri: 9:00 AM – 5:00 PM PST</span>
              </div>
            </div>
          </div>

          <!-- Hub 3: Airport Terminal -->
          <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-[#042562] flex items-center justify-center">
              <Plane class="w-6 h-6" />
            </div>
            <div>
              <h3 class="text-base font-black text-[#042562]">Airport Logistics Terminal</h3>
              <p class="text-xs text-slate-500 mt-1">Air Cargo Rapid Dispatch Hub</p>
            </div>
            <div class="space-y-2 text-xs text-slate-700 pt-2 border-t border-slate-100">
              <div class="flex items-center gap-2">
                <MapPin class="w-3.5 h-3.5 text-[#E20913]" />
                <span>880 Air Cargo Way, Dock 18</span>
              </div>
              <div class="flex items-center gap-2">
                <Phone class="w-3.5 h-3.5 text-[#1865C0]" />
                <span>+1 (555) 019-2833</span>
              </div>
              <div class="flex items-center gap-2">
                <Mail class="w-3.5 h-3.5 text-[#1865C0]" />
                <span>logistics@irack.com</span>
              </div>
              <div class="flex items-center gap-2">
                <Clock class="w-3.5 h-3.5 text-slate-400" />
                <span>24/7 Cargo Receiving & Air Freight</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Form & Map Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
        <!-- Left: Inquiry Form (7 cols) -->
        <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200 p-8 shadow-sm space-y-6">
          <div>
            <span class="text-xs font-bold tracking-widest text-[#E20913] uppercase">Direct Engineering Channel</span>
            <h3 class="text-2xl font-black text-[#042562] mt-1">Submit Technical Consultation Request</h3>
            <p class="text-xs text-slate-500 mt-1">
              Complete the inquiry form below. A senior IT solutions architect will review your project requirements and follow up within 24 business hours.
            </p>
          </div>

          <!-- Success Alert -->
          <div
            v-if="submittedTicket"
            class="p-4 bg-emerald-50 border border-emerald-300 rounded-xl flex items-start gap-3"
          >
            <CheckCircle2 class="w-5 h-5 text-[#16A34A] flex-shrink-0 mt-0.5" />
            <div>
              <h4 class="text-xs font-bold text-emerald-900">Inquiry Logged Successfully</h4>
              <p class="text-xs text-emerald-800 mt-0.5">
                Your consultation request has been assigned ticket reference
                <strong class="font-mono">{{ submittedTicket }}</strong>. A confirmation email has been dispatched.
              </p>
            </div>
          </div>

          <!-- The Form -->
          <form v-else @submit.prevent="handleSubmit" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">
                  Full Name <span class="text-[#E20913]">*</span>
                </label>
                <input
                  type="text"
                  v-model="form.name"
                  required
                  placeholder="e.g. Robert Vance"
                  class="w-full bg-[#F4F6FA] text-xs text-[#172033] rounded-lg px-3 py-2.5 border border-slate-300 focus:outline-none focus:border-[#1865C0]"
                />
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">
                  Corporate Email <span class="text-[#E20913]">*</span>
                </label>
                <input
                  type="email"
                  v-model="form.email"
                  required
                  placeholder="name@company.com"
                  class="w-full bg-[#F4F6FA] text-xs text-[#172033] rounded-lg px-3 py-2.5 border border-slate-300 focus:outline-none focus:border-[#1865C0]"
                />
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Company / Organization</label>
                <input
                  type="text"
                  v-model="form.company"
                  placeholder="e.g. Apex Cloud Systems"
                  class="w-full bg-[#F4F6FA] text-xs text-[#172033] rounded-lg px-3 py-2.5 border border-slate-300 focus:outline-none focus:border-[#1865C0]"
                />
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Direct Phone</label>
                <input
                  type="tel"
                  v-model="form.phone"
                  placeholder="+1 (555) 000-0000"
                  class="w-full bg-[#F4F6FA] text-xs text-[#172033] rounded-lg px-3 py-2.5 border border-slate-300 focus:outline-none focus:border-[#1865C0]"
                />
              </div>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">
                Inquiry Topic <span class="text-[#E20913]">*</span>
              </label>
              <select
                v-model="form.subject"
                required
                class="w-full bg-[#F4F6FA] text-xs text-[#172033] rounded-lg px-3 py-2.5 border border-slate-300 focus:outline-none focus:border-[#1865C0]"
              >
                <option value="">Select consultation subject...</option>
                <option value="Enterprise Datacenter BOM Quotation">Enterprise Datacenter BOM Quotation</option>
                <option value="Custom Server Configuration & Build">Custom Server Configuration & Build</option>
                <option value="High-Density Fiber Cabling Infrastructure">High-Density Fiber Cabling Infrastructure</option>
                <option value="UPS Runtime & Power Distribution Engineering">UPS Runtime & Power Distribution Engineering</option>
                <option value="Multi-Store Stock Availability Inquiry">Multi-Store Stock Availability Inquiry</option>
                <option value="Warranty / RMA / Post-Sales Technical Support">Warranty / RMA / Post-Sales Technical Support</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">
                Project Scope / Technical Requirements <span class="text-[#E20913]">*</span>
              </label>
              <textarea
                v-model="form.message"
                required
                rows="5"
                placeholder="Detail your hardware requirements, rack heights, electrical specs, timeline, or site deployment constraints..."
                class="w-full bg-[#F4F6FA] text-xs text-[#172033] rounded-lg p-3 border border-slate-300 focus:outline-none focus:border-[#1865C0]"
              ></textarea>
            </div>

            <button
              type="submit"
              :disabled="submitting"
              class="w-full inline-flex items-center justify-center gap-2 bg-[#042562] hover:bg-[#063485] text-white py-3 px-6 rounded-lg text-xs font-bold uppercase tracking-wider transition-colors shadow disabled:opacity-50"
            >
              <Send class="w-4 h-4" />
              <span>{{ submitting ? 'Transmitting Request...' : 'Submit Inquiry to Engineering' }}</span>
            </button>
          </form>
        </div>

        <!-- Right: Map Embed & Hotlines (5 cols) -->
        <div class="lg:col-span-5 space-y-6">
          <!-- Google Maps Embed Card -->
          <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="p-4 bg-[#F8FAFC] border-b border-slate-200 flex items-center justify-between">
              <div class="flex items-center gap-2">
                <MapPin class="w-4 h-4 text-[#E20913]" />
                <span class="text-xs font-bold text-[#042562] uppercase tracking-wider">
                  Headquarters & Showroom Location Map
                </span>
              </div>
              <span class="text-[10px] text-slate-500 font-medium">Addis Ababa, Ethiopia</span>
            </div>

            <!-- Embedded Google Map -->
            <div class="relative w-full h-72 bg-slate-100 overflow-hidden">
              <iframe
                title="iRACK IT Solution Headquarters Map"
                class="w-full h-full border-0"
                loading="lazy"
                allowfullscreen
                referrerpolicy="no-referrer-when-downgrade"
                src="https://maps.google.com/maps?q=Wastna+Commercial+Building,+General+Wingate+St,+Addis+Ababa&t=&z=15&ie=UTF8&iwloc=&output=embed"
              ></iframe>
            </div>

            <div class="p-4 bg-white text-xs text-slate-600 flex items-center justify-between border-t border-slate-100">
              <span class="truncate pr-2 font-medium">3rd Floor, Wastna Commercial Building, General Wingate St</span>
              <a
                href="https://www.google.com/maps/search/?api=1&query=Wastna+Commercial+Building,+General+Wingate+St,+Addis+Ababa"
                target="_blank"
                rel="noopener noreferrer"
                class="text-[#1865C0] font-bold hover:underline whitespace-nowrap flex-shrink-0"
              >
                Open in Google Maps &rarr;
              </a>
            </div>
          </div>

          <!-- 24/7 SLA Hotline Card -->
          <div class="bg-[#042562] text-white rounded-2xl p-6 shadow-md space-y-4">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-[#E20913] text-white flex items-center justify-center flex-shrink-0">
                <ShieldCheck class="w-5 h-5" />
              </div>
              <div>
                <h4 class="text-sm font-black">24/7 Critical NOC Support</h4>
                <p class="text-xs text-blue-200">For contracted datacenter maintenance clients</p>
              </div>
            </div>
            <div class="pt-2 border-t border-navy-700 flex items-center justify-between text-xs">
              <span class="text-slate-300">NOC Hotline:</span>
              <a href="tel:+15550192839" class="font-mono font-bold text-white hover:text-blue-300">
                +1 (555) 019-2839
              </a>
            </div>
            <div class="flex items-center justify-between text-xs">
              <span class="text-slate-300">RMA Emergency Desk:</span>
              <span class="font-mono font-bold text-white">rma@irack.com</span>
            </div>
          </div>

          <!-- Social Media Channels -->
          <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-3">
            <h4 class="text-xs font-black uppercase tracking-wider text-[#042562]">
              Corporate Channels & Ecosystem
            </h4>
            <div class="grid grid-cols-2 gap-3 text-xs">
              <a href="https://linkedin.com" target="_blank" class="p-2.5 rounded-lg bg-[#F4F6FA] hover:bg-blue-50 text-slate-700 hover:text-[#1865C0] font-semibold flex items-center gap-2 transition-colors">
                <Share2 class="w-3.5 h-3.5 text-[#1865C0]" />
                <span>LinkedIn Enterprise</span>
              </a>
              <a href="https://github.com" target="_blank" class="p-2.5 rounded-lg bg-[#F4F6FA] hover:bg-blue-50 text-slate-700 hover:text-[#1865C0] font-semibold flex items-center gap-2 transition-colors">
                <Code class="w-3.5 h-3.5 text-slate-800" />
                <span>GitHub Repositories</span>
              </a>
              <a href="https://youtube.com" target="_blank" class="p-2.5 rounded-lg bg-[#F4F6FA] hover:bg-blue-50 text-slate-700 hover:text-[#1865C0] font-semibold flex items-center gap-2 transition-colors">
                <Video class="w-3.5 h-3.5 text-[#E20913]" />
                <span>Product Tech Demos</span>
              </a>
              <a href="https://twitter.com" target="_blank" class="p-2.5 rounded-lg bg-[#F4F6FA] hover:bg-blue-50 text-slate-700 hover:text-[#1865C0] font-semibold flex items-center gap-2 transition-colors">
                <Globe class="w-3.5 h-3.5 text-[#1865C0]" />
                <span>Global Announcements</span>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </PublicLayout>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import catalogService from '@/services/catalogService';
import { configService } from '@/services/configService';
import { useNotificationStore } from '@/stores/notificationStore';
import {
  Building2,
  Building,
  Plane,
  MapPin,
  Phone,
  Mail,
  Clock,
  Send,
  CheckCircle2,
  ShieldCheck,
  Share2,
  Code,
  Video,
  Globe,
} from 'lucide-vue-next';

const notify = useNotificationStore();

const submitting = ref(false);
const submittedTicket = ref(null);
const companyInfo = ref({});

const form = reactive({
  name: '',
  email: '',
  company: '',
  phone: '',
  subject: '',
  message: '',
});

const handleSubmit = async () => {
  submitting.value = true;
  try {
    const res = await catalogService.submitContactInquiry(form);
    submittedTicket.value = res.data?.reference_ticket || 'INQ-' + Math.floor(100000 + Math.random() * 900000);
    notify.success('Your inquiry has been submitted to engineering.');
  } catch (err) {
    console.error(err);
    notify.error('Failed to submit inquiry. Please try again.');
  } finally {
    submitting.value = false;
  }
};

onMounted(async () => {
  try {
    const res = await configService.getPublicSettings();
    if (res && res.data && res.data.company) {
      companyInfo.value = res.data.company;
    }
  } catch (err) {
    // Keep fallback
  }
});
</script>
