import { defineStore } from 'pinia';
import { computed, ref } from 'vue';
import { configService } from '@/services/configService';

export const useSettingsStore = defineStore('settings', () => {
  const system = ref({
    currency_code: 'ETB',
    currency_symbol: 'ETB',
  });

  const currencyCode = computed(() => system.value.currency_code || system.value.currency_symbol || 'ETB');

  function applySystem(next) {
    if (!next || typeof next !== 'object') return;
    system.value = { ...system.value, ...next };
  }

  async function load() {
    const res = await configService.getSettings();
    if (res?.data?.system) {
      applySystem(res.data.system);
    }
  }

  function formatCurrency(val) {
    const code = currencyCode.value;
    const amount = Number(val || 0);
    return `${code} ${amount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
  }

  function formatCurrencyShort(val) {
    const code = currencyCode.value;
    const amount = Number(val || 0);
    if (!amount) return `${code} 0`;
    if (amount >= 1000000) return `${code} ${(amount / 1000000).toFixed(1)}M`;
    if (amount >= 1000) return `${code} ${(amount / 1000).toFixed(1)}k`;
    return `${code} ${amount.toLocaleString()}`;
  }

  return { system, currencyCode, load, applySystem, formatCurrency, formatCurrencyShort };
});
