import { useEffect, useState } from 'react';
import { api } from '../../../services/api';

export default function Cobranzas() {
    const [list, setList] = useState([]);
    const [form, setForm] = useState({ cuota_id:'', caja_id:'', monto:'', metodo:'efectivo' });
    const fetchAll = async () => {
        const r = await api.get('/cobranzas');
        setList(r.data);
    };
    useEffect(()=>{fetchAll();},[]);
    const save = async (e) => {
        e.preventDefault();
        try { await api.post('/cobranzas', form); setForm({ cuota_id:'', caja_id:'', monto:'', metodo:'efectivo' }); fetchAll(); }
        catch(err){ alert(err.message); }
    };
    return (
        <div>
            <h2>Cobranzas</h2>
            <form onSubmit={save} className="row g-2 mb-3">
                <div className="col"><input className="form-control" placeholder="Cuota ID" value={form.cuota_id} onChange={(e)=>setForm({...form,cuota_id:e.target.value})} required /></div>
                <div className="col"><input className="form-control" placeholder="Caja ID" value={form.caja_id} onChange={(e)=>setForm({...form,caja_id:e.target.value})} required /></div>
                <div className="col"><input className="form-control" placeholder="Monto" value={form.monto} onChange={(e)=>setForm({...form,monto:e.target.value})} required /></div>
                <div className="col"><button className="btn btn-primary">Cobrar</button></div>
            </form>
            <table className="table table-sm"><thead><tr><th>N°</th><th>Crédito</th><th>Monto</th><th>Fecha</th></tr></thead>
            <tbody>{list.map((c)=><tr key={c.id}><td>{c.numero_cobranza}</td><td>{c.numero_credito}</td><td>{c.monto}</td><td>{c.fecha}</td></tr>)}</tbody></table>
        </div>
    );
}
