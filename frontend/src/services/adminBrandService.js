import apiClient from './api';

export const adminBrandService = {
  async getBrands(params = {}) {
    try {
      const response = await apiClient.get('/admin/brands', { params });
      return response.data;
    } catch (e) {
      // Fallback to public brands endpoint
      const response = await apiClient.get('/brands', { params });
      return response.data;
    }
  },

  async createBrand(payload) {
    const isFormData = payload instanceof FormData;
    const config = isFormData ? { headers: { 'Content-Type': 'multipart/form-data' } } : {};
    const response = await apiClient.post('/admin/brands', payload, config);
    return response.data;
  },

  async updateBrand(id, payload) {
    if (payload instanceof FormData) {
      if (!payload.has('_method')) {
        payload.append('_method', 'PUT');
      }
      const response = await apiClient.post(`/admin/brands/${id}`, payload, {
        headers: { 'Content-Type': 'multipart/form-data' },
      });
      return response.data;
    }
    const response = await apiClient.put(`/admin/brands/${id}`, payload);
    return response.data;
  },

  async toggleStatus(id) {
    const response = await apiClient.patch(`/admin/brands/${id}/status`);
    return response.data;
  },

  async deleteBrand(id) {
    const response = await apiClient.delete(`/admin/brands/${id}`);
    return response.data;
  },
};

export default adminBrandService;
