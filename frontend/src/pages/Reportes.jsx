import { useEffect, useState } from 'react';
import { useSearchParams } from 'react-router-dom';
import { moraService } from '../services/clienteService.js';
import { creditoService, reporteService } from '../services/operacionService.js';
import { useSession } from '../contexts/AuthContext.jsx';
import { api } from '../services/api.js';

function csv(rows) {
  if (!rows.length) return '';
  const head = Object.keys(rows[0]);
  const esc = (v) => `"${String(v ?? '').replace(/"/g, '""')}"`;
  return [head.join(','), ...rows.map((r) => head.map((h) => esc(r[h])).join(','))].join('\n');
}

function descargar(nombre, rows) {
  const blob = new Blob([csv(rows)], { type: 'text/csv;charset=utf-8' });
  const a = document.createElement('a');
  a.href = URL.createObjectURL(blob);
  a.download = nombre;
  a.click();
}

// ?vista= del submenú del rol -> sección de la página (el antiguo eran páginas separadas).
const VISTA_ANCLA = {
  'cobros-dia': 'sec-cobros',
  'cobros-fecha': 'sec-cobros',
  'desembolsos': 'sec-cobros',
  'cierre-mes': 'sec-cierre',
  'cierre-personalizado': 'sec-cierre',
  'depositos': 'sec-ahorros',
  'sentinel': 'sec-sentinel',
  'posicion-cliente': 'sec-por-cliente',
};

export default function Reportes() {
  const { user } = useSession();
  const [params] = useSearchParams();
  const restringido = [8, 5, 1].includes(Number(user?.tipoU));

  useEffect(() => {
    const id = VISTA_ANCLA[params.get('vista')];
    if (id) {
      const t = setTimeout(() => document.getElementById(id)?.scrollIntoView({ behavior: 'smooth', block: 'start' }), 150);
      return () => clearTimeout(t);
    }
  }, [params]);
  const [deudores, setDeudores] = useState([]);
  const [idCG, setIdCG] = useState('');
  const [creditos, setCreditos] = useState([]);
  const [desde, setDesde] = useState(new Date().toISOString().slice(0, 10));
  const [hasta, setHasta] = useState(new Date().toISOString().slice(0, 10));
  const [cobros, setCobros] = useState([]);
  const [desembolsos, setDesembolsos] = useState([]);
  const [asesor, setAsesor] = useState('');
  const [mes, setMes] = useState(new Date().toISOString().slice(0, 7));
  const [cierre, setCierre] = useState([]);
  const [ahDesde, setAhDesde] = useState(new Date().toISOString().slice(0, 10));
  const [ahHasta, setAhHasta] = useState(new Date().toISOString().slice(0, 10));
  const [ahorros, setAhorros] = useState([]);
  const [morasDias, setMorasDias] = useState([]);
  const [sentinel, setSentinel] = useState([]);
  const [cancelados, setCancelados] = useState([]);
  const [sinCred, setSinCred] = useState([]);
  const [vinc, setVinc] = useState([]);

  useEffect(() => {
    moraService.deudores().then((r) => setDeudores(r.data || [])).catch(() => setDeudores([]));
    reporteService.morasDias().then((r) => setMorasDias(r.data || [])).catch(() => setMorasDias([]));
    reporteService.sentinel().then((r) => setSentinel(r.data || [])).catch(() => setSentinel([]));
    reporteService.cancelados().then((r) => setCancelados(r.data || [])).catch(() => setCancelados([]));
    reporteService.sinCreditos().then((r) => setSinCred(r.data || [])).catch(() => setSinCred([]));
    reporteService.vinculaciones().then((r) => setVinc(r.data || [])).catch(() => setVinc([]));
  }, []);

  async function porFecha(e) {
    e.preventDefault();
    const q = asesor ? `&idU=${asesor}` : '';
    const [c, d] = await Promise.all([
      reporteService.cobros(desde, hasta, q).then((r) => r.data || []).catch(() => []),
      reporteService.desembolsos(desde, hasta, q).then((r) => r.data || []).catch(() => []),
    ]);
    setCobros(c);
    setDesembolsos(d);
  }

  async function verCierre(e) {
    e.preventDefault();
    try {
      const r = await reporteService.cierre(mes);
      setCierre(r.data || []);
    } catch {
      setCierre([]);
    }
  }

  async function porCliente(e) {
    e.preventDefault();
    try {
      const r = await creditoService.porCliente(idCG);
      setCreditos(r.data || []);
    } catch {
      setCreditos([]);
    }
  }

  return (
    <div className="row">
      <div className="col-lg-12">
        <div className="ibox float-e-margins">
          <div className="ibox-title"><h5>Reporte de deudores (cuotas vencidas)</h5></div>
          <div className="ibox-content">
            <button className="btn btn-success btn-sm" onClick={() => descargar('deudores.csv', deudores)}>Exportar CSV</button>
            <div className="table-responsive" style={{ marginTop: 10 }}>
              <table className="table table-striped table-bordered">
                <thead><tr><th>DNI</th><th>Cliente</th><th>Crédito</th><th>Cuota</th><th>Pagado</th><th>Vence</th></tr></thead>
                <tbody>{deudores.map((d, i) => (<tr key={i}><td>{d.dni}</td><td>{`${d.ap || ''} ${d.am || ''} ${d.nom || ''}`}</td><td>{d.idP}</td><td>{d.cuota}</td><td>{d.montoPagado}</td><td>{d.fechaProg}</td></tr>))}</tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div className="col-lg-12" id="sec-cobros">
        <div className="ibox float-e-margins">
          <div className="ibox-title"><h5>Cobros y desembolsos por fecha</h5></div>
          <div className="ibox-content">
            <form className="form-inline" onSubmit={porFecha}>
              <div className="form-group"><input type="date" className="form-control" value={desde} onChange={(e) => setDesde(e.target.value)} /></div>{' '}
              <div className="form-group"><input type="date" className="form-control" value={hasta} onChange={(e) => setHasta(e.target.value)} /></div>{' '}
              <div className="form-group"><input className="form-control" style={{ width: 110 }} placeholder="ID asesor (op.)" value={asesor} onChange={(e) => setAsesor(e.target.value)} /></div>{' '}
              <button className="btn btn-primary" type="submit">Consultar</button>{' '}
              <button className="btn btn-success btn-sm" type="button" onClick={() => descargar('cobros.csv', cobros)}>CSV cobros</button>{' '}
              <button className="btn btn-success btn-sm" type="button" onClick={() => descargar('desembolsos.csv', desembolsos)}>CSV desembolsos</button>
            </form>
            <div className="row" style={{ marginTop: 10 }}>
              <div className="col-md-6">
                <h5>Cobros ({cobros.length}) — Total S/ {cobros.reduce((a, t) => a + Number(t.total || 0), 0).toFixed(2)}</h5>
                <div className="table-responsive" style={{ maxHeight: 300, overflow: 'auto' }}>
                  <table className="table table-striped table-bordered">
                    <thead><tr><th>ID</th><th>DNI</th><th>Total</th><th>Fecha</th></tr></thead>
                    <tbody>{cobros.map((t) => (<tr key={t.idCAD}><td>{t.idCAD}</td><td>{t.dni}</td><td>{t.total}</td><td>{t.created_at}</td></tr>))}</tbody>
                  </table>
                </div>
              </div>
              <div className="col-md-6">
                <h5>Desembolsos ({desembolsos.length}) — Total S/ {desembolsos.reduce((a, t) => a + Number(t.total || 0), 0).toFixed(2)}</h5>
                <div className="table-responsive" style={{ maxHeight: 300, overflow: 'auto' }}>
                  <table className="table table-striped table-bordered">
                    <thead><tr><th>ID</th><th>DNI</th><th>Total</th><th>Fecha</th></tr></thead>
                    <tbody>{desembolsos.map((t) => (<tr key={t.idCAD}><td>{t.idCAD}</td><td>{t.dni}</td><td>{t.total}</td><td>{t.created_at}</td></tr>))}</tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      {restringido && (
      <div className="col-lg-12" id="sec-cierre">
        <div className="ibox float-e-margins">
          <div className="ibox-title"><h5>Cierre de mes</h5></div>
          <div className="ibox-content">
            <form className="form-inline" onSubmit={verCierre}>
              <div className="form-group"><input type="month" className="form-control" value={mes} onChange={(e) => setMes(e.target.value)} /></div>{' '}
              <button className="btn btn-primary" type="submit">Ver cierre</button>
            </form>
            <div className="table-responsive" style={{ marginTop: 10 }}>
              <table className="table table-striped table-bordered">
                <thead><tr><th>Tipo</th><th>Movimientos</th><th>Total</th></tr></thead>
                <tbody>{cierre.map((c, i) => (<tr key={i}><td>{c.tipo}</td><td>{c.n}</td><td>{c.total}</td></tr>))}</tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      )}
      <div className="col-lg-12">
        <div className="ibox float-e-margins">
          <div className="ibox-title"><h5>Moras por días de atraso</h5></div>
          <div className="ibox-content">
            <button className="btn btn-success btn-sm" onClick={() => descargar('moras_dias.csv', morasDias)}>Exportar CSV</button>
            <div className="table-responsive" style={{ marginTop: 10, maxHeight: 300, overflow: 'auto' }}>
              <table className="table table-striped table-bordered">
                <thead><tr><th>DNI</th><th>Cliente</th><th>Crédito</th><th>Vence</th><th>Días</th><th>Deuda</th></tr></thead>
                <tbody>{morasDias.map((d, i) => (<tr key={i}><td>{d.dni}</td><td>{`${d.ap || ''} ${d.nom || ''}`}</td><td>{d.idP}</td><td>{d.fechaProg}</td><td>{d.dias}</td><td>{d.deuda}</td></tr>))}</tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div className="col-lg-6" id="sec-sentinel">
        <div className="ibox float-e-margins">
          <div className="ibox-title"><h5>Reporte sentinel (créditos con relaciones)</h5></div>
          <div className="ibox-content">
            <div className="table-responsive" style={{ maxHeight: 300, overflow: 'auto' }}>
              <table className="table table-striped table-bordered">
                <thead><tr><th>Crédito</th><th>DNI</th><th>Monto</th><th>Relaciones</th></tr></thead>
                <tbody>{sentinel.map((s, i) => (<tr key={i}><td>{s.idP}</td><td>{s.dni}</td><td>{s.montoPropuesto}</td><td>{s.relaciones}</td></tr>))}</tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div className="col-lg-6">
        <div className="ibox float-e-margins">
          <div className="ibox-title"><h5>Finalizados y cancelados</h5></div>
          <div className="ibox-content">
            <div className="table-responsive" style={{ maxHeight: 300, overflow: 'auto' }}>
              <table className="table table-striped table-bordered">
                <thead><tr><th>Crédito</th><th>DNI</th><th>Estado</th></tr></thead>
                <tbody>{cancelados.map((s, i) => (<tr key={i}><td>{s.idP}</td><td>{s.dni}</td><td>{s.estado}</td></tr>))}</tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div className="col-lg-6">
        <div className="ibox float-e-margins">
          <div className="ibox-title"><h5>Clientes sin créditos</h5></div>
          <div className="ibox-content">
            <button className="btn btn-success btn-sm" onClick={() => descargar('sin_creditos.csv', sinCred)}>Exportar CSV</button>
            <div className="table-responsive" style={{ marginTop: 10, maxHeight: 300, overflow: 'auto' }}>
              <table className="table table-striped table-bordered">
                <thead><tr><th>DNI</th><th>Cliente</th><th>Celular</th></tr></thead>
                <tbody>{sinCred.map((s, i) => (<tr key={i}><td>{s.dni}</td><td>{`${s.ap || ''} ${s.nom || ''}`}</td><td>{s.cel}</td></tr>))}</tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div className="col-lg-6">
        <div className="ibox float-e-margins">
          <div className="ibox-title"><h5>Vinculaciones</h5></div>
          <div className="ibox-content">
            <div className="table-responsive" style={{ maxHeight: 300, overflow: 'auto' }}>
              <table className="table table-striped table-bordered">
                <thead><tr><th>ID</th><th>Titular</th><th>Cónyuge</th><th>Aval</th></tr></thead>
                <tbody>{vinc.map((v) => (<tr key={v.idV}><td>{v.idV}</td><td>{v.titular}</td><td>{v.conyugue}</td><td>{v.aval}</td></tr>))}</tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      {restringido && (
      <div className="col-lg-12" id="sec-ahorros">
        <div className="ibox float-e-margins">
          <div className="ibox-title"><h5>Depósitos y ahorros por fecha</h5></div>
          <div className="ibox-content">
            <form className="form-inline" onSubmit={async (e) => {
              e.preventDefault();
              try {
                const r = await api.get(`/api/reportes/ahorros-fecha?desde=${ahDesde}&hasta=${ahHasta}`);
                setAhorros(r.data || []);
              } catch {
                setAhorros([]);
              }
            }}>
              <div className="form-group"><input type="date" className="form-control" value={ahDesde} onChange={(e) => setAhDesde(e.target.value)} /></div>{' '}
              <div className="form-group"><input type="date" className="form-control" value={ahHasta} onChange={(e) => setAhHasta(e.target.value)} /></div>{' '}
              <button className="btn btn-primary" type="submit">Consultar</button>{' '}
              <button className="btn btn-success btn-sm" type="button" onClick={() => descargar('ahorros.csv', ahorros)}>Exportar CSV</button>
            </form>
            <div className="table-responsive" style={{ marginTop: 10, maxHeight: 300, overflow: 'auto' }}>
              <table className="table table-striped table-bordered">
                <thead><tr><th>ID</th><th>DNI</th><th>Monto</th><th>Tipo</th><th>Fecha</th></tr></thead>
                <tbody>{ahorros.map((a) => (<tr key={a.idAd}><td>{a.idAd}</td><td>{a.dni}</td><td>{a.monto}</td><td>{a.tipo}</td><td>{a.fecha}</td></tr>))}</tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      )}
      <div className="col-lg-12" id="sec-por-cliente">
        <div className="ibox float-e-margins">
          <div className="ibox-title"><h5>Créditos por cliente (posición del cliente)</h5></div>
          <div className="ibox-content">
            <form className="form-inline" onSubmit={porCliente}>
              <div className="form-group"><input className="form-control" placeholder="idCG" value={idCG} onChange={(e) => setIdCG(e.target.value)} /></div>{' '}
              <button className="btn btn-primary" type="submit">Consultar</button>{' '}
              <button className="btn btn-success btn-sm" type="button" onClick={() => descargar('creditos.csv', creditos)}>Exportar CSV</button>
            </form>
            <div className="table-responsive" style={{ marginTop: 10 }}>
              <table className="table table-striped table-bordered">
                <thead><tr><th>ID</th><th>Monto</th><th>Estado</th></tr></thead>
                <tbody>{creditos.map((c) => (<tr key={c.idP}><td>{c.idP}</td><td>{c.montoPropuesto ?? c.capital}</td><td>{c.estado}</td></tr>))}</tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
