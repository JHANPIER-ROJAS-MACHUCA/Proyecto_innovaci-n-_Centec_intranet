import { httpClient } from '../../shared/services/httpClient';

export const authService = {
    login: (username, password) =>
        httpClient.request('/auth/login', {
            method: 'POST',
            body: JSON.stringify({ username, password }),
        }),
    profile: (token) =>
        httpClient.request('/auth/profile', { headers: { Authorization: `Bearer ${token}` } }),
};
