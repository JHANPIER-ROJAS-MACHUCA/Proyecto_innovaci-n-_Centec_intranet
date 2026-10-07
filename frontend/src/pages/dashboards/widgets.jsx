import { Link } from 'react-router-dom';

// Widgets compartidos por los 5 dashboards de rol.
// Estilo INSPINIA (ibox) igual al resto del sistema.

export function Card({ titulo, valor, icono, to }) {
  return (
    <div className="col-lg-3 col-md-6">
      <div className="ibox float-e-margins">
        <div className="ibox-title"><h5>{titulo}</h5></div>
        <div className="ibox-content">
          <h1 className="no-margins">{valor}</h1>
          <div><i className={`fa ${icono}`}></i> <Link to={to}>Ver módulo</Link></div>
        </div>
      </div>
    </div>
  );
}

export function Panel({ titulo, accion, children }) {
  return (
    <div className="ibox float-e-margins">
      <div className="ibox-title">
        <h5>{titulo}</h5>
        {accion && <div className="ibox-tools">{accion}</div>}
      </div>
      <div className="ibox-content">{children}</div>
    </div>
  );
}

export function Bienvenida({ nombre, rol, caja }) {
  return (
    <div style={{ background: 'white', padding: '24px 16px', borderRadius: 10, marginBottom: 20 }}>
      <h3 style={{ marginTop: 0 }}>Hola, {nombre || 'bienvenido'} de nuevo.</h3>
      <p className="text-muted" style={{ marginBottom: 0 }}>
        Rol: <strong>{rol}</strong>
        {' · '}Caja: <strong>{caja?.habilitada ? `ABIERTA (Efectivo S/ ${Number(caja.efectivo || 0).toFixed(2)} · Digital S/ ${Number(caja.digital || 0).toFixed(2)})` : 'CERRADA'}</strong>
      </p>
      {!caja?.habilitada && (
        <div className="alert alert-warning" style={{ marginTop: 12, marginBottom: 0 }}>
          Tu caja está cerrada: primero ve a <Link to="/caja?tab=apertura">Caja → Iniciar Operaciones</Link> para habilitar COBRAR.
        </div>
      )}
    </div>
  );
}

export function Accesos({ items }) {
  return (
    <div className="row">
      {items.map((a) => (
        <div className="col-lg-3 col-md-6" key={a.to + a.label}>
          <Link to={a.to} className="btn btn-white btn-block" style={{ marginBottom: 10, textAlign: 'left' }}>
            <i className={`fa ${a.icon}`} /> {a.label}
          </Link>
        </div>
      ))}
    </div>
  );
}

export const fmt = (n) => (n === null || n === undefined || n === '' ? '…' : n);
