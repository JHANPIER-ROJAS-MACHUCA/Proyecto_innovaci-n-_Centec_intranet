import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { api } from '../../services/api.js';
import { Card, Panel, Bienvenida, Accesos, fmt } from './widgets.jsx';

// Rol 1 GERENTE (+7 SUPERADMIN): visión global — réplica de inicio.php para tipo 1:
// meta oficina, gráficos globales, cumpleaños, billetaje/extornos/moras.
export default function GerenteDashboard({ user, resumen, rol }) {
  const [cumple, setCumple] = useState([]);

  // Cumpleaños próximos (ventana de 5 días, igual que inicio.php del antiguo).
  useEffect(() => {
    api.get('/api/usuarios').then((r) => {
      const rows = Array.isArray(r?.data) ? r.data : [];
      const hoy = new Date();
      hoy.setHours(0, 0, 0, 0);
      const lista = [];
      rows.forEach((u) => {
        const m = String(u.birthdate || '').match(/(\d{4})-(\d{2})-(\d{2})/);
        if (!m) return;
        for (let k = 0; k <= 5; k++) {
          const d = new Date(hoy);
          d.setDate(d.getDate() + k);
          const mm = String(d.getMonth() + 1).padStart(2, '0');
          const dd = String(d.getDate()).padStart(2, '0');
          if (m[2] === mm && m[3] === dd) { lista.push({ ...u, enDias: k }); break; }
        }
      });
      lista.sort((a, b) => a.enDias - b.enDias);
      setCumple(lista);
    }).catch(() => setCumple([]));
  }, []);

  return (
    <div>
      <Bienvenida nombre={user?.nombre} rol={rol} caja={resumen?.caja} />
      <div className="row">
        <Card titulo="Clientes" valor={fmt(resumen?.clientes)} icono="fa-users" to="/clientes" />
        <Card titulo="Créditos activos" valor={fmt(resumen?.creditosActivos)} icono="fa-money" to="/creditos?tab=activos" />
        <Card titulo="Créditos propuestos" valor={fmt(resumen?.creditosPropuestos)} icono="fa-file-text-o" to="/creditos?tab=propuestos" />
        <Card titulo={`Caja: ${resumen?.caja?.habilitada ? 'Habilitada' : 'Cerrada'}`} valor={resumen?.caja ? `S/ ${Number(resumen.caja.efectivo || 0).toFixed(2)}` : ''} icono="fa-bank" to="/caja?tab=estado" />
      </div>
      <div className="row">
        <Card titulo="Moras pendientes" valor={fmt(resumen?.morasPendientes)} icono="fa-exclamation-triangle" to="/mora" />
        <Card titulo="Extornos pendientes" valor={fmt(resumen?.extornosPendientes)} icono="fa-refresh" to="/extornos?tab=solicitados" />
        <Card titulo="Billetaje por confirmar" valor={fmt(resumen?.billetajePendiente)} icono="fa-list-alt" to="/caja?tab=billetaje" />
        <Card titulo="Cobros por fecha" valor="Reporte" icono="fa-calendar" to="/reportes?vista=cobros-fecha" />
      </div>
      <div className="row">
        <div className="col-lg-8">
          <Panel titulo="Atajos de gerencia" accion={<Link to="/reportes?vista=cierre-mes">Cierre de mes</Link>}>
            <Accesos items={[
              { label: 'Caja Bodega', icon: 'fa-archive', to: '/caja?tab=boveda' },
              { label: 'Confirmar Billetaje', icon: 'fa-check-square', to: '/caja?tab=billetaje' },
              { label: 'Reporte de Cierre de Mes', icon: 'fa-bar-chart', to: '/reportes?vista=cierre-mes' },
              { label: 'Depósitos y ahorros', icon: 'fa-database', to: '/reportes?vista=depositos' },
              { label: 'Monitor de metas', icon: 'fa-dashboard', to: '/instrumentos?vista=monitor' },
              { label: 'Desembolsos', icon: 'fa-credit-card', to: '/creditos?tab=aprobados' },
              { label: 'Extornos (resolver)', icon: 'fa-gavel', to: '/extornos?tab=solicitados' },
              { label: 'Configuración', icon: 'fa-cog', to: '/administracion?tab=empresa' },
            ]} />
          </Panel>
        </div>
        <div className="col-lg-4">
          <Panel titulo="Cumpleaños próximos">
            {cumple.length === 0
              ? <p className="text-muted">Sin cumpleaños en los próximos 5 días.</p>
              : <ul style={{ paddingLeft: 18 }}>{cumple.map((u, i) => (
                <li key={u.idU || i}>
                  {u.enDias === 0
                    ? <>Hoy es el cumpleaños de <b>{`${u.nomU || ''} ${u.apU || ''}`.trim()}</b></>
                    : <>Faltan {u.enDias} {u.enDias > 1 ? 'días' : 'día'} para el cumpleaños de <b>{`${u.nomU || ''} ${u.apU || ''}`.trim()}</b></>}
                </li>
              ))}</ul>}
          </Panel>
        </div>
      </div>
    </div>
  );
}
