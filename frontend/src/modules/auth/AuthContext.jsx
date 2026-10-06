import { createContext, useContext, useState, useEffect } from 'react';
import { api } from '../../services/api';

const AuthContext = createContext(null);

export function AuthProvider({ children }) {
    const [user, setUser] = useState(null);
    const [token, setToken] = useState(localStorage.getItem('token'));
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        if (token) {
            api.setToken(token);
            fetchProfile();
        } else {
            api.setToken(null);
            setLoading(false);
        }
    }, [token]);

    const fetchProfile = async () => {
        try {
            const res = await fetch('/api/auth/profile', {
                headers: { Authorization: `Bearer ${token}` },
            });
            if (res.ok) {
                const data = await res.json();
                setUser(data.data);
            } else {
                logout();
            }
        } catch {
            logout();
        } finally {
            setLoading(false);
        }
    };

    const login = async (username, password) => {
        const res = await fetch('/api/auth/login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username, password }),
        });
        const data = await res.json();
        if (!data.success) throw new Error(data.message);
        api.setToken(data.data.token);
        setToken(data.data.token);
        setUser(data.data.user);
        return data.data;
    };

    const logout = () => {
        api.setToken(null);
        setToken(null);
        setUser(null);
    };

    const hasPermission = (modulo, accion) => {
        if (!user) return false;
        const perms = user.permissions || [];
        return perms.some((p) => p.modulo === modulo && p.accion === accion);
    };

    return (
        <AuthContext.Provider value={{ user, token, login, logout, hasPermission, loading }}>
            {children}
        </AuthContext.Provider>
    );
}

export const useAuth = () => useContext(AuthContext);
