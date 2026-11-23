<template>
  <form class="space-y-4" @submit.prevent="submit">
    <div v-if="reference" class="rounded-xl border border-emerald-300 bg-emerald-50 p-4 text-xs text-emerald-900">
      Registration received. Your reference is <strong class="font-mono">{{ reference }}</strong>.
    </div>
    <template v-else>
      <div class="grid gap-4 sm:grid-cols-2">
        <label class="block text-xs font-bold text-slate-700">
          Contact name <span class="text-[#E20913]">*</span>
          <input v-model="form.name" required maxlength="120" class="mt-1 w-full rounded-lg border border-slate-300 bg-[#F4F6FA] px-3 py-2.5 text-xs" />
        </label>
        <label class="block text-xs font-bold text-slate-700">
          Company <span class="text-[#E20913]">*</span>
          <input v-model="form.company" required maxlength="150" class="mt-1 w-full rounded-lg border border-slate-300 bg-[#F4F6FA] px-3 py-2.5 text-xs" />
        </label>
        <label class="block text-xs font-bold text-slate-700">
          Email <span class="text-[#E20913]">*</span>
          <input v-model="form.email" required type="email" maxlength="150" class="mt-1 w-full rounded-lg border border-slate-300 bg-[#F4F6FA] px-3 py-2.5 text-xs" />
        </label>
        <label class="block text-xs font-bold text-slate-700">
          Phone <span class="text-[#E20913]">*</span>
          <input v-model="form.phone" required maxlength="30" class="mt-1 w-full rounded-lg border border-slate-300 bg-[#F4F6FA] px-3 py-2.5 text-xs" />
        </label>
      </div>
      <label class="block text-xs font-bold text-slate-700">
        What does your company need to procure? <span class="text-[#E20913]">*</span>
        <textarea v-model="form.message" required minlength="10" maxlength="3000" rows="4" class="mt-1 w-full rounded-lg border border-slate-300 bg-[#F4F6FA] px-3 py-2.5 text-xs" placeholder="Servers, networking, storage, power, or a full site deployment."></textarea>
      </label>
      <button type="submit" :disabled="sending" class="rounded-lg bg-[#042562] px-6 py-3 text-xs font-bold uppercase tracking-wider text-white disabled:opacity-50">
        {{ sending ? 'Sending...' : 'Register company' }}
      </button>
    </template>
  </form>
</template>

<script setup>
import { reactive, ref } from 'vue';
import catalogService from '@/services/catalogService';
import { useNotificationStore } from '@/stores/notificationStore';

const notify = useNotificationStore();
const sending = ref(false);
const reference = ref('');
const form = reactive({
  name: '',
  company: '',
  email: '',
  phone: '',
  message: '',
});

async function submit() {
  sending.value = true;
  try {
    const res = await catalogService.submitContactInquiry({
      type: 'registration',
      name: form.name,
      email: form.email,
      phone: form.phone,
      company: form.company,
      subject: 'Customer registration',
      message: form.message,
    });
    if (!res?.success) {
      notify.error(res?.message || 'Registration could not be saved.');
      return;
    }
    reference.value = res.data?.reference_ticket || '';
    notify.success(res.message || 'Registration received.');
  } catch (err) {
    notify.error(err.response?.data?.message || 'Registration could not be saved.');
  } finally {
    sending.value = false;
  }
}
</script>
