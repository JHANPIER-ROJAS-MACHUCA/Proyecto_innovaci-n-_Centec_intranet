import { useEffect, useState } from 'react';
import { api } from '../../../services/api';

export default function RutaCobros() {
    const [data, setData] = useState({ fecha: '', total: 0, items: [] });
    const [q, setQ] = useState('');
    const [categorias, setCategorias] = useState([]);
    const [detalle, setDetalle] = useState(null);
    const [justs, setJusts] = useState([]);

    const fetchAll = async () => {
        const [r, c] = await Promise.all([
            api.get(`/ruta-cobros${q ? `?q=${encodeURIComponent(q)}` : ''}`),
            api.get('/justificacion-categorias'),
        ]);
        setData(r.data); setCategorias(c.data);
    };
    useEffect(() => { fetchAll(); }, []);

    const ver = async (creditoId) => {
        const [d, j] = await Promise.all([
            api.get(`/creditos/${creditoId}/para-cobro`),
            api.get(`/creditos/${creditoId}/justificaciones`),
        ]);
        setDetalle(d.data); setJusts(j.data);
    };

    const justificar = async (creditoId) => {
        const catId = prompt('ID categoría (' + categorias.map((c) => `${c.id}=${c.descripcion}`).join(', ') + '):');
        if (!catId) return;
        const desc = prompt('Descripción:') || '';
        try {
            await api.post('/justificaciones', { credito_id: creditoId, categoria_id: catId, descripcion: desc });
            ver(creditoId); fetchAll();
        } catch (err) { alert(err.message); }
    };

    const condonar = async (cuotaId) => {
        if (!confirm('¿Condonar mora de esta cuota?')) return;
        try {
            const r = await api.request(`/cuotas/${cuotaId}/condonar-mora`, { method: 'PUT' });
            alert(r.message); ver(detalle.credito.id);
        } catch (err) { alert(err.message); }
    };

    return (
        <div>
            <h2>Ruta de cobros — {data.fecha} ({data.total})</h2>
            <div className="row g-2 mb-3">
                <div className="col"><input className="form-control" placeholder="Buscar cliente/DNI..." value={q} onChange={(e) => setQ(e.target.value)} /></div>
                <div className="col-auto"><button className="btn btn-secondary" onClick={fetchAll}>Buscar</button></div>
            </div>
            <table className="table table-sm"><thead><tr><th>Cliente</th><th>Crédito</th><th>Vencidas</th><th>Deuda</th><th>Cobrado hoy</th><th>Justif. hoy</th><th></th></tr></thead>
                <tbody>{data.items.map((c) => (
                    <tr key={c.credito_id}>
                        <td>{c.cliente_nombre}<br /><small>{c.telefono} — {c.direccion}</small></td>
                        <td>{c.numero_credito}</td><td>{c.cuotas_vencidas}</td><td>{c.deuda_vencida}</td>
                        <td>{c.cobrado_hoy}</td><td>{c.justificacion_hoy ? 'Sí' : '—'}</td>
                        <td><button className="btn btn-sm btn-info" onClick={() => ver(c.credito_id)}>Cobrar</button></td>
                    </tr>
                ))}</tbody></table>
            {detalle && (
                <div className="card"><div className="card-body">
                    <h5>{detalle.credito.numero_credito} — Vencido: {detalle.total_vencido} | Mora: {detalle.total_mora} | Total: {detalle.total_general}</h5>
                    <table className="table table-sm"><thead><tr><th>#</th><th>Vence</th><th>Monto</th><th>Mora</th><th>Días</th><th>Estado</th><th></th></tr></thead>
                        <tbody>{detalle.cuotas.map((cu) => (
                            <tr key={cu.id}><td>{cu.numero}</td><td>{cu.vencimiento}</td><td>{cu.monto}</td><td>{cu.mora}</td><td>{cu.dias_atraso}</td><td>{cu.estado}</td>
                                <td>{cu.mora > 0 && <button className="btn btn-sm btn-warning" onClick={() => condonar(cu.id)}>Condonar</button>}</td></tr>
                        ))}</tbody></table>
                    <h6>Justificaciones</h6>
                    <ul>{justs.map((j) => <li key={j.id}>{j.fecha} — {j.categoria}: {j.descripcion} ({j.usuario_nombre})</li>)}</ul>
                    <button className="btn btn-sm btn-secondary me-2" onClick={() => justificar(detalle.credito.id)}>Justificar no pago</button>
                    <button className="btn btn-sm btn-link" onClick={() => setDetalle(null)}>Cerrar</button>
                </div></div>
            )}
        </div>
    );
}
