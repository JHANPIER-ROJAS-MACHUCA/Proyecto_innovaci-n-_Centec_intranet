import { httpClient } from '../../shared/services/httpClient';

export const sucursalesService = {
    list: () => httpClient.get('/sucursales'),
    create: (payload) => httpClient.post('/sucursales', payload),
    empresas: () => httpClient.get('/empresas'),
};
