import { api } from './api.js';

export async function login(usu, pas) {
  const data = await api.post('/api/auth/login', { usu, pas }, { raw: true });
  if (String(data).trim() === '1') {
    window.location.href = '/Centecp_Intranet/frontend/';
    return true;
  }
  throw new Error('Credenciales incorrectas.');
}
