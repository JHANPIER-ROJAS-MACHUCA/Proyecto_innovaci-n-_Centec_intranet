import { Routes, Route, Navigate } from 'react-router-dom';
import { useSession } from './contexts/AuthContext.jsx';
import { puedeVer } from './config/roles.jsx';
import { usePermisos } from './hooks/usePermisos.js';
import Layout from './components/Layout.jsx';
import Login from './pages/Login.jsx';
import Dashboard from './pages/Dashboard.jsx';
import Clientes from './pages/Clientes.jsx';
import PerfilCliente from './pages/PerfilCliente.jsx';
import Creditos from './pages/Creditos.jsx';
import Cobrar from './pages/Cobrar.jsx';
import Caja from './pages/Caja.jsx';
import Instrumentos from './pages/Instrumentos.jsx';
import Administracion from './pages/Administracion.jsx';
import Usuarios from './pages/Usuarios.jsx';
import Reportes from './pages/Reportes.jsx';
import Propuestas from './pages/Propuestas.jsx';
import Mora from './pages/Mora.jsx';
import Formatos from './pages/Formatos.jsx';
import Extornos from './pages/Extornos.jsx';
import Cuenta from './pages/Cuenta.jsx';
import Campo from './pages/Campo.jsx';
import Sucursales from './pages/Sucursales.jsx';
import Roles from './pages/Roles.jsx';

// Guard por rol + permiso fino.
// `roles=null` = todos los autenticados; `permiso="modulo.vista.accion"` exige
// además el permiso RBAC del backend. Sin acceso → CPANEL de su rol.
function Guard({ children, path, roles, permiso }) {
  const { user, loading } = useSession();
  const { tiene, loading: loadingP } = usePermisos();
  if (loading || (permiso && loadingP)) return <div className="container" style={{ padding: 40 }}>Cargando...</div>;
  if (!user) return <Navigate to="/login" replace />;
  const okRoles = roles ? roles.includes(Number(user.tipoU)) : true;
  const okPath = path ? puedeVer(path, user.tipoU) : true;
  const okPermiso = permiso ? tiene(...permiso.split('.')) : true;
  if (!okRoles || !okPath || !okPermiso) return <Navigate to="/" replace />;
  return children;
}

export default function App() {
  return (
    <Routes>
      <Route path="/login" element={<Login />} />
      <Route path="/" element={<Guard><Layout /></Guard>}>
        <Route index element={<Dashboard />} />
        <Route path="clientes" element={<Guard path="/clientes"><Clientes /></Guard>} />
        <Route path="clientes/:idCG" element={<Guard path="/clientes"><PerfilCliente /></Guard>} />
        <Route path="cobrar" element={<Guard path="/cobrar"><Cobrar /></Guard>} />
        <Route path="campo" element={<Guard path="/campo"><Campo /></Guard>} />
        <Route path="caja" element={<Guard path="/caja"><Caja /></Guard>} />
        <Route path="creditos" element={<Guard path="/creditos"><Creditos /></Guard>} />
        <Route path="instrumentos" element={<Guard path="/instrumentos"><Instrumentos /></Guard>} />
        {/* Spec ROLES Y VISTAS (códigos CENTECPC): config y roles = TI(1); usuarios = Admin Sucursal(5) + TI */}
        <Route path="administracion" element={<Guard path="/administracion" roles={[1]}><Administracion /></Guard>} />
        <Route path="usuarios" element={<Guard path="/usuarios" roles={[5, 1]}><Usuarios /></Guard>} />
        <Route path="sucursales" element={<Guard path="/sucursales" roles={[8, 5, 1]} permiso="sucursales.consulta.ver"><Sucursales /></Guard>} />
        <Route path="roles" element={<Guard path="/roles" roles={[1]} permiso="roles.permisos.asignar"><Roles /></Guard>} />
        <Route path="reportes" element={<Guard path="/reportes"><Reportes /></Guard>} />
        <Route path="propuestas" element={<Guard path="/propuestas"><Propuestas /></Guard>} />
        <Route path="mora" element={<Guard path="/mora"><Mora /></Guard>} />
        <Route path="formatos" element={<Guard path="/formatos"><Formatos /></Guard>} />
        <Route path="extornos" element={<Guard path="/extornos"><Extornos /></Guard>} />
        <Route path="cuenta" element={<Guard path="/cuenta"><Cuenta /></Guard>} />
      </Route>
      <Route path="*" element={<Navigate to="/" replace />} />
    </Routes>
  );
}
