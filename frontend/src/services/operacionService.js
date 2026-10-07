import { api } from './api.js';

export const creditoService = {
  generar: (d) => api.post('/api/creditos/generar', d),
  detalle: (id) => api.get(`/api/creditos/detalle?id=${id}`),
  porCliente: (idCG) => api.get(`/api/creditos/por-cliente?idCG=${idCG}`),
  porEstado: (estado) => api.get(`/api/creditos?estado=${estado}`),
  tipos: () => api.get('/api/creditos/tipos'),
  editar: (d) => api.post('/api/creditos/editar', d),
  confirmar: (d) => api.post('/api/creditos/confirmar', d),
  activar: (idP) => api.post('/api/creditos/activar', { idP }),
  desembolsar: (idP) => api.post('/api/creditos/desembolsar', { idP }),
  cancelar: (idP) => api.post('/api/creditos/cancelar', { idP }),
};

export const cobroService = {
  cobrar: (d) => api.post('/api/cobros', d),
  simular: (d) => api.post('/api/cobros/simular', d),
  condonar: (d) => api.post('/api/cobros/condonar', d),
};

export const cajaService = {
  estado: () => api.get('/api/caja/estado'),
  movimientos: () => api.get('/api/caja/movimientos'),
  abrirGerencia: () => api.post('/api/caja/abrir-gerencia', {}),
  abrirOficina: () => api.post('/api/caja/abrir-oficina', {}),
  cerrar: (monto) => api.post('/api/caja/cerrar', { monto }),
};

export const bovedaService = {
  saldos: () => api.get('/api/boveda/saldos'),
  designar: (d) => api.post('/api/boveda/designar', d),
  consumir: (id) => api.post('/api/boveda/consumir', { id }),
};

export const cuentaService = {
  miPerfil: (d) => api.post('/api/usuarios/mi-perfil', d),
  cambiarClave: (d) => api.post('/api/usuarios/cambiar-clave', d),
};

export const billetajeService = {
  registrar: (d) => api.post('/api/billetaje', d),
  pendientes: () => api.get('/api/billetaje/pendientes'),
  confirmar: (id) => api.post('/api/billetaje/confirmar', { id }),
};

export const reciboService = {
  motivos: (tipoM) => api.get(`/api/recibos/motivos${tipoM ? `?tipoM=${tipoM}` : ''}`),
  registrar: (d) => api.post('/api/recibos', d),
};

export const extornoService = {
  solicitar: (d) => api.post('/api/extornos', d),
  listar: () => api.get('/api/extornos'),
  resolver: (codigo) => api.post('/api/extornos/resolver', { codigo }),
};

export const reporteService = {
  cobros: (desde, hasta, q) => api.get(`/api/reportes/cobros?desde=${desde}&hasta=${hasta}${q || ''}`),
  desembolsos: (desde, hasta, q) => api.get(`/api/reportes/desembolsos?desde=${desde}&hasta=${hasta}${q || ''}`),
  cierre: (mes) => api.get(`/api/reportes/cierre?mes=${mes}`),
  morasDias: () => api.get('/api/reportes/moras-dias'),
  sentinel: () => api.get('/api/reportes/sentinel'),
  cancelados: () => api.get('/api/reportes/cancelados'),
  sinCreditos: () => api.get('/api/reportes/sin-creditos'),
  vinculaciones: () => api.get('/api/reportes/vinculaciones'),
};

export const metaServiceExt = {
  porUsuario: () => api.get('/api/metas/por-usuario'),
};

export const ahorroService = {
  registrar: (d) => api.post('/api/ahorros', d),
  anular: (id) => api.post('/api/ahorros/anular', { id }),
};

export const usuarioService = {
  crear: (d) => api.post('/api/usuarios', d),
  actualizar: (d) => api.post('/api/usuarios/actualizar', d),
  toggle: (idU) => api.post('/api/usuarios/toggle', { idU }),
};
