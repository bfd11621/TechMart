export function money(value) {
  return `SAR ${Number(value || 0).toLocaleString('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  })}`;
}

export function stockLabel(product) {
  const status = String(product?.availability_status || '').toUpperCase();
  if (status === 'OUT_OF_STOCK' || Number(product?.total_stock || 0) <= 0) {
    return 'Pre-Order';
  }
  if (status === 'LOW_STOCK') {
    return 'Low Stock';
  }
  return 'In Stock';
}
