<template>
  <PublicLayout>
    <section class="bg-[#042562] py-16 text-white">
      <div class="mx-auto max-w-7xl px-4">
        <h1 class="text-3xl font-black">Register your company</h1>
        <p class="mt-3 max-w-2xl text-sm text-blue-100">
          Tell the sales desk who you are and what you procure. This does not create a staff login.
        </p>
      </div>
    </section>
    <section class="mx-auto grid max-w-7xl gap-8 px-4 py-14 lg:grid-cols-2">
      <form class="space-y-3 rounded-xl border border-slate-200 bg-white p-5" @submit.prevent="submit">
        <p v-if="reference" class="text-sm text-emerald-700">Received. Reference {{ reference }}.</p>
        <template v-else>
          <input v-model="form.name" required placeholder="Contact name" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" />
          <input v-model="form.company" required placeholder="Company" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" />
          <input v-model="form.email" required type="email" placeholder="Email" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" />
          <input v-model="form.phone" required placeholder="Phone" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" />
          <textarea v-model="form.message" required minlength="10" rows="4" placeholder="What do you need to procure?" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></textarea>
          <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
          <button type="submit" :disabled="sending" class="rounded-lg bg-[#042562] px-5 py-3 text-xs font-bold uppercase tracking-wider text-white disabled:opacity-50">
            {{ sending ? 'Sending...' : 'Register company' }}
          </button>
        </template>
      </form>
      <div class="space-y-6">
        <div class="rounded-xl border border-slate-200 bg-white p-5 text-sm text-slate-600">
          <h2 class="text-lg font-black text-[#042562]">Office</h2>
          <p class="mt-2">3rd Floor, Wastna Commercial Building, General Wingate St, Addis Ababa</p>
          <p class="mt-2 font-bold text-[#042562]">{{ company.phone }}</p>
          <p class="font-bold text-[#1865C0]">{{ company.email }}</p>
        </div>
        <OfficeMap />
      </div>
    </section>
  </PublicLayout>
</template>

<script setup>
import { computed, reactive, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import OfficeMap from '@/Components/OfficeMap.vue';

const company = computed(() => usePage().props.company || {});
const sending = ref(false);
const reference = ref('');
const error = ref('');
const form = reactive({ name: '', company: '', email: '', phone: '', message: '' });

async function submit() {
  sending.value = true;
  error.value = '';
  try {
    const response = await fetch('/api/v1/public/contact', {
      method: 'POST',
      headers: { Accept: 'application/json', 'Content-Type': 'application/json' },
      body: JSON.stringify({ ...form, type: 'registration', subject: 'Customer registration' }),
    });
    const body = await response.json();
    if (!response.ok || !body.success) {
      error.value = body.message || 'Registration could not be saved.';
      return;
    }
    reference.value = body.data?.reference_ticket || '';
  } catch {
    error.value = 'The API could not be reached.';
  } finally {
    sending.value = false;
  }
}
</script>
