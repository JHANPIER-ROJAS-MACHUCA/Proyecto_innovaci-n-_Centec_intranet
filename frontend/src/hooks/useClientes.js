import { useState } from 'react';
import { clienteService } from '../services/clienteService.js';

export function useClientes() {
  const [items, setItems] = useState([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  async function buscar(q) {
    setLoading(true);
    setError('');
    try {
      const r = await clienteService.buscar(q);
      setItems(r.data || []);
    } catch (e) {
      setError(e.message);
    } finally {
      setLoading(false);
    }
  }
  return { items, loading, error, buscar };
}
