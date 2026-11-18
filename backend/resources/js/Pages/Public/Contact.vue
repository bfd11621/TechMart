<template>
  <PublicLayout>
    <section class="mx-auto grid max-w-5xl gap-8 px-4 py-14 lg:grid-cols-2">
      <div>
        <h1 class="text-3xl font-black text-[#042562]">Contact technical sales</h1>
        <p class="mt-3 text-sm text-slate-600">{{ company.email }} · {{ company.phone }}</p>
        <p class="mt-2 text-sm text-slate-600">3rd Floor, Wastna Commercial Building, General Wingate St, Addis Ababa</p>
      </div>
      <form class="space-y-3 rounded-xl border border-slate-200 bg-white p-5" @submit.prevent="submit">
        <input v-model="form.name" required placeholder="Name" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" />
        <input v-model="form.email" required type="email" placeholder="Email" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" />
        <input v-model="form.phone" placeholder="Phone" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" />
        <input v-model="form.company" placeholder="Company" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" />
        <input v-model="form.subject" required placeholder="Subject" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" />
        <textarea v-model="form.message" required minlength="10" rows="5" placeholder="How can we help?" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></textarea>
        <p v-if="message" class="text-sm" :class="ok ? 'text-emerald-700' : 'text-red-600'">{{ message }}</p>
        <button type="submit" :disabled="sending" class="rounded-lg bg-[#042562] px-5 py-3 text-xs font-bold uppercase tracking-wider text-white disabled:opacity-50">
          {{ sending ? 'Sending...' : 'Send message' }}
        </button>
      </form>
    </section>
  </PublicLayout>
</template>

<script setup>
import { computed, reactive, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

const company = computed(() => usePage().props.company || {});
const sending = ref(false);
const message = ref('');
const ok = ref(false);
const form = reactive({
  name: '',
  email: '',
  phone: '',
  company: '',
  subject: '',
  message: '',
});

async function submit() {
  sending.value = true;
  message.value = '';
  try {
    const response = await fetch('/api/v1/public/contact', {
      method: 'POST',
      headers: { Accept: 'application/json', 'Content-Type': 'application/json' },
      body: JSON.stringify(form),
    });
    const body = await response.json();
    ok.value = response.ok;
    message.value = body.message || (response.ok ? 'Message received.' : 'The message could not be saved.');
  } catch {
    ok.value = false;
    message.value = 'The API could not be reached.';
  } finally {
    sending.value = false;
  }
}
</script>
