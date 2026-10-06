import { Outlet, Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../../modules/auth/AuthContext';
import { NAV_ITEMS } from '../../app/navigation';

export default function Layout() {
    const { user, logout } = useAuth();
    const navigate = useNavigate();

    const handleLogout = () => {
        logout();
        navigate('/login');
    };

    return (
        <div className="d-flex">
            <nav className="bg-dark text-white p-3" style={{ width: 250, minHeight: '100vh' }}>
                <h4 className="mb-4">CREDISOPORTE</h4>
                <ul className="nav flex-column">
                    {NAV_ITEMS.map((item) => (
                        <li className="nav-item" key={item.to}>
                            <Link className="nav-link text-white" to={item.to}>{item.label}</Link>
                        </li>
                    ))}
                </ul>
            </nav>
            <div className="flex-grow-1">
                <header className="bg-light p-3 d-flex justify-content-between align-items-center border-bottom">
                    <span>Bienvenido, {user?.nombre} {user?.apellido_paterno}</span>
                    <div>
                        <span className="me-3 badge bg-secondary">{user?.rol_nombre}</span>
                        <button className="btn btn-outline-danger btn-sm" onClick={handleLogout}>Salir</button>
                    </div>
                </header>
                <main className="p-4">
                    <Outlet />
                </main>
            </div>
        </div>
    );
}
