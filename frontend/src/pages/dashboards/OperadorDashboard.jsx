import { Card, Panel, Bienvenida, Accesos, fmt } from './widgets.jsx';

// Rol 3 OPERADOR: trabajo diario de caja y campo — cartera propia (inicio.php filtra por idU).
export default function OperadorDashboard({ user, resumen, rol }) {
  return (
    <div>
      <Bienvenida nombre={user?.nombre} rol={rol} caja={resumen?.caja} />
      <div className="row">
        <Card titulo="Mis clientes" valor={fmt(resumen?.misClientes)} icono="fa-users" to="/clientes" />
        <Card titulo="Mis créditos activos" valor={fmt(resumen?.misCreditos)} icono="fa-money" to="/creditos?tab=activos" />
        <Card titulo="Mis cobros de hoy" valor={fmt(resumen?.misCobrosHoy)} icono="fa-calendar" to="/campo" />
        <Card titulo={`Mi caja`} valor={resumen?.caja?.habilitada ? `S/ ${Number(resumen.caja.efectivo || 0).toFixed(2)}` : 'Cerrada'} icono="fa-bank" to="/caja?tab=estado" />
      </div>
      <div className="row">
        <div className="col-lg-12">
          <Panel titulo="Mi operación del día">
            <Accesos items={[
              { label: 'Cobrar Crédito', icon: 'fa-money', to: '/cobrar' },
              { label: 'Cartera de cobro (hoy)', icon: 'fa-map-marker', to: '/campo' },
              { label: 'Recibo de Egresos', icon: 'fa-minus-circle', to: '/caja?tab=recibos&tipo=2' },
              { label: 'Recibo de Ingresos', icon: 'fa-plus-circle', to: '/caja?tab=recibos&tipo=1' },
              { label: 'Registrar Billetaje', icon: 'fa-list-alt', to: '/caja?tab=billetaje' },
              { label: 'Solicitar Extorno', icon: 'fa-refresh', to: '/extornos' },
              { label: 'Avance de metas', icon: 'fa-dashboard', to: '/instrumentos?vista=avance' },
              { label: 'Crear propuesta', icon: 'fa-lightbulb-o', to: '/propuestas?accion=crear' },
            ]} />
          </Panel>
        </div>
      </div>
    </div>
  );
}
