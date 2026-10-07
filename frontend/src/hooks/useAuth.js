import { useState } from 'react';
import { login } from '../services/authService.js';

export function useAuth() {
  const [user, setUser] = useState('');
  const [password, setPassword] = useState('');
  const [sending, setSending] = useState(false);
  const [error, setError] = useState('');

  async function submit(e) {
    e?.preventDefault?.();
    if (sending) return;
    setSending(true);
    setError('');
    try {
      await login(user, password);
    } catch (err) {
      setError(err.message || 'Credenciales incorrectas.');
    } finally {
      setSending(false);
    }
  }
  return { user, setUser, password, setPassword, sending, error, submit };
}
