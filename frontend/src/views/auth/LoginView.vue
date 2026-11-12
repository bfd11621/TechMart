<template>
  <AuthLayout>
    <form @submit.prevent="handleLogin" class="space-y-5">
      <div v-if="errorMessage" class="p-3 rounded-lg bg-red-50 border border-red-200 text-[#DC2626] text-xs flex items-start gap-2">
        <AlertCircle class="w-4 h-4 flex-shrink-0 mt-0.5" />
        <span>{{ errorMessage }}</span>
      </div>

      <BaseInput
        v-model="email"
        label="Staff Email Address"
        type="email"
        placeholder="e.g. admin@irack.com"
        required
      >
        <template #prefix>
          <Mail class="w-4 h-4" />
        </template>
      </BaseInput>

      <BaseInput
        v-model="password"
        label="Secure Password"
        :type="showPassword ? 'text' : 'password'"
        placeholder="••••••••••••"
        required
      >
        <template #prefix>
          <Lock class="w-4 h-4" />
        </template>
        <template #suffix>
          <button
            type="button"
            @click="showPassword = !showPassword"
            class="p-1 rounded text-slate-400 hover:text-[#042562] focus:outline-none transition-colors cursor-pointer"
            :title="showPassword ? 'Hide password' : 'Show password'"
            tabindex="-1"
          >
            <EyeOff v-if="showPassword" class="w-4 h-4 text-slate-700" />
            <Eye v-else class="w-4 h-4" />
          </button>
        </template>
      </BaseInput>

      <div class="flex items-center justify-between text-xs">
        <label class="flex items-center text-slate-600 cursor-pointer">
          <input type="checkbox" v-model="rememberMe" class="rounded border-slate-300 text-[#042562] focus:ring-[#042562]" />
          <span class="ml-2">Remember credentials</span>
        </label>
        <router-link to="/admin/forgot-password" class="text-[#1865C0] hover:underline font-semibold">
          Forgot password?
        </router-link>
      </div>

      <BaseButton
        type="submit"
        variant="navy"
        class="w-full py-2.5"
        :loading="loading"
      >
        <template #prefix><LogIn class="w-4 h-4 mr-2" /></template>
        Sign In to Portal
      </BaseButton>

      <!-- Pre-seeded Demonstration Credentials -->
      <div class="mt-6 pt-5 border-t border-slate-200">
        <div class="flex items-center justify-between mb-2.5">
          <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">
            Quick Demo Credentials (Pre-Seeded)
          </p>
          <span class="text-[10px] text-[#1865C0] font-semibold">Click to Autofill</span>
        </div>
        <div class="grid grid-cols-2 gap-2 text-xs">
          <button
            type="button"
            @click="fillCredentials('dagimw@iracktech.com', 'Admin@iRACK2026!')"
            :class="[
              'p-2.5 rounded-lg border text-left transition-all',
              email === 'dagimw@iracktech.com' || email === 'admin@irack.com'
                ? 'border-[#1865C0] bg-blue-50/60 shadow-xs'
                : 'border-slate-200 hover:border-[#042562] hover:bg-slate-50'
            ]"
          >
            <div class="flex items-center justify-between">
              <span class="font-bold text-[#042562]">Super Admin</span>
              <span class="text-[9px] font-mono font-bold bg-[#042562] text-white px-1.5 py-0.2 rounded">Global</span>
            </div>
            <div class="text-[10px] text-slate-500 truncate mt-0.5">dagimw@iracktech.com</div>
            <div class="text-[10px] font-mono text-[#1865C0] mt-1 font-semibold">Admin@iRACK2026!</div>
          </button>

          <button
            type="button"
            @click="fillCredentials('manager.hq@irack.com', 'Manager@iRACK2026!')"
            :class="[
              'p-2.5 rounded-lg border text-left transition-all',
              email === 'manager.hq@irack.com'
                ? 'border-[#1865C0] bg-blue-50/60 shadow-xs'
                : 'border-slate-200 hover:border-[#042562] hover:bg-slate-50'
            ]"
          >
            <div class="flex items-center justify-between">
              <span class="font-bold text-[#042562]">Store Manager</span>
              <span class="text-[9px] font-mono font-bold bg-[#1865C0] text-white px-1.5 py-0.2 rounded">HQ Hub</span>
            </div>
            <div class="text-[10px] text-slate-500 truncate mt-0.5">manager.hq@irack.com</div>
            <div class="text-[10px] font-mono text-[#1865C0] mt-1 font-semibold">Manager@iRACK2026!</div>
          </button>

          <button
            type="button"
            @click="fillCredentials('sales@irack.com', 'Sales@iRACK2026!')"
            :class="[
              'p-2.5 rounded-lg border text-left transition-all',
              email === 'sales@irack.com'
                ? 'border-[#1865C0] bg-blue-50/60 shadow-xs'
                : 'border-slate-200 hover:border-[#042562] hover:bg-slate-50'
            ]"
          >
            <div class="flex items-center justify-between">
              <span class="font-bold text-[#042562]">Sales Manager</span>
              <span class="text-[9px] font-mono font-bold bg-emerald-700 text-white px-1.5 py-0.2 rounded">Sales</span>
            </div>
            <div class="text-[10px] text-slate-500 truncate mt-0.5">sales@irack.com</div>
            <div class="text-[10px] font-mono text-[#1865C0] mt-1 font-semibold">Sales@iRACK2026!</div>
          </button>

          <button
            type="button"
            @click="fillCredentials('inventory@irack.com', 'Inventory@iRACK2026!')"
            :class="[
              'p-2.5 rounded-lg border text-left transition-all',
              email === 'inventory@irack.com'
                ? 'border-[#1865C0] bg-blue-50/60 shadow-xs'
                : 'border-slate-200 hover:border-[#042562] hover:bg-slate-50'
            ]"
          >
            <div class="flex items-center justify-between">
              <span class="font-bold text-[#042562]">Inventory Officer</span>
              <span class="text-[9px] font-mono font-bold bg-amber-700 text-white px-1.5 py-0.2 rounded">Stock</span>
            </div>
            <div class="text-[10px] text-slate-500 truncate mt-0.5">inventory@irack.com</div>
            <div class="text-[10px] font-mono text-[#1865C0] mt-1 font-semibold">Inventory@iRACK2026!</div>
          </button>
        </div>
      </div>
    </form>
  </AuthLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import AuthLayout from '@/layouts/AuthLayout.vue';
import BaseInput from '@/components/common/BaseInput.vue';
import BaseButton from '@/components/common/BaseButton.vue';
import { authService } from '@/services/authService';
import { useAuthStore } from '@/stores/authStore';
import { useNotificationStore } from '@/stores/notificationStore';
import { Mail, Lock, LogIn, AlertCircle, Eye, EyeOff } from 'lucide-vue-next';

const router = useRouter();
const route = useRoute();
const auth = useAuthStore();
const notify = useNotificationStore();

const email = ref('dagimw@iracktech.com');
const password = ref('Admin@iRACK2026!');
const showPassword = ref(false);
const rememberMe = ref(true);
const loading = ref(false);
const errorMessage = ref('');

// Pre-seeded Demo Directory
const demoAccounts = {
  'dagimw@iracktech.com': {
    password: 'Admin@iRACK2026!',
    user: {
      id: 1,
      name: 'Dagim Wondim',
      email: 'dagimw@iracktech.com',
      phone: '+251946428560',
      role: { slug: 'super_admin', name: 'Super Administrator' },
      store: null,
    },
    permissions: ['*'],
  },
  'admin@irack.com': {
    password: 'Admin@iRACK2026!',
    user: {
      id: 1,
      name: 'Dagim Wondim',
      email: 'dagimw@iracktech.com',
      phone: '+251946428560',
      role: { slug: 'super_admin', name: 'Super Administrator' },
      store: null,
    },
    permissions: ['*'],
  },
  'manager.hq@irack.com': {
    password: 'Manager@iRACK2026!',
    user: {
      id: 2,
      name: 'Marcus Sterling',
      email: 'manager.hq@irack.com',
      role: { slug: 'store_manager', name: 'Store Manager' },
      store: { id: 1, code: 'ST-HQ-01', name: 'iRACK Central Distribution Hub (HQ)' },
    },
    permissions: [
      'dashboard.view', 'inventory.view', 'inventory.receive', 'inventory.issue',
      'inventory.adjust', 'stock_transfers.view', 'stock_transfers.create',
      'stock_transfers.approve', 'quotations.view', 'sales.view', 'reports.view',
    ],
  },
  'sales@irack.com': {
    password: 'Sales@iRACK2026!',
    user: {
      id: 3,
      name: 'Elena Rostova',
      email: 'sales@irack.com',
      role: { slug: 'sales_manager', name: 'Sales Manager' },
      store: { id: 2, code: 'ST-DOWNTOWN-02', name: 'iRACK Commercial Sales Center' },
    },
    permissions: [
      'dashboard.view', 'products.view', 'quotations.view', 'quotations.create',
      'quotations.update', 'quotations.approve', 'quotations.pdf', 'sales.view',
      'sales.create', 'sales.confirm', 'sales.payments', 'sales.invoice',
      'analytics.view', 'reports.view', 'reports.export',
    ],
  },
  'inventory@irack.com': {
    password: 'Inventory@iRACK2026!',
    user: {
      id: 4,
      name: 'David Chen',
      email: 'inventory@irack.com',
      role: { slug: 'inventory_officer', name: 'Inventory Officer' },
      store: { id: 1, code: 'ST-HQ-01', name: 'iRACK Central Distribution Hub (HQ)' },
    },
    permissions: [
      'dashboard.view', 'products.view', 'inventory.view', 'inventory.receive',
      'inventory.issue', 'inventory.adjust', 'stock_transfers.view',
    ],
  },
};

const fillCredentials = (em, pw) => {
  email.value = em;
  password.value = pw;
  errorMessage.value = '';
  notify.info(`Loaded pre-seeded credentials for ${em}`);
};

const handleLogin = async () => {
  loading.value = true;
  errorMessage.value = '';

  const cleanEmail = email.value.trim().toLowerCase();
  const cleanPassword = password.value;
  const demo = demoAccounts[cleanEmail];

  try {
    const res = await authService.login({
      email: cleanEmail,
      password: cleanPassword,
    });

    if (res.success && res.data) {
      auth.setAuth(res.data.token, res.data.user, res.data.permissions || []);
      notify.success(`Welcome to iRACK Management Portal, ${res.data.user.name}!`);

      const redirect = route.query.redirect || '/admin/dashboard';
      router.push(redirect);
      return;
    }
  } catch (err) {
    console.warn('API authentication error:', err);

    // If backend reports 500 (database not yet created/migrated) or is offline
    const isServerErrorOrOffline = !err.response || err.response.status >= 500;

    if (isServerErrorOrOffline && demo && demo.password === cleanPassword) {
      // Seamlessly activate verified demonstration session
      auth.setAuth('irack_demo_token', demo.user, demo.permissions);
      notify.success(`Welcome to iRACK Management Portal, ${demo.user.name}!`);

      if (err.response?.status >= 500) {
        notify.info('Connected in demonstration mode. (Note: Run migrate.bat to initialize live MySQL database).');
      } else {
        notify.info('Running in demonstration preview mode.');
      }

      const redirect = route.query.redirect || '/admin/dashboard';
      router.push(redirect);
      return;
    }

    // Specific error messaging
    if (demo && demo.password !== cleanPassword) {
      errorMessage.value = `Incorrect password entered for ${demo.user.name}. The correct credential is: ${demo.password}`;
    } else if (err.response?.status === 401 || err.response?.status === 422) {
      errorMessage.value = err.response.data?.message || 'Authentication failed. Please verify your credentials or click a pre-seeded role card below.';
    } else if (err.response?.status >= 500) {
      errorMessage.value = 'Database uninitialized on backend server. Click "Super Admin" below to sign in directly with demo credentials.';
    } else {
      errorMessage.value = 'Local API server unreachable. Please click "Super Admin" below to sign in.';
    }
  } finally {
    loading.value = false;
  }
};
</script>
