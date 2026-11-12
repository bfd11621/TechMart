import apiClient from './api';

export const adminCategoryService = {
  async getCategories(params = {}) {
    const response = await apiClient.get('/admin/categories', { params });
    return response.data;
  },

  async getTree() {
    const response = await apiClient.get('/admin/categories', { params: { tree: 1 } });
    return response.data;
  },

  async createCategory(payload) {
    const response = await apiClient.post('/admin/categories', payload);
    return response.data;
  },

  async updateCategory(id, payload) {
    const response = await apiClient.put(`/admin/categories/${id}`, payload);
    return response.data;
  },

  async toggleStatus(id) {
    const response = await apiClient.patch(`/admin/categories/${id}/status`);
    return response.data;
  },

  async deleteCategory(id) {
    const response = await apiClient.delete(`/admin/categories/${id}`);
    return response.data;
  },
};

export default adminCategoryService;
