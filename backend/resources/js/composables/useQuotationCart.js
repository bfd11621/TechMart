import { computed, ref } from 'vue';

const items = ref([]);
let loaded = false;

function load() {
  if (loaded || typeof window === 'undefined') {
    return;
  }

  loaded = true;
  try {
    const stored = JSON.parse(localStorage.getItem('irack_rfq_cart') || '[]');
    items.value = Array.isArray(stored) ? stored.map(normalizeItem) : [];
  } catch {
    items.value = [];
  }
}

function imageUrl(product) {
  const primary = product?.primary_image_url || product?.primary_image || product?.primaryImage || null;
  if (typeof primary === 'string' && primary) return primary;
  if (primary && typeof primary === 'object') {
    return primary.image_url || primary.url || null;
  }

  const images = Array.isArray(product?.images?.data) ? product.images.data : product?.images;
  const first = Array.isArray(images) ? images.find((image) => image?.is_primary) || images[0] : null;
  if (!first) return null;
  if (typeof first === 'string') return first;
  return first.image_url || first.url || null;
}

function snapshot(product) {
  const source = product?.data?.name ? product.data : (product?.product?.name ? product.product : product);
  return {
    id: source?.id,
    name: source?.name || source?.product_name || 'Hardware item',
    slug: source?.slug || source?.id,
    sku: source?.sku || '',
    base_price: source?.base_price ?? source?.price ?? 0,
    primary_image_url: imageUrl(source),
    brand: source?.brand?.name ? source.brand : (source?.brand || null),
  };
}

function normalizeItem(item) {
  return {
    product: snapshot(item?.product || item),
    quantity: Math.max(1, Number(item?.quantity) || 1),
    notes: item?.notes || '',
  };
}

function save() {
  localStorage.setItem('irack_rfq_cart', JSON.stringify(items.value));
}

export function useQuotationCart() {
  load();

  const uniqueItemCount = computed(() => items.value.length);

  function addItem(product, quantity = 1) {
    const productSnapshot = snapshot(product);
    const existing = items.value.find((item) => item.product.id === productSnapshot.id);
    if (existing) {
      existing.quantity += quantity;
      existing.product = productSnapshot;
    } else {
      items.value.push({
        product: productSnapshot,
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
