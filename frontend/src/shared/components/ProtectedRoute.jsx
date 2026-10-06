import { Navigate } from 'react-router-dom';
import { useAuth } from '../../modules/auth/AuthContext';

export default function ProtectedRoute({ children, roles = [] }) {
    const { user, loading } = useAuth();

    if (loading) {
        return <div className="d-flex justify-content-center mt-5"><div className="spinner-border" /></div>;
    }

    if (!user) {
        return <Navigate to="/login" replace />;
    }

    if (roles.length > 0 && !roles.includes(user.rol_id)) {
        return <Navigate to="/unauthorized" replace />;
    }

    return children;
}
