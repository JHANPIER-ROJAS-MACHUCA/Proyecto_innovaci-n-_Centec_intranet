import { Link } from 'react-router-dom';
import { Card, Panel, Bienvenida, Accesos, fmt } from './widgets.jsx';

// Rol 2 ADMINISTRADOR: como gerencia en metas/caja + gestión de usuarios y oficinas.
// (head.php: Recibo Egresos, Confirmar Billetaje, Condonación, Cartera, Justificaciones)
export default function AdminDashboard({ user, resumen, rol }) {
  return (
    <div>
      <Bienvenida nombre={user?.nombre} rol={rol} caja={resumen?.caja} />
      <div className="row">
        <Card titulo="Clientes" valor={fmt(resumen?.clientes)} icono="fa-users" to="/clientes" />
        <Card titulo="Créditos activos" valor={fmt(resumen?.creditosActivos)} icono="fa-money" to="/creditos?tab=activos" />
        <Card titulo="Créditos propuestos" valor={fmt(resumen?.creditosPropuestos)} icono="fa-file-text-o" to="/creditos?tab=propuestos" />
        <Card titulo="Cartera de cobro" valor="Hoy" icono="fa-map-marker" to="/campo" />
      </div>
      {(resumen?.usuariosActivos !== undefined || resumen?.oficinas !== undefined) && (
        <p className="text-muted">Usuarios activos: <strong>{fmt(resumen?.usuariosActivos)}</strong> · Oficinas: <strong>{fmt(resumen?.oficinas)}</strong> <small>(gestión en CONFIGURACIÓN, solo gerencia)</small></p>
      )}
      <div className="row">
        <Card titulo="Moras pendientes" valor={fmt(resumen?.morasPendientes)} icono="fa-exclamation-triangle" to="/mora" />
        <Card titulo="Extornos pendientes" valor={fmt(resumen?.extornosPendientes)} icono="fa-refresh" to="/extornos" />
        <Card titulo="Billetaje por confirmar" valor={fmt(resumen?.billetajePendiente)} icono="fa-list-alt" to="/caja?tab=billetaje" />
        <Card titulo={`Caja: ${resumen?.caja?.habilitada ? 'Abierta' : 'Cerrada'}`} valor={resumen?.caja ? `S/ ${Number(resumen.caja.efectivo || 0).toFixed(2)}` : ''} icono="fa-bank" to="/caja?tab=estado" />
      </div>
      <div className="row">
        <div className="col-lg-12">
          <Panel titulo="Atajos de administración" accion={<Link to="/creditos?tab=propuestos">Préstamos propuestos</Link>}>
            <Accesos items={[
              { label: 'Condonación de Mora', icon: 'fa-gift', to: '/cobrar?accion=condonar' },
              { label: 'Administrar Cartera', icon: 'fa-briefcase', to: '/creditos?tab=activos&admin=1' },
              { label: 'Ver justificaciones', icon: 'fa-eye', to: '/mora?vista=justificaciones' },
              { label: 'Confirmar Billetaje', icon: 'fa-check-square', to: '/caja?tab=billetaje' },
              { label: 'Recibo de Egresos', icon: 'fa-minus-circle', to: '/caja?tab=recibos&tipo=2' },
              { label: 'Prestamos propuestos', icon: 'fa-file-text-o', to: '/creditos?tab=propuestos' },
              { label: 'Reporte sentinel', icon: 'fa-shield', to: '/reportes?vista=sentinel' },
              { label: 'Movimientos de caja', icon: 'fa-list', to: '/caja?tab=movimientos' },
            ]} />
          </Panel>
        </div>
      </div>
    </div>
  );
}
