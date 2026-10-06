import { httpClient } from '../../shared/services/httpClient';

export const usuariosService = {
    list: () => httpClient.get('/usuarios'),
    create: (payload) => httpClient.post('/usuarios', payload),
    remove: (id) => httpClient.delete(`/usuarios/${id}`),
    roles: () => httpClient.get('/roles'),
    sucursales: () => httpClient.get('/sucursales'),
};
