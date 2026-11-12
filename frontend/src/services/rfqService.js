import apiClient from './api';

// Demonstration fallback mock data for testing/previewing RFQ management
const MOCK_RFQS = [
  {
    id: 1,
    rfq_number: 'RFQ-2026-A8F29',
    customer_name: 'David Sterling',
    customer_email: 'd.sterling@apexcloud.io',
    customer_phone: '+1 (555) 492-8810',
    company_name: 'Apex Cloud Solutions Inc.',
    location: '100 Silicon Parkway, Building C, San Jose, CA',
    city: 'San Jose',
    address: '100 Silicon Parkway, Building C',
    status: 'NEW_REQUEST',
    status_label: 'New Request',
    badge_variant: 'primary',
    customer_notes: 'Urgent expansion for our primary compute cluster. Need dual 25GbE optical transceivers included in the quote.',
    admin_notes: 'Preliminary stock check completed at Central HQ. Sufficient compute nodes available.',
    preferred_store: { id: 1, name: 'Central Distribution Hub (HQ)', code: 'ST-HQ-01' },
    assigned_user: { id: 3, name: 'Marcus Brody (Sales Mgr)', email: 'sales@irack.com' },
    items_count: 2,
    total_quantity: 6,
    items: [
      {
        id: 101,
        product_id: 1,
        product_name: 'Dell PowerEdge R760 2U Dual-Socket Rack Server',
        product_sku: 'SRV-DEL-R760-01',
        model_number: 'PowerEdge R760',
        product_slug: 'dell-poweredge-r760-2u-rack-server',
        primary_image_url: 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?auto=format&fit=crop&w=300&h=200&q=80',
        brand_name: 'Dell Technologies',
        category_name: 'Servers and Workstations',
        quantity: 4,
        target_price: 5200.00,
        notes: 'Configure with 128GB DDR5 and dual 1400W PSUs',
      },
      {
        id: 102,
        product_id: 2,
        product_name: 'Cisco Catalyst 9300X-48HX Enterprise Managed Switch',
        product_sku: 'NET-CIS-C9300X-48',
        model_number: 'C9300X-48HX',
        product_slug: 'cisco-catalyst-9300x-48hx-managed-switch',
        primary_image_url: 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?auto=format&fit=crop&w=300&h=200&q=80',
        brand_name: 'Cisco Systems',
        category_name: 'Networking Equipment',
        quantity: 2,
        target_price: null,
        notes: 'Include StackWise-1T cables',
      },
    ],
    status_histories: [
      {
        id: 1,
        status: 'NEW_REQUEST',
        status_label: 'New Request',
        badge_variant: 'primary',
        previous_status: null,
        user_name: 'Customer Web Submission',
        notes: 'Submitted via Online Showroom RFQ Basket',
        created_at: new Date(Date.now() - 1000 * 60 * 45).toISOString(),
        formatted_date: 'Today, 45 mins ago',
      },
    ],
    submitted_at: new Date(Date.now() - 1000 * 60 * 45).toISOString(),
    formatted_submitted_at: 'Today, 45 mins ago',
  },
  {
    id: 2,
    rfq_number: 'RFQ-2026-C4B71',
    customer_name: 'Elena Rostova',
    customer_email: 'elena@novafinance.com',
    customer_phone: '+1 (555) 301-9922',
    company_name: 'Nova Financial Technologies',
    location: '450 Financial Way, Floor 18, San Francisco, CA',
    city: 'San Francisco',
    address: '450 Financial Way, Floor 18',
    status: 'UNDER_REVIEW',
    status_label: 'Under Review',
    badge_variant: 'warning',
    customer_notes: 'Requirements for new disaster recovery site. Need backup battery runtime calculation for 45 minutes.',
    admin_notes: 'Assigned to Power Solutions engineering specialist.',
    preferred_store: { id: 2, name: 'Downtown Commercial Center', code: 'ST-DOWNTOWN-02' },
    assigned_user: { id: 3, name: 'Marcus Brody', email: 'sales@irack.com' },
    items_count: 2,
    total_quantity: 4,
    items: [
      {
        id: 103,
        product_id: 4,
        product_name: 'APC Smart-UPS On-Line SRT 5000VA 230V 3U Rack/Tower',
        product_sku: 'PWR-APC-SRT5KRMXLI-01',
        model_number: 'SRT5KRMXLI',
        product_slug: 'apc-smart-ups-online-srt-5000va-3u',
        primary_image_url: 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?auto=format&fit=crop&w=300&h=200&q=80',
        brand_name: 'Schneider Electric (APC)',
        category_name: 'Power Solutions',
        quantity: 2,
        target_price: 3800.00,
        notes: 'Requires NMC AP9641 network management cards',
      },
      {
        id: 104,
        product_id: 5,
        product_name: 'iRACK Series-X 42U Heavy-Duty Datacenter Server Rack Cabinet',
        product_sku: 'DC-IRK-42U-1070B',
        model_number: 'iRACK-42U-X1070',
        product_slug: 'irack-series-x-42u-datacenter-server-cabinet',
        primary_image_url: 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?auto=format&fit=crop&w=300&h=200&q=80',
        brand_name: 'Dell Technologies',
        category_name: 'Data Center Infrastructure',
        quantity: 2,
        target_price: null,
        notes: 'Include vertical 0U cable management brackets',
      },
    ],
    status_histories: [
      {
        id: 2,
        status: 'UNDER_REVIEW',
        status_label: 'Under Review',
        badge_variant: 'warning',
        previous_status: 'NEW_REQUEST',
        user_name: 'Marcus Brody (Sales Mgr)',
        notes: 'Assigned to power solution engineer for runtime calculation.',
        created_at: new Date(Date.now() - 1000 * 60 * 180).toISOString(),
        formatted_date: 'Today, 3 hours ago',
      },
      {
        id: 3,
        status: 'NEW_REQUEST',
        status_label: 'New Request',
        badge_variant: 'primary',
        previous_status: null,
        user_name: 'Customer Web Submission',
        notes: 'Submitted via Online Showroom RFQ Basket',
        created_at: new Date(Date.now() - 1000 * 60 * 360).toISOString(),
        formatted_date: 'Today, 6 hours ago',
      },
    ],
    submitted_at: new Date(Date.now() - 1000 * 60 * 360).toISOString(),
    formatted_submitted_at: 'Today, 6 hours ago',
  },
  {
    id: 3,
    rfq_number: 'RFQ-2026-F9102',
    customer_name: 'Michael Chen',
    customer_email: 'mchen@datacenter-west.net',
    customer_phone: '+1 (555) 781-9003',
    company_name: 'West Coast Datacenter Colocation',
    location: 'Airport Logistics Bay 12, Terminal Cargo Park',
    city: 'San Jose',
    address: '880 Air Cargo Way',
    status: 'QUOTATION_PREPARED',
    status_label: 'Quotation Prepared',
    badge_variant: 'corporate',
    customer_notes: 'Annual colocation infrastructure refresh. Require 12-bay NAS units with SAS 12G expansion.',
    admin_notes: 'Formal quotation QT-2026-0042 generated with 15% tier volume rebate.',
    preferred_store: { id: 3, name: 'Airport Logistics Center', code: 'ST-NORTH-03' },
    assigned_user: { id: 3, name: 'Marcus Brody', email: 'sales@irack.com' },
    items_count: 1,
    total_quantity: 3,
    items: [
      {
        id: 105,
        product_id: 3,
        product_name: 'Synology RackStation RS3621xs+ 12-Bay High-Performance NAS',
        product_sku: 'STG-SYN-RS3621XS-01',
        model_number: 'RS3621xs+',
        product_slug: 'synology-rackstation-rs3621xs-plus-12-bay-nas',
        primary_image_url: 'https://images.unsplash.com/photo-1597852074816-d933c7d2b988?auto=format&fit=crop&w=300&h=200&q=80',
        brand_name: 'Synology',
        category_name: 'Storage Solutions',
        quantity: 3,
        target_price: 3200.00,
        notes: 'Include 10GbE SFP+ dual-port add-in cards',
      },
    ],
    status_histories: [
      {
        id: 4,
        status: 'QUOTATION_PREPARED',
        status_label: 'Quotation Prepared',
        badge_variant: 'corporate',
        previous_status: 'UNDER_REVIEW',
        user_name: 'Marcus Brody (Sales Mgr)',
        notes: 'Generated Quotation document QT-2026-0042 with 30-day price lock.',
        created_at: new Date(Date.now() - 1000 * 60 * 720).toISOString(),
        formatted_date: 'Yesterday',
      },
    ],
    submitted_at: new Date(Date.now() - 1000 * 60 * 1440).toISOString(),
    formatted_submitted_at: 'Yesterday',
  },
];

export const rfqService = {
  /**
   * Submit Customer RFQ from the public quotation cart
   */
  async submitRfq(formData) {
    try {
      const response = await apiClient.post('/public/rfqs', formData);
      return response.data;
    } catch (err) {
      if (err.response?.data?.errors) {
        throw err;
      }
      // Graceful fallback for offline / demo mode
      const year = new Date().getFullYear();
      const mockRef = `RFQ-${year}-${Math.random().toString(36).substring(2, 7).toUpperCase()}`;
      return {
        success: true,
        message: `Your Request for Quotation has been received with reference ${mockRef}.`,
        data: {
          id: Math.floor(1000 + Math.random() * 9000),
          rfq_number: mockRef,
          customer_name: formData.customer_name,
          customer_email: formData.customer_email,
          company_name: formData.company_name,
          items_count: formData.items?.length || 0,
          submitted_at: new Date().toISOString(),
        },
      };
    }
  },

  /**
   * List RFQs for administrative portal
   */
  async getAdminRfqs(params = {}) {
    try {
      const response = await apiClient.get('/admin/rfqs', { params });
      if (response.data?.data) {
        return response.data;
      }
    } catch {
      // Fallback
    }

    let filtered = [...MOCK_RFQS];

    if (params.search) {
      const q = params.search.toLowerCase();
      filtered = filtered.filter(
        (r) =>
          r.rfq_number.toLowerCase().includes(q) ||
          r.customer_name.toLowerCase().includes(q) ||
          (r.company_name && r.company_name.toLowerCase().includes(q)) ||
          r.customer_email.toLowerCase().includes(q)
      );
    }

    if (params.status) {
      filtered = filtered.filter((r) => r.status === params.status);
    }

    if (params.store_id) {
      filtered = filtered.filter((r) => r.preferred_store?.id === Number(params.store_id));
    }

    return {
      success: true,
      data: filtered,
      meta: {
        pagination: {
          current_page: 1,
          per_page: 15,
          total: filtered.length,
          last_page: 1,
        },
      },
    };
  },

  /**
   * Get single RFQ details
   */
  async getAdminRfq(id) {
    try {
      const response = await apiClient.get(`/admin/rfqs/${id}`);
      if (response.data?.data) {
        return response.data;
      }
    } catch {
      // Fallback
    }

    const found = MOCK_RFQS.find((r) => String(r.id) === String(id) || r.rfq_number === id);
    if (found) {
      return { success: true, data: found };
    }
    throw new Error('RFQ request not found.');
  },

  /**
   * Update RFQ status and record staff user responsible
   */
  async updateRfqStatus(id, { status, notes }) {
    try {
      const response = await apiClient.patch(`/admin/rfqs/${id}/status`, { status, notes });
      return response.data;
    } catch (err) {
      if (err.response?.data?.errors) {
        throw err;
      }
      // Local mutation for demo mode
      const item = MOCK_RFQS.find((r) => String(r.id) === String(id));
      if (item) {
        const prev = item.status;
        item.status = status;
        item.status_label = status.replace(/_/g, ' ');
        item.status_histories.unshift({
          id: Date.now(),
          status,
          status_label: status.replace(/_/g, ' '),
          previous_status: prev,
          user_name: 'Current Staff Administrator',
          notes: notes || 'Status updated via administrative console.',
          created_at: new Date().toISOString(),
          formatted_date: 'Just now',
        });
        return { success: true, message: `Status updated to ${status}.`, data: item };
      }
      throw err;
    }
  },

  /**
   * Update internal admin notes
   */
  async updateRfqNotes(id, { admin_notes, assigned_to_user_id }) {
    try {
      const response = await apiClient.put(`/admin/rfqs/${id}/notes`, { admin_notes, assigned_to_user_id });
      return response.data;
    } catch (err) {
      const item = MOCK_RFQS.find((r) => String(r.id) === String(id));
      if (item) {
        item.admin_notes = admin_notes;
        return { success: true, message: 'Notes saved.', data: item };
      }
      throw err;
    }
  },

  /**
   * Delete RFQ
   */
  async deleteRfq(id) {
    try {
      const response = await apiClient.delete(`/admin/rfqs/${id}`);
      return response.data;
    } catch (err) {
      const idx = MOCK_RFQS.findIndex((r) => String(r.id) === String(id));
      if (idx >= 0) {
        MOCK_RFQS.splice(idx, 1);
        return { success: true, message: 'RFQ removed.' };
      }
      throw err;
    }
  },
};

export default rfqService;
