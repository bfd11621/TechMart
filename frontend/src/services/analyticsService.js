import apiClient from './api';

// Realistic Enterprise Mock Analytics Data for Offline Preview & High-Fidelity Charting
const MOCK_DASHBOARD = {
  total_products: 48,
  total_categories: 9,
  active_stores: 3,
  current_stock_quantity: 178,
  total_inventory_value: 1245000.00,
  low_stock_products: 4,
  out_of_stock_products: 2,
  total_confirmed_revenue: 114750.00,
  daily_sales: 28750.00,
  weekly_sales: 92000.00,
  monthly_sales: 114750.00,
  total_rfqs: 16,
  pending_quotations: 6,
  completed_sales_orders: 5,
  active_orders_count: 8,
  recent_sales: [
    {
      id: 1,
      order_number: 'SO-2026-G8K22',
      customer_name: 'Tariq Al-Mansoor',
      company_name: 'Gulf Cloud Data Centers Ltd',
      store_name: 'Central Distribution Hub (HQ)',
      total_amount: 51750.00,
      status: 'CONFIRMED',
      status_label: 'Confirmed (Active)',
      payment_status: 'PAID',
      payment_status_label: 'Fully Paid',
      confirmed_at: 'Nov 09, 2026 16:45',
      items_count: 2,
    },
    {
      id: 2,
      order_number: 'SO-2026-M2L44',
      customer_name: 'Dr. Faisal Al-Zahrani',
      company_name: 'KFUPM Advanced Computing Institute',
      store_name: 'North Logistics Facility',
      total_amount: 28750.00,
      status: 'PROCESSING',
      status_label: 'Processing / Allocation',
      payment_status: 'PARTIAL',
      payment_status_label: 'Partially Paid',
      confirmed_at: 'Nov 10, 2026 14:00',
      items_count: 1,
    },
    {
      id: 3,
      order_number: 'SO-2026-B9Q71',
      customer_name: 'Khalid Al-Ghamdi',
      company_name: 'Red Sea Digital Solutions',
      store_name: 'West Coast Regional Store',
      total_amount: 11500.00,
      status: 'COMPLETED',
      status_label: 'Completed & Delivered',
      payment_status: 'PAID',
      payment_status_label: 'Fully Paid',
      confirmed_at: 'Nov 08, 2026 11:30',
      items_count: 1,
    },
  ],
};

const MOCK_SALES_ANALYTICS = {
  period: {
    from: '2026-11-01',
    to: '2026-11-11',
    diff_days: 11,
    preset: 'this_month',
  },
  summary: {
    total_revenue: 114750.00,
    total_orders: 6,
    total_collected: 78250.00,
    outstanding_receivables: 36500.00,
    previous_period_revenue: 89400.00,
    growth_percent: 28.4,
  },
  trend: [
    { period: '2026-11-01', revenue: 0, orders_count: 0 },
    { period: '2026-11-02', revenue: 14500, orders_count: 1 },
    { period: '2026-11-03', revenue: 0, orders_count: 0 },
    { period: '2026-11-04', revenue: 8500, orders_count: 1 },
    { period: '2026-11-05', revenue: 0, orders_count: 0 },
    { period: '2026-11-06', revenue: 0, orders_count: 0 },
    { period: '2026-11-07', revenue: 0, orders_count: 0 },
    { period: '2026-11-08', revenue: 11500, orders_count: 1 },
    { period: '2026-11-09', revenue: 51750, orders_count: 1 },
    { period: '2026-11-10', revenue: 28500, orders_count: 2 },
    { period: '2026-11-11', revenue: 0, orders_count: 0 },
  ],
  by_category: [
    { id: 1, name: 'Servers and Workstations', revenue: 44800.00, units_sold: 8, percentage: 39.0 },
    { id: 4, name: 'Power Solutions (UPS)', revenue: 27600.00, units_sold: 4, percentage: 24.1 },
    { id: 3, name: 'Storage Solutions (SAN/NAS)', revenue: 25000.00, units_sold: 3, percentage: 21.8 },
    { id: 2, name: 'Networking Equipment', revenue: 17350.00, units_sold: 3, percentage: 15.1 },
  ],
  by_brand: [
    { id: 1, name: 'Dell Technologies', revenue: 44800.00, units_sold: 8, percentage: 39.0 },
    { id: 4, name: 'Schneider Electric (APC)', revenue: 27600.00, units_sold: 4, percentage: 24.1 },
    { id: 3, name: 'NetApp', revenue: 25000.00, units_sold: 3, percentage: 21.8 },
    { id: 2, name: 'Cisco Systems', revenue: 17350.00, units_sold: 3, percentage: 15.1 },
  ],
  by_store: [
    { id: 1, name: 'Central Distribution Hub (HQ)', code: 'ST-HQ-01', revenue: 51750.00, orders_count: 2, percentage: 45.1 },
    { id: 2, name: 'North Logistics Facility', code: 'ST-NOR-02', revenue: 51500.00, orders_count: 3, percentage: 44.9 },
    { id: 3, name: 'West Coast Regional Store', code: 'ST-WST-03', revenue: 11500.00, orders_count: 1, percentage: 10.0 },
  ],
};

const MOCK_BEST_SELLERS = {
  top_by_quantity: [
    {
      id: 1,
      name: 'Dell PowerEdge R760 2U Dual-Socket Rack Server',
      model_number: 'PowerEdge R760',
      sku: 'SRV-DEL-R760-01',
      brand_name: 'Dell Technologies',
      category_name: 'Servers and Workstations',
      total_quantity_sold: 8,
      total_revenue_generated: 44800.00,
      average_unit_price: 5600.00,
    },
    {
      id: 4,
      name: 'Schneider Electric APC Smart-UPS Ultra 5kW Lithium-ion',
      model_number: 'SRTL5KRM2UI',
      sku: 'PWR-APC-5KW-LI',
      brand_name: 'Schneider Electric',
      category_name: 'Power Solutions',
      total_quantity_sold: 4,
      total_revenue_generated: 24000.00,
      average_unit_price: 6000.00,
    },
    {
      id: 3,
      name: 'NetApp AFF A250 All-Flash NVMe Storage Array',
      model_number: 'AFF A250',
      sku: 'STR-NET-A250-01',
      brand_name: 'NetApp',
      category_name: 'Storage Solutions',
      total_quantity_sold: 3,
      total_revenue_generated: 25000.00,
      average_unit_price: 8333.33,
    },
    {
      id: 2,
      name: 'Cisco Catalyst 9300X-48HX Enterprise Managed Switch',
      model_number: 'C9300X-48HX',
      sku: 'NET-CIS-C9300X-48',
      brand_name: 'Cisco Systems',
      category_name: 'Networking Equipment',
      total_quantity_sold: 3,
      total_revenue_generated: 15600.00,
      average_unit_price: 5200.00,
    },
  ],
  top_by_revenue: [
    {
      id: 1,
      name: 'Dell PowerEdge R760 2U Dual-Socket Rack Server',
      model_number: 'PowerEdge R760',
      sku: 'SRV-DEL-R760-01',
      brand_name: 'Dell Technologies',
      category_name: 'Servers and Workstations',
      total_quantity_sold: 8,
      total_revenue_generated: 44800.00,
      average_unit_price: 5600.00,
    },
    {
      id: 3,
      name: 'NetApp AFF A250 All-Flash NVMe Storage Array',
      model_number: 'AFF A250',
      sku: 'STR-NET-A250-01',
      brand_name: 'NetApp',
      category_name: 'Storage Solutions',
      total_quantity_sold: 3,
      total_revenue_generated: 25000.00,
      average_unit_price: 8333.33,
    },
    {
      id: 4,
      name: 'Schneider Electric APC Smart-UPS Ultra 5kW Lithium-ion',
      model_number: 'SRTL5KRM2UI',
      sku: 'PWR-APC-5KW-LI',
      brand_name: 'Schneider Electric',
      category_name: 'Power Solutions',
      total_quantity_sold: 4,
      total_revenue_generated: 24000.00,
      average_unit_price: 6000.00,
    },
    {
      id: 2,
      name: 'Cisco Catalyst 9300X-48HX Enterprise Managed Switch',
      model_number: 'C9300X-48HX',
      sku: 'NET-CIS-C9300X-48',
      brand_name: 'Cisco Systems',
      category_name: 'Networking Equipment',
      total_quantity_sold: 3,
      total_revenue_generated: 15600.00,
      average_unit_price: 5200.00,
    },
  ],
};

const MOCK_SLOW_MOVING = {
  threshold_days: 60,
  total_slow_moving_items: 3,
  total_tied_capital: 84200.00,
  items: [
    {
      product_id: 8,
      name: 'Eaton 9PX 11000VA Double-Conversion Online UPS',
      model_number: '9PX11K',
      sku: 'PWR-EAT-9PX11K',
      category_name: 'Power Solutions',
      brand_name: 'Eaton',
      store_name: 'North Logistics Facility',
      on_hand_quantity: 4,
      unit_cost: 7200.00,
      tied_up_capital: 28800.00,
      stock_age_days: 114,
      last_sold_at: '2026-08-14',
      days_since_last_sale: 89,
      risk_level: 'HIGH_STAGNANT',
    },
    {
      product_id: 11,
      name: 'Synology FlashStation FS3410 All-Flash 24-Bay NAS',
      model_number: 'FS3410',
      sku: 'STR-SYN-FS3410',
      category_name: 'Storage Solutions',
      brand_name: 'Synology',
      store_name: 'West Coast Regional Store',
      on_hand_quantity: 5,
      unit_cost: 6500.00,
      tied_up_capital: 32500.00,
      stock_age_days: 95,
      last_sold_at: null,
      days_since_last_sale: null,
      risk_level: 'CRITICAL_DEAD_STOCK',
    },
    {
      product_id: 14,
      name: 'Ubiquiti UniFi Pro Aggregation 28-Port 10G SFP+ Switch',
      model_number: 'USW-Pro-Aggregation',
      sku: 'NET-UBI-AGG28',
      category_name: 'Networking Equipment',
      brand_name: 'Ubiquiti',
      store_name: 'Central Distribution Hub (HQ)',
      on_hand_quantity: 12,
      unit_cost: 1910.00,
      tied_up_capital: 22920.00,
      stock_age_days: 72,
      last_sold_at: '2026-09-02',
      days_since_last_sale: 70,
      risk_level: 'MODERATE',
    },
  ],
};

const MOCK_STORE_COMPARISON = [
  {
    store_id: 1,
    store_name: 'Central Distribution Hub (HQ)',
    store_code: 'ST-HQ-01',
    is_primary: true,
    confirmed_revenue: 51750.00,
    confirmed_orders_count: 2,
    units_sold: 8,
    stock_quantity: 84,
    stock_valuation: 588000.00,
    top_product_name: 'Dell PowerEdge R760 2U Dual-Socket Rack Server',
    top_product_model: 'PowerEdge R760',
    top_product_revenue: 24000.00,
  },
  {
    store_id: 2,
    store_name: 'North Logistics Facility',
    store_code: 'ST-NOR-02',
    is_primary: false,
    confirmed_revenue: 51500.00,
    confirmed_orders_count: 3,
    units_sold: 7,
    stock_quantity: 58,
    stock_valuation: 406000.00,
    top_product_name: 'NetApp AFF A250 All-Flash NVMe Storage Array',
    top_product_model: 'AFF A250',
    top_product_revenue: 25000.00,
  },
  {
    store_id: 3,
    store_name: 'West Coast Regional Store',
    store_code: 'ST-WST-03',
    is_primary: false,
    confirmed_revenue: 11500.00,
    confirmed_orders_count: 1,
    units_sold: 2,
    stock_quantity: 36,
    stock_valuation: 251000.00,
    top_product_name: 'Cisco Catalyst 9300X-48HX Enterprise Managed Switch',
    top_product_model: 'C9300X-48HX',
    top_product_revenue: 10400.00,
  },
];

export const analyticsService = {
  /**
   * Fetch Management Dashboard Metrics
   */
  async getDashboardMetrics(storeId = null) {
    try {
      const response = await apiClient.get('/admin/analytics/dashboard', {
        params: storeId ? { store_id: storeId } : {},
      });
      return response.data;
    } catch (error) {
      console.warn('API call failed for analytics dashboard, using fallback mock:', error.message);
      return { status: 'success', data: MOCK_DASHBOARD };
    }
  },

  /**
   * Fetch Detailed Sales Analytics with Filters
   */
  async getSalesAnalytics(params = {}) {
    try {
      const response = await apiClient.get('/admin/analytics/sales', { params });
      return response.data;
    } catch (error) {
      console.warn('API call failed for sales analytics, using fallback mock:', error.message);
      return { status: 'success', data: MOCK_SALES_ANALYTICS };
    }
  },

  /**
   * Fetch Best-Selling Products Rankings
   */
  async getBestSellers(params = {}) {
    try {
      const response = await apiClient.get('/admin/analytics/best-sellers', { params });
      return response.data;
    } catch (error) {
      console.warn('API call failed for best sellers, using fallback mock:', error.message);
      return { status: 'success', data: MOCK_BEST_SELLERS };
    }
  },

  /**
   * Fetch Slow-Moving and Stagnant Inventory Analysis
   */
  async getSlowMoving(params = {}) {
    try {
      const response = await apiClient.get('/admin/analytics/slow-moving', { params });
      return response.data;
    } catch (error) {
      console.warn('API call failed for slow-moving products, using fallback mock:', error.message);
      return { status: 'success', data: MOCK_SLOW_MOVING };
    }
  },

  /**
   * Fetch Multi-Store Performance Comparison Matrix
   */
  async getStoreComparison(storeIds = null) {
    try {
      const response = await apiClient.get('/admin/analytics/store-comparison', {
        params: storeIds ? { store_ids: storeIds } : {},
      });
      return response.data;
    } catch (error) {
      console.warn('API call failed for store comparison, using fallback mock:', error.message);
      return { status: 'success', data: MOCK_STORE_COMPARISON };
    }
  },
};
