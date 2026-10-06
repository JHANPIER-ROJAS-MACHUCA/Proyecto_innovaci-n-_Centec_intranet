import { useEffect, useState } from 'react';
import { api } from '../../../services/api';

export default function Dashboard() {
    const [stats, setStats] = useState(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        fetchStats();
    }, []);

    const fetchStats = async () => {
        try {
            const res = await api.get('/dashboard/resumen');
            setStats(res.data);
        } catch (err) {
            console.error(err);
        } finally {
            setLoading(false);
        }
    };

    if (loading) return <div className="text-center mt-5"><div className="spinner-border" /></div>;

    const card = (titulo, valor, cls) => (
        <div className="col-md-3"><div className={`card ${cls} text-white`}><div className="card-body"><h6>{titulo}</h6><h4>{valor}</h4></div></div></div>
    );

    return (
        <div>
            <h2 className="mb-4">Dashboard Gerencia</h2>
            <div className="row g-3">
                {card('Clientes', stats?.total_clientes || 0, 'bg-primary')}
                {card('Activos', stats?.clientes_activos || 0, 'bg-success')}
                {card('Créditos', stats?.total_creditos || 0, 'bg-info')}
                {card('Ahorros S/', Number(stats?.total_ahorros || 0).toFixed(2), 'bg-secondary')}
                {card('Desembolsado S/', Number(stats?.total_desembolsado || 0).toFixed(2), 'bg-dark')}
                {card('Cobrado S/', Number(stats?.total_cobrado || 0).toFixed(2), 'bg-success')}
                {card('Morosidad S/', Number(stats?.total_morosidad || 0).toFixed(2), 'bg-danger')}
                {card('Faltantes S/', Number(stats?.faltantes || 0).toFixed(2), 'bg-warning')}
            </div>
            <h5 className="mt-4">Por sucursal</h5>
            <table className="table table-sm"><thead><tr><th>Sucursal</th><th>Clientes</th><th>Créditos</th><th>Cobranza</th><th>Ahorros</th><th>Morosidad</th></tr></thead>
            <tbody>{(stats?.por_sucursal || []).map((s)=><tr key={s.id}><td>{s.codigo}</td><td>{s.clientes}</td><td>{s.creditos}</td><td>{s.cobranza}</td><td>{s.ahorros}</td><td>{s.morosidad}</td></tr>)}</tbody></table>
        </div>
    );
}
