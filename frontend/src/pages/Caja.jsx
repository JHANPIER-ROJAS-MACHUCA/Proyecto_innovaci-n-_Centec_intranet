import { useEffect, useState } from 'react';
import { Link, useSearchParams } from 'react-router-dom';
import { useSession } from '../contexts/AuthContext.jsx';
import { api } from '../services/api.js';
import { ahorroService, cajaService, billetajeService, reciboService, bovedaService } from '../services/operacionService.js';

const DENOM = ['b200', 'b100', 'b50', 'b20', 'b10', 'm5', 'm2', 'm1', 'm05', 'm02', 'm01'];
const TABS = ['estado', 'apertura', 'cierre', 'billetaje', 'boveda', 'recibos', 'movimientos', 'ahorros'];

export default function Caja() {
  const [params] = useSearchParams();
  const [estado, setEstado] = useState(null);
  const [msg, setMsg] = useState('');
  // El tab lo gobierna la URL (?tab=) para que cada submenú del rol abra su vista.
  const tabParam = params.get('tab');
  const tab = TABS.includes(tabParam) ? tabParam : 'estado';
  const tipoParam = params.get('tipo');

  function cargar() {
    api.get('/api/caja/estado').then((r) => setEstado(r.data)).catch(() => setEstado(null));
  }

  useEffect(() => { cargar(); }, []);

  return (
    <div className="row">
      <div className="col-lg-12">
        <div className="ibox float-e-margins">
          <div className="ibox-title"><h5>Caja — {estado?.habilitada ? <span className="label label-primary">ABIERTA</span> : <span className="label label-danger">CERRADA</span>} EFECTIVO S/ {(estado?.efectivo ?? 0).toFixed(2)} DIGITAL S/ {(estado?.digital ?? 0).toFixed(2)}</h5></div>
          <div className="ibox-content">
            <ul className="nav nav-tabs">
              {TABS.map((t) => (
                <li key={t} className={tab === t ? 'active' : ''}><Link to={`/caja?tab=${t}`}>{t[0].toUpperCase() + t.slice(1)}</Link></li>
              ))}
            </ul>
            <div style={{ marginTop: 15 }}>
              {tab === 'estado' && <Estado estado={estado} setMsg={setMsg} cargar={cargar} />}
              {tab === 'apertura' && <Apertura msg={msg} setMsg={setMsg} cargar={cargar} />}
              {tab === 'cierre' && <Cierre setMsg={setMsg} cargar={cargar} />}
              {tab === 'billetaje' && <Billetaje msg={msg} setMsg={setMsg} />}
              {tab === 'boveda' && <Boveda setMsg={setMsg} />}
              {tab === 'recibos' && <Recibos key={tipoParam} tipoInicial={tipoParam} msg={msg} setMsg={setMsg} />}
              {tab === 'movimientos' && <Movimientos />}
              {tab === 'ahorros' && <Ahorros msg={msg} setMsg={setMsg} />}
            </div>
            {msg !== '' && <div className="alert alert-info" style={{ marginTop: 10 }}>{msg}</div>}
          </div>
        </div>
      </div>
    </div>
  );
}

function Estado({ estado }) {
  return (
    <p className="text-muted">Fecha: {estado?.fecha || '---'} · Clientes: {estado?.clientes ?? '…'} · Activos: {estado?.creditosActivos ?? '…'} · Propuestos: {estado?.creditosPropuestos ?? '…'}</p>
  );
}

function Boveda({ setMsg }) {
  const { user } = useSession();
  const [saldos, setSaldos] = useState([]);
  const [form, setForm] = useState({ monto: '', idO: '1' });
  const puede = user && [8, 1].includes(Number(user.tipoU));
  function cargar() {
    bovedaService.saldos().then((r) => setSaldos(r.data || [])).catch(() => setSaldos([]));
  }
  useEffect(() => { if (puede) cargar(); }, []);
  if (!puede) return <p className="text-muted">Solo gerencia.</p>;
  return (
    <div>
      <form className="form-inline" onSubmit={async (e) => {
        e.preventDefault();
        try {
          await bovedaService.designar({ ...form, monto: Number(form.monto) });
          setMsg('Bóveda designada.');
          cargar();
        } catch (err) {
          setMsg(err.message);
        }
      }}>
        <div className="form-group"><input className="form-control" placeholder="Monto" value={form.monto} onChange={(e) => setForm({ ...form, monto: e.target.value })} /></div>{' '}
        <div className="form-group"><input className="form-control" placeholder="Oficina" value={form.idO} onChange={(e) => setForm({ ...form, idO: e.target.value })} /></div>{' '}
        <button className="btn btn-success" type="submit">Designar</button>
      </form>
      <div className="table-responsive" style={{ marginTop: 10 }}>
        <table className="table table-striped table-bordered">
          <thead><tr><th>ID</th><th>Monto</th><th>Oficina</th><th>Fecha</th><th></th></tr></thead>
          <tbody>{saldos.map((b) => (<tr key={b.idBo}><td>{b.idBo}</td><td>{b.monto}</td><td>{b.idO}</td><td>{b.fecha}</td><td><button className="btn btn-xs btn-warning" onClick={() => bovedaService.consumir(b.idBo).then(cargar)}>Consumir</button></td></tr>))}</tbody>
        </table>
      </div>
    </div>
  );
}
function Cierre({ setMsg, cargar }) {
  const [monto, setMonto] = useState('');
  async function cerrar(e) {
    e.preventDefault();
    try {
      await cajaService.cerrar(monto);
      setMsg('Caja cerrada.');
      cargar();
    } catch (err) {
      setMsg(err.message);
    }
  }
  return (
    <form className="form-inline" onSubmit={cerrar}>
      <div className="form-group"><input className="form-control" placeholder="Monto final" value={monto} onChange={(e) => setMonto(e.target.value)} /></div>{' '}
      <button className="btn btn-danger" type="submit">Cerrar mi caja</button>
    </form>
  );
}

function Apertura({ setMsg, cargar }) {
  async function abrir(fn) {
    try {
      await fn();
      setMsg('Caja abierta.');
      cargar();
    } catch (e) {
      setMsg(e.message);
    }
  }
  return (
    <div>
      <button className="btn btn-primary" onClick={() => abrir(cajaService.abrirGerencia)}>Abrir caja gerencia (CG)</button>{' '}
      <button className="btn btn-info" onClick={() => abrir(cajaService.abrirOficina)}>Abrir caja de mi oficina</button>
    </div>
  );
}

function Billetaje({ setMsg }) {
  const [form, setForm] = useState({});
  const [pend, setPend] = useState([]);
  async function registrar(e) {
    e.preventDefault();
    try {
      const r = await billetajeService.registrar(form);
      setMsg(`Billetaje registrado. Total S/ ${r.data.total}`);
      pendientes();
    } catch (err) {
      setMsg(err.message);
    }
  }
  async function pendientes() {
    try {
      const r = await billetajeService.pendientes();
      setPend(r.data || []);
    } catch {
      setPend([]);
    }
  }
  useEffect(() => { pendientes(); }, []);
  return (
    <div>
      <form className="form-inline" onSubmit={registrar}>
        {DENOM.map((d) => (
          <span key={d} style={{ marginRight: 6 }}><input className="form-control" style={{ width: 80 }} placeholder={d} value={form[d] || ''} onChange={(e) => setForm({ ...form, [d]: e.target.value })} /></span>
        ))}{' '}
        <button className="btn btn-success" type="submit">Registrar billetaje</button>
      </form>
      <hr />
      <div className="table-responsive">
        <table className="table table-striped table-bordered">
          <thead><tr><th>ID</th><th>Usuario</th><th>Total</th><th>Fecha</th><th></th></tr></thead>
          <tbody>{pend.map((b) => (<tr key={b.idBille}><td>{b.idBille}</td><td>{b.dniU}</td><td>{b.total}</td><td>{b.fecha}</td><td><button className="btn btn-xs btn-primary" onClick={() => billetajeService.confirmar(b.idBille).then(pendientes)}>Confirmar</button></td></tr>))}</tbody>
        </table>
      </div>
    </div>
  );
}

function Recibos({ setMsg, tipoInicial }) {
  const [motivos, setMotivos] = useState([]);
  // ?tipo=2 (Egresos) / ?tipo=1 (Ingresos) según el submenú del rol.
  const [tipoM, setTipoM] = useState(tipoInicial === '2' ? '2' : '1');
  const [form, setForm] = useState({ motivo: '', monto: '', cliente: '' });
  useEffect(() => {
    reciboService.motivos(tipoM).then((r) => setMotivos(r.data || [])).catch(() => setMotivos([]));
  }, [tipoM]);
  async function registrar(e) {
    e.preventDefault();
    try {
      await reciboService.registrar({ ...form, motivo: Number(form.motivo), monto: Number(form.monto) });
      setMsg('Recibo registrado.');
    } catch (err) {
      setMsg(err.message);
    }
  }
  return (
    <form className="form-inline" onSubmit={registrar}>
      <select className="form-control" value={tipoM} onChange={(e) => setTipoM(e.target.value)}>
        <option value="1">Ingresos</option>
        <option value="2">Egresos</option>
      </select>{' '}
      <select className="form-control" value={form.motivo} onChange={(e) => setForm({ ...form, motivo: e.target.value })}>
        <option value="">Motivo…</option>
        {motivos.map((m) => (<option key={m.idam} value={m.idam}>{m.motivo}</option>))}
      </select>{' '}
      <input className="form-control" style={{ width: 110 }} placeholder="Monto" value={form.monto} onChange={(e) => setForm({ ...form, monto: e.target.value })} />{' '}
      <input className="form-control" style={{ width: 110 }} placeholder="ID cliente (op.)" value={form.cliente} onChange={(e) => setForm({ ...form, cliente: e.target.value })} />{' '}
      <button className="btn btn-success" type="submit">Registrar recibo</button>
    </form>
  );
}

function Movimientos() {
  const [rows, setRows] = useState([]);
  useEffect(() => {
    api.get('/api/caja/movimientos').then((r) => setRows(r.data || [])).catch(() => setRows([]));
  }, []);
  return (
    <div className="table-responsive">
      <table className="table table-striped table-bordered">
        <thead><tr><th>ID</th><th>Tipo</th><th>Total</th><th>Cliente</th><th>Fecha</th><th>Estado</th></tr></thead>
        <tbody>{rows.map((t) => (<tr key={t.idCAD}><td>{t.idCAD}</td><td>{t.tipo}</td><td>{t.total}</td><td>{t.cliente}</td><td>{t.created_at}</td><td>{t.estadodt}</td></tr>))}</tbody>
      </table>
    </div>
  );
}

function Ahorros({ setMsg }) {
  const [form, setForm] = useState({ customer_id: '', tipo: '7', motivo: '11', monto: '' });
  const [det, setDet] = useState([]);
  async function registrar(e) {
    e.preventDefault();
    try {
      await ahorroService.registrar({ ...form, customer_id: Number(form.customer_id), motivo: Number(form.motivo), monto: Number(form.monto) });
      setMsg('Movimiento de ahorro registrado.');
      detalle();
    } catch (err) {
      setMsg(err.message);
    }
  }
  async function detalle() {
    try {
      const r = await api.get(`/api/ahorros/detalle?customer_id=${form.customer_id}`);
      setDet(r.data || []);
    } catch {
      setDet([]);
    }
  }
  return (
    <div>
      <form className="form-inline" onSubmit={registrar}>
        <input className="form-control" style={{ width: 110 }} placeholder="ID cliente" value={form.customer_id} onChange={(e) => setForm({ ...form, customer_id: e.target.value })} />{' '}
        <select className="form-control" value={form.tipo} onChange={(e) => setForm({ ...form, tipo: e.target.value })}>
          <option value="7">Depósito</option>
          <option value="8">Retiro</option>
        </select>{' '}
        <input className="form-control" style={{ width: 110 }} placeholder="Monto" value={form.monto} onChange={(e) => setForm({ ...form, monto: e.target.value })} />{' '}
        <button className="btn btn-success" type="submit">Registrar</button>{' '}
        <button className="btn btn-info" type="button" onClick={detalle}>Ver detalle</button>
      </form>
      <div className="table-responsive" style={{ marginTop: 10 }}>
        <table className="table table-striped table-bordered">
          <thead><tr><th>ID</th><th>Monto</th><th>Tipo</th><th>Fecha</th></tr></thead>
          <tbody>{det.map((d) => (<tr key={d.idAd}><td>{d.idAd}</td><td>{d.monto}</td><td>{d.tipo}</td><td>{d.fecha}</td></tr>))}</tbody>
        </table>
      </div>
    </div>
  );
}
