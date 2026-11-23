import { defineStore } from 'pinia';
import { computed, ref } from 'vue';
import { configService } from '@/services/configService';

export const useSettingsStore = defineStore('settings', () => {
  const system = ref({
    currency_code: 'ETB',
    currency_symbol: 'ETB',
  });

  const company = ref({
    company_name: 'iRACK IT Solution',
    company_logo_url: '',
  });

  const currencyCode = computed(() => system.value.currency_code || system.value.currency_symbol || 'ETB');
  const logoUrl = computed(() => {
    const url = company.value.company_logo_url || '';
    if (!url || url === '/images/logo.svg') return '';
    return url;
  });

  function applySystem(next) {
    if (!next || typeof next !== 'object') return;
    system.value = { ...system.value, ...next };
  }

  function applyCompany(next) {
    if (!next || typeof next !== 'object') return;
    company.value = { ...company.value, ...next };
  }

  async function load() {
    const res = await configService.getSettings();
    if (res?.data?.system) applySystem(res.data.system);
    if (res?.data?.company) applyCompany(res.data.company);
  }

  async function loadPublic() {
    const res = await configService.getPublicSettings();
    if (res?.data?.system) applySystem(res.data.system);
    if (res?.data?.company) applyCompany(res.data.company);
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

  return { system, company, currencyCode, logoUrl, load, loadPublic, applySystem, applyCompany, formatCurrency, formatCurrencyShort };
});
