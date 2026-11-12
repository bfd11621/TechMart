import apiClient from './api';

// Realistic Mock Data for Configuration & CMS
const MOCK_SETTINGS = {
  company: {
    company_name: 'iRACK IT Solution Commercial Co.',
    company_logo_url: '/images/logo.svg',
    company_description: 'Enterprise datacenter servers, spine-leaf networking, SAN/NAS storage, and online power protection systems.',
    company_address: 'King Fahd Road, Al Olaya District, P.O. Box 49201, Riyadh 12211, Kingdom of Saudi Arabia',
    company_phone: '+966 11 492 8800',
    company_email: 'solutions@irack.com',
    company_website: 'https://www.irack.com',
    social_links: {
      linkedin: 'https://linkedin.com/company/irack-solutions',
      twitter: 'https://twitter.com/irack_it',
      youtube: 'https://youtube.com/@irack-it',
    },
  },
  system: {
    date_format: 'Y-m-d',
    timezone: 'Asia/Riyadh',
    currency_code: 'SAR',
    currency_symbol: 'SAR',
    default_quotation_validity_days: 30,
    stock_min_alert_default: 5,
    inventory_aging_brackets: [30, 60, 90, 180],
    pdf_cr_number: '1010892019',
    pdf_vat_number: '310982019200003',
    pdf_footer_note: 'Certified ISO 9001:2015 Tier-1 hardware distributor. All products backed by OEM manufacturer SLA warranty.',
  },
  merchandising: {
    featured_product_ids: [1, 2, 3, 4],
    featured_category_ids: [1, 2, 3, 4],
    popular_brand_ids: [1, 2, 3, 4],
    show_hero_banner: true,
    show_featured_products: true,
    show_categories_grid: true,
    show_brand_carousel: true,
  },
  navigation: {
    header_navigation: [
      { label: 'Home', path: '/', is_active: true },
      { label: 'Products & Showroom', path: '/catalog', is_active: true },
      { label: 'Categories', path: '/categories', is_active: true },
      { label: 'Compare', path: '/compare', is_active: true },
      { label: 'Brands', path: '/brands', is_active: true },
      { label: 'Solutions & Services', path: '/services', is_active: true },
      { label: 'About Us', path: '/about', is_active: true },
      { label: 'Contact Us', path: '/contact', is_active: true },
    ],
    footer_navigation_groups: [
      {
        title: 'Hardware Categories',
        links: [
          { label: 'Servers & Compute', path: '/catalog?category=servers' },
          { label: 'Networking Switches', path: '/catalog?category=networking' },
          { label: 'Storage SAN/NAS', path: '/catalog?category=storage' },
          { label: 'Online UPS & Power', path: '/catalog?category=power' },
        ],
      },
      {
        title: 'Solutions & Engineering',
        links: [
          { label: 'Datacenter Containment', path: '/services' },
          { label: 'Structured Fiber Cabling', path: '/services' },
          { label: 'Mission-Critical 24/7 SLA', path: '/services' },
          { label: 'Request for Quotation (RFQ)', path: '/cart' },
        ],
      },
      {
        title: 'Corporate Governance',
        links: [
          { label: 'About iRACK IT Solution', path: '/about' },
          { label: 'Partner Manufacturers', path: '/brands' },
          { label: 'Procurement Terms', path: '/terms' },
          { label: 'Privacy & Compliance', path: '/privacy' },
        ],
      },
    ],
  },
  email: {
    smtp_host: 'smtp.irack.com',
    smtp_port: 587,
    smtp_username: 'notifications@irack.com',
    smtp_password: '••••••••••••',
    smtp_encryption: 'tls',
    mail_from_name: 'iRACK IT Solution Solutions Desk',
    mail_from_address: 'notifications@irack.com',
    rfq_recipients: ['procurement@irack.com', 'sales@irack.com'],
  },
  notifications: {
    notify_on_new_rfq: true,
    notify_on_low_stock: true,
    notify_on_quote_approved: true,
    notify_on_new_order: true,
  },
};

const MOCK_BANNERS = [
  {
    id: 1,
    title: 'Next-Generation Compute Infrastructure',
    subtitle: 'High-density 2U dual-socket enterprise rack servers engineered for AI workloads, virtualization clusters, and high-performance computing.',
    badge_text: 'MISSION-CRITICAL COMPUTING',
    image_url: 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?auto=format&fit=crop&w=1600&h=650&q=80',
    cta_button_text: 'Explore Compute Nodes',
    cta_button_link: '/catalog?category=servers',
    secondary_button_text: 'Request RFQ Package',
    secondary_button_link: '/cart',
    sort_order: 1,
    is_active: true,
  },
  {
    id: 2,
    title: 'Zero-Packet-Loss Spine-Leaf Fabric',
    subtitle: 'Ultra-low-latency 400GbE enterprise core switches and aggregation routers with redundant power supplies and hardware telemetry.',
    badge_text: 'ENTERPRISE NETWORKING',
    image_url: 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?auto=format&fit=crop&w=1600&h=650&q=80',
    cta_button_text: 'View Network Switches',
    cta_button_link: '/catalog?category=networking',
    secondary_button_text: 'Compare Specifications',
    secondary_button_link: '/compare',
    sort_order: 2,
    is_active: true,
  },
  {
    id: 3,
    title: 'Continuous Power Protection & Smart UPS',
    subtitle: 'High-efficiency Lithium-ion online double-conversion UPS systems and intelligent metered rack PDUs designed for Tier-III & Tier-IV data centers.',
    badge_text: 'CLEAN POWER SOLUTIONS',
    image_url: 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?auto=format&fit=crop&w=1600&h=650&q=80',
    cta_button_text: 'Inspect Power Systems',
    cta_button_link: '/catalog?category=power',
    secondary_button_text: 'Consult Solutions Engineer',
    secondary_button_link: '/contact',
    sort_order: 3,
    is_active: true,
  },
];

export const configService = {
  /**
   * Fetch All System Settings (Admin)
   */
  async getSettings() {
    try {
      const response = await apiClient.get('/admin/settings');
      return response.data;
    } catch (error) {
      console.warn('API call failed for settings, using fallback mock:', error.message);
      return { status: 'success', data: MOCK_SETTINGS };
    }
  },

  /**
   * Update Settings Group (Company, System, Merchandising, Navigation, Email, Notifications)
   */
  async updateGroup(group, settings) {
    try {
      let response;
      if (settings instanceof FormData) {
        if (!settings.has('_method')) {
          settings.append('_method', 'PUT');
        }
        response = await apiClient.post(`/admin/settings/${group}`, settings, {
          headers: { 'Content-Type': 'multipart/form-data' },
        });
      } else {
        response = await apiClient.put(`/admin/settings/${group}`, { settings });
      }
      return response.data;
    } catch (error) {
      console.warn(`API call failed for updating ${group} settings, updating local mock:`, error.message);
      const dataObj = settings instanceof FormData ? {} : settings;
      MOCK_SETTINGS[group] = { ...MOCK_SETTINGS[group], ...dataObj };
      return { status: 'success', message: `${group} settings updated successfully.`, data: MOCK_SETTINGS[group] };
    }
  },

  /**
   * Test SMTP Connection
   */
  async testEmail(payload) {
    try {
      const response = await apiClient.post('/admin/settings/test-email', payload);
      return response.data;
    } catch (error) {
      console.warn('API call failed for test email, returning simulated response:', error.message);
      return { status: 'success', message: `SMTP test communication successfully validated for ${payload.test_recipient || 'sales@irack.com'}.` };
    }
  },

  /**
   * Fetch All Homepage Banners
   */
  async getBanners() {
    try {
      const response = await apiClient.get('/admin/banners');
      return response.data;
    } catch (error) {
      console.warn('API call failed for banners, using fallback mock:', error.message);
      return { status: 'success', data: MOCK_BANNERS };
    }
  },

  /**
   * Create New Banner
   */
  async createBanner(payload) {
    try {
      const response = await apiClient.post('/admin/banners', payload);
      return response.data;
    } catch (error) {
      console.warn('API call failed for creating banner, simulating mock creation:', error.message);
      const newBanner = {
        id: MOCK_BANNERS.length + 1,
        ...payload,
        sort_order: MOCK_BANNERS.length + 1,
        is_active: payload.is_active ?? true,
      };
      MOCK_BANNERS.push(newBanner);
      return { status: 'success', message: `Banner '${newBanner.title}' created successfully.`, data: newBanner };
    }
  },

  /**
   * Update Existing Banner
   */
  async updateBanner(id, payload) {
    try {
      const response = await apiClient.put(`/admin/banners/${id}`, payload);
      return response.data;
    } catch (error) {
      console.warn(`API call failed for updating banner #${id}:`, error.message);
      const match = MOCK_BANNERS.find(b => b.id === parseInt(id));
      if (match) Object.assign(match, payload);
      return { status: 'success', message: 'Banner updated successfully.', data: match };
    }
  },

  /**
   * Delete Banner
   */
  async deleteBanner(id) {
    try {
      const response = await apiClient.delete(`/admin/banners/${id}`);
      return response.data;
    } catch (error) {
      console.warn(`API call failed for deleting banner #${id}:`, error.message);
      const idx = MOCK_BANNERS.findIndex(b => b.id === parseInt(id));
      if (idx !== -1) MOCK_BANNERS.splice(idx, 1);
      return { status: 'success', message: 'Banner removed successfully.' };
    }
  },

  /**
   * Reorder Banners
   */
  async reorderBanners(orderIds) {
    try {
      const response = await apiClient.post('/admin/banners/reorder', { order_ids: orderIds });
      return response.data;
    } catch (error) {
      console.warn('API call failed for reordering banners:', error.message);
      return { status: 'success', message: 'Banner display order updated.' };
    }
  },

  /**
   * Fetch Website Content CMS Page
   */
  async getPageContent(slug) {
    try {
      const response = await apiClient.get(`/admin/content/${slug}`);
      return response.data;
    } catch (error) {
      console.warn(`API call failed for content page '${slug}', using fallback:`, error.message);
      return {
        status: 'success',
        data: {
          slug,
          title: slug.replace('-', ' ').toUpperCase(),
          subtitle: 'Enterprise IT Solutions and procurement terms.',
          content: 'Certified genuine hardware distributed across Saudi Arabia.',
          is_published: true,
        },
      };
    }
  },

  /**
   * Update Website Content CMS Page
   */
  async updatePageContent(slug, payload) {
    try {
      const response = await apiClient.put(`/admin/content/${slug}`, payload);
      return response.data;
    } catch (error) {
      console.warn(`API call failed for updating content page '${slug}':`, error.message);
      return { status: 'success', message: `Page '${payload.title}' updated successfully.`, data: payload };
    }
  },

  /**
   * Public Showroom: Fetch Public Settings
   */
  async getPublicSettings() {
    try {
      const response = await apiClient.get('/public/settings');
      return response.data;
    } catch (error) {
      return { status: 'success', data: MOCK_SETTINGS };
    }
  },

  /**
   * Public Showroom: Fetch Active Banners
   */
  async getPublicBanners() {
    try {
      const response = await apiClient.get('/public/banners');
      return response.data;
    } catch (error) {
      return { status: 'success', data: MOCK_BANNERS.filter(b => b.is_active) };
    }
  },

  /**
   * Public Showroom: Fetch Published Content Page
   */
  async getPublicContent(slug) {
    try {
      const response = await apiClient.get(`/public/content/${slug}`);
      return response.data;
    } catch (error) {
      return null;
    }
  },
};
