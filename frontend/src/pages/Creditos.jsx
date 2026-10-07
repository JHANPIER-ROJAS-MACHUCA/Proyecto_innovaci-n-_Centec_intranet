import { useEffect, useState } from 'react';
import { useSearchParams } from 'react-router-dom';
import { useCreditos } from '../hooks/useCreditos.js';
import { creditoService } from '../services/operacionService.js';

const TABS = [
  { key: 'propuestos', label: 'Propuestos', estado: 1 },
  { key: 'aprobados', label: 'Aprobados / Desembolsos', estado: 2 },
  { key: 'activos', label: 'Activos', estado: 4 },
  { key: 'finalizados', label: 'Finalizados', estado: 5 },
  { key: 'todos', label: 'Finalizados y activos', estado: '4,5' },
];

export default function Creditos() {
  const [idCG, setIdCG] = useState('');
  const { items, loading, error, cargar, cobrar } = useCreditos(idCG);
  const [form, setForm] = useState({ idCG: '', txtmonto: '', txtcuotaf1: '', txtinteres: '', txtpago: '1', txtplazo: '', txttipoPresta: '', txtmora: '', txtdni: '', txtdni1: '' });
  const [tipos, setTipos] = useState([]);
  const [editando, setEditando] = useState(null);
  const [edit, setEdit] = useState({ tasa: '', fechaDesembolso: '', prestamo: '', tipoPago: '1', fechaInicio: '', plazo: '' });

  useEffect(() => {
    creditoService.tipos().then((r) => setTipos(r.data || [])).catch(() => setTipos([]));
  }, []);
  const [msg, setMsg] = useState('');
  const [params] = useSearchParams();
  const [lista, setLista] = useState([]);
  const tab = params.get('tab') || 'activos';

  useEffect(() => {
    const t = TABS.find((x) => x.key === tab) || TABS[1];
    creditoService.porEstado(t.estado).then((r) => setLista(r.data || [])).catch(() => setLista([]));
  }, [tab]);

  async function generar(e) {
    e.preventDefault();
    setMsg('');
    try {
      await creditoService.generar({ identi: Number(form.idCG), txtmonto: Number(form.txtmonto), txtcuotaf1: Number(form.txtcuotaf1), txtinteres: Number(form.txtinteres), txtpago: Number(form.txtpago), txtplazo: Number(form.txtplazo), txttipoPresta: form.txttipoPresta, txtmora: Number(form.txtmora), txtdni: form.txtdni, txtdni1: form.txtdni1 });
      setMsg('Crédito generado (propuesto).');
    } catch (err) {
      setMsg(err.message);
    }
  }

  async function guardarEdicion(e) {
    e.preventDefault();
    try {
      await creditoService.editar({ ...edit, creditId: editando });
      setMsg('Crédito editado (cuotas regeneradas).');
      setEditando(null);
    } catch (err) {
      setMsg(err.message);
    }
  }

  async function accion(c, op) {
    setMsg('');
    try {
      if (op === 'cobrar') await cobrar({ creditId: c.idP, value: 1, paymentType: 'cuota' });
      else if (op === 'confirmar') await creditoService.confirmar({ edit_id: c.idP, edit_montoa: c.montoPropuesto ?? c.capital, edit_taza: c.taza ?? 0 });
      else await creditoService[op]({ idP: c.idP });
      setMsg(`Operación ${op} ejecutada.`);
      cargar();
    } catch (err) {
      setMsg(err.message);
    }
  }

  return (
    <div className="row">
      <div className="col-lg-12">
        <div className="ibox float-e-margins">
          <div className="ibox-title"><h5>Préstamos por estado</h5></div>
          <div className="ibox-content">
            <ul className="nav nav-tabs">
              {TABS.map((t) => (
                <li key={t.key} className={tab === t.key ? 'active' : ''}><a href={`#/creditos?tab=${t.key}`}>{t.label} ({t.key === tab ? lista.length : '…'})</a></li>
              ))}
            </ul>
            <div className="table-responsive" style={{ marginTop: 10 }}>
              <table className="table table-striped table-bordered">
                <thead><tr><th>ID</th><th>Cliente</th><th>Monto</th><th>Estado</th></tr></thead>
                <tbody>{lista.map((c) => (<tr key={c.idP}><td>{c.idP}</td><td>{c.idCG}</td><td>{c.montoPropuesto ?? c.capital}</td><td>{c.estado}</td></tr>))}</tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div className="col-lg-12">
        <div className="ibox float-e-margins">
          <div className="ibox-title"><h5>Créditos por cliente</h5></div>
          <div className="ibox-content">
            <form className="form-inline" onSubmit={(e) => { e.preventDefault(); cargar(); }}>
              <div className="form-group">
                <input className="form-control" placeholder="idCG del cliente" value={idCG} onChange={(e) => setIdCG(e.target.value)} />
              </div>{' '}
              <button className="btn btn-primary" type="submit">Cargar</button>
            </form>
            <hr />
            <form onSubmit={generar}>
              <div className="row">
                {[['idCG', 'ID titular'], ['txtmonto', 'Monto'], ['txtcuotaf1', 'Cuota'], ['txtinteres', 'Interés %'], ['txtplazo', 'Plazo'], ['txtmora', 'Mora'], ['txtdni', 'DNI cónyuge'], ['txtdni1', 'DNI aval']].map(([f, ph]) => (
                  <div className="col-md-4" key={f} style={{ marginBottom: 8 }}><input className="form-control" placeholder={ph} value={form[f]} onChange={(e) => setForm({ ...form, [f]: e.target.value })} /></div>
                ))}
                <div className="col-md-4" style={{ marginBottom: 8 }}>
                  <select className="form-control" value={form.txttipoPresta} onChange={(e) => setForm({ ...form, txttipoPresta: e.target.value })}>
                    <option value="">Tipo préstamo…</option>
                    {tipos.map((t) => (<option key={t.id} value={t.id}>{t.name}</option>))}
                  </select>
                </div>
                <div className="col-md-4" style={{ marginBottom: 8 }}>
                  <select className="form-control" value={form.txtpago} onChange={(e) => setForm({ ...form, txtpago: e.target.value })}>
                    <option value="1">Diario</option>
                    <option value="2">Semanal</option>
                    <option value="3">Único</option>
                    <option value="4">Mensual</option>
                  </select>
                </div>
              </div>
              <button className="btn btn-success" type="submit">Generar préstamo</button>
            </form>
            {editando && (
              <div style={{ position: 'fixed', inset: 0, zIndex: 3000, background: 'rgba(0,0,0,0.5)', padding: 20 }}>
                <div style={{ background: 'white', maxWidth: 500, margin: '0 auto', borderRadius: 5, padding: 20 }}>
                  <h4>Editar crédito #{editando} <button className="btn btn-xs btn-default pull-right" onClick={() => setEditando(null)}>X</button></h4>
                  <form onSubmit={guardarEdicion}>
                    {[['tasa', 'Tasa'], ['fechaDesembolso', 'F. desembolso'], ['prestamo', 'Monto aprobado'], ['fechaInicio', 'F. inicio'], ['plazo', 'Plazo']].map(([f, ph]) => (
                      <div className="form-group" key={f}><input type={f.startsWith('fecha') ? 'date' : 'text'} className="form-control" placeholder={ph} value={edit[f]} onChange={(e) => setEdit({ ...edit, [f]: e.target.value })} /></div>
                    ))}
                    <div className="form-group">
                      <select className="form-control" value={edit.tipoPago} onChange={(e) => setEdit({ ...edit, tipoPago: e.target.value })}>
                        <option value="1">Diario</option>
                        <option value="2">Semanal</option>
                        <option value="3">Único</option>
                        <option value="4">Mensual</option>
                        <option value="5">Quincenal</option>
                      </select>
                    </div>
                    <button className="btn btn-success" type="submit">Guardar (regenera cuotas)</button>
                  </form>
                </div>
              </div>
            )}
            {msg !== '' && <div className="alert alert-info" style={{ marginTop: 10 }}>{msg}</div>}
            {error !== '' && <div className="alert alert-danger" style={{ marginTop: 10 }}>{error}</div>}
            <div className="table-responsive" style={{ marginTop: 10 }}>
              <table className="table table-striped table-bordered">
                <thead><tr><th>ID</th><th>Monto</th><th>Estado</th><th>Acciones</th></tr></thead>
                <tbody>
                  {loading && <tr><td colSpan="4">Cargando...</td></tr>}
                  {items.map((c) => (
                    <tr key={c.idP || c.id}>
                      <td>{c.idP}</td>
                      <td>{c.montoPropuesto ?? c.capital}</td>
                      <td>{c.estado}</td>
                      <td>
                        <div className="btn-group">
                          <button className="btn btn-xs btn-info" onClick={() => accion(c, 'confirmar')}>Confirmar</button>
                          <button className="btn btn-xs btn-primary" onClick={() => accion(c, 'activar')}>Activar</button>
                          <button className="btn btn-xs btn-warning" onClick={() => accion(c, 'desembolsar')}>Desembolsar</button>
                          <button className="btn btn-xs btn-success" onClick={() => accion(c, 'cobrar')}>Cobrar</button>
                          <button className="btn btn-xs btn-default" onClick={() => { setEditando(c.idP); setEdit({ tasa: c.taza || '', fechaDesembolso: c.fechaDesembolso || '', prestamo: c.montoAprovado || c.montoPropuesto || '', tipoPago: String(c.pago || '1'), fechaInicio: c.started_at || '', plazo: c.plazo || '' }); }}>Editar</button>
                        </div>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
