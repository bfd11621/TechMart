import apiClient from './api';

export const adminProductService = {
  async getProducts(params = {}) {
    const response = await apiClient.get('/admin/products', { params });
    return response.data;
  },

  async getProduct(id) {
    const response = await apiClient.get(`/admin/products/${id}`);
    return response.data;
  },

  async createProduct(payload) {
    const headers = payload instanceof FormData ? { 'Content-Type': 'multipart/form-data' } : {};
    const response = await apiClient.post('/admin/products', payload, { headers });
    return response.data;
  },

  async updateProduct(id, payload) {
    if (payload instanceof FormData) {
      if (!payload.has('_method')) {
        payload.append('_method', 'PUT');
      }
      const response = await apiClient.post(`/admin/products/${id}`, payload, {
        headers: { 'Content-Type': 'multipart/form-data' },
      });
      return response.data;
    }

    // If a plain JavaScript object was passed, wrap in FormData with method spoofing
    const formData = new FormData();
    formData.append('_method', 'PUT');
    Object.keys(payload).forEach(key => {
      if (payload[key] !== null && payload[key] !== undefined) {
        if (typeof payload[key] === 'object' && !(payload[key] instanceof File)) {
          formData.append(key, JSON.stringify(payload[key]));
        } else {
          formData.append(key, payload[key]);
        }
      }
    });

    const response = await apiClient.post(`/admin/products/${id}`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });
    return response.data;
  },

  async togglePublish(id) {
    const response = await apiClient.patch(`/admin/products/${id}/publish`);
    return response.data;
  },

  async toggleFeatured(id) {
    const response = await apiClient.patch(`/admin/products/${id}/featured`);
    return response.data;
  },

  async uploadImage(productId, file) {
    const formData = new FormData();
    formData.append('image', file);
    const response = await apiClient.post(`/admin/products/${productId}/images`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });
    return response.data;
  },

  async setPrimaryImage(productId, imageId) {
    const response = await apiClient.patch(`/admin/products/${productId}/images/${imageId}/primary`);
    return response.data;
  },

  async deleteImage(productId, imageId) {
    const response = await apiClient.delete(`/admin/products/${productId}/images/${imageId}`);
    return response.data;
  },

  async uploadDatasheet(productId, file) {
    const formData = new FormData();
    formData.append('datasheet', file);
    const response = await apiClient.post(`/admin/products/${productId}/datasheet`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });
    return response.data;
  },

  async deleteDatasheet(productId) {
    const response = await apiClient.delete(`/admin/products/${productId}/datasheet`);
    return response.data;
  },

  async deleteProduct(id) {
    const response = await apiClient.delete(`/admin/products/${id}`);
    return response.data;
  },
};

export default adminProductService;
