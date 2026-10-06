import { httpClient } from '../../shared/services/httpClient';

export const empresasService = {
    list: () => httpClient.get('/empresas'),
    create: (payload) => httpClient.post('/empresas', payload),
};
