import { computed, ref } from 'vue';

const items = ref([]);
let loaded = false;

function load() {
  if (loaded || typeof window === 'undefined') {
    return;
  }

  loaded = true;
  try {
    items.value = JSON.parse(localStorage.getItem('irack_rfq_cart') || '[]');
  } catch {
    items.value = [];
  }
}

function save() {
  localStorage.setItem('irack_rfq_cart', JSON.stringify(items.value));
}

export function useQuotationCart() {
  load();

  const uniqueItemCount = computed(() => items.value.length);

  function addItem(product, quantity = 1) {
    const existing = items.value.find((item) => item.product.id === product.id);
    if (existing) {
      existing.quantity += quantity;
    } else {
      items.value.push({
        product: {
          id: product.id,
          name: product.name,
          slug: product.slug,
          sku: product.sku,
          base_price: product.base_price,
          primary_image_url: product.primary_image_url || product.primary_image || null,
          brand: product.brand || null,
        },
        quantity,
        notes: '',
      });
    }
    save();
  }

  function updateQuantity(productId, quantity) {
    const item = items.value.find((row) => row.product.id === productId);
    if (item) {
      item.quantity = Math.max(1, Number(quantity) || 1);
      save();
    }
  }

  function removeItem(productId) {
    items.value = items.value.filter((row) => row.product.id !== productId);
    save();
  }

  function clearCart() {
    items.value = [];
    save();
  }

  return { items, uniqueItemCount, addItem, updateQuantity, removeItem, clearCart };
}
