import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

export const useCompareStore = defineStore('compare', () => {
  const items = ref(JSON.parse(localStorage.getItem('irack_compare_items') || '[]'));
  const isDrawerOpen = ref(false);

  const count = computed(() => items.value.length);
  const maxItems = 4;

  const saveToStorage = () => {
    localStorage.setItem('irack_compare_items', JSON.stringify(items.value));
  };

  const isInCompare = (productId) => {
    return items.value.some((item) => item.id === productId);
  };

  const toggleCompare = (product) => {
    const index = items.value.findIndex((item) => item.id === product.id);
    if (index >= 0) {
      items.value.splice(index, 1);
    } else {
      if (items.value.length >= maxItems) {
        items.value.shift(); // Remove oldest item if max reached
      }
      items.value.push(product);
      isDrawerOpen.value = true;
    }
    saveToStorage();
  };

  const removeCompare = (productId) => {
    items.value = items.value.filter((item) => item.id !== productId);
    saveToStorage();
  };

  const clearCompare = () => {
    items.value = [];
    isDrawerOpen.value = false;
    saveToStorage();
  };

  return {
    items,
    count,
    maxItems,
    isDrawerOpen,
    isInCompare,
    toggleCompare,
    removeCompare,
    clearCompare,
  };
});
