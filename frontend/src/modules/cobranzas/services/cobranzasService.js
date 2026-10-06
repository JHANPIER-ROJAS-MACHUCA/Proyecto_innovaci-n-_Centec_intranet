import { httpClient } from '../../shared/services/httpClient';

export const cobranzasService = {
    list: () => httpClient.get('/cobranzas'),
    create: (payload) => httpClient.post('/cobranzas', payload),
};
