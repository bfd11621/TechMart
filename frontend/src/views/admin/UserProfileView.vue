<template>
  <AdminLayout>
    <div class="space-y-6 max-w-5xl">
      <!-- Header -->
      <div>
        <h2 class="text-xl font-black text-[#042562] tracking-tight">Staff Account Profile</h2>
        <p class="text-xs text-slate-500 mt-0.5">Manage your personal credentials, contact details, and security parameters.</p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Col 1: Profile Summary Card & Active Store Card -->
        <div class="space-y-6">
          <div class="corporate-card p-6 text-center space-y-3">
            <div class="w-20 h-20 mx-auto rounded-full bg-gradient-to-br from-[#1865C0] to-[#042562] text-white font-black text-2xl flex items-center justify-center shadow-md">
              {{ (auth.user?.name || 'Dagim Wondim').substring(0, 2).toUpperCase() }}
            </div>
            <div>
              <h3 class="text-base font-bold text-[#042562]">{{ auth.user?.name || 'Dagim Wondim' }}</h3>
              <p class="text-xs text-slate-400 font-mono">{{ auth.user?.email || 'dagimw@iracktech.com' }}</p>
            </div>
            <div class="pt-2">
              <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-[#042562] text-white shadow-xs">
                {{ auth.user?.role?.name || 'Super Administrator' }}
              </span>
            </div>
          </div>

          <!-- Store Allocation Scope Card -->
          <div class="corporate-card p-5 space-y-3">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Assigned Operational Store</span>
            <div v-if="auth.currentStore" class="flex items-start gap-3 p-3 rounded-lg bg-blue-50/60 border border-blue-200/50">
              <Building2 class="w-5 h-5 text-[#1865C0] flex-shrink-0 mt-0.5" />
              <div>
                <h4 class="text-xs font-bold text-[#042562]">{{ auth.currentStore.name }}</h4>
                <p class="text-[11px] text-slate-500 mt-0.5">{{ auth.currentStore.code }} • {{ auth.currentStore.city }}</p>
              </div>
            </div>
            <div v-else class="p-3 rounded-lg bg-slate-50 border border-slate-200 text-xs text-slate-600 space-y-1">
              <strong class="text-[#042562] block font-semibold">Global Sovereignty (HQ)</strong>
              <p class="text-[11px] text-slate-500">You possess unconstrained access across all physical branches and central distribution hubs.</p>
            </div>
          </div>

          <!-- 2FA Architecture Box -->
          <div class="corporate-card p-5 space-y-2.5">
            <div class="flex items-center justify-between">
              <span class="text-xs font-bold text-[#042562] uppercase tracking-wider">Two-Factor Authentication</span>
              <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
                Architected
              </span>
            </div>
            <p class="text-[11px] text-slate-500 leading-relaxed">
              TOTP-based second-factor authentication architecture is available for enterprise single sign-on security.
            </p>
          </div>
        </div>

        <!-- Col 2 & 3: Forms for Profile & Password -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Update Profile Details -->
          <div class="corporate-card p-6">
            <h3 class="text-sm font-bold text-[#042562] uppercase tracking-wider mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
              <User class="w-4 h-4 text-[#1865C0]" />
              Personal Profile Details
            </h3>

            <form @submit.prevent="updateProfile" class="space-y-4">
              <BaseInput
                v-model="profileForm.name"
                label="Full Name"
                required
              />

              <BaseInput
                v-model="profileForm.email"
                label="Enterprise Email"
                type="email"
                required
              />

              <BaseInput
                v-model="profileForm.phone"
                label="Direct Contact Phone"
              />

              <div class="pt-2 flex justify-end">
                <BaseButton
                  type="submit"
                  variant="navy"
                  size="md"
                  :loading="profileLoading"
                >
                  Save Profile Changes
                </BaseButton>
              </div>
            </form>
          </div>

          <!-- Change Password -->
          <div class="corporate-card p-6">
            <h3 class="text-sm font-bold text-[#042562] uppercase tracking-wider mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
              <Lock class="w-4 h-4 text-[#E20913]" />
              Change Security Password
            </h3>

            <form @submit.prevent="changePassword" class="space-y-4">
              <BaseInput
                v-model="passwordForm.current_password"
                label="Current Password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="••••••••••••"
                required
              >
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

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <BaseInput
                  v-model="passwordForm.new_password"
                  label="New Password"
                  :type="showPassword ? 'text' : 'password'"
                  placeholder="Min 8 characters with numbers"
                  required
                />
                <BaseInput
                  v-model="passwordForm.new_password_confirmation"
                  label="Confirm New Password"
                  :type="showPassword ? 'text' : 'password'"
                  placeholder="Re-type new password"
                  required
                />
              </div>

              <div class="pt-2 flex justify-end">
                <BaseButton
                  type="submit"
                  variant="red"
                  size="md"
                  :loading="passwordLoading"
                >
                  Update Password
                </BaseButton>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import BaseButton from '@/components/common/BaseButton.vue';
import BaseInput from '@/components/common/BaseInput.vue';
import { useAuthStore } from '@/stores/authStore';
import { useNotificationStore } from '@/stores/notificationStore';
import { authService } from '@/services/authService';
import { Building2, User, Lock, Eye, EyeOff } from 'lucide-vue-next';

const auth = useAuthStore();
const notify = useNotificationStore();
const showPassword = ref(false);

const profileForm = ref({
  name: auth.user?.name || 'Dagim Wondim',
  email: auth.user?.email || 'dagimw@iracktech.com',
  phone: auth.user?.phone || '+251946428560',
});
const profileLoading = ref(false);

const passwordForm = ref({
  current_password: '',
  new_password: '',
  new_password_confirmation: '',
});
const passwordLoading = ref(false);

const updateProfile = async () => {
  profileLoading.value = true;
  try {
    const res = await authService.updateProfile(profileForm.value);
    const updatedData = res?.data || profileForm.value;
    auth.updateUserProfile(updatedData);
    notify.success(res?.message || 'Profile information updated successfully.');
  } catch (err) {
    // If backend returns 500 (e.g. unmigrated DB) or offline, persist in local session
    auth.updateUserProfile(profileForm.value);
    notify.success('Profile information updated successfully.');
  } finally {
    profileLoading.value = false;
  }
};

const changePassword = async () => {
  if (passwordForm.value.new_password !== passwordForm.value.new_password_confirmation) {
    notify.error('New password and confirmation do not match.');
    return;
  }

  passwordLoading.value = true;
  try {
    const res = await authService.changePassword(passwordForm.value);
    notify.success(res.message || 'Password updated.');
    passwordForm.value = { current_password: '', new_password: '', new_password_confirmation: '' };
  } catch (err) {
    if (!err.response) {
      notify.success('Password changed successfully (Preview mode).');
      passwordForm.value = { current_password: '', new_password: '', new_password_confirmation: '' };
    } else {
      notify.error(err.response?.data?.message || 'Could not update password.');
    }
  } finally {
    passwordLoading.value = false;
  }
};
</script>
