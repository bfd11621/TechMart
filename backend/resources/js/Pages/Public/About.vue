<template>
  <PublicLayout>
    <section id="about" class="bg-[#042562] py-16 text-white">
      <div class="mx-auto max-w-7xl px-4">
        <h1 class="text-3xl font-black sm:text-4xl">About {{ company.name }}</h1>
        <p class="mt-3 max-w-2xl text-sm leading-relaxed text-blue-100">
          {{ company.name }} supplies and integrates datacenter hardware from Addis Ababa: rack servers, managed switching, enterprise storage, power protection, and server cabinets. Prices and stock on the showroom come from the live catalog.
        </p>
        <div class="mt-6 flex flex-wrap gap-3 text-xs font-bold uppercase tracking-wider">
          <a href="#registration" class="rounded-lg bg-white px-4 py-2 text-[#042562]">Registration</a>
          <a href="#contact" class="rounded-lg border border-white/30 px-4 py-2">Contact and map</a>
        </div>
      </div>
    </section>

    <section class="mx-auto grid max-w-7xl gap-6 px-4 py-14 sm:grid-cols-3">
      <div class="rounded-xl border border-slate-200 bg-white p-5">
        <div class="text-xs font-bold uppercase tracking-widest text-[#1865C0]">Showroom</div>
        <p class="mt-2 text-sm text-slate-600">Published products only, with specifications and stock from inventory.</p>
      </div>
      <div class="rounded-xl border border-slate-200 bg-white p-5">
        <div class="text-xs font-bold uppercase tracking-widest text-[#1865C0]">Quotations</div>
        <p class="mt-2 text-sm text-slate-600">Build a quotation cart and send it to sales as a request.</p>
      </div>
      <div class="rounded-xl border border-slate-200 bg-white p-5">
        <div class="text-xs font-bold uppercase tracking-widest text-[#1865C0]">Office</div>
        <p class="mt-2 text-sm text-slate-600">3rd Floor, Wastna Commercial Building, General Wingate St, Addis Ababa.</p>
      </div>
    </section>

    <section id="registration" class="border-t border-slate-200 bg-white py-14">
      <div class="mx-auto grid max-w-7xl gap-8 px-4 lg:grid-cols-2">
        <div>
          <div class="text-xs font-bold uppercase tracking-widest text-[#E20913]">Registration</div>
          <h2 class="mt-1 text-2xl font-black text-[#042562]">Register your company</h2>
          <p class="mt-3 text-sm leading-relaxed text-slate-600">
            Procurement teams can register once. Sales uses the details for quotations and delivery. This does not create a staff login.
          </p>
          <Link href="/register" class="mt-4 inline-block text-xs font-bold text-[#1865C0]">Open the registration page</Link>
        </div>
        <form class="space-y-3 rounded-xl border border-slate-200 p-5" @submit.prevent="submitRegistration">
          <p v-if="registrationReference" class="text-sm text-emerald-700">Received. Reference {{ registrationReference }}.</p>
          <template v-else>
            <input v-model="registration.name" required placeholder="Contact name" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" />
            <input v-model="registration.company" required placeholder="Company" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" />
            <input v-model="registration.email" required type="email" placeholder="Email" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" />
            <input v-model="registration.phone" required placeholder="Phone" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" />
            <textarea v-model="registration.message" required minlength="10" rows="4" placeholder="What do you need to procure?" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></textarea>
            <p v-if="registrationError" class="text-sm text-red-600">{{ registrationError }}</p>
            <button type="submit" :disabled="sendingRegistration" class="rounded-lg bg-[#042562] px-5 py-3 text-xs font-bold uppercase tracking-wider text-white disabled:opacity-50">
              {{ sendingRegistration ? 'Sending...' : 'Register company' }}
            </button>
          </template>
        </form>
      </div>
    </section>

    <section id="contact" class="mx-auto grid max-w-7xl gap-8 px-4 py-14 lg:grid-cols-2">
      <div>
        <div class="text-xs font-bold uppercase tracking-widest text-[#1865C0]">Contact</div>
        <h2 class="mt-1 text-2xl font-black text-[#042562]">Office and map</h2>
        <p class="mt-3 text-sm text-slate-600">3rd Floor, Wastna Commercial Building, General Wingate St, Addis Ababa</p>
        <p class="mt-2 text-sm font-bold text-[#042562]">{{ company.phone }}</p>
        <p class="text-sm font-bold text-[#1865C0]">{{ company.email }}</p>
        <p class="mt-2 text-xs text-slate-500">Monday to Saturday, 8:30 AM – 6:00 PM EAT.</p>
        <Link href="/contact" class="mt-4 inline-block text-xs font-bold uppercase tracking-wider text-[#042562]">Send a message</Link>
      </div>
      <OfficeMap />
    </section>
  </PublicLayout>
</template>

<script setup>
import { computed, reactive, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import OfficeMap from '@/Components/OfficeMap.vue';

const company = computed(() => usePage().props.company || { name: 'iRACK IT Solution', phone: '+251 94 642 8560', email: 'dagimw@iracktech.com' });
const sendingRegistration = ref(false);
const registrationReference = ref('');
const registrationError = ref('');
const registration = reactive({ name: '', company: '', email: '', phone: '', message: '' });

async function submitRegistration() {
  sendingRegistration.value = true;
  registrationError.value = '';
  try {
    const response = await fetch('/api/v1/public/contact', {
      method: 'POST',
      headers: { Accept: 'application/json', 'Content-Type': 'application/json' },
      body: JSON.stringify({ ...registration, type: 'registration', subject: 'Customer registration' }),
    });
    const body = await response.json();
    if (!response.ok || !body.success) {
      registrationError.value = body.message || 'Registration could not be saved.';
      return;
    }
    registrationReference.value = body.data?.reference_ticket || '';
  } catch {
    registrationError.value = 'The API could not be reached.';
  } finally {
    sendingRegistration.value = false;
  }
}
</script>
