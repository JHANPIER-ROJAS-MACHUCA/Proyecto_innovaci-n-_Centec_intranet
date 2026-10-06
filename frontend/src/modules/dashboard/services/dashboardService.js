import { httpClient } from '../../shared/services/httpClient';

export const dashboardService = {
    resumen: () => httpClient.get('/dashboard/resumen'),
};
