import { useEffect, useState } from 'react';
import { api } from '../../../services/api';

export default function Notificaciones() {
    const [data, setData] = useState({ no_leidas: 0, items: [] });
    const fetchAll = async () => {
        const r = await api.get('/notificaciones');
        setData(r.data);
    };
    useEffect(() => { fetchAll(); }, []);
    const leida = async (id) => {
        await api.request(`/notificaciones/${id}/leida`, { method: 'PUT' });
        fetchAll();
    };
    const todas = async () => {
        await api.post('/notificaciones/leidas', {});
        fetchAll();
    };
    return (
        <div>
            <div className="d-flex justify-content-between align-items-center">
                <h2>Mis avisos ({data.no_leidas})</h2>
                <button className="btn btn-sm btn-secondary" onClick={todas}>Marcar todas</button>
            </div>
            <table className="table table-sm"><thead><tr><th>Fecha</th><th>Título</th><th>Mensaje</th><th></th></tr></thead>
                <tbody>{data.items.map((n) => (
                    <tr key={n.id} className={n.leida ? '' : 'table-warning'}>
                        <td>{n.created_at}</td><td>{n.titulo}</td><td>{n.mensaje}</td>
                        <td>{!n.leida && <button className="btn btn-sm btn-link" onClick={() => leida(n.id)}>leída</button>}</td>
                    </tr>
                ))}</tbody></table>
        </div>
    );
}
