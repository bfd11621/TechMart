import { computed, ref } from 'vue';

const items = ref([]);
let loaded = false;

function load() {
  if (loaded || typeof window === 'undefined') {
    return;
  }

  loaded = true;
  try {
    items.value = JSON.parse(localStorage.getItem('irack_compare') || '[]');
  } catch {
    items.value = [];
  }
}

function save() {
  localStorage.setItem('irack_compare', JSON.stringify(items.value));
}

export function useCompare() {
  load();

  const count = computed(() => items.value.length);

  function isInCompare(productId) {
    return items.value.some((item) => item.id === productId);
  }

  function toggle(product) {
    if (isInCompare(product.id)) {
      items.value = items.value.filter((item) => item.id !== product.id);
    } else if (items.value.length < 4) {
      items.value.push({
        id: product.id,
        name: product.name,
        slug: product.slug,
        sku: product.sku,
        base_price: product.base_price,
        short_description: product.short_description || '',
        warranty_info: product.warranty_info || '',
        total_stock: product.total_stock || 0,
        primary_image_url: product.primary_image_url || product.primary_image || null,
        brand: product.brand?.name || '',
        category: product.category?.name || '',
      });
    }
    save();
  }

  function remove(productId) {
    items.value = items.value.filter((item) => item.id !== productId);
    save();
  }

  return { items, count, isInCompare, toggle, remove };
}
