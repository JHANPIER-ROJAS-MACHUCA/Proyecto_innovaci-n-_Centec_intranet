import { httpClient } from '../../shared/services/httpClient';

export const cuentasService = {
    list: () => httpClient.get('/cuentas'),
    create: (payload) => httpClient.post('/cuentas', payload),
    operar: (cuentaId, tipo, payload) => httpClient.post(`/cuentas/${cuentaId}/${tipo}`, payload),
    tiposAhorro: () => httpClient.get('/tipos-ahorro'),
};
