import { useEffect, useState } from 'react';
import { api } from '../../../services/api';

export default function Creditos() {
    const [list, setList] = useState([]);
    const [sols, setSols] = useState([]);
    const [tipos, setTipos] = useState([]);
    const [form, setForm] = useState({ cliente_id:'', sucursal_id:'', asesor_id:'', tipo_credito_id:'', monto_solicitado:'', plazo:'' });
    const fetchAll = async () => {
        const [c, s, t] = await Promise.all([api.get('/creditos'), api.get('/solicitudes'), api.get('/tipos-credito')]);
        setList(c.data); setSols(s.data); setTipos(t.data);
    };
    useEffect(()=>{fetchAll();},[]);
    const saveSol = async (e) => {
        e.preventDefault();
        try { await api.post('/solicitudes', form); setForm({ cliente_id:'', sucursal_id:'', asesor_id:'', tipo_credito_id:'', monto_solicitado:'', plazo:'' }); fetchAll(); }
        catch(err){ alert(err.message); }
    };
    const estado = async (id, estado) => {
        try { await api.request(`/solicitudes/${id}/estado`, {method:'PUT', body:JSON.stringify({estado})}); fetchAll(); }
        catch(err){ alert(err.message); }
    };
    const crearCredito = async (solId) => {
        try { await api.post('/creditos', { solicitud_id: solId }); fetchAll(); }
        catch(err){ alert(err.message); }
    };
    return (
        <div>
            <h2>Créditos</h2>
            <h5>Nueva solicitud</h5>
            <form onSubmit={saveSol} className="row g-2 mb-3">
                <div className="col"><input className="form-control" placeholder="Cliente ID" value={form.cliente_id} onChange={(e)=>setForm({...form,cliente_id:e.target.value})} required /></div>
                <div className="col"><input className="form-control" placeholder="Suc ID" value={form.sucursal_id} onChange={(e)=>setForm({...form,sucursal_id:e.target.value})} required /></div>
                <div className="col"><input className="form-control" placeholder="Asesor ID" value={form.asesor_id} onChange={(e)=>setForm({...form,asesor_id:e.target.value})} required /></div>
                <div className="col"><select className="form-select" value={form.tipo_credito_id} onChange={(e)=>setForm({...form,tipo_credito_id:e.target.value})} required><option value="">Tipo</option>{tipos.map((t)=><option key={t.id} value={t.id}>{t.nombre}</option>)}</select></div>
                <div className="col"><input className="form-control" placeholder="Monto" value={form.monto_solicitado} onChange={(e)=>setForm({...form,monto_solicitado:e.target.value})} required /></div>
                <div className="col"><input className="form-control" placeholder="Plazo" value={form.plazo} onChange={(e)=>setForm({...form,plazo:e.target.value})} required /></div>
                <div className="col"><button className="btn btn-primary">Crear</button></div>
            </form>
            <h5>Solicitudes</h5>
            <table className="table table-sm"><thead><tr><th>N°</th><th>Cliente</th><th>Monto</th><th>Estado</th><th></th></tr></thead>
            <tbody>{sols.map((s)=><tr key={s.id}><td>{s.numero_solicitud}</td><td>{s.cliente_nombre}</td><td>{s.monto_solicitado}</td><td>{s.estado}</td><td>
                {s.estado==='pendiente' && <button className="btn btn-sm btn-info me-1" onClick={()=>estado(s.id,'evaluacion')}>Evaluar</button>}
                {s.estado==='evaluacion' && <><button className="btn btn-sm btn-success me-1" onClick={()=>estado(s.id,'aprobado')}>Aprobar</button><button className="btn btn-sm btn-danger" onClick={()=>estado(s.id,'desaprobado')}>Denegar</button></>}
                {s.estado==='aprobado' && <button className="btn btn-sm btn-primary" onClick={()=>crearCredito(s.id)}>Crear crédito</button>}
            </td></tr>)}</tbody></table>
            <h5>Créditos</h5>
            <table className="table table-sm"><thead><tr><th>N°</th><th>Cliente</th><th>Monto</th><th>Estado</th></tr></thead>
            <tbody>{list.map((c)=><tr key={c.id}><td>{c.numero_credito}</td><td>{c.cliente_nombre}</td><td>{c.monto_aprobado}</td><td>{c.estado}</td></tr>)}</tbody></table>
        </div>
    );
}
