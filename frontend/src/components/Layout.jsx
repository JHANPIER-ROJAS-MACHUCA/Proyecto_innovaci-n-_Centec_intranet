import { Link, Outlet, useNavigate } from 'react-router-dom';
import { useEffect, useState } from 'react';
import { useSession } from '../contexts/AuthContext.jsx';
import { roleName, menuParaRol } from '../config/roles.jsx';
import { api } from '../services/api.js';

export default function Layout() {
  const { user, logout } = useSession();
  const navigate = useNavigate();
  const [caja, setCaja] = useState(null);
  const [abierto, setAbierto] = useState(null);
  const tipo = Number(user?.tipoU);
  const menu = menuParaRol(tipo);

  useEffect(() => {
    api.get('/api/caja/estado').then((r) => setCaja(r.data)).catch(() => setCaja(null));
  }, []);

  async function salir() {
    await logout();
    navigate('/login');
  }

  return (
    <div id="wrapper">
      <nav className="navbar-default navbar-static-side" role="navigation">
        <div className="sidebar-collapse">
          <ul className="nav metismenu" id="side-menu">
            <li className="nav-header">
              <div className="dropdown profile-element">
                <span><img alt="logo" className="img-circle" src="./img/logo.png" width="48" /></span>
                <span className="block m-t-xs"><strong className="font-bold">{user?.nombre || ''}</strong></span>
                <span className="text-muted text-xs block">{roleName(user?.tipoU)}</span>
                <div style={{ marginTop: 6 }}><Link to="/cuenta" style={{ color: '#fff', fontSize: 12 }}>Modificar / Password</Link></div>
              </div>
            </li>
            {menu.map((m) => {
              if (!m.subs) {
                return <li key={m.label}><Link to={m.to}><i className={`fa ${m.icon}`}></i> <span className="nav-label">{m.label}</span></Link></li>;
              }
              const open = abierto === m.label;
              return (
                <li key={m.label} className={open ? 'active' : ''}>
                  <a href="#/" onClick={(e) => { e.preventDefault(); setAbierto(open ? null : m.label); }}>
                    <i className={`fa ${m.icon}`}></i> <span className="nav-label">{m.label}</span> <span className="fa arrow"></span>
                  </a>
                  <ul className={open ? 'nav nav-second-level collapse in' : 'nav nav-second-level collapse'}>
                    {m.subs.map((s) => (
                      <li key={s.label}><Link to={s.to}>{s.label}</Link></li>
                    ))}
                  </ul>
                </li>
              );
            })}
            <li><a href="#/" onClick={(e) => { e.preventDefault(); salir(); }}><i className="fa fa-sign-out" style={{ color: 'red' }}></i> <span className="nav-label">Cerrar Sesión</span></a></li>
          </ul>
        </div>
      </nav>
      <div id="page-wrapper" className="gray-bg">
        <div className="row border-bottom">
          <nav className="navbar navbar-static-top" role="navigation" style={{ marginBottom: 0 }}>
            <div className="navbar-header" style={{ padding: '14px 20px' }}>
              <span className="text-navy"><strong>CAJA {caja?.habilitada ? 'ABIERTA' : 'CERRADA'}</strong></span>{' '}
              <span>{caja?.fecha || ''}</span>{' '}
              <span className="text-navy"><strong>EFECTIVO</strong></span> S/ {(caja?.efectivo ?? 0).toFixed(2)}{' '}
              <span className="text-navy"><strong>DIGITAL</strong></span> S/ {(caja?.digital ?? 0).toFixed(2)}
            </div>
            <ul className="nav navbar-top-links navbar-right">
              <li><span className="m-r-sm text-muted">CREDISOPORTE FINANCIERO</span></li>
              <li><a href="#/" onClick={(e) => { e.preventDefault(); salir(); }}><i className="fa fa-sign-out"></i> Cerrar Sesión</a></li>
            </ul>
          </nav>
        </div>
        <div className="wrapper wrapper-content animated fadeInRight">
          <Outlet />
        </div>
        <div className="footer"><div className="text-center">SISTEMA INTEGRAL DE PRESTAMOS Y AHORROS DIARIOS - SIPAD V.2.0</div></div>
      </div>
    </div>
  );
}
