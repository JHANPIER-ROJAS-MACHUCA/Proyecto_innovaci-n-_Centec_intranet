import { useState } from 'react';
import { useSession } from '../contexts/AuthContext.jsx';
import { cuentaService } from '../services/operacionService.js';
import { api } from '../services/api.js';

export default function Cuenta() {
  const { user, refresh } = useSession();
  const [perfil, setPerfil] = useState({ celU: '', direcU: '', correoU: '' });
  const [clave, setClave] = useState({ actual: '', nueva: '' });
  const [msg, setMsg] = useState('');

  async function guardar(e) {
    e.preventDefault();
    try {
      await cuentaService.miPerfil(perfil);
      setMsg('Perfil actualizado.');
      refresh();
    } catch (err) {
      setMsg(err.message);
    }
  }

  async function cambiar(e) {
    e.preventDefault();
    try {
      await cuentaService.cambiarClave(clave);
      setMsg('Clave cambiada.');
      setClave({ actual: '', nueva: '' });
    } catch (err) {
      setMsg(err.message);
    }
  }

  async function avatar(e) {
    const file = e.target.files[0];
    if (!file) return;
    const fd = new FormData();
    fd.append('file', file);
    const res = await fetch('/Centecp_Intranet/backend/public/index.php/api/usuarios/avatar', { method: 'POST', body: fd, credentials: 'same-origin' });
    if (res.ok) {
      setMsg('Foto actualizada.');
      refresh();
    } else {
      setMsg('No se pudo subir la foto.');
    }
  }

  return (
    <div className="row">
      <div className="col-lg-4">
        <div className="ibox float-e-margins">
          <div className="ibox-title"><h5>Mi cuenta — {user?.nombre}</h5></div>
          <div className="ibox-content">
            <p><strong>Foto</strong></p>
            <input type="file" accept="image/*" onChange={avatar} />
            <button className="btn btn-xs btn-danger" style={{ marginTop: 8 }} onClick={() => api.post('/api/usuarios/eliminar-avatar', {}).then(() => { setMsg('Foto eliminada.'); refresh(); })}>Quitar foto</button>
            <hr />
            <form onSubmit={guardar}>
              <div className="form-group"><input className="form-control" placeholder="Celular" value={perfil.celU} onChange={(e) => setPerfil({ ...perfil, celU: e.target.value })} /></div>
              <div className="form-group"><input className="form-control" placeholder="Dirección" value={perfil.direcU} onChange={(e) => setPerfil({ ...perfil, direcU: e.target.value })} /></div>
              <div className="form-group"><input className="form-control" placeholder="Correo" value={perfil.correoU} onChange={(e) => setPerfil({ ...perfil, correoU: e.target.value })} /></div>
              <button className="btn btn-primary" type="submit">Modificar datos</button>
            </form>
          </div>
        </div>
      </div>
      <div className="col-lg-4">
        <div className="ibox float-e-margins">
          <div className="ibox-title"><h5>Cambiar password</h5></div>
          <div className="ibox-content">
            <form onSubmit={cambiar}>
              <div className="form-group"><input type="password" className="form-control" placeholder="Clave actual" value={clave.actual} onChange={(e) => setClave({ ...clave, actual: e.target.value })} /></div>
              <div className="form-group"><input type="password" className="form-control" placeholder="Nueva clave (6+)" value={clave.nueva} onChange={(e) => setClave({ ...clave, nueva: e.target.value })} /></div>
              <button className="btn btn-warning" type="submit">Cambiar</button>
            </form>
            {msg !== '' && <div className="alert alert-info" style={{ marginTop: 10 }}>{msg}</div>}
          </div>
        </div>
      </div>
    </div>
  );
}

export async function miPerfil() {
  return api.get('/api/auth/me');
}
