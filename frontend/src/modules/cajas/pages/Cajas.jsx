import { useEffect, useState } from 'react';
import { api } from '../../../services/api';

export default function Cajas() {
    const [list, setList] = useState([]);
    const [form, setForm] = useState({ tipo:'operativa', sucursal_id:'', usuario_id:'' });
    const [op, setOp] = useState({ caja_id:'', monto:'', saldo_fisico:'', caja_destino:'', concepto:'' });
    const fetchAll = async () => {
        const r = await api.get('/cajas');
        setList(r.data);
    };
    useEffect(()=>{fetchAll();},[]);
    const crear = async (e) => {
        e.preventDefault();
        try { await api.post('/cajas', form); setForm({ tipo:'operativa', sucursal_id:'', usuario_id:'' }); fetchAll(); }
        catch(err){ alert(err.message); }
    };
    const abrir = async () => {
        try { await api.post(`/cajas/${op.caja_id}/abrir`, { monto: op.monto }); fetchAll(); }
        catch(err){ alert(err.message); }
    };
    const cerrar = async () => {
        try { const r = await api.post(`/cajas/${op.caja_id}/cerrar`, { saldo_fisico: op.saldo_fisico }); alert(`Faltante: ${r.data.faltante} Sobrante: ${r.data.sobrante}`); fetchAll(); }
        catch(err){ alert(err.message); }
    };
    const transferir = async () => {
        try { await api.post('/cajas/transferir', { origen_id: op.caja_id, destino_id: op.caja_destino, monto: op.monto, concepto: op.concepto || 'Transferencia' }); fetchAll(); }
        catch(err){ alert(err.message); }
    };
    return (
        <div>
            <h2>Cajas</h2>
            <form onSubmit={crear} className="row g-2 mb-3">
                <div className="col"><select className="form-select" value={form.tipo} onChange={(e)=>setForm({...form,tipo:e.target.value})}><option value="general">general</option><option value="ti">ti</option><option value="sucursal">sucursal</option><option value="operativa">operativa</option></select></div>
                <div className="col"><input className="form-control" placeholder="Sucursal ID" value={form.sucursal_id} onChange={(e)=>setForm({...form,sucursal_id:e.target.value})} /></div>
                <div className="col"><input className="form-control" placeholder="Usuario ID" value={form.usuario_id} onChange={(e)=>setForm({...form,usuario_id:e.target.value})} /></div>
                <div className="col"><button className="btn btn-primary">Crear</button></div>
            </form>
            <div className="row g-2 mb-3">
                <div className="col"><input className="form-control" placeholder="Caja ID" value={op.caja_id} onChange={(e)=>setOp({...op,caja_id:e.target.value})} /></div>
                <div className="col"><input className="form-control" placeholder="Monto" value={op.monto} onChange={(e)=>setOp({...op,monto:e.target.value})} /></div>
                <div className="col"><input className="form-control" placeholder="Saldo físico" value={op.saldo_fisico} onChange={(e)=>setOp({...op,saldo_fisico:e.target.value})} /></div>
                <div className="col"><input className="form-control" placeholder="Destino ID" value={op.caja_destino} onChange={(e)=>setOp({...op,caja_destino:e.target.value})} /></div>
                <div className="col">
                    <button className="btn btn-success btn-sm me-1" onClick={abrir}>Abrir</button>
                    <button className="btn btn-warning btn-sm me-1" onClick={cerrar}>Cerrar</button>
                    <button className="btn btn-info btn-sm" onClick={transferir}>Transferir</button>
                </div>
            </div>
            <table className="table table-sm"><thead><tr><th>Código</th><th>Tipo</th><th>Nivel</th><th>Saldo</th><th>Estado</th></tr></thead>
            <tbody>{list.map((c)=><tr key={c.id}><td>{c.codigo}</td><td>{c.tipo}</td><td>{c.nivel}</td><td>{c.saldo_actual}</td><td>{c.estado}</td></tr>)}</tbody></table>
        </div>
    );
}
