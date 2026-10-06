import { httpClient } from '../../shared/services/httpClient';

export const solicitudesService = {
    search: ({ tipo, q, estado } = {}) =>
        httpClient.get(
            `/simulaciones?tipo=${tipo}${q ? `&q=${encodeURIComponent(q)}` : ''}${estado ? `&estado=${estado}` : ''}`,
        ),
    conteos: () => httpClient.get('/simulaciones/conteos'),
    detail: (id) => httpClient.get(`/simulaciones/${id}`),
};
