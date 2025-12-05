const API_BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:3001/api';

// Helper function to get auth token
const getAuthToken = () => {
  return localStorage.getItem('admin_token');
};

// Helper function to make API requests
const apiRequest = async (endpoint: string, options: RequestInit = {}) => {
  const url = `${API_BASE_URL}${endpoint}`;
  const token = getAuthToken();

  const headers: HeadersInit = {
    'Content-Type': 'application/json',
    ...options.headers,
  };

  if (token) {
    headers['Authorization'] = `Bearer ${token}`;
  }

  let response: Response;
  try {
    response = await fetch(url, {
      ...options,
      headers,
    });
  } catch (fetchError) {
    // Network error - server is not reachable
    throw new Error('Failed to fetch - make sure the backend server is running on port 3001');
  }

  if (!response.ok) {
    const error = await response.json().catch(() => ({ error: 'Request failed' }));
    throw new Error(error.error || 'Request failed');
  }

  return response.json();
};

// Messages API
export const messagesApi = {
  submit: async (data: { name: string; email: string; message: string }) => {
    return apiRequest('/messages', {
      method: 'POST',
      body: JSON.stringify(data),
    });
  },
  getAll: async () => {
    return apiRequest('/admin/messages');
  },
  markAsRead: async (id: number) => {
    return apiRequest(`/admin/messages/${id}/read`, {
      method: 'PATCH',
    });
  },
  delete: async (id: number) => {
    return apiRequest(`/admin/messages/${id}`, {
      method: 'DELETE',
    });
  },
};

// Projects API
export const projectsApi = {
  getAll: async () => {
    return apiRequest('/projects');
  },
  getById: async (id: number) => {
    return apiRequest(`/projects/${id}`);
  },
  create: async (data: {
    title: string;
    description: string;
    tech: string[];
    repoUrl?: string;
    liveUrl?: string;
    imageUrl?: string;
    featured?: boolean;
  }) => {
    return apiRequest('/admin/projects', {
      method: 'POST',
      body: JSON.stringify(data),
    });
  },
  update: async (id: number, data: {
    title: string;
    description: string;
    tech: string[];
    repoUrl?: string;
    liveUrl?: string;
    imageUrl?: string;
    featured?: boolean;
  }) => {
    return apiRequest(`/admin/projects/${id}`, {
      method: 'PUT',
      body: JSON.stringify(data),
    });
  },
  delete: async (id: number) => {
    return apiRequest(`/admin/projects/${id}`, {
      method: 'DELETE',
    });
  },
};

// Auth API
export const authApi = {
  login: async (username: string, password: string) => {
    return apiRequest('/admin/login', {
      method: 'POST',
      body: JSON.stringify({ username, password }),
    });
  },
};

// CV API
export const cvApi = {
  upload: async (file: File) => {
    const formData = new FormData();
    formData.append('cv', file);

    const token = getAuthToken();
    const url = `${API_BASE_URL}/admin/cv/upload`;

    const response = await fetch(url, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
      },
      body: formData,
    });

    if (!response.ok) {
      const error = await response.json().catch(() => ({ error: 'Upload failed' }));
      throw new Error(error.error || 'Upload failed');
    }

    return response.json();
  },
  getInfo: async () => {
    return apiRequest('/cv/info');
  },
  getAdminInfo: async () => {
    return apiRequest('/admin/cv/info');
  },
  getDownloadUrl: () => {
    return `${API_BASE_URL}/cv/download`;
  },
};

