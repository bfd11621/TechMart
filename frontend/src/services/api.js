import axios from 'axios';
import { useAuthStore } from '@/stores/authStore';
import { useNotificationStore } from '@/stores/notificationStore';

const apiClient = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || '/api/v1',
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  },
  withCredentials: true,
});

// Request Interceptor: Attach Sanctum Bearer Token
apiClient.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('irack_auth_token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response Interceptor: Global Error & Unauthenticated Handling
apiClient.interceptors.response.use(
  (response) => {
    return response;
  },
  (error) => {
    const notify = useNotificationStore();
    const auth = useAuthStore();

    if (!error.response) {
      notify.error('Network Error: Unable to reach the iRACK API service. Please verify your backend server.');
      return Promise.reject(error);
    }

    const { status, data } = error.response;

    if (status === 401) {
      auth.logout(false);
      notify.warning('Your session has expired or is invalid. Please log in again.');
    } else if (status === 403) {
      notify.error(data?.message || 'Access Forbidden: You lack authorization for this action.');
    } else if (status === 422) {
      // Validation error handled in calling component or highlighted
      const message = data?.message || 'Validation failed. Please verify your inputs.';
      notify.error(message);
    } else if (status >= 500) {
      notify.error(data?.message || 'An internal server error occurred on the iRACK management system.');
    }

    return Promise.reject(error);
  }
);

export default apiClient;
