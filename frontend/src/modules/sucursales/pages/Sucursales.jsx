import { useEffect, useState } from 'react';
import { api } from '../../../services/api';

export default function Sucursales() {
    const [sucursales, setSucursales] = useState([]);
    const [empresas, setEmpresas] = useState([]);
    const [loading, setLoading] = useState(true);
    const [showForm, setShowForm] = useState(false);
    const [form, setForm] = useState({ empresa_id: '', nombre: '', direccion: '', telefono: '', email: '', departamento: '', provincia: '', distrito: '' });

    useEffect(() => {
        fetchSucursales();
        fetchEmpresas();
    }, []);

    const fetchSucursales = async () => {
        try {
            const res = await api.get('/sucursales');
            setSucursales(res.data);
        } catch (err) {
            console.error(err);
        } finally {
            setLoading(false);
        }
    };

    const fetchEmpresas = async () => {
        try {
            const res = await api.get('/empresas');
            setEmpresas(res.data);
        } catch (err) {
            console.error(err);
        }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        try {
            await api.post('/sucursales', form);
            setShowForm(false);
            setForm({ empresa_id: '', nombre: '', direccion: '', telefono: '', email: '', departamento: '', provincia: '', distrito: '' });
            fetchSucursales();
        } catch (err) {
            alert(err.message);
        }
    };

    if (loading) return <div className="text-center mt-5"><div className="spinner-border" /></div>;

    return (
        <div>
            <div className="d-flex justify-content-between align-items-center mb-4">
                <h2>Sucursales</h2>
                <button className="btn btn-primary" onClick={() => setShowForm(!showForm)}>
                    {showForm ? 'Cancelar' : 'Nueva Sucursal'}
                </button>
            </div>

            {showForm && (
                <div className="card mb-4">
                    <div className="card-body">
                        <form onSubmit={handleSubmit}>
                            <div className="row g-3">
                                <div className="col-md-4">
                                    <label className="form-label">Empresa</label>
                                    <select className="form-select" value={form.empresa_id} onChange={(e) => setForm({ ...form, empresa_id: e.target.value })} required>
                                        <option value="">Seleccionar...</option>
                                        {empresas.map((emp) => (
                                            <option key={emp.id} value={emp.id}>{emp.nombre}</option>
                                        ))}
                                    </select>
                                </div>
                                <div className="col-md-4">
                                    <label className="form-label">Nombre</label>
                                    <input className="form-control" value={form.nombre} onChange={(e) => setForm({ ...form, nombre: e.target.value })} required />
                                </div>
                                <div className="col-md-4">
                                    <label className="form-label">Dirección</label>
                                    <input className="form-control" value={form.direccion} onChange={(e) => setForm({ ...form, direccion: e.target.value })} />
                                </div>
                                <div className="col-md-3">
                                    <label className="form-label">Teléfono</label>
                                    <input className="form-control" value={form.telefono} onChange={(e) => setForm({ ...form, telefono: e.target.value })} />
                                </div>
                                <div className="col-md-3">
                                    <label className="form-label">Email</label>
                                    <input type="email" className="form-control" value={form.email} onChange={(e) => setForm({ ...form, email: e.target.value })} />
                                </div>
                                <div className="col-md-2">
                                    <label className="form-label">Departamento</label>
                                    <input className="form-control" value={form.departamento} onChange={(e) => setForm({ ...form, departamento: e.target.value })} />
                                </div>
                                <div className="col-md-2">
                                    <label className="form-label">Provincia</label>
                                    <input className="form-control" value={form.provincia} onChange={(e) => setForm({ ...form, provincia: e.target.value })} />
                                </div>
                                <div className="col-md-2">
                                    <label className="form-label">Distrito</label>
                                    <input className="form-control" value={form.distrito} onChange={(e) => setForm({ ...form, distrito: e.target.value })} />
                                </div>
                            </div>
                            <button type="submit" className="btn btn-success mt-3">Guardar</button>
                        </form>
                    </div>
                </div>
            )}

            <div className="table-responsive">
                <table className="table table-striped">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Empresa</th>
                            <th>Dirección</th>
                            <th>Teléfono</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        {sucursales.map((suc) => (
                            <tr key={suc.id}>
                                <td><span className="badge bg-info">{suc.codigo}</span></td>
                                <td>{suc.nombre}</td>
                                <td>{suc.empresa_nombre}</td>
                                <td>{suc.direccion}</td>
                                <td>{suc.telefono}</td>
                                <td><span className={`badge bg-${suc.estado === 'activo' ? 'success' : 'secondary'}`}>{suc.estado}</span></td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </div>
    );
}
