import { useEffect, useState } from 'react';
import { useSearchParams } from 'react-router-dom';
import { creditoService, cobroService } from '../services/operacionService.js';

export default function Cobrar() {
  const [params] = useSearchParams();
  const [creditId, setCreditId] = useState(params.get('id') || '');
  const [credito, setCredito] = useState(null);
  const [monto, setMonto] = useState('');
  const [mora, setMora] = useState('0');
  const [modo, setModo] = useState('amount');
  const [sim, setSim] = useState(null);
  const [msg, setMsg] = useState('');

  useEffect(() => {
    if (params.get('id')) buscar();
  }, []);

  async function buscar(e) {
    e?.preventDefault?.();
    setMsg('');
    setSim(null);
    try {
      const r = await creditoService.detalle(creditId);
      setCredito(r.data);
    } catch (err) {
      setMsg(err.message);
      setCredito(null);
    }
  }

  async function simular() {
    setMsg('');
    try {
      const r = await cobroService.simular({ creditId: Number(creditId), value: Number(monto), paymentType: modo, mora: Number(mora) });
      setSim(r);
    } catch (err) {
      setMsg(err.message);
      setSim(null);
    }
  }

  async function confirmar() {
    setMsg('');
    try {
      const r = await cobroService.cobrar({ creditId: Number(creditId), value: Number(monto), paymentType: modo, mora: Number(mora) });
      setMsg(`${r.message} Operación #${r.operationNumber}`);
      setSim(null);
      buscar();
    } catch (err) {
      setMsg(err.message);
    }
  }

  async function condonar() {
    setMsg('');
    try {
      await cobroService.condonar({ creditId: Number(creditId), date: new Date().toISOString().slice(0, 10) });
      setMsg('Mora condonada.');
    } catch (err) {
      setMsg(err.message);
    }
  }

  // ?accion=condonar (ADMINISTRACIÓN del antiguo, solo 1/2) -> aviso contextual.
  const esCondonacion = params.get('accion') === 'condonar';

  return (
    <div className="row">
      <div className="col-lg-12">
        <div className="ibox float-e-margins">
          <div className="ibox-title"><h5>Cobrar crédito{esCondonacion && ' — Condonación de Mora'}</h5></div>
          <div className="ibox-content">
            {esCondonacion && (
              <div className="alert alert-warning">Busca el crédito y usa <strong>Condonar mora</strong> para condonar la mora del día.</div>
            )}
            <form className="form-inline" onSubmit={buscar}>
              <div className="form-group"><input className="form-control" placeholder="ID de crédito" value={creditId} onChange={(e) => setCreditId(e.target.value)} /></div>{' '}
              <button className="btn btn-primary" type="submit">Buscar</button>
            </form>
            {credito && (
              <div style={{ marginTop: 15 }}>
                <p><strong>Cliente:</strong> {credito.customer ? `${credito.customer.ap || ''} ${credito.customer.am || ''} ${credito.customer.nom || ''}` : `#${credito.idCG}`} | <strong>Capital:</strong> {credito.capital} | <strong>Estado:</strong> {credito.estado}</p>
                <div className="table-responsive">
                  <table className="table table-striped table-bordered">
                    <thead><tr><th>#</th><th>Cuota</th><th>Pagado</th><th>Prog.</th><th>Estado</th></tr></thead>
                    <tbody>{(credito.installments || []).map((q, i) => (<tr key={q.idPD || i}><td>{i + 1}</td><td>{q.cuota}</td><td>{q.montoPagado}</td><td>{q.fechaProg}</td><td>{q.estado}</td></tr>))}</tbody>
                  </table>
                </div>
                <div className="form-inline">
                  <select className="form-control" value={modo} onChange={(e) => setModo(e.target.value)}>
                    <option value="amount">Por monto</option>
                    <option value="installment">Por cuotas</option>
                  </select>{' '}
                  <div className="form-group"><input className="form-control" placeholder={modo === 'amount' ? 'Monto S/' : 'N° cuotas'} value={monto} onChange={(e) => setMonto(e.target.value)} /></div>{' '}
                  <div className="form-group"><input className="form-control" placeholder="Moras a pagar" value={mora} onChange={(e) => setMora(e.target.value)} /></div>{' '}
                  <button className="btn btn-info" type="button" onClick={simular}>Simular</button>{' '}
                  <button className="btn btn-warning" type="button" onClick={condonar}>Condonar mora</button>
                </div>
                {sim && (
                  <div style={{ marginTop: 15 }}>
                    <p><strong>Deuda capital:</strong> {sim.debtCapital} | <strong>Interés:</strong> {sim.debtInterest} | <strong>Mora:</strong> {sim.penalty}</p>
                    <div className="table-responsive">
                      <table className="table table-striped table-bordered">
                        <thead><tr><th>Cuota</th><th>Capital</th><th>Interés</th><th>Mora</th></tr></thead>
                        <tbody>{(sim.resumen || []).map((s) => (<tr key={s.id}><td>{s.number}</td><td>{s.capitalPayment}</td><td>{s.interestPayment}</td><td>{s.penaltyPayment}</td></tr>))}</tbody>
                      </table>
                    </div>
                    <button className="btn btn-success" type="button" onClick={confirmar}>Confirmar cobro</button>
                  </div>
                )}
              </div>
            )}
            {msg !== '' && <div className="alert alert-info" style={{ marginTop: 10 }}>{msg}</div>}
          </div>
        </div>
      </div>
    </div>
  );
}
