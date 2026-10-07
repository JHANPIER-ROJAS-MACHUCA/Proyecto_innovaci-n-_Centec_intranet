import { useEffect, useState } from 'react';
import { api } from '../services/api.js';
import { usePermisos } from '../hooks/usePermisos.js';

// Spec: Gestión de Sucursales (TI) + consulta (Gerencia, Admin Sucursal).
export default function Sucursales() {
  const [lista, setLista] = useState([]);
  const [sel, setSel] = useState(null);
  const [detalle, setDetalle] = useState(null);
  const { tiene } = usePermisos();

  useEffect(() => {
    api.get('/api/sucursales').then((r) => setLista(r.data || [])).catch(() => setLista([]));
  }, []);

  async function ver(idS) {
    setSel(idS);
    try {
      const r = await api.get(`/api/sucursales/detalle?id=${idS}`);
      setDetalle(r.data);
    } catch {
      setDetalle(null);
    }
  }

  return (
    <div className="row">
      <div className="col-lg-5">
        <div className="ibox float-e-margins">
          <div className="ibox-title"><h5>Sucursales</h5></div>
          <div className="ibox-content">
            <table className="table table-striped">
              <thead><tr><th>Código</th><th>Nombre</th><th>Tipo</th><th></th></tr></thead>
              <tbody>
                {lista.map((s) => (
                  <tr key={s.idS} className={sel === s.idS ? 'info' : ''}>
                    <td>{s.codigo}</td><td>{s.nombre}</td><td>{s.tipo}</td>
                    <td><button className="btn btn-xs btn-primary" onClick={() => ver(s.idS)}>Ver</button></td>
                  </tr>
                ))}
                {lista.length === 0 && <tr><td colSpan="4">Sin sucursales.</td></tr>}
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div className="col-lg-7">
        <div className="ibox float-e-margins">
          <div className="ibox-title"><h5>Detalle {detalle ? `— ${detalle.nombre}` : ''}</h5></div>
          <div className="ibox-content">
            {!detalle && <p>Selecciona una sucursal.</p>}
            {detalle && (
              <div>
                <p><strong>Dirección:</strong> {detalle.direccion || '---'} | <strong>Empresa:</strong> {detalle.empresa?.nombre || '---'}</p>
                <p><strong>Métodos de pago:</strong> {(detalle.metodos_pago || []).map((m) => m.name).join(', ') || '---'}</p>
                <p><strong>Servicios:</strong> {(detalle.servicios || []).join(', ') || '---'}</p>
                <h5>Asesores activos ({(detalle.asesores_activos || []).length})</h5>
                <ul>{(detalle.asesores_activos || []).map((a) => <li key={a.idU}>{a.nomU} {a.apU} — {a.correoU}</li>)}</ul>
                {tiene('sucursales', 'financiero', 'ver') && (
                  <p><strong>Tipos crédito:</strong> {(detalle.tiposCredito || []).map((t) => t.nombre || t.slug).join(', ') || '---'}</p>
                )}
              </div>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}
