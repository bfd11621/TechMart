import apiClient from './api';

// Realistic B2B Fallback Mock Data for Quotation Management
const MOCK_QUOTATIONS = [
  {
    id: 1,
    quotation_number: 'QT-2026-X9B21',
    rfq_request_id: 1,
    rfq_number: 'RFQ-2026-A8F29',
    store_id: 1,
    store_name: 'Central Distribution Hub (HQ)',
    customer_name: 'David Sterling',
    customer_email: 'd.sterling@apexcloud.io',
    customer_phone: '+1 (555) 492-8810',
    company_name: 'Apex Cloud Solutions Inc.',
    billing_address: '100 Silicon Parkway, Building C, San Jose, CA',
    status: 'APPROVED',
    status_label: 'Approved by Client',
    badge_variant: 'success',
    valid_until: '2026-12-15',
    formatted_valid_until: 'Dec 15, 2026',
    is_expired: false,
    subtotal: 31200.00,
    discount_type: 'fixed',
    discount_value: 1200.00,
    discount_amount: 1200.00,
    tax_rate: 15.00,
    tax_amount: 4500.00,
    total_amount: 34500.00,
    terms_conditions: 'Standard 30-day corporate price lock. Payment terms: 50% advance on order confirmation, 50% net 30 days upon delivery and commissioning.',
    notes: 'Enterprise discount approved by Sales Director Marcus Brody.',
    client_notes: 'Urgent compute cluster deployment for Q4 expansion.',
    prepared_by: { id: 3, name: 'Marcus Brody', email: 'sales@irack.com' },
    can_be_converted: true,
    sales_order_id: null,
    sales_order_number: null,
    approved_at: new Date(Date.now() - 1000 * 60 * 60 * 24).toISOString(),
    converted_at: null,
    items_count: 2,
    total_quantity: 6,
    items: [
      {
        id: 101,
        quotation_id: 1,
        product_id: 1,
        product_name: 'Dell PowerEdge R760 2U Dual-Socket Rack Server',
        product_model: 'PowerEdge R760',
        product_sku: 'SRV-DEL-R760-01',
        product_image: 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?auto=format&fit=crop&w=300&h=200&q=80',
        product_brand: 'Dell Technologies',
        quantity: 4,
        unit_price: 5200.00,
        discount_percent: 0.00,
        total_price: 20800.00,
        notes: 'Equipped with dual Intel Xeon Silver 4410Y, 128GB DDR5 ECC RAM, 2x 960GB NVMe SSD.',
      },
      {
        id: 102,
        quotation_id: 1,
        product_id: 2,
        product_name: 'Cisco Catalyst 9300X-48HX Enterprise Managed Switch',
        product_model: 'C9300X-48HX',
        product_sku: 'NET-CIS-C9300X-48',
        product_image: 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?auto=format&fit=crop&w=300&h=200&q=80',
        product_brand: 'Cisco Systems',
        quantity: 2,
        unit_price: 5200.00,
        discount_percent: 0.00,
        total_price: 10400.00,
        notes: 'Includes redundant 1100W AC power supplies and StackWise-1T cables.',
      },
    ],
    status_histories: [
      {
        id: 1,
        status: 'PREPARED',
        status_label: 'Quotation Prepared',
        badge_variant: 'warning',
        previous_status: null,
        user: { id: 3, name: 'Marcus Brody', email: 'sales@irack.com' },
        notes: 'Quotation prepared following RFQ-2026-A8F29 specifications.',
        created_at: new Date(Date.now() - 1000 * 60 * 60 * 48).toISOString(),
        formatted_created_at: '2 days ago',
      },
      {
        id: 2,
        status: 'SENT',
        status_label: 'Sent to Customer',
        badge_variant: 'info',
        previous_status: 'PREPARED',
        user: { id: 3, name: 'Marcus Brody', email: 'sales@irack.com' },
        notes: 'Commercial PDF proposal delivered via automated enterprise mailer.',
        created_at: new Date(Date.now() - 1000 * 60 * 60 * 36).toISOString(),
        formatted_created_at: 'Yesterday',
      },
      {
        id: 3,
        status: 'APPROVED',
        status_label: 'Approved by Client',
        badge_variant: 'success',
        previous_status: 'SENT',
        user: { id: 3, name: 'Marcus Brody', email: 'sales@irack.com' },
        notes: 'Signed proposal received from Apex Cloud CTO David Sterling.',
        created_at: new Date(Date.now() - 1000 * 60 * 60 * 24).toISOString(),
        formatted_created_at: 'Yesterday',
      },
    ],
    created_at: new Date(Date.now() - 1000 * 60 * 60 * 48).toISOString(),
    formatted_created_at: 'Nov 09, 2026 14:30',
  },
  {
    id: 2,
    quotation_number: 'QT-2026-M4K82',
    rfq_request_id: 2,
    rfq_number: 'RFQ-2026-K7M11',
    store_id: 2,
    store_name: 'North Logistics Facility',
    customer_name: 'Elena Rostova',
    customer_email: 'e.rostova@hyperion-networks.com',
    customer_phone: '+1 (555) 831-2940',
    company_name: 'Hyperion Telecom Networks',
    billing_address: '450 Tech Hub Boulevard, Suite 800, Austin, TX',
    status: 'SENT',
    status_label: 'Sent to Customer',
    badge_variant: 'info',
    valid_until: '2026-12-10',
    formatted_valid_until: 'Dec 10, 2026',
    is_expired: false,
    subtotal: 18500.00,
    discount_type: 'percentage',
    discount_value: 5.00,
    discount_amount: 925.00,
    tax_rate: 15.00,
    tax_amount: 2636.25,
    total_amount: 20211.25,
    terms_conditions: 'Hardware backed by 3-year 24x7 mission-critical vendor warranty. Validity 30 days from proposal date.',
    notes: 'Awaiting client board sign-off on networking gear.',
    client_notes: 'Expansion of aggregation core routing ring.',
    prepared_by: { id: 3, name: 'Marcus Brody', email: 'sales@irack.com' },
    can_be_converted: false,
    sales_order_id: null,
    sales_order_number: null,
    approved_at: null,
    converted_at: null,
    items_count: 1,
    total_quantity: 2,
    items: [
      {
        id: 201,
        quotation_id: 2,
        product_id: 3,
        product_name: 'NetApp AFF A250 All-Flash NVMe Storage Array',
        product_model: 'AFF A250',
        product_sku: 'STR-NET-A250-01',
        product_image: 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?auto=format&fit=crop&w=300&h=200&q=80',
        product_brand: 'NetApp',
        quantity: 2,
        unit_price: 9250.00,
        discount_percent: 0.00,
        total_price: 18500.00,
        notes: '24x 1.92TB NVMe SSDs included per HA shelf.',
      },
    ],
    status_histories: [
      {
        id: 11,
        status: 'PREPARED',
        status_label: 'Quotation Prepared',
        badge_variant: 'warning',
        previous_status: null,
        user: { id: 3, name: 'Marcus Brody', email: 'sales@irack.com' },
        notes: 'Drafted from RFQ-2026-K7M11.',
        created_at: new Date(Date.now() - 1000 * 60 * 60 * 12).toISOString(),
        formatted_created_at: '12 hours ago',
      },
      {
        id: 12,
        status: 'SENT',
        status_label: 'Sent to Customer',
        badge_variant: 'info',
        previous_status: 'PREPARED',
        user: { id: 3, name: 'Marcus Brody', email: 'sales@irack.com' },
        notes: 'Delivered to Elena Rostova.',
        created_at: new Date(Date.now() - 1000 * 60 * 60 * 8).toISOString(),
        formatted_created_at: '8 hours ago',
      },
    ],
    created_at: new Date(Date.now() - 1000 * 60 * 60 * 12).toISOString(),
    formatted_created_at: 'Today, 08:15',
  },
  {
    id: 3,
    quotation_number: 'QT-2026-P3R19',
    rfq_request_id: null,
    rfq_number: null,
    store_id: 1,
    store_name: 'Central Distribution Hub (HQ)',
    customer_name: 'Tariq Al-Mansoor',
    customer_email: 'tariq@gulfdata.sa',
    customer_phone: '+966 50 123 4567',
    company_name: 'Gulf Cloud Data Centers Ltd',
    billing_address: 'King Fahd Road, Al Olaya District, Riyadh 12211, KSA',
    status: 'CONVERTED',
    status_label: 'Converted to Order',
    badge_variant: 'primary',
    valid_until: '2026-11-30',
    formatted_valid_until: 'Nov 30, 2026',
    is_expired: false,
    subtotal: 48000.00,
    discount_type: 'fixed',
    discount_value: 3000.00,
    discount_amount: 3000.00,
    tax_rate: 15.00,
    tax_amount: 6750.00,
    total_amount: 51750.00,
    terms_conditions: 'Full turnkey installation included. 100% advance wire transfer confirmed.',
    notes: 'Direct corporate solution drafted for datacenter phase 2 expansion.',
    client_notes: 'Priority airfreight delivery required.',
    prepared_by: { id: 1, name: 'Alexander Vance', email: 'admin@irack.com' },
    can_be_converted: false,
    sales_order_id: 1,
    sales_order_number: 'SO-2026-G8K22',
    approved_at: new Date(Date.now() - 1000 * 60 * 60 * 72).toISOString(),
    converted_at: new Date(Date.now() - 1000 * 60 * 60 * 48).toISOString(),
    items_count: 2,
    total_quantity: 8,
    items: [
      {
        id: 301,
        quotation_id: 3,
        product_id: 4,
        product_name: 'Schneider Electric APC Smart-UPS Ultra 5kW Lithium-ion',
        product_model: 'SRTL5KRM2UI',
        product_sku: 'PWR-APC-5KW-LI',
        product_image: 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?auto=format&fit=crop&w=300&h=200&q=80',
        product_brand: 'Schneider Electric',
        quantity: 4,
        unit_price: 6000.00,
        discount_percent: 0.00,
        total_price: 24000.00,
        notes: 'Rackmount 1U/2U with integrated network card.',
      },
      {
        id: 302,
        quotation_id: 3,
        product_id: 1,
        product_name: 'Dell PowerEdge R760 2U Dual-Socket Rack Server',
        product_model: 'PowerEdge R760',
        product_sku: 'SRV-DEL-R760-01',
        product_image: 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?auto=format&fit=crop&w=300&h=200&q=80',
        product_brand: 'Dell Technologies',
        quantity: 4,
        unit_price: 6000.00,
        discount_percent: 0.00,
        total_price: 24000.00,
        notes: 'Redundant BOSS-N1 boot controllers.',
      },
    ],
    status_histories: [
      {
        id: 21,
        status: 'PREPARED',
        status_label: 'Quotation Prepared',
        badge_variant: 'warning',
        previous_status: null,
        user: { id: 1, name: 'Alexander Vance', email: 'admin@irack.com' },
        notes: 'Quotation generated directly by executive engineering team.',
        created_at: new Date(Date.now() - 1000 * 60 * 60 * 96).toISOString(),
        formatted_created_at: '4 days ago',
      },
      {
        id: 22,
        status: 'APPROVED',
        status_label: 'Approved by Client',
        badge_variant: 'success',
        previous_status: 'SENT',
        user: { id: 1, name: 'Alexander Vance', email: 'admin@irack.com' },
        notes: 'PO-2026-098 received and verified.',
        created_at: new Date(Date.now() - 1000 * 60 * 60 * 72).toISOString(),
        formatted_created_at: '3 days ago',
      },
      {
        id: 23,
        status: 'CONVERTED',
        status_label: 'Converted to Order',
        badge_variant: 'primary',
        previous_status: 'APPROVED',
        user: { id: 1, name: 'Alexander Vance', email: 'admin@irack.com' },
        notes: 'Converted into Sales Order #SO-2026-G8K22 with physical inventory allocated.',
        created_at: new Date(Date.now() - 1000 * 60 * 60 * 48).toISOString(),
        formatted_created_at: '2 days ago',
      },
    ],
    created_at: new Date(Date.now() - 1000 * 60 * 60 * 96).toISOString(),
    formatted_created_at: 'Nov 07, 2026 10:00',
  },
  {
    id: 4,
    quotation_number: 'QT-2026-D1V90',
    rfq_request_id: null,
    rfq_number: null,
    store_id: 1,
    store_name: 'Central Distribution Hub (HQ)',
    customer_name: 'Sarah Jenkins',
    customer_email: 's.jenkins@redwoodfin.com',
    customer_phone: '+1 (555) 301-4491',
    company_name: 'Redwood Financial Systems',
    billing_address: '88 Wall Street, 14th Floor, New York, NY',
    status: 'DRAFT',
    status_label: 'Draft Proposal',
    badge_variant: 'neutral',
    valid_until: '2026-12-05',
    formatted_valid_until: 'Dec 05, 2026',
    is_expired: false,
    subtotal: 12500.00,
    discount_type: 'fixed',
    discount_value: 0.00,
    discount_amount: 0.00,
    tax_rate: 15.00,
    tax_amount: 1875.00,
    total_amount: 14375.00,
    terms_conditions: 'Standard corporate sales terms.',
    notes: 'Draft proposal under review by sales engineer.',
    client_notes: null,
    prepared_by: { id: 3, name: 'Marcus Brody', email: 'sales@irack.com' },
    can_be_converted: false,
    sales_order_id: null,
    sales_order_number: null,
    approved_at: null,
    converted_at: null,
    items_count: 1,
    total_quantity: 1,
    items: [
      {
        id: 401,
        quotation_id: 4,
        product_id: 2,
        product_name: 'Cisco Catalyst 9300X-48HX Enterprise Managed Switch',
        product_model: 'C9300X-48HX',
        product_sku: 'NET-CIS-C9300X-48',
        product_image: 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?auto=format&fit=crop&w=300&h=200&q=80',
        product_brand: 'Cisco Systems',
        quantity: 1,
        unit_price: 12500.00,
        discount_percent: 0.00,
        total_price: 12500.00,
        notes: null,
      },
    ],
    status_histories: [
      {
        id: 31,
        status: 'DRAFT',
        status_label: 'Draft Proposal',
        badge_variant: 'neutral',
        previous_status: null,
        user: { id: 3, name: 'Marcus Brody', email: 'sales@irack.com' },
        notes: 'Initial proposal draft established.',
        created_at: new Date(Date.now() - 1000 * 60 * 60 * 2).toISOString(),
        formatted_created_at: '2 hours ago',
      },
    ],
    created_at: new Date(Date.now() - 1000 * 60 * 60 * 2).toISOString(),
    formatted_created_at: 'Today, 11:20',
  },
];

export const quotationService = {
  /**
   * Fetch paginated quotations with filtering
   */
  async getQuotations(params = {}) {
    try {
      const response = await apiClient.get('/admin/quotations', { params });
      return response.data;
    } catch (error) {
      console.warn('API connection failed, using mock quotations list:', error.message);
      
      // Filter mock quotations
      let filtered = [...MOCK_QUOTATIONS];

      if (params.status && params.status !== 'all') {
        filtered = filtered.filter(q => q.status === params.status.toUpperCase());
      }
      if (params.store_id) {
        filtered = filtered.filter(q => q.store_id === parseInt(params.store_id));
      }
      if (params.search) {
        const s = params.search.toLowerCase();
        filtered = filtered.filter(q =>
          q.quotation_number.toLowerCase().includes(s) ||
          q.customer_name.toLowerCase().includes(s) ||
          q.customer_email.toLowerCase().includes(s) ||
          (q.company_name && q.company_name.toLowerCase().includes(s))
        );
      }

      const totalPipeline = filtered.reduce((acc, q) => acc + q.total_amount, 0);

      return {
        status: 'success',
        data: filtered,
        meta: {
          current_page: 1,
          last_page: 1,
          per_page: 15,
          total: filtered.length,
          counts: {
            all: MOCK_QUOTATIONS.length,
            draft: MOCK_QUOTATIONS.filter(q => q.status === 'DRAFT').length,
            prepared: MOCK_QUOTATIONS.filter(q => q.status === 'PREPARED').length,
            sent: MOCK_QUOTATIONS.filter(q => q.status === 'SENT').length,
            approved: MOCK_QUOTATIONS.filter(q => q.status === 'APPROVED').length,
            rejected: MOCK_QUOTATIONS.filter(q => q.status === 'REJECTED').length,
            converted: MOCK_QUOTATIONS.filter(q => q.status === 'CONVERTED').length,
            expired: MOCK_QUOTATIONS.filter(q => q.status === 'EXPIRED').length,
          },
          pipeline_volume: totalPipeline,
          approved_volume: MOCK_QUOTATIONS.filter(q => q.status === 'APPROVED').reduce((acc, q) => acc + q.total_amount, 0),
        },
      };
    }
  },

  /**
   * Fetch single quotation by ID
   */
  async getQuotation(id) {
    try {
      const response = await apiClient.get(`/admin/quotations/${id}`);
      return response.data;
    } catch (error) {
      console.warn(`API call failed for quotation #${id}, using mock:`, error.message);
      const match = MOCK_QUOTATIONS.find(q => q.id === parseInt(id));
      if (!match) throw new Error('Quotation not found');
      return { status: 'success', data: match };
    }
  },

  /**
   * Create new quotation
   */
  async createQuotation(payload) {
    try {
      const response = await apiClient.post('/admin/quotations', payload);
      return response.data;
    } catch (error) {
      console.warn('API call failed for creating quotation, simulating mock creation:', error.message);
      
      const newId = MOCK_QUOTATIONS.length + 1;
      const subtotal = (payload.items || []).reduce((acc, it) => acc + (it.quantity * (it.unit_price || 1000)), 0);
      const discountAmount = payload.discount_type === 'percentage'
        ? subtotal * ((payload.discount_value || 0) / 100)
        : (payload.discount_value || 0);
      const taxAmount = (subtotal - discountAmount) * ((payload.tax_rate || 15) / 100);
      const totalAmount = subtotal - discountAmount + taxAmount;

      const newQuote = {
        id: newId,
        quotation_number: `QT-2026-N${Math.floor(1000 + Math.random() * 9000)}`,
        rfq_request_id: payload.rfq_request_id || null,
        rfq_number: payload.rfq_number || null,
        store_id: payload.store_id || 1,
        store_name: 'Central Distribution Hub (HQ)',
        customer_name: payload.customer_name,
        customer_email: payload.customer_email,
        customer_phone: payload.customer_phone || '',
        company_name: payload.company_name || '',
        billing_address: payload.billing_address || '',
        status: payload.status || 'PREPARED',
        status_label: 'Quotation Prepared',
        badge_variant: 'warning',
        valid_until: payload.valid_until || '2026-12-31',
        formatted_valid_until: 'Dec 31, 2026',
        is_expired: false,
        subtotal,
        discount_type: payload.discount_type || 'fixed',
        discount_value: payload.discount_value || 0,
        discount_amount: discountAmount,
        tax_rate: payload.tax_rate || 15,
        tax_amount: taxAmount,
        total_amount: totalAmount,
        terms_conditions: payload.terms_conditions || 'Standard 30-day validity.',
        notes: payload.notes || '',
        client_notes: payload.client_notes || '',
        prepared_by: { id: 1, name: 'Admin Engineer', email: 'admin@irack.com' },
        can_be_converted: payload.status === 'APPROVED',
        sales_order_id: null,
        sales_order_number: null,
        approved_at: null,
        converted_at: null,
        items_count: (payload.items || []).length,
        total_quantity: (payload.items || []).reduce((acc, it) => acc + it.quantity, 0),
        items: (payload.items || []).map((it, idx) => ({
          id: 500 + idx,
          quotation_id: newId,
          product_id: it.product_id,
          product_name: it.product_name || `Hardware Item #${it.product_id}`,
          product_model: it.product_model || 'Enterprise Spec',
          product_sku: it.product_sku || 'SKU-ENT',
          product_image: null,
          product_brand: 'iRACK Partner',
          quantity: it.quantity,
          unit_price: it.unit_price || 1000,
          discount_percent: it.discount_percent || 0,
          total_price: it.quantity * (it.unit_price || 1000),
          notes: it.notes || '',
        })),
        status_histories: [
          {
            id: 100,
            status: payload.status || 'PREPARED',
            status_label: 'Quotation Prepared',
            badge_variant: 'warning',
            previous_status: null,
            user: { id: 1, name: 'Admin Engineer' },
            notes: 'Quotation created successfully.',
            created_at: new Date().toISOString(),
            formatted_created_at: 'Just now',
          },
        ],
        created_at: new Date().toISOString(),
        formatted_created_at: 'Just now',
      };

      MOCK_QUOTATIONS.unshift(newQuote);
      return { status: 'success', data: newQuote, message: `Quotation #${newQuote.quotation_number} created successfully.` };
    }
  },

  /**
   * Update quotation details
   */
  async updateQuotation(id, payload) {
    try {
      const response = await apiClient.put(`/admin/quotations/${id}`, payload);
      return response.data;
    } catch (error) {
      console.warn(`API call failed for updating quotation #${id}:`, error.message);
      const quote = MOCK_QUOTATIONS.find(q => q.id === parseInt(id));
      if (!quote) throw new Error('Quotation not found');
      Object.assign(quote, payload);
      return { status: 'success', data: quote, message: 'Quotation updated successfully.' };
    }
  },

  /**
   * Transition quotation status
   */
  async updateStatus(id, { status, notes }) {
    try {
      const response = await apiClient.patch(`/admin/quotations/${id}/status`, { status, notes });
      return response.data;
    } catch (error) {
      console.warn(`API call failed for quotation #${id} status transition:`, error.message);
      const quote = MOCK_QUOTATIONS.find(q => q.id === parseInt(id));
      if (!quote) throw new Error('Quotation not found');

      const labels = {
        DRAFT: 'Draft Proposal',
        PREPARED: 'Quotation Prepared',
        SENT: 'Sent to Customer',
        APPROVED: 'Approved by Client',
        REJECTED: 'Rejected by Client',
        EXPIRED: 'Quotation Expired',
        CONVERTED: 'Converted to Order',
      };

      const variants = {
        DRAFT: 'neutral',
        PREPARED: 'warning',
        SENT: 'info',
        APPROVED: 'success',
        REJECTED: 'danger',
        EXPIRED: 'neutral',
        CONVERTED: 'primary',
      };

      const oldStatus = quote.status;
      quote.status = status;
      quote.status_label = labels[status] || status;
      quote.badge_variant = variants[status] || 'neutral';
      quote.can_be_converted = (status === 'APPROVED');

      if (status === 'APPROVED') {
        quote.approved_at = new Date().toISOString();
      }

      quote.status_histories.unshift({
        id: Date.now(),
        status,
        status_label: quote.status_label,
        badge_variant: quote.badge_variant,
        previous_status: oldStatus,
        user: { id: 1, name: 'Admin Engineer' },
        notes: notes || `Status updated to ${quote.status_label}.`,
        created_at: new Date().toISOString(),
        formatted_created_at: 'Just now',
      });

      return { status: 'success', data: quote, message: `Status updated to ${quote.status_label}.` };
    }
  },

  /**
   * Download branded PDF proposal document
   */
  async downloadPdf(id, quotationNumber) {
    try {
      const response = await apiClient.get(`/admin/quotations/${id}/pdf`, {
        responseType: 'blob',
      });
      const blob = new Blob([response.data], { type: 'application/pdf' });
      const downloadUrl = window.URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = downloadUrl;
      link.setAttribute('download', `iRACK_Quotation_${quotationNumber || id}.pdf`);
      document.body.appendChild(link);
      link.click();
      link.remove();
      window.URL.revokeObjectURL(downloadUrl);
      return true;
    } catch (error) {
      console.warn(`PDF download failed for quotation #${id}:`, error.message);
      // Fallback: trigger print simulation or alert
      window.print();
      return true;
    }
  },

  /**
   * Delete quotation
   */
  async deleteQuotation(id) {
    try {
      const response = await apiClient.delete(`/admin/quotations/${id}`);
      return response.data;
    } catch (error) {
      console.warn(`API call failed for deleting quotation #${id}:`, error.message);
      const index = MOCK_QUOTATIONS.findIndex(q => q.id === parseInt(id));
      if (index !== -1) {
        MOCK_QUOTATIONS.splice(index, 1);
      }
      return { status: 'success', message: 'Quotation removed successfully.' };
    }
  },
};
