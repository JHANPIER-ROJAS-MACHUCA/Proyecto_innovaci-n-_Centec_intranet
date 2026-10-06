import { useEffect, useState } from 'react';
import { api } from '../../../services/api';

export default function Auditoria() {
    const [list, setList] = useState([]);
    useEffect(()=>{ api.get('/auditoria').then((r)=>setList(r.data)).catch(console.error); },[]);
    return (
        <div>
            <h2>Auditoría</h2>
            <table className="table table-sm"><thead><tr><th>Fecha</th><th>Usuario</th><th>Acción</th><th>Tabla</th><th>ID</th></tr></thead>
            <tbody>{list.map((a)=><tr key={a.id}><td>{a.fecha}</td><td>{a.username}</td><td>{a.accion}</td><td>{a.tabla}</td><td>{a.registro_id}</td></tr>)}</tbody></table>
        </div>
    );
}
