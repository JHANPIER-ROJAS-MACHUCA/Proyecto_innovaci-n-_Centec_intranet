import { useEffect, useState } from 'react';
import { useNavigate, Navigate } from 'react-router-dom';
import { useSession } from '../contexts/AuthContext.jsx';

const LANDING_CSS = ['./CSS/normalize.css', './CSS/estilos.css'];

export default function Login() {
  const { user, loading, login } = useSession();
  const navigate = useNavigate();
  const [usu, setUsu] = useState('');
  const [pas, setPas] = useState('');
  const [sending, setSending] = useState(false);
  const [error, setError] = useState('');

  useEffect(() => {
    const links = LANDING_CSS.map((href) => {
      const l = document.createElement('link');
      l.rel = 'stylesheet';
      l.href = href;
      document.head.appendChild(l);
      return l;
    });
    return () => links.forEach((l) => l.remove());
  }, []);

  if (!loading && user) return <Navigate to="/" replace />;

  async function submit(e) {
    e.preventDefault();
    if (sending) return;
    setSending(true);
    setError('');
    try {
      await login(usu, pas);
      navigate('/', { replace: true });
    } catch {
      setError('Credenciales incorrectas.');
    } finally {
      setSending(false);
    }
  }

  return (
    <div className="modal__flex" style={{ display: 'flex' }}>
      <div className="modal__header"><h2>Iniciar Sesión</h2></div>
      <form onSubmit={submit}>
        <div className="content__flex">
          <div className="modal__img"><img src="./IMG/logotipo.png" alt="" /></div>
          <div className="form__flex">
            <label>Usuario</label>
            <input type="text" value={usu} onChange={(e) => setUsu(e.target.value)} />
            <label>Contraseña</label>
            <input type="password" value={pas} onChange={(e) => setPas(e.target.value)} />
            {error !== '' && <div style={{ marginTop: 20, color: 'red' }}>{error}</div>}
            <input className="form__button" type="submit" value={sending ? 'Ingresando...' : 'Ingresar'} />
          </div>
        </div>
      </form>
    </div>
  );
}
