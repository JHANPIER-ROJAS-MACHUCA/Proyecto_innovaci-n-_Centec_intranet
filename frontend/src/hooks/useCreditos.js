import { useState } from 'react';
import { creditoService, cobroService } from '../services/operacionService.js';

export function useCreditos(idCG) {
  const [items, setItems] = useState([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  async function cargar() {
    setLoading(true);
    setError('');
    try {
      const r = await creditoService.porCliente(idCG);
      setItems(r.data || []);
    } catch (e) {
      setError(e.message);
    } finally {
      setLoading(false);
    }
  }

  async function cobrar(payload) {
    setLoading(true);
    setError('');
    try {
      await cobroService.cobrar(payload);
      await cargar();
    } catch (e) {
      setError(e.message);
    } finally {
      setLoading(false);
    }
  }
  return { items, loading, error, cargar, cobrar };
}
