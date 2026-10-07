import { useEffect, useState } from 'react';
import { Link, useSearchParams } from 'react-router-dom';
import { useSession } from '../contexts/AuthContext.jsx';
import { extornoService } from '../services/operacionService.js';
import { api } from '../services/api.js';

const PUEDE_VER = [8, 5, 1];

export default function Extornos() {
  const { user } = useSession();
  const [params] = useSearchParams();
  const puedeVer = PUEDE_VER.includes(Number(user?.tipoU));
  const [lista, setLista] = useState([]);
  const [eliminados, setEliminados] = useState([]);
  // El tab lo gobierna la URL (?tab=) para que SERVICIOS abra la vista correcta.
  const tabParam = params.get('tab');
  const tab = tabParam === 'eliminados' ? 'eliminados' : 'solicitados';
  const [page, setPage] = useState(1);
  const [form, setForm] = useState({ codigo: '', motivo: '' });
  const [msg, setMsg] = useState('');

  function cargar() {
    if (!puedeVer) return;
    extornoService.listar().then((r) => setLista(r.data || [])).catch(() => setLista([]));
    api.get(`/api/reportes/eliminados?page=${page}`).then((r) => setEliminados(r.data || [])).catch(() => setEliminados([]));
  }

  useEffect(() => { cargar(); }, [page]);

  async function solicitar(e) {
    e.preventDefault();
    try {
      await extornoService.solicitar(form);
      setMsg('Extorno solicitado.');
      setForm({ codigo: '', motivo: '' });
    } catch (err) {
      setMsg(err.message);
    }
  }

  return (
    <div className="row">
      <div className="col-lg-5">
        <div className="ibox float-e-margins">
          <div className="ibox-title"><h5>Solicitar extorno</h5></div>
          <div className="ibox-content">
            <form onSubmit={solicitar}>
              <div className="form-group"><input className="form-control" placeholder="Código de transacción (idCAD)" value={form.codigo} onChange={(e) => setForm({ ...form, codigo: e.target.value })} /></div>
              <div className="form-group"><textarea className="form-control" placeholder="Motivo" value={form.motivo} onChange={(e) => setForm({ ...form, motivo: e.target.value })} /></div>
              <button className="btn btn-warning" type="submit">Solicitar</button>
            </form>
            {msg !== '' && <div className="alert alert-info" style={{ marginTop: 10 }}>{msg}</div>}
          </div>
        </div>
      </div>
      <div className="col-lg-7">
        {puedeVer && (
        <div className="ibox float-e-margins">
          <div className="ibox-title"><h5>Extornos</h5></div>
          <div className="ibox-content">
            <ul className="nav nav-tabs">
              {['solicitados', 'eliminados'].map((t) => (
                <li key={t} className={tab === t ? 'active' : ''}><Link to={`/extornos?tab=${t}`}>{t[0].toUpperCase() + t.slice(1)}</Link></li>
              ))}
            </ul>
            <div style={{ marginTop: 10 }}>
            {tab === 'solicitados' && (
            <div className="table-responsive">
              <table className="table table-striped table-bordered">
                <thead><tr><th>Código</th><th>Motivo</th><th>Fecha</th><th>Usuario</th><th></th></tr></thead>
                <tbody>{lista.map((x, i) => (
                  <tr key={i}>
                    <td>{x.cod}</td>
                    <td>{x.motivo}</td>
                    <td>{x.fecha}</td>
                    <td>{x.dniU}</td>
                    <td><button className="btn btn-xs btn-danger" onClick={() => extornoService.resolver(x.cod).then(cargar)}>Aplicar</button></td>
                  </tr>
                ))}</tbody>
              </table>
            </div>
            )}
            {tab === 'eliminados' && (
              <div>
                <div className="table-responsive">
                  <table className="table table-striped table-bordered">
                    <thead><tr><th>ID</th><th>Cliente</th><th>Total</th><th>Usuario</th></tr></thead>
                    <tbody>{eliminados.map((t) => (<tr key={t.idCAD}><td>{t.idCAD}</td><td>{`${t.ap || ''} ${t.nom || ''}`}</td><td>{t.total}</td><td>{`${t.apU || ''} ${t.nomU || ''}`}</td></tr>))}</tbody>
                  </table>
                </div>
                <button className="btn btn-xs btn-default" disabled={page <= 1} onClick={() => setPage(page - 1)}>Anterior</button>{' '}
                <span>Página {page}</span>{' '}
                <button className="btn btn-xs btn-default" onClick={() => setPage(page + 1)}>Siguiente</button>
              </div>
            )}
            </div>
          </div>
        </div>
        )}
      </div>
    </div>
  );
}
