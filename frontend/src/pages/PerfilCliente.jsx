import { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import { clienteService, relacionService } from '../services/clienteService.js';
import { api } from '../services/api.js';

function Ibox({ titulo, children }) {
  return (
    <div className="ibox float-e-margins">
      <div className="ibox-title"><h5>{titulo}</h5></div>
      <div className="ibox-content">{children}</div>
    </div>
  );
}

export default function PerfilCliente() {
  const { idCG } = useParams();
  const [c, setC] = useState(null);
  const [tab, setTab] = useState('resumen');
  const [rels, setRels] = useState([]);
  const [galeria, setGaleria] = useState([]);
  const [ahorros, setAhorros] = useState([]);
  const [operaciones, setOperaciones] = useState([]);
  const [dni, setDni] = useState('');
  const [tipo, setTipo] = useState('spouse');

  function cargar() {
    clienteService.detalle(idCG).then((r) => setC(r.data)).catch(() => setC(null));
    relacionService.listar(idCG).then((r) => setRels(r.data || [])).catch(() => setRels([]));
    api.get(`/api/adjuntos?customer_id=${idCG}`).then((r) => setGaleria(r.data || [])).catch(() => setGaleria([]));
    api.get(`/api/ahorros/detalle?customer_id=${idCG}`).then((r) => setAhorros(r.data || [])).catch(() => setAhorros([]));
    api.get(`/api/clientes/operaciones?idCG=${idCG}`).then((r) => setOperaciones(r.data || [])).catch(() => setOperaciones([]));
  }

  async function subir(e) {
    const file = e.target.files[0];
    if (!file) return;
    const fd = new FormData();
    fd.append('file', file);
    const res = await fetch('/Centecp_Intranet/backend/public/index.php/api/adjuntos/subir', { method: 'POST', body: fd, credentials: 'same-origin' });
    const j = await res.json();
    await api.post('/api/adjuntos', { customer_id: Number(idCG), file: j.data?.name || file.name });
    cargar();
  }

  useEffect(() => { cargar(); }, [idCG]);

  async function agregar(e) {
    e.preventDefault();
    await relacionService.agregar({ customer_id: Number(idCG), type: tipo, dni });
    setDni('');
    cargar();
  }

  if (!c) return <div className="row"><div className="col-lg-12">Cargando perfil...</div></div>;

  return (
    <div className="row">
      <div className="col-lg-12">
        <Ibox titulo={`Perfil del Cliente — ${c.ap || ''} ${c.am || ''} ${c.nom || ''}`}>
          <ul className="nav nav-tabs">
            {['resumen', 'creditos', 'relaciones', 'galeria', 'ubicacion', 'ahorros', 'operaciones'].map((t) => (
              <li key={t} className={tab === t ? 'active' : ''}><a href="#/" onClick={(e) => { e.preventDefault(); setTab(t); }}>{t[0].toUpperCase() + t.slice(1)}</a></li>
            ))}
          </ul>
          <div style={{ marginTop: 15 }}>
            {tab === 'resumen' && (
              <div className="row">
                <div className="col-md-4"><p><strong>DNI:</strong> {c.dni}</p><p><strong>Celular:</strong> {c.cel || '---'}</p><p><strong>Correo:</strong> {c.correo || '---'}</p></div>
                <div className="col-md-4"><p><strong>Dirección:</strong> {c.direc || '---'}</p><p><strong>Referencia:</strong> {c.referencia || '---'}</p><p><strong>Ubigeo:</strong> {c.ubigeo ? `${c.ubigeo.department} / ${c.ubigeo.province} / ${c.ubigeo.district}` : '---'}</p></div>
                <div className="col-md-4"><p><strong>Celulares:</strong> {(c.cel || '').split(',').filter(Boolean).length}</p><p><strong>Adjuntos:</strong> {(c.attachments || []).length}</p><p><strong>Coords:</strong> {c.coordinate_lat || '---'}, {c.coordinate_lng || '---'}</p></div>
              </div>
            )}
            {tab === 'creditos' && (
              <div className="table-responsive">
                <table className="table table-striped table-bordered">
                  <thead><tr><th>ID</th><th>Monto propuesto</th><th>Capital</th><th>Estado</th></tr></thead>
                  <tbody>{(c.creditos || []).map((cr) => (<tr key={cr.idP}><td>{cr.idP}</td><td>{cr.montoPropuesto}</td><td>{cr.capital}</td><td>{cr.estado}</td></tr>))}</tbody>
                </table>
              </div>
            )}
            {tab === 'relaciones' && (
              <div>
                <form className="form-inline" onSubmit={agregar}>
                  <div className="form-group"><input className="form-control" placeholder="Buscar por nombre o dni" value={dni} onChange={(e) => setDni(e.target.value)} /></div>{' '}
                  <select className="form-control" value={tipo} onChange={(e) => setTipo(e.target.value)}>
                    <option value="spouse">Cónyuge</option>
                    <option value="endorsement">Aval</option>
                  </select>{' '}
                  <button className="btn btn-success" type="submit">Agregar</button>
                </form>
                <hr />
                {(rels || []).map((r) => (
                  <div className="well well-sm" key={r.id} style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                    <span><strong>{r.nombre}</strong> <small>{r.doc} - {r.tipo}</small></span>
                    <button className="btn btn-xs btn-danger" onClick={() => relacionService.eliminar(r.id).then(cargar)}>Eliminar</button>
                  </div>
                ))}
              </div>
            )}
            {tab === 'galeria' && (
              <div>
                <input type="file" onChange={subir} />
                <hr />
                {(galeria || []).map((g) => (
                  <div className="well well-sm" key={g.id} style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                    <span>{g.file || g.id}</span>
                    <button className="btn btn-xs btn-danger" onClick={() => api.post('/api/adjuntos/eliminar', { id: g.id }).then(cargar)}>Eliminar</button>
                  </div>
                ))}
              </div>
            )}
            {tab === 'ubicacion' && (
              <div>
                <p><strong>Dirección:</strong> {c.direc || '---'}</p>
                <p><strong>Referencia:</strong> {c.referencia || '---'}</p>
                <p><strong>Ubigeo:</strong> {c.ubigeo ? `${c.ubigeo.department} / ${c.ubigeo.province} / ${c.ubigeo.district}` : '---'}</p>
                <p><strong>Coordenadas:</strong> {c.coordinate_lat || '---'}, {c.coordinate_lng || '---'}</p>
                {c.coordinate_lat && c.coordinate_lng && (
                  <p><a target="_blank" rel="noreferrer" href={`https://www.openstreetmap.org/?mlat=${c.coordinate_lat}&mlon=${c.coordinate_lng}#map=16/${c.coordinate_lat}/${c.coordinate_lng}`}>Ver recorrido en mapa</a></p>
                )}
              </div>
            )}
            {tab === 'ahorros' && (
              <div className="table-responsive">
                <table className="table table-striped table-bordered">
                  <thead><tr><th>ID</th><th>Monto</th><th>Tipo</th><th>Fecha</th></tr></thead>
                  <tbody>{ahorros.map((a) => (<tr key={a.idAd}><td>{a.idAd}</td><td>{a.monto}</td><td>{a.tipo}</td><td>{a.fecha}</td></tr>))}</tbody>
                </table>
              </div>
            )}
            {tab === 'operaciones' && (
              <div className="table-responsive">
                <table className="table table-striped table-bordered">
                  <thead><tr><th>ID</th><th>Tipo</th><th>Total</th><th>Fecha</th><th>Estado</th></tr></thead>
                  <tbody>{operaciones.map((o) => (<tr key={o.idCAD}><td>{o.idCAD}</td><td>{o.tipo}</td><td>{o.total}</td><td>{o.created_at}</td><td>{o.estadodt}</td></tr>))}</tbody>
                </table>
              </div>
            )}
          </div>
        </Ibox>
      </div>
    </div>
  );
}
