import { httpClient } from '../../shared/services/httpClient';

export const reportesService = {
    estadoCuenta: (clienteId) => httpClient.get(`/reportes/estado-cuenta/${clienteId}`),
};
