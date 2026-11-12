import apiClient from './api';

export const roleService = {
  async getRoles() {
    const response = await apiClient.get('/admin/roles');
    return response.data;
  },

  async getRole(id) {
    const response = await apiClient.get(`/admin/roles/${id}`);
    return response.data;
  },

  async createRole(payload) {
    const response = await apiClient.post('/admin/roles', payload);
    return response.data;
  },

  async updateRole(id, payload) {
    const response = await apiClient.put(`/admin/roles/${id}`, payload);
    return response.data;
  },

  async deleteRole(id) {
    const response = await apiClient.delete(`/admin/roles/${id}`);
    return response.data;
  },

  async getPermissions() {
    const response = await apiClient.get('/admin/permissions');
    return response.data;
  },

  async syncRolePermissions(roleId, permissionIds) {
    const response = await apiClient.post(`/admin/roles/${roleId}/permissions`, {
      permission_ids: permissionIds,
    });
    return response.data;
  },
};

export default roleService;
