import { httpClient } from '../../shared/services/httpClient';

export const cajasService = {
    list: () => httpClient.get('/cajas'),
    create: (payload) => httpClient.post('/cajas', payload),
    abrir: (cajaId, payload) => httpClient.post(`/cajas/${cajaId}/abrir`, payload),
    cerrar: (cajaId, payload) => httpClient.post(`/cajas/${cajaId}/cerrar`, payload),
    transferir: (payload) => httpClient.post('/cajas/transferir', payload),
};
