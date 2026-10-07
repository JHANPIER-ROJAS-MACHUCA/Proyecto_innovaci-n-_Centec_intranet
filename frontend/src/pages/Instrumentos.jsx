import { useEffect, useState } from 'react';
import { useSearchParams } from 'react-router-dom';
import { metaService } from '../services/clienteService.js';
import { justificacionService } from '../services/clienteService.js';
import { metaServiceExt } from '../services/operacionService.js';
import { api } from '../services/api.js';

// ?vista= del submenú del rol -> sección de la página.
const VISTA_ANCLA = {
  'monitor': 'sec-monitor',
  'resumen': 'sec-metas',
  'avance': 'sec-monitor',
  'proyecciones': 'sec-proyecciones',
};

export default function Instrumentos() {
  const [params] = useSearchParams();
  const [form, setForm] = useState({ name: '', amount: '' });
  const [msg, setMsg] = useState('');
  const [monitor, setMonitor] = useState([]);
  const [metas, setMetas] = useState([]);
  const [desde, setDesde] = useState(new Date(Date.now() - 30 * 864e5).toISOString().slice(0, 10));
  const [hasta, setHasta] = useState(new Date().toISOString().slice(0, 10));
  const [proy, setProy] = useState(null);

  useEffect(() => {
    metaServiceExt.porUsuario().then((r) => setMonitor(r.data || [])).catch(() => setMonitor([]));
    api.get('/api/metas').then((r) => setMetas(r.data || [])).catch(() => setMetas([]));
  }, []);

  useEffect(() => {
    const id = VISTA_ANCLA[params.get('vista')];
    if (id) {
      const t = setTimeout(() => document.getElementById(id)?.scrollIntoView({ behavior: 'smooth', block: 'start' }), 150);
      return () => clearTimeout(t);
    }
  }, [params]);

  async function verProyeccion(e) {
    e.preventDefault();
    try {
      const r = await api.get(`/api/reportes/proyecciones?desde=${desde}&hasta=${hasta}`);
      setProy(r.data);
    } catch {
      setProy(null);
    }
  }

  async function crear(e) {
    e.preventDefault();
    try {
      await metaService.crear({ ...form, amount: Number(form.amount) });
      setMsg('Meta registrada.');
      setForm({ name: '', amount: '' });
      api.get('/api/metas').then((r) => setMetas(r.data || [])).catch(() => {});
    } catch (err) {
      setMsg(err.message);
    }
  }

  return (
    <div className="row">
      <div className="col-lg-12" id="sec-proyecciones">
        <div className="ibox float-e-margins">
          <div className="ibox-title"><h5>Proyecciones (desembolsos · cobros · cartera proyectada)</h5></div>
          <div className="ibox-content">
            <form className="form-inline" onSubmit={verProyeccion}>
              <div className="form-group"><input type="date" className="form-control" value={desde} onChange={(e) => setDesde(e.target.value)} /></div>{' '}
              <div className="form-group"><input type="date" className="form-control" value={hasta} onChange={(e) => setHasta(e.target.value)} /></div>{' '}
              <button className="btn btn-primary" type="submit">Ver</button>
            </form>
            {proy && (
              <div className="row" style={{ marginTop: 10 }}>
                <div className="col-md-4"><div className="widget style1"><h4>Desembolsado</h4><h2>S/ {Number(proy.desembolso?.totalDesembolso || 0).toFixed(2)}</h2></div></div>
                <div className="col-md-4"><div className="widget style1"><h4>Cobrado (cuota+mora)</h4><h2>S/ {Number(proy.cobros?.total || 0).toFixed(2)}</h2></div></div>
                <div className="col-md-4"><div className="widget style1"><h4>Proyectado</h4><h2>S/ {Number(proy.proyeccion?.cuota || 0).toFixed(2)}</h2></div></div>
              </div>
            )}
          </div>
        </div>
      </div>
      <div className="col-lg-12" id="sec-monitor">
        <div className="ibox float-e-margins">
          <div className="ibox-title"><h5>Monitor de seguimiento de metas</h5></div>
          <div className="ibox-content">
            <div className="table-responsive">
              <table className="table table-striped table-bordered">
                <thead><tr><th>ID</th><th>Usuario</th><th>Operación</th><th>Saldo</th><th>Inicio</th><th>Fin</th></tr></thead>
                <tbody>{monitor.map((m) => (<tr key={m.id}><td>{m.id}</td><td>{m.dniU}</td><td>{m.operation}</td><td>{m.saldo}</td><td>{m.start_at}</td><td>{m.end_at}</td></tr>))}</tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div className="col-lg-6" id="sec-metas">
        <div className="ibox float-e-margins">
          <div className="ibox-title"><h5>Metas</h5></div>
          <div className="ibox-content">
            <form className="form-inline" onSubmit={crear}>
              <div className="form-group"><input className="form-control" placeholder="Nombre" value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })} /></div>{' '}
              <div className="form-group"><input className="form-control" placeholder="Monto" value={form.amount} onChange={(e) => setForm({ ...form, amount: e.target.value })} /></div>{' '}
              <button className="btn btn-success" type="submit">Crear</button>
            </form>
            {msg !== '' && <div className="alert alert-info" style={{ marginTop: 10 }}>{msg}</div>}
            <div className="table-responsive" style={{ marginTop: 10, maxHeight: 250, overflow: 'auto' }}>
              <table className="table table-striped table-bordered">
                <thead><tr><th>ID</th><th>Nombre</th><th>Monto</th><th></th></tr></thead>
                <tbody>{metas.map((m) => (
                  <tr key={m.id}>
                    <td>{m.id}</td>
                    <td><input className="form-control input-sm" value={m.name || ''} onChange={(e) => setMetas(metas.map((x) => x.id === m.id ? { ...x, name: e.target.value } : x))} /></td>
                    <td><input className="form-control input-sm" style={{ width: 100 }} value={m.amount ?? ''} onChange={(e) => setMetas(metas.map((x) => x.id === m.id ? { ...x, amount: e.target.value } : x))} /></td>
                    <td>
                      <div className="btn-group">
                        <button className="btn btn-xs btn-primary" onClick={() => api.post('/api/metas/actualizar', { id: m.id, name: m.name, amount: m.amount }).then(() => setMsg('Meta actualizada.'))}>Guardar</button>
                        <button className="btn btn-xs btn-danger" onClick={() => api.post('/api/metas/eliminar', { id: m.id }).then(() => setMetas(metas.filter((x) => x.id !== m.id)))}>Eliminar</button>
                      </div>
                    </td>
                  </tr>
                ))}</tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div className="col-lg-6">
        <div className="ibox float-e-margins">
          <div className="ibox-title"><h5>Justificación de no pago</h5></div>
          <div className="ibox-content">
            <JustificacionForm />
          </div>
        </div>
      </div>
    </div>
  );
}

function JustificacionForm() {
  const [form, setForm] = useState({ credit_id: '', comment: '' });
  const [msg, setMsg] = useState('');
  async function crear(e) {
    e.preventDefault();
    try {
      await justificacionService.crear({ ...form, credit_id: Number(form.credit_id) });
      setMsg('Justificación registrada.');
      setForm({ credit_id: '', comment: '' });
    } catch (err) {
      setMsg(err.message);
    }
  }
  return (
    <form className="form-inline" onSubmit={crear}>
      <div className="form-group"><input className="form-control" placeholder="ID crédito" value={form.credit_id} onChange={(e) => setForm({ ...form, credit_id: e.target.value })} /></div>{' '}
      <div className="form-group"><input className="form-control" placeholder="Motivo" value={form.comment} onChange={(e) => setForm({ ...form, comment: e.target.value })} /></div>{' '}
      <button className="btn btn-success" type="submit">Registrar</button>
      {msg !== '' && <div className="alert alert-info" style={{ marginTop: 10 }}>{msg}</div>}
    </form>
  );
}
