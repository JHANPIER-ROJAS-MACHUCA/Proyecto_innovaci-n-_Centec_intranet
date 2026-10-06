import { httpClient } from '../../shared/services/httpClient';

export const auditoriaService = {
    list: () => httpClient.get('/auditoria'),
};
