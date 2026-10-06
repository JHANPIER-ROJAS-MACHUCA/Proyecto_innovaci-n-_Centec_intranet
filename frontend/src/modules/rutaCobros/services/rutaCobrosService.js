import { httpClient } from '../../shared/services/httpClient';

export const rutaCobrosService = {
    search: (q) => httpClient.get(`/ruta-cobros${q ? `?q=${encodeURIComponent(q)}` : ''}`),
    justificacionCategorias: () => httpClient.get('/justificacion-categorias'),
    justificar: (payload) => httpClient.post('/justificaciones', payload),
};
