import apiClient from './api';

// Realistic fallback demonstration dataset for preview and offline capability
const MOCK_INVENTORIES = [
  {
    id: 1,
    store_id: 1,
    store_name: 'iRACK Central Distribution Hub (HQ)',
    store_code: 'ST-HQ-01',
    product_id: 1,
    product_name: 'Dell PowerEdge R760 2U Enterprise Server',
    sku: 'DEL-PE-R760-01',
    model_number: 'PE-R760-2U',
    product_slug: 'dell-poweredge-r760-2u-rack-server',
    brand_name: 'Dell Technologies',
    category_name: 'Servers and Workstations',
    primary_image_url: 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=800&auto=format&fit=crop&q=80',
    quantity: 18,
    reserved_quantity: 2,
    available_quantity: 16,
    min_alert_quantity: 5,
    shelf_location: 'AISLE-A-BAY-1',
    stock_status: 'IN_STOCK',
    stock_status_label: 'In Stock',
    badge_variant: 'success',
    cost_price: 4150.00,
    base_price: 5490.00,
    stock_valuation: 74700.00,
    last_movement_at: '2026-11-10T14:30:00Z',
    formatted_last_movement: 'Nov 10, 2026 14:30',
    aging_days: 1,
  },
  {
    id: 2,
    store_id: 2,
    store_name: 'iRACK Commercial Sales Center',
    store_code: 'ST-DOWNTOWN-02',
    product_id: 1,
    product_name: 'Dell PowerEdge R760 2U Enterprise Server',
    sku: 'DEL-PE-R760-01',
    model_number: 'PE-R760-2U',
    product_slug: 'dell-poweredge-r760-2u-rack-server',
    brand_name: 'Dell Technologies',
    category_name: 'Servers and Workstations',
    primary_image_url: 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=800&auto=format&fit=crop&q=80',
    quantity: 6,
    reserved_quantity: 1,
    available_quantity: 5,
    min_alert_quantity: 5,
    shelf_location: 'AISLE-B-BAY-2',
    stock_status: 'LOW_STOCK',
    stock_status_label: 'Low Stock Alert',
    badge_variant: 'warning',
    cost_price: 4150.00,
    base_price: 5490.00,
    stock_valuation: 24900.00,
    last_movement_at: '2026-11-08T09:15:00Z',
    formatted_last_movement: 'Nov 08, 2026 09:15',
    aging_days: 3,
  },
  {
    id: 3,
    store_id: 3,
    store_name: 'iRACK North Regional Depot',
    store_code: 'ST-NORTH-03',
    product_id: 1,
    product_name: 'Dell PowerEdge R760 2U Enterprise Server',
    sku: 'DEL-PE-R760-01',
    model_number: 'PE-R760-2U',
    product_slug: 'dell-poweredge-r760-2u-rack-server',
    brand_name: 'Dell Technologies',
    category_name: 'Servers and Workstations',
    primary_image_url: 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=800&auto=format&fit=crop&q=80',
    quantity: 8,
    reserved_quantity: 0,
    available_quantity: 8,
    min_alert_quantity: 4,
    shelf_location: 'AISLE-C-BAY-1',
    stock_status: 'IN_STOCK',
    stock_status_label: 'In Stock',
    badge_variant: 'success',
    cost_price: 4150.00,
    base_price: 5490.00,
    stock_valuation: 33200.00,
    last_movement_at: '2026-11-05T11:00:00Z',
    formatted_last_movement: 'Nov 05, 2026 11:00',
    aging_days: 6,
  },
  {
    id: 4,
    store_id: 1,
    store_name: 'iRACK Central Distribution Hub (HQ)',
    store_code: 'ST-HQ-01',
    product_id: 2,
    product_name: 'Cisco Catalyst 9300 48-Port PoE+ Layer 3 Switch',
    sku: 'CSC-C9300-48P',
    model_number: 'C9300-48P-A',
    product_slug: 'cisco-catalyst-9300-48p-switch',
    brand_name: 'Cisco Systems',
    category_name: 'Networking Equipment',
    primary_image_url: 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?w=800&auto=format&fit=crop&q=80',
    quantity: 34,
    reserved_quantity: 4,
    available_quantity: 30,
    min_alert_quantity: 10,
    shelf_location: 'AISLE-NET-01',
    stock_status: 'IN_STOCK',
    stock_status_label: 'In Stock',
    badge_variant: 'success',
    cost_price: 2800.00,
    base_price: 3850.00,
    stock_valuation: 95200.00,
    last_movement_at: '2026-11-09T16:45:00Z',
    formatted_last_movement: 'Nov 09, 2026 16:45',
    aging_days: 2,
  },
  {
    id: 5,
    store_id: 2,
    store_name: 'iRACK Commercial Sales Center',
    store_code: 'ST-DOWNTOWN-02',
    product_id: 2,
    product_name: 'Cisco Catalyst 9300 48-Port PoE+ Layer 3 Switch',
    sku: 'CSC-C9300-48P',
    model_number: 'C9300-48P-A',
    product_slug: 'cisco-catalyst-9300-48p-switch',
    brand_name: 'Cisco Systems',
    category_name: 'Networking Equipment',
    primary_image_url: 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?w=800&auto=format&fit=crop&q=80',
    quantity: 0,
    reserved_quantity: 0,
    available_quantity: 0,
    min_alert_quantity: 6,
    shelf_location: 'AISLE-NET-02',
    stock_status: 'OUT_OF_STOCK',
    stock_status_label: 'Out of Stock',
    badge_variant: 'danger',
    cost_price: 2800.00,
    base_price: 3850.00,
    stock_valuation: 0.00,
    last_movement_at: '2026-11-02T13:20:00Z',
    formatted_last_movement: 'Nov 02, 2026 13:20',
    aging_days: 9,
  },
  {
    id: 6,
    store_id: 1,
    store_name: 'iRACK Central Distribution Hub (HQ)',
    store_code: 'ST-HQ-01',
    product_id: 3,
    product_name: 'Synology FlashStation FS3410 24-Bay All-Flash NAS',
    sku: 'SYN-FS-3410-01',
    model_number: 'FS3410-24B',
    product_slug: 'synology-flashstation-fs3410',
    brand_name: 'Synology Inc.',
    category_name: 'Storage Solutions',
    primary_image_url: 'https://images.unsplash.com/photo-1597852074816-d933c4d2b988?w=800&auto=format&fit=crop&q=80',
    quantity: 12,
    reserved_quantity: 1,
    available_quantity: 11,
    min_alert_quantity: 4,
    shelf_location: 'AISLE-SAN-01',
    stock_status: 'IN_STOCK',
    stock_status_label: 'In Stock',
    badge_variant: 'success',
    cost_price: 4900.00,
    base_price: 6490.00,
    stock_valuation: 58800.00,
    last_movement_at: '2026-11-10T10:00:00Z',
    formatted_last_movement: 'Nov 10, 2026 10:00',
    aging_days: 1,
  },
  {
    id: 7,
    store_id: 1,
    store_name: 'iRACK Central Distribution Hub (HQ)',
    store_code: 'ST-HQ-01',
    product_id: 4,
    product_name: 'APC Smart-UPS On-Line 10kVA 208V/230V Double Conversion',
    sku: 'APC-SRT-10KXLI',
    model_number: 'SRT10KXLI',
    product_slug: 'apc-smart-ups-on-line-10kva',
    brand_name: 'APC by Schneider Electric',
    category_name: 'Power Solutions',
    primary_image_url: 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=800&auto=format&fit=crop&q=80',
    quantity: 15,
    reserved_quantity: 0,
    available_quantity: 15,
    min_alert_quantity: 5,
    shelf_location: 'AISLE-PWR-01',
    stock_status: 'IN_STOCK',
    stock_status_label: 'In Stock',
    badge_variant: 'success',
    cost_price: 3650.00,
    base_price: 4980.00,
    stock_valuation: 54750.00,
    last_movement_at: '2026-11-07T12:00:00Z',
    formatted_last_movement: 'Nov 07, 2026 12:00',
    aging_days: 4,
  },
  {
    id: 8,
    store_id: 1,
    store_name: 'iRACK Central Distribution Hub (HQ)',
    store_code: 'ST-HQ-01',
    product_id: 5,
    product_name: 'iRACK NetShelter Enterprise 42U Server Cabinet Rack',
    sku: 'IRK-NET-42U812',
    model_number: 'IRK-42U-800x1200',
    product_slug: 'irack-netshelter-enterprise-42u',
    brand_name: 'iRACK Custom Solutions',
    category_name: 'Data Center Infrastructure',
    primary_image_url: 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=800&auto=format&fit=crop&q=80',
    quantity: 22,
    reserved_quantity: 2,
    available_quantity: 20,
    min_alert_quantity: 6,
    shelf_location: 'BAY-RCK-01',
    stock_status: 'IN_STOCK',
    stock_status_label: 'In Stock',
    badge_variant: 'success',
    cost_price: 1350.00,
    base_price: 1890.00,
    stock_valuation: 29700.00,
    last_movement_at: '2026-11-10T16:00:00Z',
    formatted_last_movement: 'Nov 10, 2026 16:00',
    aging_days: 1,
  },
];

const MOCK_MOVEMENTS = [
  {
    id: 101,
    movement_number: 'RCV-2026-09A12',
    type: 'RECEIVE',
    type_label: 'Stock Receiving',
    badge_variant: 'success',
    direction: 'IN',
    supplier: 'Dell Technologies Global Logistics',
    external_reference: 'PO-2026-90412',
    status: 'COMPLETED',
    source_store: null,
    destination_store: { id: 1, name: 'iRACK Central Distribution Hub (HQ)', code: 'ST-HQ-01' },
    user: { id: 4, name: 'Tarek Miller (Inventory Officer)', email: 'inventory@irack.com' },
    reason: 'Monthly Restock from OEM Master Distributor',
    notes: 'All compute nodes inspected; seal integrity intact.',
    items_count: 1,
    total_quantity: 10,
    total_valuation: 41500.00,
    items: [
      {
        id: 1,
        product_id: 1,
        product_name: 'Dell PowerEdge R760 2U Enterprise Server',
        sku: 'DEL-PE-R760-01',
        model_number: 'PE-R760-2U',
        quantity: 10,
        previous_quantity: 8,
        new_quantity: 18,
        unit_cost: 4150.00,
        line_total: 41500.00,
        notes: 'Rack configuration with PERC H755',
      }
    ],
    created_at: '2026-11-10T14:30:00Z',
    formatted_created_at: 'Nov 10, 2026 14:30',
  },
  {
    id: 102,
    movement_number: 'TRF-2026-78B44',
    type: 'TRANSFER_OUT',
    type_label: 'Inter-Store Transfer (Out)',
    badge_variant: 'info',
    direction: 'OUT',
    supplier: null,
    external_reference: 'TRF-HQ-DOWNTOWN-04',
    status: 'COMPLETED',
    source_store: { id: 1, name: 'iRACK Central Distribution Hub (HQ)', code: 'ST-HQ-01' },
    destination_store: { id: 2, name: 'iRACK Commercial Sales Center', code: 'ST-DOWNTOWN-02' },
    user: { id: 1, name: 'Alexander Vance (Super Admin)', email: 'admin@irack.com' },
    reason: 'Regional Stock Rebalancing for Financial District Client Demo',
    notes: 'Expedited van courier dispatch.',
    items_count: 1,
    total_quantity: 3,
    total_valuation: 8400.00,
    items: [
      {
        id: 2,
        product_id: 2,
        product_name: 'Cisco Catalyst 9300 48-Port PoE+ Layer 3 Switch',
        sku: 'CSC-C9300-48P',
        model_number: 'C9300-48P-A',
        quantity: 3,
        previous_quantity: 37,
        new_quantity: 34,
        unit_cost: 2800.00,
        line_total: 8400.00,
        notes: 'StackWise cable included',
      }
    ],
    created_at: '2026-11-09T16:45:00Z',
    formatted_created_at: 'Nov 09, 2026 16:45',
  },
  {
    id: 103,
    movement_number: 'ISS-2026-55C18',
    type: 'ISSUE',
    type_label: 'Stock Issuing',
    badge_variant: 'danger',
    direction: 'OUT',
    supplier: null,
    external_reference: 'RMA-DEF-0912',
    status: 'COMPLETED',
    source_store: { id: 1, name: 'iRACK Central Distribution Hub (HQ)', code: 'ST-HQ-01' },
    destination_store: null,
    user: { id: 4, name: 'Tarek Miller (Inventory Officer)', email: 'inventory@irack.com' },
    reason: 'Defective Power Supply Unit Return to OEM (RMA)',
    notes: 'Authorized return under ProSupport SLA.',
    items_count: 1,
    total_quantity: 1,
    total_valuation: 3650.00,
    items: [
      {
        id: 3,
        product_id: 4,
        product_name: 'APC Smart-UPS On-Line 10kVA 208V/230V Double Conversion',
        sku: 'APC-SRT-10KXLI',
        model_number: 'SRT10KXLI',
        quantity: 1,
        previous_quantity: 16,
        new_quantity: 15,
        unit_cost: 3650.00,
        line_total: 3650.00,
        notes: 'Serial # APC-994101A return',
      }
    ],
    created_at: '2026-11-07T12:00:00Z',
    formatted_created_at: 'Nov 07, 2026 12:00',
  },
  {
    id: 104,
    movement_number: 'ADJ-2026-33D90',
    type: 'ADJUSTMENT',
    type_label: 'Stock Adjustment',
    badge_variant: 'warning',
    direction: 'ADJUST',
    supplier: null,
    external_reference: 'AUDIT-Q4-BAY1',
    status: 'COMPLETED',
    source_store: { id: 1, name: 'iRACK Central Distribution Hub (HQ)', code: 'ST-HQ-01' },
    destination_store: null,
    user: { id: 1, name: 'Alexander Vance (Super Admin)', email: 'admin@irack.com' },
    reason: 'Q4 Physical Cycle Count Reconciliation',
    notes: 'Found unrecorded unit in Aisle B back bay; verified serial against packing list.',
    items_count: 1,
    total_quantity: 1,
    total_valuation: 4900.00,
    items: [
      {
        id: 4,
        product_id: 3,
        product_name: 'Synology FlashStation FS3410 24-Bay All-Flash NAS',
        sku: 'SYN-FS-3410-01',
        model_number: 'FS3410-24B',
        quantity: 1,
        previous_quantity: 11,
        new_quantity: 12,
        unit_cost: 4900.00,
        line_total: 4900.00,
        notes: 'Physical inventory audit sheet #044',
      }
    ],
    created_at: '2026-11-10T10:00:00Z',
    formatted_created_at: 'Nov 10, 2026 10:00',
  },
];

export const inventoryService = {
  /**
   * Fetch Store Inventory records with faceted filters and pagination
   */
  async getInventory(params = {}) {
    const response = await apiClient.get('/admin/inventory', { params });
    return response.data;
  },

  /**
   * Fetch single inventory record details
   */
  async getInventoryItem(id) {
    const response = await apiClient.get(`/admin/inventory/${id}`);
    return response.data;
  },

  /**
   * Update minimum stock threshold & shelf location
   */
  async updateAlert(id, payload) {
    const response = await apiClient.patch(`/admin/inventory/${id}/alert`, payload);
    return response.data;
  },

  /**
   * Fetch Inventory Dashboard Metrics
   */
  async getDashboard(storeId = null) {
    const response = await apiClient.get('/admin/inventory/dashboard', {
      params: storeId ? { store_id: storeId } : {},
    });
    return response.data;
  },

  /**
   * Fetch Stock Movement transactions
   */
  async getMovements(params = {}) {
    const response = await apiClient.get('/admin/stock-movements', { params });
    return response.data;
  },

  /**
   * Stock Receiving: Record received goods from supplier
   */
  async receiveStock(payload) {
    const response = await apiClient.post('/admin/stock-movements/receive', payload);
    return response.data;
  },

  /**
   * Stock Issuing: Issue products from store with negative stock prevention
   */
  async issueStock(payload) {
    const response = await apiClient.post('/admin/stock-movements/issue', payload);
    return response.data;
  },

  /**
   * Inter-Store Stock Transfer: Atomic stock movement between branches
   */
  async transferStock(payload) {
    const response = await apiClient.post('/admin/stock-movements/transfer', payload);
    return response.data;
  },

  /**
   * Stock Adjustment: Physical cycle count reconciliation with mandatory reason
   */
  async adjustStock(payload) {
    const response = await apiClient.post('/admin/stock-movements/adjust', payload);
    return response.data;
  },

  /**
   * Fetch inventory reports
   */
  async getReports(type = 'current_stock', storeId = null) {
    const response = await apiClient.get('/admin/inventory/reports', {
      params: { type, ...(storeId ? { store_id: storeId } : {}) },
    });
    return response.data;
  },

};

export default inventoryService;
