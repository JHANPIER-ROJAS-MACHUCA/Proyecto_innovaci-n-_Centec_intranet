import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { campoService } from '../services/campoService.js';

export default function Campo() {
  const [q, setQ] = useState('');
  const [lista, setLista] = useState([]);
  const [vencidos, setVencidos] = useState([]);
  const [sel, setSel] = useState(null);

  function cargar(busqueda) {
    campoService.cobrosHoy(busqueda).then((r) => {
      setLista(r.data || []);
      setVencidos(r.charges || []);
    }).catch(() => {
      setLista([]);
      setVencidos([]);
    });
  }

  useEffect(() => { cargar(''); }, []);

  async function ver(idP) {
    try {
      const r = await campoService.creditToPay(idP);
      setSel({ ...r.data, cuotas: r.cuotas, total: r.totalPendiente });
    } catch {
      setSel(null);
    }
  }

  return (
    <div className="row">
      <div className="col-lg-6">
        <div className="ibox float-e-margins">
          <div className="ibox-title"><h5>Cobros de hoy (campo)</h5></div>
          <div className="ibox-content">
            <form className="form-inline" onSubmit={(e) => { e.preventDefault(); cargar(q); }}>
              <div className="form-group"><input className="form-control" placeholder="Nombre o DNI" value={q} onChange={(e) => setQ(e.target.value)} /></div>{' '}
              <button className="btn btn-primary" type="submit">Buscar</button>
            </form>
            <div className="table-responsive" style={{ marginTop: 10, maxHeight: 400, overflow: 'auto' }}>
              <table className="table table-striped table-bordered">
                <thead><tr><th>Cliente</th><th>Cel</th><th></th></tr></thead>
                <tbody>
                  {lista.map((c) => (
                    <tr key={c.idP}>
                      <td>{c.customer}<br /><small>{c.idP} · {c.payment_period}</small></td>
                      <td><a href={`tel:${c.cell_phone}`}>{c.cell_phone}</a></td>
                      <td><button className="btn btn-xs btn-info" onClick={() => ver(c.idP)}>Ver</button></td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
            {vencidos.length > 0 && (
              <div style={{ marginTop: 10 }}>
                <h5>No diarios vencidos</h5>
                <div className="table-responsive" style={{ maxHeight: 250, overflow: 'auto' }}>
                  <table className="table table-striped table-bordered">
                    <thead><tr><th>Cliente</th><th></th></tr></thead>
                    <tbody>{vencidos.map((c) => (<tr key={c.idP}><td>{c.customer}</td><td><button className="btn btn-xs btn-info" onClick={() => ver(c.idP)}>Ver</button></td></tr>))}</tbody>
                  </table>
                </div>
              </div>
            )}
          </div>
        </div>
      </div>
      <div className="col-lg-6">
        <div className="ibox float-e-margins">
          <div className="ibox-title"><h5>Crédito a cobrar</h5></div>
          <div className="ibox-content">
            {!sel && <p className="text-muted">Selecciona un crédito.</p>}
            {sel && (
              <div>
                <p><strong>{sel.customer}</strong> ({sel.credit_type || '---'})</p>
                <p><strong>Total pendiente:</strong> S/ {sel.total}</p>
                <div className="table-responsive" style={{ maxHeight: 350, overflow: 'auto' }}>
                  <table className="table table-striped table-bordered">
                    <thead><tr><th>#</th><th>Prog.</th><th>Deuda</th></tr></thead>
                    <tbody>{(sel.cuotas || []).map((c) => (<tr key={c.idPD}><td>{c.ncuota}</td><td>{c.fechaProg}</td><td>{c.deuda}</td></tr>))}</tbody>
                  </table>
                </div>
                <Link className="btn btn-success" to={`/cobrar?id=${sel.idP}`}>Ir a cobrar</Link>
              </div>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}
