// Config central de roles, menús y guards — matriz "ROLES Y VISTAS DEL SISTEMA".
// Roles = códigos CENTECPC tabla `rol` (los usan cookie tuser y tusuarios.idRol):
//   8 GERENCIA · 1 TI/SUPER ADMIN · 5 ADMIN SUCURSAL (admin_personal) ·
//   7 PLATAFORMA · 2 ASESOR · 9 SEGUIMIENTO · 3 CLIENTE.
// (4 Postulante y 6 Soporte Web existen en `rol` pero sin spec.)
// Jerarquía operativa: GERENCIA > TI > ADMIN_SUC > staff > CLIENTE.
// Financiera: CAJA GERENCIA > CAJA TI > CAJA SUCURSAL > OPERATIVAS.

export const ROLES = {
  8: 'GERENCIA',
  1: 'TI / ADMIN SISTEMA',
  5: 'ADMINISTRADOR DE SUCURSAL',
  7: 'PLATAFORMA',
  2: 'ASESOR',
  9: 'SEGUIMIENTO',
  3: 'CLIENTE',
};

export const roleName = (t) => ROLES[Number(t)] || (t ? `TIPO ${t}` : '');

// Grupos de conveniencia
export const ES_GERENCIA = [8];               // visión global + caja general
export const ES_TI = [1];                     // estructura, usuarios, roles, config
export const ES_ADMIN_SUC = [5];              // opera su sucursal
export const ES_STAFF = [7, 2, 9];            // plataforma / asesor / seguimiento
export const ES_CLIENTE = [3];                // solo consulta propia
export const ES_JEFATURA = [8, 5, 1];         // supervisión (reportes, cierres)
export const ES_OPERATIVO = [5, 7, 2, 9, 1];  // opera clientes/créditos/cobros

function r(...roles) { return roles; }

// Menú por rol — secciones de la spec; cada ítem apunta a una ruta implementada.
// Módulos sin página aún (fondos, auditoría, prospectos, visitas, compromisos,
// caja general, finanzas) = FASE 2: no aparecen hasta tener su vista+API.
export const MENU_POR_ROL = [
  { label: 'DASHBOARD', icon: 'fa-th-large', to: '/', roles: r(8, 5, 7, 2, 9, 1) },
  { label: 'INICIO', icon: 'fa-home', to: '/', roles: r(3) },

  {
    label: 'SUCURSALES', icon: 'fa-building', roles: r(8, 5, 1), subs: [
      { label: 'Sucursales', to: '/sucursales', roles: r(8, 5, 1) },
    ],
  },
  {
    label: 'USUARIOS', icon: 'fa-users', roles: r(5, 1), subs: [
      { label: 'Personal / Usuarios', to: '/usuarios', roles: r(5, 1) },
      { label: 'Roles y permisos', to: '/roles', roles: r(1) },
    ],
  },
  {
    label: 'CLIENTES', icon: 'fa-user-circle', roles: r(8, 5, 7, 2, 9, 1), subs: [
      { label: 'Clientes', to: '/clientes', roles: r(8, 5, 7, 2, 9, 1) },
      { label: 'Posición del cliente', to: '/reportes?vista=posicion-cliente', roles: r(8, 5, 1) },
    ],
  },
  {
    label: 'MI CUENTA', icon: 'fa-user', roles: r(3), subs: [
      { label: 'Mis cuentas y créditos', to: '/cuenta', roles: r(3) },
    ],
  },
  {
    label: 'COBRAR', icon: 'fa-money', roles: r(8, 5, 7, 2, 9, 1), subs: [
      { label: 'Cobrar Crédito', to: '/cobrar', roles: r(8, 5, 7, 9, 1) },
      { label: 'Ahorros', to: '/caja?tab=ahorros', roles: r(8, 5, 7, 1) },
      { label: 'Campo', to: '/campo', roles: r(8, 5, 7, 2, 9, 1) },
    ],
  },
  {
    label: 'CAJA', icon: 'fa-empire', roles: r(8, 5, 7, 2, 9, 1), subs: [
      { label: 'Caja Bodega', to: '/caja?tab=boveda', roles: r(8, 1) },
      { label: 'Iniciar Operaciones', to: '/caja?tab=apertura', roles: r(8, 5, 7, 2, 9, 1) },
      { label: 'Recibo de Egresos', to: '/caja?tab=recibos&tipo=2', roles: r(8, 5, 1) },
      { label: 'Recibo de Ingresos', to: '/caja?tab=recibos&tipo=1', roles: r(8, 5, 7, 2, 9, 1) },
      { label: 'Confirmar Billetaje', to: '/caja?tab=billetaje', roles: r(8, 5, 1) },
      { label: 'Movimientos de caja', to: '/caja?tab=movimientos', roles: r(8, 5, 7, 2, 9, 1) },
      { label: 'Cobros', to: '/reportes?vista=cobros-dia', roles: r(8, 5, 1) },
      { label: 'Cerrar Caja', to: '/caja?tab=cierre', roles: r(8, 5, 7, 2, 9, 1) },
    ],
  },
  {
    label: 'PRESTAMOS', icon: 'fa-credit-card', roles: r(8, 5, 7, 2, 9, 1), subs: [
      { label: 'Desembolsos', to: '/creditos?tab=aprobados', roles: r(8, 5, 7, 1) },
      { label: 'Activos', to: '/creditos?tab=activos', roles: r(8, 5, 7, 2, 9, 1) },
      { label: 'Finalizados', to: '/creditos?tab=finalizados', roles: r(8, 5, 9, 1) },
      { label: 'Carteras vencidas', to: '/mora', roles: r(8, 5, 2, 9, 1) },
      { label: 'Finalizados y activos', to: '/creditos?tab=todos', roles: r(8, 5, 9, 1) },
      { label: 'Cobros por fecha', to: '/reportes?vista=cobros-fecha', roles: r(8, 5, 1) },
    ],
  },
  {
    label: 'CARTERA', icon: 'fa-briefcase', roles: r(9,), subs: [
      { label: 'Cartera', to: '/creditos?tab=todos', roles: r(9) },
      { label: 'Cuotas / Morosidad', to: '/mora', roles: r(9) },
    ],
  },
  {
    label: 'INSTRUMENTOS DE CONTROL', icon: 'fa-slack', roles: r(8, 5, 1), subs: [
      { label: 'Monitor de seguimiento de metas', to: '/instrumentos?vista=monitor', roles: r(8, 5, 1) },
      { label: 'Resumen metas', to: '/instrumentos?vista=resumen', roles: r(8, 5, 1) },
      { label: 'Desembolsos', to: '/reportes?vista=desembolsos', roles: r(8, 5, 1) },
      { label: 'Proyecciones', to: '/instrumentos?vista=proyecciones', roles: r(8, 5, 1) },
    ],
  },
  {
    label: 'ADMINISTRACIÓN', icon: 'fa-bar-chart-o', roles: r(8, 5, 1), subs: [
      { label: 'Condonación de Mora', to: '/cobrar?accion=condonar', roles: r(8, 5) },
      { label: 'Administrar Cartera', to: '/creditos?tab=activos&admin=1', roles: r(8, 5) },
      { label: 'Ver justificaciones', to: '/mora?vista=justificaciones', roles: r(8, 5) },
      { label: 'Prestamos (propuestos)', to: '/creditos?tab=propuestos', roles: r(8, 5, 1) },
      { label: 'Solicitar Extorno', to: '/extornos', roles: r(8, 5, 7, 1) },
      { label: 'Reporte sentinel', to: '/reportes?vista=sentinel', roles: r(8, 5, 1) },
    ],
  },
  {
    label: 'REPORTES', icon: 'fa-line-chart', roles: r(8, 5, 1), subs: [
      { label: 'Depositos y ahorros', to: '/reportes?vista=depositos', roles: r(8, 5, 1) },
      { label: 'Cobros del día', to: '/reportes?vista=cobros-dia', roles: r(8, 5, 1) },
      { label: 'Reporte de Cierre de Mes', to: '/reportes?vista=cierre-mes', roles: r(8, 5, 1) },
      { label: 'Reporte de Cierre Personalizado', to: '/reportes?vista=cierre-personalizado', roles: r(8, 5, 1) },
      { label: 'Metas', to: '/instrumentos?vista=monitor', roles: r(8, 5, 1) },
      { label: 'Cartera de cobro', to: '/campo', roles: r(8, 5, 1) },
    ],
  },
  {
    label: 'SERVICIOS', icon: 'fa-database', roles: r(8, 5, 1), subs: [
      { label: 'Extornos', to: '/extornos?tab=solicitados', roles: r(8, 5, 1) },
      { label: 'Extornos eliminados', to: '/extornos?tab=eliminados', roles: r(8, 5, 1) },
    ],
  },
  {
    label: 'CONFIGURACIÓN', icon: 'fa-cog', roles: r(1), subs: [
      { label: 'Usuarios del Sistema', to: '/usuarios', roles: r(1) },
      { label: 'Roles y permisos', to: '/roles', roles: r(1) },
      { label: 'Sucursales', to: '/sucursales', roles: r(1) },
      { label: 'Oficinas', to: '/administracion?tab=oficinas', roles: r(1) },
      { label: 'Depositos', to: '/administracion?tab=depositos', roles: r(1) },
      { label: 'Operaciones', to: '/administracion?tab=operaciones', roles: r(1) },
      { label: 'Empresa', to: '/administracion?tab=empresa', roles: r(1) },
      { label: 'Metas', to: '/administracion?tab=metas', roles: r(1) },
    ],
  },
  {
    label: 'PROPUESTA', icon: 'fa-lightbulb-o', roles: r(8, 5, 7, 2, 9, 1), subs: [
      { label: 'Propuestas', to: '/propuestas', roles: r(8, 5, 7, 2, 9, 1) },
      { label: 'Crear propuesta', to: '/propuestas?accion=crear', roles: r(7, 2) },
    ],
  },
  { label: 'SEGUIMIENTO DE MORA', icon: 'fa-download', to: '/mora', roles: r(8, 5, 2, 9, 1) },
  { label: 'FORMATOS', icon: 'fa-download', to: '/formatos', roles: r(8, 5, 7, 2, 9, 1) },
];

// Rutas protegidas por rol (acceso directo por URL también bloqueado en backend).
// null = todos los autenticados.
export const RUTAS_POR_ROL = {
  '/': null,
  '/clientes': [8, 5, 7, 2, 9, 1],
  '/cobrar': [8, 5, 7, 9, 1],
  '/campo': [8, 5, 7, 2, 9, 1],
  '/caja': [8, 5, 7, 2, 9, 1],
  '/creditos': [8, 5, 7, 2, 9, 1],
  '/instrumentos': [8, 5, 1],
  '/reportes': [8, 5, 1],
  '/propuestas': [8, 5, 7, 2, 9, 1],
  '/mora': [8, 5, 2, 9, 1],
  '/formatos': [8, 5, 7, 2, 9, 1],
  '/cuenta': null,
  '/extornos': [8, 5, 7, 1],
  '/sucursales': [8, 5, 1],
  '/roles': [1],
  '/administracion': [1],
  '/usuarios': [5, 1],
};

export function puedeVer(ruta, tipoU) {
  const base = `/${String(ruta).split('?')[0].split('/').filter(Boolean)[0] || ''}`;
  const key = base === '/' ? '/' : base;
  const permitidos = RUTAS_POR_ROL[key];
  if (!permitidos) return true;
  return permitidos.includes(Number(tipoU));
}

export function menuParaRol(tipoU) {
  const t = Number(tipoU);
  return MENU_POR_ROL
    .filter((m) => (m.roles || []).includes(t))
    .map((m) => ({
      ...m,
      subs: (m.subs || []).filter((s) => (s.roles || []).includes(t)),
    }))
    .filter((m) => (m.subs ? m.subs.length > 0 : !!m.to));
}
