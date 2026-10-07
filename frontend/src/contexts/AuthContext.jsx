import { createContext, useCallback, useContext, useEffect, useState } from 'react';
import { api } from '../services/api.js';

const AuthContext = createContext(null);

export function AuthProvider({ children }) {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);

  const refresh = useCallback(async () => {
    try {
      const r = await api.get('/api/auth/me');
      setUser(r.data);
    } catch {
      setUser(null);
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => { refresh(); }, [refresh]);

  const login = useCallback(async (usu, pas) => {
    const data = await api.post('/api/auth/login', { usu, pas });
    if (String(data).trim() !== '1') throw new Error('Credenciales incorrectas.');
    await refresh();
  }, [refresh]);

  const logout = useCallback(async () => {
    await api.post('/api/auth/logout', {});
    setUser(null);
  }, []);

  return (
    <AuthContext.Provider value={{ user, loading, login, logout, refresh }}>
      {children}
    </AuthContext.Provider>
  );
}

export function useSession() {
  return useContext(AuthContext);
}
