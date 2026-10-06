import { httpClient } from '../../shared/services/httpClient';

export const clientesService = {
    list: () => httpClient.get('/clientes'),
    create: (payload) => httpClient.post('/clientes', payload),
    sucursales: () => httpClient.get('/sucursales'),
};
