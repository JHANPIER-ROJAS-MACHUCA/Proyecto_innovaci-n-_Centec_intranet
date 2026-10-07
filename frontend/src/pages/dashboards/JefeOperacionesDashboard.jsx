import { Card, Panel, Bienvenida, Accesos, fmt } from './widgets.jsx';

// Rol 5 JEFE DE OPERACIONES: supervisión — cierres, depósitos, metas del equipo, extornos.
export default function JefeOperacionesDashboard({ user, resumen, rol }) {
  return (
    <div>
      <Bienvenida nombre={user?.nombre} rol={rol} caja={resumen?.caja} />
      <div className="row">
        <Card titulo="Clientes" valor={fmt(resumen?.clientes)} icono="fa-users" to="/clientes" />
        <Card titulo="Créditos activos" valor={fmt(resumen?.creditosActivos)} icono="fa-money" to="/creditos?tab=activos" />
        <Card titulo="Moras pendientes" valor={fmt(resumen?.morasPendientes)} icono="fa-exclamation-triangle" to="/mora" />
        <Card titulo="Cobros de hoy (equipo)" valor={fmt(resumen?.cobrosHoy)} icono="fa-calendar" to="/campo" />
      </div>
      <div className="row">
        <Card titulo="Metas" valor={fmt(resumen?.metas)} icono="fa-dashboard" to="/instrumentos?vista=monitor" />
        <Card titulo="Extornos pendientes" valor={fmt(resumen?.extornosPendientes)} icono="fa-refresh" to="/extornos?tab=solicitados" />
        <Card titulo="Billetaje pendiente" valor={fmt(resumen?.billetajePendiente)} icono="fa-list-alt" to="/caja?tab=billetaje" />
        <Card titulo={`Caja: ${resumen?.caja?.habilitada ? 'Abierta' : 'Cerrada'}`} valor={resumen?.caja ? `S/ ${Number(resumen.caja.efectivo || 0).toFixed(2)}` : ''} icono="fa-bank" to="/caja?tab=estado" />
      </div>
      <div className="row">
        <div className="col-lg-12">
          <Panel titulo="Supervisión operativa">
            <Accesos items={[
              { label: 'Reporte de Cierre de Mes', icon: 'fa-bar-chart', to: '/reportes?vista=cierre-mes' },
              { label: 'Cierre Personalizado', icon: 'fa-calendar-o', to: '/reportes?vista=cierre-personalizado' },
              { label: 'Depósitos y ahorros', icon: 'fa-database', to: '/reportes?vista=depositos' },
              { label: 'Cobros del día', icon: 'fa-clock-o', to: '/reportes?vista=cobros-dia' },
              { label: 'Monitor de metas', icon: 'fa-dashboard', to: '/instrumentos?vista=monitor' },
              { label: 'Extornos (resolver)', icon: 'fa-gavel', to: '/extornos?tab=solicitados' },
              { label: 'Carteras vencidas', icon: 'fa-exclamation-triangle', to: '/mora' },
              { label: 'Cartera de cobro', icon: 'fa-map-marker', to: '/campo' },
            ]} />
          </Panel>
        </div>
      </div>
    </div>
  );
}
