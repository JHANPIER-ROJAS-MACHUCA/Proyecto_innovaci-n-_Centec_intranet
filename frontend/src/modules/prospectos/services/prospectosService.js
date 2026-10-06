import { httpClient } from '../../shared/services/httpClient';

export const prospectosService = {
    list: () => httpClient.get('/prospectos'),
    create: (payload) => httpClient.post('/prospectos', payload),
    convertir: (id) => httpClient.post(`/prospectos/${id}/convertir`, {}),
};
