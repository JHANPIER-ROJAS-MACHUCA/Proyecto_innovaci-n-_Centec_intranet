import { useEffect, useState } from 'react';
import { api } from '../../../services/api';

export default function Usuarios() {
    const [usuarios, setUsuarios] = useState([]);
    const [roles, setRoles] = useState([]);
    const [sucursales, setSucursales] = useState([]);
    const [loading, setLoading] = useState(true);
    const [showForm, setShowForm] = useState(false);
    const [form, setForm] = useState({ username:'', password:'', email:'', nombre:'', apellido_paterno:'', dni:'', telefono:'', rol_id:'', sucursal_id:'' });

    useEffect(() => { fetchAll(); }, []);

    const fetchAll = async () => {
        try {
            const [u, r, s] = await Promise.all([api.get('/usuarios'), api.get('/roles'), api.get('/sucursales')]);
            setUsuarios(u.data); setRoles(r.data); setSucursales(s.data);
        } catch (e) { console.error(e); }
        finally { setLoading(false); }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        try {
            await api.post('/usuarios', form);
            setShowForm(false);
            setForm({ username:'', password:'', email:'', nombre:'', apellido_paterno:'', dni:'', telefono:'', rol_id:'', sucursal_id:'' });
            fetchAll();
        } catch (err) { alert(err.message); }
    };

    const handleDelete = async (id) => {
        if (!confirm('¿Desactivar usuario?')) return;
        try { await api.delete(`/usuarios/${id}`); fetchAll(); }
        catch (err) { alert(err.message); }
    };

    if (loading) return <div className="text-center mt-5"><div className="spinner-border" /></div>;

    return (
        <div>
            <div className="d-flex justify-content-between align-items-center mb-4">
                <h2>Usuarios y Personal</h2>
                <button className="btn btn-primary" onClick={() => setShowForm(!showForm)}>{showForm ? 'Cancelar' : 'Nuevo Usuario'}</button>
            </div>
            {showForm && (
                <div className="card mb-4"><div className="card-body">
                    <form onSubmit={handleSubmit}>
                        <div className="row g-3">
                            <div className="col-md-3"><label className="form-label">Username</label><input className="form-control" value={form.username} onChange={(e)=>setForm({...form,username:e.target.value})} required /></div>
                            <div className="col-md-3"><label className="form-label">Password</label><input type="password" className="form-control" value={form.password} onChange={(e)=>setForm({...form,password:e.target.value})} required /></div>
                            <div className="col-md-3"><label className="form-label">Email</label><input type="email" className="form-control" value={form.email} onChange={(e)=>setForm({...form,email:e.target.value})} required /></div>
                            <div className="col-md-3"><label className="form-label">Nombre</label><input className="form-control" value={form.nombre} onChange={(e)=>setForm({...form,nombre:e.target.value})} required /></div>
                            <div className="col-md-3"><label className="form-label">Apellido</label><input className="form-control" value={form.apellido_paterno} onChange={(e)=>setForm({...form,apellido_paterno:e.target.value})} /></div>
                            <div className="col-md-3"><label className="form-label">DNI</label><input className="form-control" value={form.dni} onChange={(e)=>setForm({...form,dni:e.target.value})} /></div>
                            <div className="col-md-3"><label className="form-label">Rol</label>
                                <select className="form-select" value={form.rol_id} onChange={(e)=>setForm({...form,rol_id:e.target.value})} required>
                                    <option value="">Seleccionar...</option>
                                    {roles.map((r)=><option key={r.id} value={r.id}>{r.nombre}</option>)}
                                </select>
                            </div>
                            <div className="col-md-3"><label className="form-label">Sucursal</label>
                                <select className="form-select" value={form.sucursal_id} onChange={(e)=>setForm({...form,sucursal_id:e.target.value})}>
                                    <option value="">Sin asignar (global)</option>
                                    {sucursales.map((s)=><option key={s.id} value={s.id}>{s.codigo} - {s.nombre}</option>)}
                                </select>
                            </div>
                        </div>
                        <button type="submit" className="btn btn-success mt-3">Guardar</button>
                    </form>
                </div></div>
            )}
            <div className="table-responsive">
                <table className="table table-striped">
                    <thead><tr><th>Usuario</th><th>Nombre</th><th>Email</th><th>Rol</th><th>Estado</th><th>Acciones</th></tr></thead>
                    <tbody>
                        {usuarios.map((u)=>(
                            <tr key={u.id}>
                                <td>{u.username}</td>
                                <td>{u.nombre} {u.apellido_paterno}</td>
                                <td>{u.email}</td>
                                <td><span className="badge bg-info">{u.rol_nombre}</span></td>
                                <td><span className={`badge bg-${u.estado==='activo'?'success':'secondary'}`}>{u.estado}</span></td>
                                <td><button className="btn btn-sm btn-outline-danger" onClick={()=>handleDelete(u.id)}>Desactivar</button></td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </div>
    );
}
