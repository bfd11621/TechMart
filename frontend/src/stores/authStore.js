import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

export const useAuthStore = defineStore('auth', () => {
  const token = ref(localStorage.getItem('irack_auth_token') || null);
  const user = ref(JSON.parse(localStorage.getItem('irack_auth_user') || 'null'));
  const permissions = ref(JSON.parse(localStorage.getItem('irack_permissions') || '[]'));
  const currentStore = ref(JSON.parse(localStorage.getItem('irack_active_store') || 'null'));

  const isAuthenticated = computed(() => !!token.value);
  const role = computed(() => user.value?.role?.slug || null);
  const isSuperAdmin = computed(() => role.value === 'super_admin');

  const setAuth = (newToken, newUser, userPermissions = []) => {
    token.value = newToken;
    user.value = newUser;
    permissions.value = userPermissions;

    localStorage.setItem('irack_auth_token', newToken);
    localStorage.setItem('irack_auth_user', JSON.stringify(newUser));
    localStorage.setItem('irack_permissions', JSON.stringify(userPermissions));

    if (newUser?.store) {
      currentStore.value = newUser.store;
      localStorage.setItem('irack_active_store', JSON.stringify(newUser.store));
    } else {
      currentStore.value = null;
      localStorage.removeItem('irack_active_store');
    }
  };

  const updateUserProfile = (updatedUser) => {
    user.value = { ...user.value, ...updatedUser };
    localStorage.setItem('irack_auth_user', JSON.stringify(user.value));
  };

  const setActiveStore = (store) => {
    currentStore.value = store;
    if (store) {
      localStorage.setItem('irack_active_store', JSON.stringify(store));
    } else {
      localStorage.removeItem('irack_active_store');
    }
  };

  const hasPermission = (permissionSlug) => {
    if (isSuperAdmin.value) return true;
    if (permissions.value.includes('*')) return true;
    return permissions.value.includes(permissionSlug);
  };

  const logout = () => {
    token.value = null;
    user.value = null;
    permissions.value = [];
    currentStore.value = null;
    localStorage.removeItem('irack_auth_token');
    localStorage.removeItem('irack_auth_user');
    localStorage.removeItem('irack_permissions');
    localStorage.removeItem('irack_active_store');
  };

  return {
    token,
    user,
    permissions,
    currentStore,
    isAuthenticated,
    role,
    isSuperAdmin,
    setAuth,
    updateUserProfile,
    setActiveStore,
    hasPermission,
    logout,
  };
});
