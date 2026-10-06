import { useState } from 'react';
import { api } from '../../../services/api';

export default function Reportes() {
    const [clienteId, setClienteId] = useState('');
    const [data, setData] = useState(null);
    const ver = async () => {
        try { const r = await api.get(`/reportes/estado-cuenta/${clienteId}`); setData(r.data); }
        catch(err){ alert(err.message); }
    };
    return (
        <div>
            <h2>Reportes</h2>
            <div className="row g-2 mb-3">
                <div className="col"><input className="form-control" placeholder="Cliente ID" value={clienteId} onChange={(e)=>setClienteId(e.target.value)} /></div>
                <div className="col"><button className="btn btn-primary" onClick={ver}>Estado de cuenta</button></div>
                <div className="col"><a className="btn btn-success" href="/api/reportes/cobranzas.csv" target="_blank" rel="noreferrer">CSV Cobranzas</a></div>
            </div>
            {data && <pre>{JSON.stringify(data, null, 2)}</pre>}
        </div>
    );
}
