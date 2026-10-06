import { httpClient } from '../../shared/services/httpClient';

export const desembolsosService = {
    list: () => httpClient.get('/desembolsos'),
    create: (payload) => httpClient.post('/desembolsos', payload),
};
