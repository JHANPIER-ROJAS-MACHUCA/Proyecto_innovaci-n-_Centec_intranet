import { httpClient } from '../../shared/services/httpClient';

export const notificacionesService = {
    list: () => httpClient.get('/notificaciones'),
    marcarLeidas: () => httpClient.post('/notificaciones/leidas', {}),
};
