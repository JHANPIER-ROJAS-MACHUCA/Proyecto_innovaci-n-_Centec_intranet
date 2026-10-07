import { useEffect, useState } from 'react';
import { Navigate } from 'react-router-dom';
import { useSession } from '../contexts/AuthContext.jsx';
import { roleName } from '../config/roles.jsx';
import { api } from '../services/api.js';
import GerenteDashboard from './dashboards/GerenteDashboard.jsx';
import AdminDashboard from './dashboards/AdminDashboard.jsx';
import OperadorDashboard from './dashboards/OperadorDashboard.jsx';
import AsesorDashboard from './dashboards/AsesorDashboard.jsx';
import JefeOperacionesDashboard from './dashboards/JefeOperacionesDashboard.jsx';

// Dispatcher por rol CENTECPC: 8 Gerencia, 1 TI, 5 Admin Sucursal,
// 7 Plataforma, 2 Asesor, 9 Seguimiento, 3 Cliente → su cuenta.
export default function Dashboard() {
  const { user } = useSession();
  const [r, setR] = useState(null);
  const tipo = Number(user?.tipoU);

  useEffect(() => {
    api.get('/api/cpanel/resumen').then((x) => setR(x.data)).catch(() => setR(null));
  }, []);

  if (!r) return <div className="row"><div className="col-lg-12">Cargando panel...</div></div>;

  const rol = roleName(tipo);
  const props = { user, resumen: r, rol };

  if (tipo === 8 || tipo === 1) return <GerenteDashboard {...props} />;
  if (tipo === 5) return <AdminDashboard {...props} />;
  if (tipo === 7) return <OperadorDashboard {...props} />;
  if (tipo === 2) return <AsesorDashboard {...props} />;
  if (tipo === 9) return <JefeOperacionesDashboard {...props} />;
  if (tipo === 3) return <Navigate to="/cuenta" replace />;
  return <GerenteDashboard {...props} />;
}
