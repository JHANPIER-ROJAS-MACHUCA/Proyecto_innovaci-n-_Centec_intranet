import { useEffect, useState } from 'react';
import { useSearchParams } from 'react-router-dom';
import { propuestaService } from '../services/clienteService.js';
import { useSession } from '../contexts/AuthContext.jsx';
import { api } from '../services/api.js';

const PUEDE_CREAR = [7, 2];

export default function Propuestas() {
  const { user } = useSession();
  const [params] = useSearchParams();
  const [items, setItems] = useState([]);
  const [resp, setResp] = useState({});
  const [msg, setMsg] = useState('');
  const [det, setDet] = useState(null);
  const [form, setForm] = useState({ customer_id: '', amount: '', installment: '', rate: '', fee: '', about_business: '', about_destiny: '' });

  function cargar() {
    propuestaService.listar().then((r) => setItems(r.data || [])).catch(() => setItems([]));
  }

  useEffect(() => { cargar(); }, []);

  // ?accion=crear (PROPUESTA del antiguo, roles 3/4) -> baja al formulario.
  useEffect(() => {
    if (params.get('accion') === 'crear') {
      const t = setTimeout(() => document.getElementById('sec-crear')?.scrollIntoView({ behavior: 'smooth', block: 'start' }), 150);
      return () => clearTimeout(t);
    }
  }, [params]);

  async function crear(e) {
    e.preventDefault();
    try {
      await api.post('/api/propuestas/crear', { ...form, customer_id: Number(form.customer_id), amount: Number(form.amount) });
      setMsg('Propuesta creada.');
      setForm({ customer_id: '', amount: '', installment: '', rate: '', fee: '', about_business: '', about_destiny: '' });
      cargar();
    } catch (err) {
      setMsg(err.message);
    }
  }

  async function ver(id) {
    try {
      const r = await api.get(`/api/propuestas/detalle?id=${id}`);
      setDet(r.data);
    } catch {
      setDet(null);
    }
  }

  async function responder(id) {
    try {
      await propuestaService.responder({ proposal_id: id, response: resp[id] || '', status: 'APROBADO' });
      setMsg(`Propuesta #${id} respondida.`);
      cargar();
    } catch (err) {
      setMsg(err.message);
    }
  }

  async function evaluar() {
    if (!det) return;
    try {
      await api.post('/api/propuestas/evaluar', { id: det.id, evaluation: det.evaluation, guaranty: det.guaranty, about_experience: det.about_experience, about_family: det.about_family });
      setMsg('Evaluación guardada.');
    } catch (err) {
      setMsg(err.message);
    }
  }

  async function eliminar(id) {
    try {
      await api.post('/api/propuestas/eliminar', { id });
      setMsg(`Propuesta #${id} eliminada.`);
      setDet(null);
      cargar();
    } catch (err) {
      setMsg(err.message);
    }
  }

  return (
    <div className="row">
      {PUEDE_CREAR.includes(Number(user?.tipoU)) && (
      <div className="col-lg-5" id="sec-crear">
        <div className="ibox float-e-margins">
          <div className="ibox-title"><h5>Crear propuesta</h5></div>
          <div className="ibox-content">
            <form onSubmit={crear}>
              <div className="row">
                {[['customer_id', 'ID cliente'], ['amount', 'Monto'], ['installment', 'Cuotas'], ['rate', 'Tasa'], ['fee', 'Cuota S/']].map(([f, ph]) => (
                  <div className="col-md-6" key={f} style={{ marginBottom: 8 }}><input className="form-control" placeholder={ph} value={form[f]} onChange={(e) => setForm({ ...form, [f]: e.target.value })} /></div>
                ))}
              </div>
              <div className="form-group"><textarea className="form-control" placeholder="Sobre el negocio" value={form.about_business} onChange={(e) => setForm({ ...form, about_business: e.target.value })} /></div>
              <div className="form-group"><textarea className="form-control" placeholder="Destino del crédito" value={form.about_destiny} onChange={(e) => setForm({ ...form, about_destiny: e.target.value })} /></div>
              <button className="btn btn-success" type="submit">Crear</button>
            </form>
            {msg !== '' && <div className="alert alert-info" style={{ marginTop: 10 }}>{msg}</div>}
            {det && (
              <div className="well" style={{ marginTop: 10 }}>
                <p><strong>Propuesta #{det.id}</strong> — Monto {det.amount} × {det.installment} cuotas</p>
                <p><strong>Negocio:</strong> {det.about_business || '---'}</p>
                <p><strong>Destino:</strong> {det.about_destiny || '---'}</p>
                <div className="form-group"><textarea className="form-control" placeholder="Evaluación" value={det.evaluation || ''} onChange={(e) => setDet({ ...det, evaluation: e.target.value })} /></div>
                <div className="form-group"><textarea className="form-control" placeholder="Garantía" value={det.guaranty || ''} onChange={(e) => setDet({ ...det, guaranty: e.target.value })} /></div>
                <button className="btn btn-xs btn-success" onClick={evaluar}>Guardar evaluación</button>{' '}
                <button className="btn btn-xs btn-danger" onClick={() => eliminar(det.id)}>Eliminar</button>
              </div>
            )}
          </div>
        </div>
      </div>
      )}
      <div className={PUEDE_CREAR.includes(Number(user?.tipoU)) ? 'col-lg-7' : 'col-lg-12'}>
        <div className="ibox float-e-margins">
          <div className="ibox-title"><h5>Propuestas de crédito</h5></div>
          <div className="ibox-content">
            {msg !== '' && <div className="alert alert-info">{msg}</div>}
            <div className="table-responsive">
              <table className="table table-striped table-bordered">
                <thead><tr><th>ID</th><th>Monto</th><th>Cuotas</th><th>Respuesta</th><th>Acción</th></tr></thead>
                <tbody>{items.map((p) => (
                  <tr key={p.id}>
                    <td><a href="#/" onClick={(e) => { e.preventDefault(); ver(p.id); }}>{p.id}</a></td>
                    <td>{p.amount}</td>
                    <td>{p.installment}</td>
                    <td>{p.response ? 'Respondida' : 'Pendiente'}</td>
                    <td>
                      <div className="form-inline">
                        <input className="form-control input-sm" placeholder="Respuesta" value={resp[p.id] || ''} onChange={(e) => setResp({ ...resp, [p.id]: e.target.value })} />{' '}
                        <button className="btn btn-xs btn-primary" onClick={() => responder(p.id)}>Responder</button>
                      </div>
                    </td>
                  </tr>
                ))}</tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
