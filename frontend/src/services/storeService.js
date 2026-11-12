import apiClient from './api';

export const storeService = {
  async getStores() {
    const response = await apiClient.get('/admin/stores');
    return response.data;
  },

  async getStore(id) {
    const response = await apiClient.get(`/admin/stores/${id}`);
    return response.data;
  },

  async createStore(payload) {
    const response = await apiClient.post('/admin/stores', payload);
    return response.data;
  },

  async updateStore(id, payload) {
    const response = await apiClient.put(`/admin/stores/${id}`, payload);
    return response.data;
  },

  async toggleStatus(id) {
    const response = await apiClient.patch(`/admin/stores/${id}/status`);
    return response.data;
  },

  async assignEmployees(id, userIds) {
    const response = await apiClient.post(`/admin/stores/${id}/employees`, { user_ids: userIds });
    return response.data;
  },

  async deleteStore(id) {
    const response = await apiClient.delete(`/admin/stores/${id}`);
    return response.data;
  },
};

export default storeService;
