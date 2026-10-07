import { useState } from 'react';
import { creditoService } from '../services/operacionService.js';
import { clienteService } from '../services/clienteService.js';
import { api } from '../services/api.js';

function Hoja({ titulo, niños }) {
  return (
    <div className="ibox float-e-margins">
      <div className="ibox-title"><h5>{titulo}</h5><div className="ibox-tools"><button className="btn btn-xs btn-primary" onClick={() => window.print()}>Imprimir</button></div></div>
      <div className="ibox-content">{niños}</div>
    </div>
  );
}

export default function Formatos() {
  const [idP, setIdP] = useState('');
  const [credito, setCredito] = useState(null);
  const [idCG, setIdCG] = useState('');
  const [cliente, setCliente] = useState(null);
  const [contrato, setContrato] = useState(null);

  async function cargar(e) {
    e.preventDefault();
    try {
      const [cr, cl, ct] = await Promise.all([
        creditoService.detalle(idP).then((r) => r.data).catch(() => null),
        clienteService.detalle(idCG).then((r) => r.data).catch(() => null),
        idP ? api.get(`/api/formatos/contrato?idP=${idP}`).then((r) => r.data).catch(() => null) : null,
      ]);
      setCredito(cr);
      setCliente(cl);
      setContrato(ct);
    } catch {
      setCredito(null);
      setCliente(null);
      setContrato(null);
    }
  }

  return (
    <div className="row">
      <div className="col-lg-12">
        <div className="ibox float-e-margins">
          <div className="ibox-title"><h5>Formatos imprimibles</h5></div>
          <div className="ibox-content">
            <form className="form-inline" onSubmit={cargar}>
              <div className="form-group"><input className="form-control" placeholder="ID crédito" value={idP} onChange={(e) => setIdP(e.target.value)} /></div>{' '}
              <div className="form-group"><input className="form-control" placeholder="idCG cliente" value={idCG} onChange={(e) => setIdCG(e.target.value)} /></div>{' '}
              <button className="btn btn-primary" type="submit">Cargar datos</button>
            </form>
          </div>
        </div>
      </div>
      <div className="col-lg-6">
        <Hoja titulo="Voucher de pago" niños={
          <div><p><strong>CREDISOPORTE FINANCIERO — CENTECP E.I.R.L.</strong></p><p>Crédito: {credito?.idP || '---'} | Cliente: {cliente ? `${cliente.ap || ''} ${cliente.nom || ''} (${cliente.dni || ''})` : '---'}</p><p>Capital: {credito?.capital || '---'} | Estado: {credito?.estado || '---'}</p><p>Fecha: {new Date().toLocaleString()}</p></div>
        } />
      </div>
      <div className="col-lg-6">
        <Hoja titulo="Constancia de no adeudo" niños={
          <div><p><strong>CREDISOPORTE FINANCIERO — CENTECP E.I.R.L.</strong></p><p>Por el presente se deja constancia que el cliente {cliente ? `${cliente.ap || ''} ${cliente.am || ''} ${cliente.nom || ''}` : '---'} con DNI {cliente?.dni || '---'} {credito ? `registra el crédito #${credito.idP} en estado ${credito.estado}` : 'no registra datos cargados'}.</p><p>Satipo, {new Date().toLocaleDateString()}</p></div>
        } />
      </div>
      <div className="col-lg-12">
        <Hoja titulo="Contrato privado de préstamo" niños={
          !contrato ? <p className="text-muted">Ingresa el ID de crédito para generar el contrato.</p> : (
            <div style={{ fontFamily: "'Franklin Gothic Medium','Arial Narrow',Arial,sans-serif", fontSize: 13 }}>
              <h4 className="text-center" style={{ marginBottom: 5 }}>{contrato.negocio?.nombreEmpresa}</h4>
              <h3 className="text-center" style={{ marginTop: 0 }}>CONTRATO PRIVADO DE PRÉSTAMO</h3>
              <div className="text-center">NUMERO DE CONTRATO {contrato.numero}</div>
              <p style={{ textAlign: 'justify' }}>
                Conste por el presente documento el contrato de préstamo que celebran de una parte, el <b>{contrato.negocio?.nombreEmpresa} {contrato.negocio?.siglas}</b>, con RUC <b>{contrato.negocio?.ruc}</b>, con domicilio en <b>{contrato.negocio?.direccion}</b>, debidamente representada por su Gerente General, <b>{contrato.negocio?.representante}</b>, identificado con D.N.I. <b>{contrato.negocio?.dnir}</b>,
                y de la otra parte el <b>Sr./Sra {contrato.credit?.client}</b>, identificado/a con <b>D.N.I. {contrato.credit?.dni}</b>, con domicilio en {contrato.credit?.direc}, a quien en adelante se denominará <b>PRESTATARIO</b>.
              </p>
              <h5>ANTECEDENTES</h5>
              <p>Monto: S/ {contrato.credit?.montoPropuesto} · Forma de pago: {contrato.formaPago} · Plazo: {contrato.credit?.plazo} · {contrato.fecha}.</p>
              {(contrato.vinculacion) && <p>Cónyuge: {contrato.vinculacion.conyuge_ap} {contrato.vinculacion.conyuge_nom} ({contrato.vinculacion.conyuge_dni}) · Aval: {contrato.vinculacion.aval_ap} {contrato.vinculacion.aval_nom} ({contrato.vinculacion.aval_dni})</p>}
            </div>
          )
        } />
      </div>
      <div className="col-lg-12">
        <Hoja titulo="Historial de pagos" niños={
          <div className="table-responsive">
            <table className="table table-striped table-bordered">
              <thead><tr><th>#</th><th>Cuota</th><th>Pagado</th><th>Prog.</th><th>Pago</th></tr></thead>
              <tbody>{(credito?.installments || []).map((q, i) => (<tr key={q.idPD || i}><td>{i + 1}</td><td>{q.cuota}</td><td>{q.montoPagado}</td><td>{q.fechaProg}</td><td>{q.fechaPago || '---'}</td></tr>))}</tbody>
            </table>
          </div>
        } />
      </div>
    </div>
  );
}
