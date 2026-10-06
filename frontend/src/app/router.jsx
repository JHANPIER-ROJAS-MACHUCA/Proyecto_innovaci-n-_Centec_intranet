import { Routes, Route } from 'react-router-dom';
import ProtectedRoute from '../shared/components/ProtectedRoute';
import Layout from '../shared/components/Layout';
import Login from '../modules/auth/pages/Login';
import Dashboard from '../modules/dashboard/pages/Dashboard';
import Empresas from '../modules/empresas/pages/Empresas';
import Sucursales from '../modules/sucursales/pages/Sucursales';
import Usuarios from '../modules/usuarios/pages/Usuarios';
import Clientes from '../modules/clientes/pages/Clientes';
import Prospectos from '../modules/prospectos/pages/Prospectos';
import Cuentas from '../modules/cuentas/pages/Cuentas';
import Cajas from '../modules/cajas/pages/Cajas';
import Movimientos from '../modules/movimientos/pages/Movimientos';
import Creditos from '../modules/creditos/pages/Creditos';
import Desembolsos from '../modules/desembolsos/pages/Desembolsos';
import Cobranzas from '../modules/cobranzas/pages/Cobranzas';
import Auditoria from '../modules/auditoria/pages/Auditoria';
import Reportes from '../modules/reportes/pages/Reportes';
import Solicitudes from '../modules/solicitudes/pages/Solicitudes';
import RutaCobros from '../modules/rutaCobros/pages/RutaCobros';
import Notificaciones from '../modules/notificaciones/pages/Notificaciones';

export default function AppRouter() {
    return (
        <Routes>
            <Route path="/login" element={<Login />} />
            <Route path="/" element={<ProtectedRoute><Layout /></ProtectedRoute>}>
                <Route index element={<Dashboard />} />
                <Route path="empresas" element={<Empresas />} />
                <Route path="sucursales" element={<Sucursales />} />
                <Route path="usuarios" element={<Usuarios />} />
                <Route path="clientes" element={<Clientes />} />
                <Route path="prospectos" element={<Prospectos />} />
                <Route path="cuentas" element={<Cuentas />} />
                <Route path="cajas" element={<Cajas />} />
                <Route path="movimientos" element={<Movimientos />} />
                <Route path="creditos" element={<Creditos />} />
                <Route path="desembolsos" element={<Desembolsos />} />
                <Route path="cobranzas" element={<Cobranzas />} />
                <Route path="auditoria" element={<Auditoria />} />
                <Route path="reportes" element={<Reportes />} />
                <Route path="solicitudes" element={<Solicitudes />} />
                <Route path="ruta-cobros" element={<RutaCobros />} />
                <Route path="notificaciones" element={<Notificaciones />} />
            </Route>
        </Routes>
    );
}
