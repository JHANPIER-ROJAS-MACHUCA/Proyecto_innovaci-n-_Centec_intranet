import { api } from './api.js';

export const clienteService = {
  crear: (d) => api.post('/api/clientes', d),
  actualizar: (d) => api.post('/api/clientes/actualizar', d),
  buscar: (q) => api.get(`/api/clientes/search?q=${encodeURIComponent(q)}`),
  detalle: (idCG) => api.get(`/api/clientes/detalle?idCG=${idCG}`),
};

export const relacionService = {
  listar: (idCG) => api.get(`/api/relaciones?idCG=${idCG}`),
  agregar: (d) => api.post('/api/relaciones', d),
  eliminar: (id) => api.post('/api/relaciones/eliminar', { id }),
};

export const oficinaService = {
  listar: () => api.get('/api/oficinas'),
};

export const propuestaService = {
  listar: () => api.get('/api/propuestas'),
  responder: (d) => api.post('/api/propuestas/responder', d),
};

export const moraService = {
  deudores: () => api.get('/api/mora/deudores'),
};

export const justificacionService = {
  crear: (d) => api.post('/api/justificaciones', d),
  actualizar: (d) => api.post('/api/justificaciones/actualizar', d),
  eliminar: (id) => api.post('/api/justificaciones/eliminar', { id }),
};

export const metaService = {
  crear: (d) => api.post('/api/metas', d),
  actualizar: (d) => api.post('/api/metas/actualizar', d),
  eliminar: (id) => api.post('/api/metas/eliminar', { id }),
};
