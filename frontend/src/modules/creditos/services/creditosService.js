import { httpClient } from '../../shared/services/httpClient';

export const creditosService = {
    list: () => httpClient.get('/creditos'),
    create: (payload) => httpClient.post('/creditos', payload),
    solicitudes: () => httpClient.get('/solicitudes'),
    crearSolicitud: (payload) => httpClient.post('/solicitudes', payload),
    tiposCredito: () => httpClient.get('/tipos-credito'),
    paraCobro: (creditoId) => httpClient.get(`/creditos/${creditoId}/para-cobro`),
    justificaciones: (creditoId) => httpClient.get(`/creditos/${creditoId}/justificaciones`),
};
