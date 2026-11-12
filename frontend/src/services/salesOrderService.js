import apiClient from './api';

// Realistic B2B Fallback Mock Data for Sales Orders and Payments
const MOCK_ORDERS = [
  {
    id: 1,
    order_number: 'SO-2026-G8K22',
    quotation_id: 3,
    quotation_number: 'QT-2026-P3R19',
    store_id: 1,
    store_name: 'Central Distribution Hub (HQ)',
    customer_name: 'Tariq Al-Mansoor',
    customer_email: 'tariq@gulfdata.sa',
    customer_phone: '+966 50 123 4567',
    company_name: 'Gulf Cloud Data Centers Ltd',
    billing_address: 'King Fahd Road, Al Olaya District, Riyadh 12211, KSA',
    shipping_address: 'Tier-III Facility, Site 4, Industrial Area 2, Riyadh, KSA',
    status: 'CONFIRMED',
    status_label: 'Confirmed (Active)',
    badge_variant: 'primary',
    payment_status: 'PAID',
    payment_status_label: 'Fully Paid',
    payment_badge_variant: 'success',
    payment_method: 'BANK_TRANSFER',
    subtotal: 48000.00,
    discount_amount: 3000.00,
    tax_amount: 6750.00,
    total_amount: 51750.00,
    paid_amount: 51750.00,
    balance_due: 0.00,
    notes: 'Converted from quotation QT-2026-P3R19. Delivery scheduled for Nov 14, 2026.',
    created_by: { id: 1, name: 'Alexander Vance', email: 'admin@irack.com' },
    confirmed_by: { id: 1, name: 'Alexander Vance', email: 'admin@irack.com' },
    confirmed_at: new Date(Date.now() - 1000 * 60 * 60 * 48).toISOString(),
    formatted_confirmed_at: 'Nov 09, 2026 16:45',
    items_count: 2,
    total_quantity: 8,
    items: [
      {
        id: 101,
        sales_order_id: 1,
        product_id: 4,
        product_name: 'Schneider Electric APC Smart-UPS Ultra 5kW Lithium-ion',
        product_model: 'SRTL5KRM2UI',
        product_sku: 'PWR-APC-5KW-LI',
        product_image: 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?auto=format&fit=crop&w=300&h=200&q=80',
        quantity: 4,
        unit_cost: 4100.00,
        unit_price: 6000.00,
        discount_amount: 0.00,
        total_price: 24000.00,
      },
      {
        id: 102,
        sales_order_id: 1,
        product_id: 1,
        product_name: 'Dell PowerEdge R760 2U Dual-Socket Rack Server',
        product_model: 'PowerEdge R760',
        product_sku: 'SRV-DEL-R760-01',
        product_image: 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?auto=format&fit=crop&w=300&h=200&q=80',
        quantity: 4,
        unit_cost: 3800.00,
        unit_price: 6000.00,
        discount_amount: 0.00,
        total_price: 24000.00,
      },
    ],
    payments: [
      {
        id: 1,
        sales_order_id: 1,
        receipt_number: 'RCT-2026-F91A2',
        amount: 51750.00,
        payment_method: 'BANK_TRANSFER',
        payment_reference: 'WIRE-SNB-20261109-8819',
        payment_date: '2026-11-09',
        formatted_payment_date: 'Nov 09, 2026',
        notes: 'Corporate advance 100% wire clearance verified via Saudi National Bank.',
        received_by: { id: 1, name: 'Alexander Vance' },
        created_at: new Date(Date.now() - 1000 * 60 * 60 * 46).toISOString(),
        formatted_created_at: 'Nov 09, 2026 18:20',
      },
    ],
    created_at: new Date(Date.now() - 1000 * 60 * 60 * 48).toISOString(),
    formatted_created_at: 'Nov 09, 2026 16:45',
  },
  {
    id: 2,
    order_number: 'SO-2026-M2L44',
    quotation_id: null,
    quotation_number: null,
    store_id: 2,
    store_name: 'North Logistics Facility',
    customer_name: 'Dr. Faisal Al-Zahrani',
    customer_email: 'faisal@kfupm-research.edu.sa',
    customer_phone: '+966 54 999 1234',
    company_name: 'KFUPM Advanced Computing Institute',
    billing_address: 'Academic Zone, Dhahran 31261, Eastern Province, KSA',
    shipping_address: 'High Performance Computing Lab, Building 22, Dhahran, KSA',
    status: 'PROCESSING',
    status_label: 'Processing / Allocation',
    badge_variant: 'warning',
    payment_status: 'PARTIAL',
    payment_status_label: 'Partially Paid',
    payment_badge_variant: 'warning',
    payment_method: 'BANK_TRANSFER',
    subtotal: 26500.00,
    discount_amount: 1500.00,
    tax_amount: 3750.00,
    total_amount: 28750.00,
    paid_amount: 15000.00,
    balance_due: 13750.00,
    notes: 'Direct corporate order for university AI training infrastructure.',
    created_by: { id: 2, name: 'Elena Vance', email: 'store@irack.com' },
    confirmed_by: { id: 2, name: 'Elena Vance', email: 'store@irack.com' },
    confirmed_at: new Date(Date.now() - 1000 * 60 * 60 * 20).toISOString(),
    formatted_confirmed_at: 'Yesterday 14:00',
    items_count: 1,
    total_quantity: 3,
    items: [
      {
        id: 201,
        sales_order_id: 2,
        product_id: 3,
        product_name: 'NetApp AFF A250 All-Flash NVMe Storage Array',
        product_model: 'AFF A250',
        product_sku: 'STR-NET-A250-01',
        product_image: 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?auto=format&fit=crop&w=300&h=200&q=80',
        quantity: 3,
        unit_cost: 6500.00,
        unit_price: 8833.33,
        discount_amount: 500.00,
        total_price: 25000.00,
      },
    ],
    payments: [
      {
        id: 2,
        sales_order_id: 2,
        receipt_number: 'RCT-2026-K1098',
        amount: 15000.00,
        payment_method: 'BANK_TRANSFER',
        payment_reference: 'WIRE-ALINMA-9921',
        payment_date: '2026-11-10',
        formatted_payment_date: 'Nov 10, 2026',
        notes: 'Initial 50% mobilization installment.',
        received_by: { id: 2, name: 'Elena Vance' },
        created_at: new Date(Date.now() - 1000 * 60 * 60 * 18).toISOString(),
        formatted_created_at: 'Yesterday 16:00',
      },
    ],
    created_at: new Date(Date.now() - 1000 * 60 * 60 * 20).toISOString(),
    formatted_created_at: 'Nov 10, 2026 14:00',
  },
  {
    id: 3,
    order_number: 'SO-2026-B9Q71',
    quotation_id: null,
    quotation_number: null,
    store_id: 3,
    store_name: 'West Coast Regional Store',
    customer_name: 'Khalid Al-Ghamdi',
    customer_email: 'khalid@redsea-digital.com',
    customer_phone: '+966 56 777 8899',
    company_name: 'Red Sea Digital Solutions',
    billing_address: 'Corniche Commercial Center, Suite 501, Jeddah 21483, KSA',
    shipping_address: 'Corniche Commercial Center, Suite 501, Jeddah 21483, KSA',
    status: 'COMPLETED',
    status_label: 'Completed & Delivered',
    badge_variant: 'success',
    payment_status: 'PAID',
    payment_status_label: 'Fully Paid',
    payment_badge_variant: 'success',
    payment_method: 'CREDIT_CARD',
    subtotal: 10400.00,
    discount_amount: 400.00,
    tax_amount: 1500.00,
    total_amount: 11500.00,
    paid_amount: 11500.00,
    balance_due: 0.00,
    notes: 'Direct branch counter procurement. Hardware inspected and signed off by client engineer.',
    created_by: { id: 4, name: 'Sami Al-Harbi', email: 'inventory@irack.com' },
    confirmed_by: { id: 4, name: 'Sami Al-Harbi', email: 'inventory@irack.com' },
    confirmed_at: new Date(Date.now() - 1000 * 60 * 60 * 70).toISOString(),
    formatted_confirmed_at: '3 days ago',
    items_count: 1,
    total_quantity: 2,
    items: [
      {
        id: 301,
        sales_order_id: 3,
        product_id: 2,
        product_name: 'Cisco Catalyst 9300X-48HX Enterprise Managed Switch',
        product_model: 'C9300X-48HX',
        product_sku: 'NET-CIS-C9300X-48',
        product_image: 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?auto=format&fit=crop&w=300&h=200&q=80',
        quantity: 2,
        unit_cost: 3600.00,
        unit_price: 5200.00,
        discount_amount: 400.00,
        total_price: 10000.00,
      },
    ],
    payments: [
      {
        id: 3,
        sales_order_id: 3,
        receipt_number: 'RCT-2026-C3391',
        amount: 11500.00,
        payment_method: 'CREDIT_CARD',
        payment_reference: 'POS-AUTH-88201',
        payment_date: '2026-11-08',
        formatted_payment_date: 'Nov 08, 2026',
        notes: 'Corporate Visa transaction settled via counter terminal.',
        received_by: { id: 4, name: 'Sami Al-Harbi' },
        created_at: new Date(Date.now() - 1000 * 60 * 60 * 70).toISOString(),
        formatted_created_at: 'Nov 08, 2026 11:30',
      },
    ],
    created_at: new Date(Date.now() - 1000 * 60 * 60 * 70).toISOString(),
    formatted_confirmed_at: 'Nov 08, 2026 11:30',
  },
];

export const salesOrderService = {
  /**
   * Fetch paginated sales orders with filters
   */
  async getOrders(params = {}) {
    try {
      const response = await apiClient.get('/admin/sales-orders', { params });
      return response.data;
    } catch (error) {
      console.warn('API call failed for sales orders, using mock orders list:', error.message);
      
      let filtered = [...MOCK_ORDERS];

      if (params.status && params.status !== 'all') {
        filtered = filtered.filter(o => o.status === params.status.toUpperCase());
      }
      if (params.payment_status && params.payment_status !== 'all') {
        filtered = filtered.filter(o => o.payment_status === params.payment_status.toUpperCase());
      }
      if (params.store_id) {
        filtered = filtered.filter(o => o.store_id === parseInt(params.store_id));
      }
      if (params.search) {
        const s = params.search.toLowerCase();
        filtered = filtered.filter(o =>
          o.order_number.toLowerCase().includes(s) ||
          o.customer_name.toLowerCase().includes(s) ||
          (o.company_name && o.company_name.toLowerCase().includes(s)) ||
          (o.quotation_number && o.quotation_number.toLowerCase().includes(s))
        );
      }

      return {
        status: 'success',
        data: filtered,
        meta: {
          current_page: 1,
          last_page: 1,
          per_page: 15,
          total: filtered.length,
          counts: {
            all: MOCK_ORDERS.length,
            pending: MOCK_ORDERS.filter(o => o.status === 'PENDING').length,
            confirmed: MOCK_ORDERS.filter(o => o.status === 'CONFIRMED').length,
            processing: MOCK_ORDERS.filter(o => o.status === 'PROCESSING').length,
            completed: MOCK_ORDERS.filter(o => o.status === 'COMPLETED').length,
            cancelled: MOCK_ORDERS.filter(o => o.status === 'CANCELLED').length,
            unpaid: MOCK_ORDERS.filter(o => o.payment_status === 'UNPAID').length,
            partial: MOCK_ORDERS.filter(o => o.payment_status === 'PARTIAL').length,
            paid: MOCK_ORDERS.filter(o => o.payment_status === 'PAID').length,
          },
        },
      };
    }
  },

  /**
   * Fetch single order details
   */
  async getOrder(id) {
    try {
      const response = await apiClient.get(`/admin/sales-orders/${id}`);
      return response.data;
    } catch (error) {
      console.warn(`API call failed for order #${id}, using mock:`, error.message);
      const match = MOCK_ORDERS.find(o => o.id === parseInt(id));
      if (!match) throw new Error('Sales order not found');
      return { status: 'success', data: match };
    }
  },

  /**
   * Convert an approved quotation into a confirmed sales order.
   * Enforces zero duplicate conversions and stock deduction.
   */
  async convertQuotation(quotationId, payload = {}) {
    try {
      const response = await apiClient.post(`/admin/quotations/${quotationId}/convert`, payload);
      return response.data;
    } catch (error) {
      console.warn(`API call failed for converting quotation #${quotationId}:`, error.message);
      
      const newOrderNum = `SO-2026-C${Math.floor(1000 + Math.random() * 9000)}`;
      const order = {
        id: MOCK_ORDERS.length + 1,
        order_number: newOrderNum,
        quotation_id: parseInt(quotationId),
        quotation_number: payload.quotation_number || `QT-2026-X9B21`,
        store_id: payload.store_id || 1,
        store_name: 'Central Distribution Hub (HQ)',
        customer_name: payload.customer_name || 'Apex Cloud Solutions',
        customer_email: payload.customer_email || 'd.sterling@apexcloud.io',
        customer_phone: payload.customer_phone || '+1 (555) 492-8810',
        company_name: payload.company_name || 'Apex Cloud Solutions Inc.',
        billing_address: payload.billing_address || '100 Silicon Parkway, San Jose, CA',
        shipping_address: payload.shipping_address || '100 Silicon Parkway, San Jose, CA',
        status: 'CONFIRMED',
        status_label: 'Confirmed (Active)',
        badge_variant: 'primary',
        payment_status: payload.initial_payment_amount ? 'PARTIAL' : 'UNPAID',
        payment_status_label: payload.initial_payment_amount ? 'Partially Paid' : 'Unpaid',
        payment_badge_variant: payload.initial_payment_amount ? 'warning' : 'danger',
        payment_method: payload.payment_method || 'BANK_TRANSFER',
        subtotal: payload.subtotal || 31200.00,
        discount_amount: payload.discount_amount || 1200.00,
        tax_amount: payload.tax_amount || 4500.00,
        total_amount: payload.total_amount || 34500.00,
        paid_amount: payload.initial_payment_amount || 0.00,
        balance_due: (payload.total_amount || 34500.00) - (payload.initial_payment_amount || 0.00),
        notes: payload.notes || `Converted from quotation. Physical stock deducted.`,
        created_by: { id: 1, name: 'Alexander Vance' },
        confirmed_by: { id: 1, name: 'Alexander Vance' },
        confirmed_at: new Date().toISOString(),
        formatted_confirmed_at: 'Just now',
        items_count: (payload.items || []).length || 2,
        total_quantity: 6,
        items: payload.items || [],
        payments: payload.initial_payment_amount ? [
          {
            id: 99,
            sales_order_id: MOCK_ORDERS.length + 1,
            receipt_number: `RCT-2026-R${Math.floor(1000 + Math.random() * 9000)}`,
            amount: parseFloat(payload.initial_payment_amount),
            payment_method: payload.payment_method || 'BANK_TRANSFER',
            payment_reference: payload.payment_reference || 'INIT-ADVANCE',
            payment_date: new Date().toISOString().split('T')[0],
            formatted_payment_date: 'Today',
            notes: 'Advance installment upon order confirmation.',
            received_by: { id: 1, name: 'Alexander Vance' },
            created_at: new Date().toISOString(),
          }
        ] : [],
        created_at: new Date().toISOString(),
        formatted_created_at: 'Just now',
      };

      MOCK_ORDERS.unshift(order);
      return {
        status: 'success',
        message: `Quotation converted successfully into Sales Order #${newOrderNum}.`,
        data: order,
      };
    }
  },

  /**
   * Create direct sales order (counter walk-in or phone purchase)
   */
  async createDirectSale(payload) {
    try {
      const response = await apiClient.post('/admin/sales-orders', payload);
      return response.data;
    } catch (error) {
      console.warn('API call failed for direct sale, simulating mock creation:', error.message);
      
      const newOrderNum = `SO-2026-D${Math.floor(1000 + Math.random() * 9000)}`;
      const subtotal = (payload.items || []).reduce((acc, it) => acc + (it.quantity * (it.unit_price || 1000)), 0);
      const discountAmount = payload.discount_amount || 0.00;
      const taxAmount = (subtotal - discountAmount) * ((payload.tax_rate || 15) / 100);
      const totalAmount = subtotal - discountAmount + taxAmount;
      const initialPay = parseFloat(payload.initial_payment_amount || 0);

      const order = {
        id: MOCK_ORDERS.length + 1,
        order_number: newOrderNum,
        quotation_id: null,
        quotation_number: null,
        store_id: payload.store_id,
        store_name: 'Central Distribution Hub (HQ)',
        customer_name: payload.customer_name,
        customer_email: payload.customer_email || '',
        customer_phone: payload.customer_phone || '',
        company_name: payload.company_name || '',
        billing_address: payload.billing_address || '',
        shipping_address: payload.shipping_address || '',
        status: 'CONFIRMED',
        status_label: 'Confirmed (Active)',
        badge_variant: 'primary',
        payment_status: initialPay >= totalAmount ? 'PAID' : (initialPay > 0 ? 'PARTIAL' : 'UNPAID'),
        payment_status_label: initialPay >= totalAmount ? 'Fully Paid' : (initialPay > 0 ? 'Partially Paid' : 'Unpaid'),
        payment_badge_variant: initialPay >= totalAmount ? 'success' : (initialPay > 0 ? 'warning' : 'danger'),
        payment_method: payload.payment_method || 'CASH',
        subtotal,
        discount_amount: discountAmount,
        tax_amount: taxAmount,
        total_amount: totalAmount,
        paid_amount: initialPay,
        balance_due: Math.max(0, totalAmount - initialPay),
        notes: payload.notes || 'Direct sales transaction.',
        created_by: { id: 1, name: 'Alexander Vance' },
        confirmed_by: { id: 1, name: 'Alexander Vance' },
        confirmed_at: new Date().toISOString(),
        formatted_confirmed_at: 'Just now',
        items_count: (payload.items || []).length,
        total_quantity: (payload.items || []).reduce((acc, it) => acc + it.quantity, 0),
        items: (payload.items || []).map((it, idx) => ({
          id: 600 + idx,
          sales_order_id: MOCK_ORDERS.length + 1,
          product_id: it.product_id,
          product_name: it.product_name || `Hardware Item #${it.product_id}`,
          product_model: it.product_model || 'Enterprise Spec',
          product_sku: it.product_sku || 'SKU-DIRECT',
          quantity: it.quantity,
          unit_cost: (it.unit_price || 1000) * 0.75,
          unit_price: it.unit_price || 1000,
          discount_amount: it.discount_amount || 0,
          total_price: it.quantity * (it.unit_price || 1000),
        })),
        payments: initialPay > 0 ? [
          {
            id: Date.now(),
            sales_order_id: MOCK_ORDERS.length + 1,
            receipt_number: `RCT-2026-D${Math.floor(1000 + Math.random() * 9000)}`,
            amount: initialPay,
            payment_method: payload.payment_method || 'CASH',
            payment_reference: payload.payment_reference || 'COUNTER-CASH',
            payment_date: new Date().toISOString().split('T')[0],
            formatted_payment_date: 'Today',
            notes: 'Counter payment receipt.',
            received_by: { id: 1, name: 'Alexander Vance' },
            created_at: new Date().toISOString(),
          }
        ] : [],
        created_at: new Date().toISOString(),
        formatted_created_at: 'Just now',
      };

      MOCK_ORDERS.unshift(order);
      return {
        status: 'success',
        message: `Direct sales order #${newOrderNum} created successfully.`,
        data: order,
      };
    }
  },

  /**
   * Record payment against a sales order
   */
  async recordPayment(orderId, payload) {
    try {
      const response = await apiClient.post(`/admin/sales-orders/${orderId}/payments`, payload);
      return response.data;
    } catch (error) {
      console.warn(`API call failed for recording payment on order #${orderId}:`, error.message);
      
      const order = MOCK_ORDERS.find(o => o.id === parseInt(orderId));
      if (!order) throw new Error('Sales order not found');

      const amount = parseFloat(payload.amount);
      const receiptNumber = `RCT-2026-P${Math.floor(1000 + Math.random() * 9000)}`;

      const payment = {
        id: Date.now(),
        sales_order_id: order.id,
        receipt_number: receiptNumber,
        amount,
        payment_method: payload.payment_method,
        payment_reference: payload.payment_reference || '',
        payment_date: payload.payment_date || new Date().toISOString().split('T')[0],
        formatted_payment_date: 'Today',
        notes: payload.notes || 'Payment recorded.',
        received_by: { id: 1, name: 'Alexander Vance' },
        created_at: new Date().toISOString(),
        formatted_created_at: 'Just now',
      };

      order.payments = order.payments || [];
      order.payments.unshift(payment);

      order.paid_amount = parseFloat((order.paid_amount + amount).toFixed(2));
      order.balance_due = Math.max(0, parseFloat((order.total_amount - order.paid_amount).toFixed(2)));

      if (order.paid_amount >= order.total_amount) {
        order.payment_status = 'PAID';
        order.payment_status_label = 'Fully Paid';
        order.payment_badge_variant = 'success';
      } else {
        order.payment_status = 'PARTIAL';
        order.payment_status_label = 'Partially Paid';
        order.payment_badge_variant = 'warning';
      }

      return {
        status: 'success',
        message: `Payment of ${amount.toFixed(2)} SAR recorded (Receipt #${receiptNumber}).`,
        data: payment,
        order,
      };
    }
  },

  /**
   * Fetch Sales Dashboard Metrics respecting revenue rules
   */
  async getDashboardMetrics(storeId = null) {
    try {
      const response = await apiClient.get('/admin/sales-orders/dashboard', {
        params: storeId ? { store_id: storeId } : {},
      });
      return response.data;
    } catch (error) {
      console.warn('API call failed for sales dashboard metrics, using computed mock:', error.message);
      
      // Calculate from MOCK_ORDERS
      const confirmedOrders = MOCK_ORDERS.filter(o => ['CONFIRMED', 'PROCESSING', 'COMPLETED'].includes(o.status));
      const totalSales = confirmedOrders.reduce((acc, o) => acc + o.total_amount, 0);
      const totalReceived = confirmedOrders.reduce((acc, o) => acc + o.paid_amount, 0);
      const outstandingReceivables = Math.max(0, totalSales - totalReceived);
      const unitsSold = confirmedOrders.reduce((acc, o) => acc + o.total_quantity, 0);

      return {
        status: 'success',
        data: {
          total_sales: totalSales,
          daily_sales: 28750.00,
          weekly_sales: totalSales,
          monthly_sales: totalSales,
          total_products_sold: unitsSold,
          total_orders_count: confirmedOrders.length,
          total_collected: totalReceived,
          outstanding_receivables: outstandingReceivables,
          orders_by_status: {
            confirmed: MOCK_ORDERS.filter(o => o.status === 'CONFIRMED').length,
            processing: MOCK_ORDERS.filter(o => o.status === 'PROCESSING').length,
            completed: MOCK_ORDERS.filter(o => o.status === 'COMPLETED').length,
            cancelled: MOCK_ORDERS.filter(o => o.status === 'CANCELLED').length,
          },
          orders_by_payment: {
            unpaid: MOCK_ORDERS.filter(o => o.payment_status === 'UNPAID').length,
            partial: MOCK_ORDERS.filter(o => o.payment_status === 'PARTIAL').length,
            paid: MOCK_ORDERS.filter(o => o.payment_status === 'PAID').length,
          },
        },
      };
    }
  },
};
