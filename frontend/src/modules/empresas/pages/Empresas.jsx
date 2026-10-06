import { useEffect, useState } from 'react';
import { api } from '../../../services/api';

export default function Empresas() {
    const [empresas, setEmpresas] = useState([]);
    const [loading, setLoading] = useState(true);
    const [showForm, setShowForm] = useState(false);
    const [form, setForm] = useState({ nombre: '', razon_social: '', ruc: '', direccion: '', telefono: '', email: '' });

    useEffect(() => { fetchEmpresas(); }, []);

    const fetchEmpresas = async () => {
        try {
            const res = await api.get('/empresas');
            setEmpresas(res.data);
        } catch (err) {
            console.error(err);
        } finally {
            setLoading(false);
        }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        try {
            await api.post('/empresas', form);
            setShowForm(false);
            setForm({ nombre: '', razon_social: '', ruc: '', direccion: '', telefono: '', email: '' });
            fetchEmpresas();
        } catch (err) {
            alert(err.message);
        }
    };

    if (loading) return <div className="text-center mt-5"><div className="spinner-border" /></div>;

    return (
        <div>
            <div className="d-flex justify-content-between align-items-center mb-4">
                <h2>Empresas</h2>
                <button className="btn btn-primary" onClick={() => setShowForm(!showForm)}>
                    {showForm ? 'Cancelar' : 'Nueva Empresa'}
                </button>
            </div>

            {showForm && (
                <div className="card mb-4">
                    <div className="card-body">
                        <form onSubmit={handleSubmit}>
                            <div className="row g-3">
                                <div className="col-md-4">
                                    <label className="form-label">Nombre</label>
                                    <input className="form-control" value={form.nombre} onChange={(e) => setForm({ ...form, nombre: e.target.value })} required />
                                </div>
                                <div className="col-md-4">
                                    <label className="form-label">Razón Social</label>
                                    <input className="form-control" value={form.razon_social} onChange={(e) => setForm({ ...form, razon_social: e.target.value })} required />
                                </div>
                                <div className="col-md-4">
                                    <label className="form-label">RUC</label>
                                    <input className="form-control" value={form.ruc} onChange={(e) => setForm({ ...form, ruc: e.target.value })} required />
                                </div>
                                <div className="col-md-4">
                                    <label className="form-label">Dirección</label>
                                    <input className="form-control" value={form.direccion} onChange={(e) => setForm({ ...form, direccion: e.target.value })} />
                                </div>
                                <div className="col-md-4">
                                    <label className="form-label">Teléfono</label>
                                    <input className="form-control" value={form.telefono} onChange={(e) => setForm({ ...form, telefono: e.target.value })} />
                                </div>
                                <div className="col-md-4">
                                    <label className="form-label">Email</label>
                                    <input type="email" className="form-control" value={form.email} onChange={(e) => setForm({ ...form, email: e.target.value })} />
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
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Razón Social</th>
                            <th>RUC</th>
                            <th>Teléfono</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        {empresas.map((emp) => (
                            <tr key={emp.id}>
                                <td>{emp.id}</td>
                                <td>{emp.nombre}</td>
                                <td>{emp.razon_social}</td>
                                <td>{emp.ruc}</td>
                                <td>{emp.telefono}</td>
                                <td><span className={`badge bg-${emp.estado === 'activo' ? 'success' : 'secondary'}`}>{emp.estado}</span></td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </div>
    );
}
