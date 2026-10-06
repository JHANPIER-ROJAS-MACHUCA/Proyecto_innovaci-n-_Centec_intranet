import { useEffect, useState } from 'react';
import { api } from '../../../services/api';

export default function Movimientos() {
    const [list, setList] = useState([]);
    useEffect(()=>{ api.get('/movimientos').then((r)=>setList(r.data)).catch(console.error); },[]);
    return (
        <div>
            <h2>Movimientos</h2>
            <table className="table table-sm"><thead><tr><th>Fecha</th><th>Caja</th><th>Tipo</th><th>Concepto</th><th>Monto</th><th>Ant</th><th>Post</th></tr></thead>
            <tbody>{list.map((m)=><tr key={m.id}><td>{m.fecha}</td><td>{m.caja_codigo}</td><td>{m.tipo}</td><td>{m.concepto}</td><td>{m.monto}</td><td>{m.saldo_anterior}</td><td>{m.saldo_posterior}</td></tr>)}</tbody></table>
        </div>
    );
}
