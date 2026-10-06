import { httpClient } from '../../shared/services/httpClient';

export const movimientosService = {
    list: () => httpClient.get('/movimientos'),
};
