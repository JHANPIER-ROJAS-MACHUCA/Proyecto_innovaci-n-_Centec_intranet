import { useEffect, useState } from 'react';
import { api } from '../../../services/api';

export default function Clientes() {
    const [list, setList] = useState([]);
    const [sucursales, setSucursales] = useState([]);
    const [form, setForm] = useState({ sucursal_id:'', nombre:'', dni:'', telefono:'' });
    const fetchAll = async () => {
        const [c, s] = await Promise.all([api.get('/clientes'), api.get('/sucursales')]);
        setList(c.data); setSucursales(s.data);
    };
    useEffect(()=>{fetchAll();},[]);
    const save = async (e) => {
        e.preventDefault();
        try { await api.post('/clientes', form); setForm({ sucursal_id:'', nombre:'', dni:'', telefono:'' }); fetchAll(); }
        catch(err){ alert(err.message); }
    };
    const estado = async (id, estado) => {
        try { await api.request(`/clientes/${id}/estado`, {method:'PUT', body:JSON.stringify({estado})}); fetchAll(); }
        catch(err){ alert(err.message); }
    };
    return (
        <div>
            <h2>Clientes</h2>
            <form onSubmit={save} className="row g-2 mb-3">
                <div className="col"><select className="form-select" value={form.sucursal_id} onChange={(e)=>setForm({...form,sucursal_id:e.target.value})} required><option value="">Sucursal</option>{sucursales.map((s)=><option key={s.id} value={s.id}>{s.codigo}</option>)}</select></div>
                <div className="col"><input className="form-control" placeholder="Nombre" value={form.nombre} onChange={(e)=>setForm({...form,nombre:e.target.value})} required /></div>
                <div className="col"><input className="form-control" placeholder="DNI" value={form.dni} onChange={(e)=>setForm({...form,dni:e.target.value})} /></div>
                <div className="col"><input className="form-control" placeholder="Tel" value={form.telefono} onChange={(e)=>setForm({...form,telefono:e.target.value})} /></div>
                <div className="col"><button className="btn btn-primary">Guardar</button></div>
            </form>
            <table className="table table-sm"><thead><tr><th>Código</th><th>Nombre</th><th>DNI</th><th>Estado</th><th></th></tr></thead>
            <tbody>{list.map((c)=><tr key={c.id}><td>{c.codigo_cliente}</td><td>{c.nombre}</td><td>{c.dni}</td><td>{c.estado}</td><td>{c.estado!=='activo' ? <button className="btn btn-sm btn-success" onClick={()=>estado(c.id,'activo')}>Activar</button> : <button className="btn btn-sm btn-warning" onClick={()=>estado(c.id,'bloqueado')}>Bloquear</button>}</td></tr>)}</tbody></table>
        </div>
    );
}
