import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

export const useCartStore = defineStore('cart', () => {
  const items = ref(JSON.parse(localStorage.getItem('irack_rfq_cart') || '[]'));

  const itemCount = computed(() => {
    return items.value.reduce((total, item) => total + (item.quantity || 1), 0);
  });

  const uniqueItemCount = computed(() => items.value.length);

  const saveToStorage = () => {
    localStorage.setItem('irack_rfq_cart', JSON.stringify(items.value));
  };

  const addItem = (product, quantity = 1, targetPrice = null) => {
    const existing = items.value.find(i => i.product.id === product.id);
    if (existing) {
      existing.quantity += quantity;
    } else {
      items.value.push({
        product,
        quantity,
        targetPrice,
        notes: '',
      });
    }
    saveToStorage();
  };

  const updateQuantity = (productId, quantity) => {
    const item = items.value.find(i => i.product.id === productId);
    if (item) {
      item.quantity = Math.max(1, quantity);
      saveToStorage();
    }
  };

  const removeItem = (productId) => {
    items.value = items.value.filter(i => i.product.id !== productId);
    saveToStorage();
  };

  const clearCart = () => {
    items.value = [];
    saveToStorage();
  };

  return {
    items,
    itemCount,
    uniqueItemCount,
    addItem,
    updateQuantity,
    removeItem,
    clearCart,
  };
});
