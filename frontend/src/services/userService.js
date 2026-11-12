import apiClient from './api';

export const userService = {
  async getUsers(params = {}) {
    const response = await apiClient.get('/admin/users', { params });
    return response.data;
  },

  async getUser(id) {
    const response = await apiClient.get(`/admin/users/${id}`);
    return response.data;
  },

  async createUser(payload) {
    const response = await apiClient.post('/admin/users', payload);
    return response.data;
  },

  async updateUser(id, payload) {
    const response = await apiClient.put(`/admin/users/${id}`, payload);
    return response.data;
  },

  async toggleStatus(id) {
    const response = await apiClient.patch(`/admin/users/${id}/status`);
    return response.data;
  },

  async resetPassword(id, payload) {
    const response = await apiClient.post(`/admin/users/${id}/reset-password`, payload);
    return response.data;
  },

  async deleteUser(id) {
    const response = await apiClient.delete(`/admin/users/${id}`);
    return response.data;
  },
};

export default userService;
