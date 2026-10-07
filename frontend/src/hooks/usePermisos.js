import { useEffect, useState } from 'react';
import { api } from '../services/api.js';

// Permisos finos del backend (GET /api/rbac/mis-permisos → {"modulo.vista.accion": true}).
// El backend es la fuente de verdad; esto solo oculta/muestra UI.
// El acceso directo por URL lo bloquean Guard (rol) + backend (403).
let cache = null;

export function usePermisos() {
  const [perms, setPerms] = useState(cache);
  const [loading, setLoading] = useState(!cache);

  useEffect(() => {
    if (cache) return;
    api
      .get('/api/rbac/mis-permisos')
      .then((r) => {
        cache = r.data || {};
        setPerms(cache);
      })
      .catch(() => setPerms({}))
      .finally(() => setLoading(false));
  }, []);

  const actual = perms || {};
  const tiene = (modulo, vista, accion) => {
    if (accion) return !!actual[`${modulo}.${vista}.${accion}`];
    const pref = `${modulo}.${vista}.`;
    return Object.keys(actual).some((k) => k.startsWith(pref));
  };

  return { perms: actual, loading, tiene };
}
