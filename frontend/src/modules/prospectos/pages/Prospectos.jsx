import { useEffect, useState } from 'react';
import { api } from '../../../services/api';

export default function Prospectos() {
    const [list, setList] = useState([]);
    const [form, setForm] = useState({ sucursal_id:'', asesor_id:'', nombre:'', telefono:'', interes:'credito' });
    const fetchAll = async () => {
        const r = await api.get('/prospectos');
        setList(r.data);
    };
    useEffect(()=>{fetchAll();},[]);
    const save = async (e) => {
        e.preventDefault();
        try { await api.post('/prospectos', form); setForm({ sucursal_id:'', asesor_id:'', nombre:'', telefono:'', interes:'credito' }); fetchAll(); }
        catch(err){ alert(err.message); }
    };
    const convertir = async (id) => {
        try { const r = await api.post(`/prospectos/${id}/convertir`, {}); alert('Cliente ID: '+r.data.cliente_id); fetchAll(); }
        catch(err){ alert(err.message); }
    };
    return (
        <div>
            <h2>Prospectos</h2>
            <form onSubmit={save} className="row g-2 mb-3">
                <div className="col"><input className="form-control" placeholder="Sucursal ID" value={form.sucursal_id} onChange={(e)=>setForm({...form,sucursal_id:e.target.value})} required /></div>
                <div className="col"><input className="form-control" placeholder="Asesor ID" value={form.asesor_id} onChange={(e)=>setForm({...form,asesor_id:e.target.value})} required /></div>
                <div className="col"><input className="form-control" placeholder="Nombre" value={form.nombre} onChange={(e)=>setForm({...form,nombre:e.target.value})} required /></div>
                <div className="col"><input className="form-control" placeholder="Tel" value={form.telefono} onChange={(e)=>setForm({...form,telefono:e.target.value})} /></div>
                <div className="col"><button className="btn btn-primary">Guardar</button></div>
            </form>
            <table className="table table-sm"><thead><tr><th>ID</th><th>Nombre</th><th>Estado</th><th></th></tr></thead>
            <tbody>{list.map((p)=><tr key={p.id}><td>{p.id}</td><td>{p.nombre}</td><td>{p.estado}</td><td>{p.estado!=='convertido' && <button className="btn btn-sm btn-success" onClick={()=>convertir(p.id)}>Convertir</button>}</td></tr>)}</tbody></table>
        </div>
    );
}
