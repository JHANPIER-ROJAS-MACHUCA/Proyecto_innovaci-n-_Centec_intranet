import { Card, Panel, Bienvenida, Accesos, fmt } from './widgets.jsx';

// Rol 4 ASESOR: como operador pero centrado en colocación — propuestas y desembolsos propios.
export default function AsesorDashboard({ user, resumen, rol }) {
  return (
    <div>
      <Bienvenida nombre={user?.nombre} rol={rol} caja={resumen?.caja} />
      <div className="row">
        <Card titulo="Mis clientes" valor={fmt(resumen?.misClientes)} icono="fa-users" to="/clientes" />
        <Card titulo="Mis créditos activos" valor={fmt(resumen?.misCreditos)} icono="fa-money" to="/creditos?tab=activos" />
        <Card titulo="Mis cobros de hoy" valor={fmt(resumen?.misCobrosHoy)} icono="fa-calendar" to="/campo" />
        <Card titulo="Créditos propuestos" valor={fmt(resumen?.creditosPropuestos)} icono="fa-file-text-o" to="/creditos?tab=propuestos" />
      </div>
      <div className="row">
        <div className="col-lg-12">
          <Panel titulo="Mi colocación y cobranza">
            <Accesos items={[
              { label: 'Crear propuesta', icon: 'fa-lightbulb-o', to: '/propuestas?accion=crear' },
              { label: 'Mis propuestas', icon: 'fa-files-o', to: '/propuestas' },
              { label: 'Cartera de cobro (hoy)', icon: 'fa-map-marker', to: '/campo' },
              { label: 'Cobrar Crédito', icon: 'fa-money', to: '/cobrar' },
              { label: 'Posición del cliente', icon: 'fa-user-circle', to: '/clientes?vista=posicion' },
              { label: 'Avance de metas', icon: 'fa-dashboard', to: '/instrumentos?vista=avance' },
              { label: 'Solicitar Extorno', icon: 'fa-refresh', to: '/extornos' },
              { label: 'Seguimiento de mora', icon: 'fa-download', to: '/mora' },
            ]} />
          </Panel>
        </div>
      </div>
    </div>
  );
}
