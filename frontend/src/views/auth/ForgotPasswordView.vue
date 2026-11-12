<template>
  <AuthLayout>
    <div class="space-y-5">
      <div class="text-center">
        <h3 class="text-base font-bold text-[#042562]">Password Recovery</h3>
        <p class="text-xs text-slate-500 mt-1">
          Enter your registered enterprise email address to generate a secure password reset token.
        </p>
      </div>

      <div v-if="successToken" class="p-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs space-y-2">
        <div class="flex items-center gap-2 font-bold">
          <CheckCircle2 class="w-4 h-4 text-emerald-600" />
          <span>Reset Token Generated!</span>
        </div>
        <p class="text-slate-600">
          In production this token is dispatched via SMTP. For administrative verification:
        </p>
        <div class="p-2 rounded bg-white font-mono text-[11px] select-all border border-emerald-300 break-all">
          {{ successToken }}
        </div>
        <router-link
          :to="{ path: '/admin/reset-password', query: { email: email, token: successToken } }"
          class="inline-block font-bold text-[#1865C0] hover:underline pt-1"
        >
          Proceed to Set New Password →
        </router-link>
      </div>

      <form v-else @submit.prevent="handleSubmit" class="space-y-4">
        <BaseInput
          v-model="email"
          label="Registered Staff Email"
          type="email"
          placeholder="admin@irack.com"
          required
        >
          <template #prefix><Mail class="w-4 h-4" /></template>
        </BaseInput>

        <BaseButton
          type="submit"
          variant="navy"
          class="w-full py-2.5"
          :loading="loading"
        >
          <template #prefix><KeyRound class="w-4 h-4 mr-2" /></template>
          Generate Reset Token
        </BaseButton>

        <div class="text-center pt-2">
          <router-link to="/admin/login" class="text-xs text-slate-500 hover:text-[#042562]">
            Remember your credentials? <strong class="text-[#1865C0]">Sign In</strong>
          </router-link>
        </div>
      </form>
    </div>
  </AuthLayout>
</template>

<script setup>
import { ref } from 'vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import BaseInput from '@/components/common/BaseInput.vue';
import BaseButton from '@/components/common/BaseButton.vue';
import { authService } from '@/services/authService';
import { useNotificationStore } from '@/stores/notificationStore';
import { Mail, KeyRound, CheckCircle2 } from 'lucide-vue-next';

const notify = useNotificationStore();

const email = ref('admin@irack.com');
const loading = ref(false);
const successToken = ref('');

const handleSubmit = async () => {
  loading.value = true;
  try {
    const res = await authService.forgotPassword(email.value);
    if (res.success && res.data?.reset_token) {
      successToken.value = res.data.reset_token;
      notify.success(res.message);
    }
  } catch (err) {
    if (!err.response) {
      // Demo fallback
      successToken.value = 'demo_token_' + Math.random().toString(36).substring(2, 15);
      notify.success('Demonstration reset token generated.');
    } else {
      notify.error(err.response?.data?.message || 'Could not issue password reset token.');
    }
  } finally {
    loading.value = false;
  }
};
</script>
