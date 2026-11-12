import apiClient from './api';

export const authService = {
  async login(credentials) {
    const response = await apiClient.post('/auth/login', credentials);
    return response.data;
  },

  async logout() {
    const response = await apiClient.post('/auth/logout');
    return response.data;
  },

  async getMe() {
    const response = await apiClient.get('/auth/me');
    return response.data;
  },

  async forgotPassword(email) {
    const response = await apiClient.post('/auth/forgot-password', { email });
    return response.data;
  },

  async resetPassword(payload) {
    const response = await apiClient.post('/auth/reset-password', payload);
    return response.data;
  },

  async updateProfile(payload) {
    const response = await apiClient.put('/auth/profile', payload);
    return response.data;
  },

  async changePassword(payload) {
    const response = await apiClient.put('/auth/change-password', payload);
    return response.data;
  },
};

export default authService;
