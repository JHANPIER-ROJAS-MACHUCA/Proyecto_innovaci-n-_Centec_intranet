import { useEffect, useState } from 'react';
import { api } from '../../../services/api';

export default function Solicitudes() {
    const [tipo, setTipo] = useState('SOLICITUD');
    const [data, setData] = useState({ total: 0, items: [] });
    const [conteos, setConteos] = useState({});
    const [q, setQ] = useState('');
    const [estado, setEstado] = useState('');
    const [detalle, setDetalle] = useState(null);

    const fetchAll = async () => {
        const [r, c] = await Promise.all([
            api.get(`/simulaciones?tipo=${tipo}${q ? `&q=${encodeURIComponent(q)}` : ''}${estado ? `&estado=${estado}` : ''}`),
            api.get('/simulaciones/conteos'),
        ]);
        setData(r.data); setConteos(c.data);
    };
    useEffect(() => { fetchAll(); }, [tipo]);

    const ver = async (id) => {
        const r = await api.get(`/simulaciones/${id}`);
        setDetalle(r.data);
    };
    const cambiarEstado = async (id, est) => {
        await api.request(`/simulaciones/${id}/estado`, { method: 'PUT', body: JSON.stringify({ estado: est }) });
        setDetalle(null); fetchAll();
    };
    const asignar = async (id, asesorId) => {
        await api.request(`/simulaciones/${id}/asesor`, { method: 'PUT', body: JSON.stringify({ asesor_id: asesorId || null }) });
        ver(id); fetchAll();
    };

    const kpis = conteos[tipo] || {};
    return (
        <div>
            <h2>Solicitudes del simulador</h2>
            <div className="btn-group mb-3">
                {['SOLICITUD', 'SIMULACION'].map((t) => (
                    <button key={t} className={`btn btn-${tipo === t ? 'primary' : 'outline-primary'}`} onClick={() => setTipo(t)}>{t}</button>
                ))}
            </div>
            <div className="mb-3">
                {Object.entries(kpis).map(([k, v]) => (
                    <button key={k} className="btn btn-sm btn-outline-secondary me-1" onClick={() => { setEstado(k); setTimeout(fetchAll); }}>{k}: {v}</button>
                ))}
                {estado && <button className="btn btn-sm btn-link" onClick={() => { setEstado(''); }}>limpiar</button>}
            </div>
            <div className="row g-2 mb-3">
                <div className="col"><input className="form-control" placeholder="Buscar nombre, DNI, celular..." value={q} onChange={(e) => setQ(e.target.value)} /></div>
                <div className="col-auto"><button className="btn btn-secondary" onClick={fetchAll}>Filtrar</button></div>
            </div>
            <p>Total: {data.total}</p>
            <table className="table table-sm"><thead><tr><th>ID</th><th>Fecha</th><th>Nombre</th><th>Monto</th><th>Cuotas</th><th>Estado</th><th></th></tr></thead>
                <tbody>{data.items.map((s) => (
                    <tr key={s.id}><td>{s.id}</td><td>{s.created_at}</td><td>{s.nombres} {s.apellido_paterno} {s.razon_social}</td><td>{s.monto_solicitado}</td><td>{s.numero_cuotas}</td><td>{s.estado}</td>
                        <td><button className="btn btn-sm btn-info" onClick={() => ver(s.id)}>Ver</button></td></tr>
                ))}</tbody></table>
            {detalle && (
                <div className="card"><div className="card-body">
                    <h5>Detalle #{detalle.id} — {detalle.estado}</h5>
                    <pre>{JSON.stringify(detalle, null, 2)}</pre>
                    {detalle.sucursal_cercana && <p>Sucursal cercana: {detalle.sucursal_cercana.codigo} ({detalle.sucursal_cercana.distancia_km} km)</p>}
                    <div className="d-flex gap-2">
                        <button className="btn btn-sm btn-warning" onClick={() => cambiarEstado(detalle.id, 'EN_GESTION')}>En gestión</button>
                        <button className="btn btn-sm btn-success" onClick={() => cambiarEstado(detalle.id, 'ATENDIDO')}>Atendido</button>
                        <button className="btn btn-sm btn-secondary" onClick={() => { const a = prompt('ID asesor (vacío para liberar):'); if (a !== null) asignar(detalle.id, a); }}>Asignar asesor</button>
                        <button className="btn btn-sm btn-link" onClick={() => setDetalle(null)}>Cerrar</button>
                    </div>
                </div></div>
            )}
        </div>
    );
}
