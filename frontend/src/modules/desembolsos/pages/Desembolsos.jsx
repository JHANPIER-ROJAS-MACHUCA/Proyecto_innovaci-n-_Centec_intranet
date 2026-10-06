import { useEffect, useState } from 'react';
import { api } from '../../../services/api';

export default function Desembolsos() {
    const [list, setList] = useState([]);
    const [form, setForm] = useState({ credito_id:'', caja_id:'', monto:'', metodo:'efectivo' });
    const fetchAll = async () => {
        const r = await api.get('/desembolsos');
        setList(r.data);
    };
    useEffect(()=>{fetchAll();},[]);
    const save = async (e) => {
        e.preventDefault();
        try { await api.post('/desembolsos', form); setForm({ credito_id:'', caja_id:'', monto:'', metodo:'efectivo' }); fetchAll(); }
        catch(err){ alert(err.message); }
    };
    return (
        <div>
            <h2>Desembolsos</h2>
            <form onSubmit={save} className="row g-2 mb-3">
                <div className="col"><input className="form-control" placeholder="Crédito ID" value={form.credito_id} onChange={(e)=>setForm({...form,credito_id:e.target.value})} required /></div>
                <div className="col"><input className="form-control" placeholder="Caja ID" value={form.caja_id} onChange={(e)=>setForm({...form,caja_id:e.target.value})} required /></div>
                <div className="col"><input className="form-control" placeholder="Monto" value={form.monto} onChange={(e)=>setForm({...form,monto:e.target.value})} required /></div>
                <div className="col"><button className="btn btn-primary">Desembolsar</button></div>
            </form>
            <table className="table table-sm"><thead><tr><th>N°</th><th>Crédito</th><th>Monto</th><th>Estado</th></tr></thead>
            <tbody>{list.map((d)=><tr key={d.id}><td>{d.numero_desembolso}</td><td>{d.numero_credito}</td><td>{d.monto}</td><td>{d.estado}</td></tr>)}</tbody></table>
        </div>
    );
}
