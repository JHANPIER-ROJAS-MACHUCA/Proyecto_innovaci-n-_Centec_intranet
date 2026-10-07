import { useEffect, useState } from 'react';
import { useSearchParams } from 'react-router-dom';
import { moraService, justificacionService } from '../services/clienteService.js';
import { api } from '../services/api.js';

export default function Mora() {
  const [params] = useSearchParams();
  const [deudores, setDeudores] = useState([]);
  const [form, setForm] = useState({ credit_id: '', comment: '' });
  const [msg, setMsg] = useState('');
  const [justs, setJusts] = useState([]);
  const [filtro, setFiltro] = useState('');

  function cargarJusts(creditId) {
    api.get(`/api/justificaciones${creditId ? `?credit_id=${creditId}` : ''}`).then((r) => setJusts(r.data || [])).catch(() => setJusts([]));
  }

  useEffect(() => {
    moraService.deudores().then((r) => setDeudores(r.data || [])).catch(() => setDeudores([]));
    cargarJusts('');
  }, []);

  // ?vista=justificaciones (ADMINISTRACIÓN del antiguo) -> baja a justificaciones.
  useEffect(() => {
    if (params.get('vista') === 'justificaciones') {
      const t = setTimeout(() => document.getElementById('sec-justificaciones')?.scrollIntoView({ behavior: 'smooth', block: 'start' }), 150);
      return () => clearTimeout(t);
    }
  }, [params]);

  async function justificar(e) {
    e.preventDefault();
    try {
      await justificacionService.crear({ ...form, credit_id: Number(form.credit_id) });
      setMsg('Justificación registrada.');
      setForm({ credit_id: '', comment: '' });
      cargarJusts('');
    } catch (err) {
      setMsg(err.message);
    }
  }

  return (
    <div className="row">
      <div className="col-lg-8">
        <div className="ibox float-e-margins">
          <div className="ibox-title"><h5>Seguimiento de mora</h5></div>
          <div className="ibox-content">
            <div className="table-responsive">
              <table className="table table-striped table-bordered">
                <thead><tr><th>DNI</th><th>Cliente</th><th>Crédito</th><th>Vence</th><th>Deuda</th></tr></thead>
                <tbody>{deudores.map((d, i) => (<tr key={i}><td>{d.dni}</td><td>{`${d.ap || ''} ${d.nom || ''}`}</td><td>{d.idP}</td><td>{d.fechaProg}</td><td>{(Number(d.cuota) - Number(d.montoPagado)).toFixed(2)}</td></tr>))}</tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div className="col-lg-4" id="sec-justificaciones">
        <div className="ibox float-e-margins">
          <div className="ibox-title"><h5>Justificar no pago</h5></div>
          <div className="ibox-content">
            <form onSubmit={justificar}>
              <div className="form-group"><input className="form-control" placeholder="ID crédito" value={form.credit_id} onChange={(e) => setForm({ ...form, credit_id: e.target.value })} /></div>
              <div className="form-group"><textarea className="form-control" placeholder="Motivo" value={form.comment} onChange={(e) => setForm({ ...form, comment: e.target.value })} /></div>
              <button className="btn btn-success" type="submit">Registrar</button>
            </form>
            {msg !== '' && <div className="alert alert-info" style={{ marginTop: 10 }}>{msg}</div>}
            <hr />
            <div className="form-inline">
              <div className="form-group"><input className="form-control input-sm" placeholder="Filtrar por crédito" value={filtro} onChange={(e) => setFiltro(e.target.value)} /></div>{' '}
              <button className="btn btn-xs btn-info" onClick={() => cargarJusts(filtro)}>Ver</button>
            </div>
            <div style={{ marginTop: 10 }}>
              {justs.map((j) => (
                <div className="well well-sm" key={j.id}>
                  <p><strong>Crédito #{j.credit_id}</strong> <small>{j.created_at}</small></p>
                  <p>{j.description}</p>
                  <button className="btn btn-xs btn-danger" onClick={() => justificacionService.eliminar(j.id).then(() => cargarJusts(filtro))}>Eliminar</button>
                </div>
              ))}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
