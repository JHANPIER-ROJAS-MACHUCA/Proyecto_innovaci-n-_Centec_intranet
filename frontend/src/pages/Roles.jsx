import { useEffect, useMemo, useState } from 'react';
import { api } from '../services/api.js';

// Spec: Gestión de Roles y Permisos (TI). Matriz Rol × Permiso editable.
export default function Roles() {
  const [matriz, setMatriz] = useState(null);
  const [rolSel, setRolSel] = useState(1);
  const [sel, setSel] = useState(new Set());
  const [msg, setMsg] = useState('');

  useEffect(() => {
    api.get('/api/rbac/matriz').then((r) => setMatriz(r.data)).catch(() => setMatriz(null));
  }, []);

  const porRol = useMemo(() => {
    if (!matriz) return new Set();
    return new Set(matriz.asignaciones.filter((a) => Number(a.idRol) === Number(rolSel) && Number(a.estado) === 1).map((a) => a.idPermiso));
  }, [matriz, rolSel]);

  useEffect(() => { setSel(porRol); }, [porRol]);

  function toggle(id) {
    const n = new Set(sel);
    if (n.has(id)) n.delete(id);
    else n.add(id);
    setSel(n);
  }

  async function guardar() {
    try {
      await api.post('/api/rbac/asignar', { idRol: Number(rolSel), permisos: [...sel] });
      setMsg('Permisos guardados.');
    } catch {
      setMsg('Error al guardar.');
    }
  }

  if (!matriz) return <div className="ibox-content"><p>Cargando matriz...</p></div>;
  const modulos = [...new Set(matriz.permisos.map((p) => p.modulo))];

  return (
    <div className="row">
      <div className="col-lg-12">
        <div className="ibox float-e-margins">
          <div className="ibox-title"><h5>Roles y permisos</h5></div>
          <div className="ibox-content">
            <div className="form-inline">
              <select className="form-control" value={rolSel} onChange={(e) => setRolSel(e.target.value)}>
                {matriz.roles.map((x) => <option key={x.codigo} value={x.codigo}>{x.codigo} — {x.nombre}</option>)}
              </select>{' '}
              <button className="btn btn-primary" onClick={guardar}>Guardar</button>{' '}
              <span>{msg}</span>
            </div>
          </div>
        </div>
        {modulos.map((m) => (
          <div className="ibox float-e-margins" key={m}>
            <div className="ibox-title"><h5>{m}</h5></div>
            <div className="ibox-content">
              <table className="table table-striped">
                <thead><tr><th></th><th>Vista</th><th>Acción</th><th>Descripción</th></tr></thead>
                <tbody>
                  {matriz.permisos.filter((p) => p.modulo === m).map((p) => (
                    <tr key={p.id}>
                      <td><input type="checkbox" checked={sel.has(p.id)} onChange={() => toggle(p.id)} /></td>
                      <td>{p.vista}</td><td><span className="label label-info">{p.accion}</span></td><td>{p.descripcion}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}
