import { useEffect, useState } from 'react';
import { api } from '../../../services/api';

export default function Cuentas() {
    const [list, setList] = useState([]);
    const [tipos, setTipos] = useState([]);
    const [form, setForm] = useState({ cliente_id:'', sucursal_id:'', tipo_ahorro_id:'' });
    const [op, setOp] = useState({ cuenta_id:'', caja_id:'', monto:'' });
    const fetchAll = async () => {
        const [c, t] = await Promise.all([api.get('/cuentas'), api.get('/tipos-ahorro')]);
        setList(c.data); setTipos(t.data);
    };
    useEffect(()=>{fetchAll();},[]);
    const save = async (e) => {
        e.preventDefault();
        try { await api.post('/cuentas', form); setForm({ cliente_id:'', sucursal_id:'', tipo_ahorro_id:'' }); fetchAll(); }
        catch(err){ alert(err.message); }
    };
    const mover = async (tipo) => {
        try { await api.post(`/cuentas/${op.cuenta_id}/${tipo}`, { monto: op.monto, caja_id: op.caja_id }); setOp({ cuenta_id:'', caja_id:'', monto:'' }); fetchAll(); }
        catch(err){ alert(err.message); }
    };
    return (
        <div>
            <h2>Cuentas de Ahorro</h2>
            <form onSubmit={save} className="row g-2 mb-3">
                <div className="col"><input className="form-control" placeholder="Cliente ID" value={form.cliente_id} onChange={(e)=>setForm({...form,cliente_id:e.target.value})} required /></div>
                <div className="col"><input className="form-control" placeholder="Sucursal ID" value={form.sucursal_id} onChange={(e)=>setForm({...form,sucursal_id:e.target.value})} required /></div>
                <div className="col"><select className="form-select" value={form.tipo_ahorro_id} onChange={(e)=>setForm({...form,tipo_ahorro_id:e.target.value})} required><option value="">Tipo</option>{tipos.map((t)=><option key={t.id} value={t.id}>{t.nombre}</option>)}</select></div>
                <div className="col"><button className="btn btn-primary">Crear</button></div>
            </form>
            <div className="row g-2 mb-3">
                <div className="col"><input className="form-control" placeholder="Cuenta ID" value={op.cuenta_id} onChange={(e)=>setOp({...op,cuenta_id:e.target.value})} /></div>
                <div className="col"><input className="form-control" placeholder="Caja ID" value={op.caja_id} onChange={(e)=>setOp({...op,caja_id:e.target.value})} /></div>
                <div className="col"><input className="form-control" placeholder="Monto" value={op.monto} onChange={(e)=>setOp({...op,monto:e.target.value})} /></div>
                <div className="col"><button className="btn btn-success me-1" onClick={()=>mover('deposito')}>Depósito</button><button className="btn btn-warning" onClick={()=>mover('retiro')}>Retiro</button></div>
            </div>
            <table className="table table-sm"><thead><tr><th>Número</th><th>Cliente</th><th>Tipo</th><th>Saldo</th><th>Estado</th></tr></thead>
            <tbody>{list.map((c)=><tr key={c.id}><td>{c.numero_cuenta}</td><td>{c.cliente_nombre}</td><td>{c.tipo_nombre}</td><td>{c.saldo}</td><td>{c.estado}</td></tr>)}</tbody></table>
        </div>
    );
}
