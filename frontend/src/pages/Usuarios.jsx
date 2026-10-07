import { useEffect, useState } from 'react';
import { Navigate } from 'react-router-dom';
import { useSession } from '../contexts/AuthContext.jsx';
import { usuarioService } from '../services/operacionService.js';
import { api } from '../services/api.js';
import { ROLES } from '../config/roles.jsx';

// Spec: Gestión de Usuarios = TI(1); Admin Sucursal(5) ve personal.
// Roles = códigos CENTECPC tabla `rol`. Creación usa idRol + DNI (userU=DNI).
export default function Usuarios() {
  const { user } = useSession();
  const [items, setItems] = useState([]);
  const [msg, setMsg] = useState('');
  const [form, setForm] = useState({ dniU: '', apU: '', amU: '', nomU: '', idRol: '2', celU: '', correoU: '' });
  const esTI = user && Number(user.tipoU) === 1;

  if (user && ![5, 1].includes(Number(user.tipoU))) return <Navigate to="/" replace />;

  async function cargar() {
    try {
      const r = await api.get('/api/usuarios');
      setItems(r.data || []);
    } catch (e) {
      setMsg(e.message);
    }
  }

  useEffect(() => { cargar(); }, []);

  async function crear(e) {
    e.preventDefault();
    try {
      await usuarioService.crear(form);
      setMsg('Usuario registrado (clave inicial 123456).');
      cargar();
    } catch (err) {
      setMsg(err.message);
    }
  }

  async function toggle(idU) {
    await usuarioService.toggle(idU);
    cargar();
  }

  async function reset(idU) {
    await api.post('/api/usuarios/reset', { idU });
    setMsg('Clave restablecida a 123456.');
  }

  return (
    <div className="row">
      <div className="col-lg-12">
        <div className="ibox float-e-margins">
          <div className="ibox-title"><h5>Usuarios</h5></div>
          <div className="ibox-content">
            {esTI && (
              <form className="form-inline" onSubmit={crear}>
                {['dniU', 'apU', 'amU', 'nomU', 'celU', 'correoU'].map((f) => (
                  <span key={f}> <input className="form-control" placeholder={f.toUpperCase()} value={form[f]} onChange={(e) => setForm({ ...form, [f]: e.target.value })} /></span>
                ))}{' '}
                <select className="form-control" value={form.idRol} onChange={(e) => setForm({ ...form, idRol: e.target.value })}>
                  {Object.entries(ROLES).map(([cod, nom]) => <option key={cod} value={cod}>{cod} — {nom}</option>)}
                </select>{' '}
                <button className="btn btn-success" type="submit">Registrar</button>
              </form>
            )}
            {msg !== '' && <div className="alert alert-info" style={{ marginTop: 10 }}>{msg}</div>}
            <div className="table-responsive" style={{ marginTop: 10 }}>
              <table className="table table-striped table-bordered">
                <thead><tr><th>DNI</th><th>Nombres</th><th>Rol</th><th>Estado</th>{esTI && <th>Acciones</th>}</tr></thead>
                <tbody>
                  {items.map((u) => (
                    <tr key={u.idU}>
                      <td>{u.dniU}</td>
                      <td>{`${u.apU || ''} ${u.amU || ''} ${u.nomU || ''}`}</td>
                      <td>{u.rolNombre || u.idRol}</td>
                      <td>{Number(u.idEstado) === 1 ? 'ACTIVO' : 'INACTIVO'}</td>
                      {esTI && (
                        <td>
                          <div className="btn-group">
                            <button className="btn btn-xs btn-warning" onClick={() => toggle(u.idU)}>Activar/Desactivar</button>
                            <button className="btn btn-xs btn-danger" onClick={() => reset(u.idU)}>Reset clave</button>
                          </div>
                        </td>
                      )}
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
