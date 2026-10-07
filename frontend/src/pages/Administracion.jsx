import { useEffect, useState } from 'react';
import { Link, useSearchParams } from 'react-router-dom';
import { useSession } from '../contexts/AuthContext.jsx';
import { oficinaService } from '../services/clienteService.js';
import { api } from '../services/api.js';

// ?tab= del submenú CONFIGURACIÓN (oficinas/empresa existen; depositos/operaciones/metas aún no tienen UI).
const TAB_ANCLA = { oficinas: 'sec-oficinas', empresa: 'sec-empresa' };

export default function Administracion() {
  const { user } = useSession();
  const [params] = useSearchParams();
  const [oficinas, setOficinas] = useState([]);
  const [empresa, setEmpresa] = useState(null);
  const puedeEmpresa = user && [1].includes(Number(user.tipoU));

  useEffect(() => {
    oficinaService.listar().then((r) => setOficinas(r.data || [])).catch(() => setOficinas([]));
    if (puedeEmpresa) api.get('/api/empresa').then((r) => setEmpresa(r.data)).catch(() => setEmpresa(null));
  }, []);

  useEffect(() => {
    const id = TAB_ANCLA[params.get('tab')];
    if (id) {
      const t = setTimeout(() => document.getElementById(id)?.scrollIntoView({ behavior: 'smooth', block: 'start' }), 150);
      return () => clearTimeout(t);
    }
  }, [params]);

  return (
    <div className="row">
      <div className="col-lg-6">
        <div className="ibox float-e-margins">
          <div className="ibox-title"><h5>Administración</h5></div>
          <div className="ibox-content">
            <p><Link className="btn btn-primary" to="/usuarios">Gestionar usuarios</Link></p>
            <p className="text-muted">Alta, baja, cambio de estado y restablecimiento de clave (123456).</p>
          </div>
        </div>
      </div>
      <div className="col-lg-6" id="sec-oficinas">
        <div className="ibox float-e-margins">
          <div className="ibox-title"><h5>Oficinas</h5></div>
          <div className="ibox-content">
            <div className="table-responsive">
              <table className="table table-striped table-bordered">
                <thead><tr><th>ID</th><th>Dirección</th><th>Distrito</th><th>Responsable</th></tr></thead>
                <tbody>{oficinas.map((o) => (<tr key={o.idO}><td>{o.idO}</td><td>{o.direccion}</td><td>{o.distrito}</td><td>{o.responsable}</td></tr>))}</tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      {puedeEmpresa && empresa && (
      <div className="col-lg-12" id="sec-empresa">
        <div className="ibox float-e-margins">
          <div className="ibox-title"><h5>Empresa — {empresa.titulo}</h5></div>
          <div className="ibox-content">
            <div className="row">
              {[['nombreEmpresa', 'Nombre'], ['siglas', 'Siglas'], ['ruc', 'RUC'], ['representante', 'Representante'], ['dnir', 'DNI representante'], ['direccion', 'Dirección'], ['partida', 'Partida'], ['color', 'Color'], ['comentario', 'Comentario']].map(([f, ph]) => (
                <div className="col-md-4" key={f} style={{ marginBottom: 8 }}>
                  <label>{ph}</label>
                  <input className="form-control" value={empresa[f] || ''} onChange={(e) => setEmpresa({ ...empresa, [f]: e.target.value })} />
                </div>
              ))}
            </div>
            <button className="btn btn-primary" onClick={() => api.post('/api/empresa/actualizar', empresa)}>Guardar empresa</button>
          </div>
        </div>
      </div>
      )}
    </div>
  );
}
