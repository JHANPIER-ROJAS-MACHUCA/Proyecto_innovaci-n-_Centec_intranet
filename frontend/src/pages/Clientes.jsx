import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { clienteService } from '../services/clienteService.js';
import { dniService } from '../services/campoService.js';

function colorRiskProfile(value) {
  switch (value) {
    case 'red': return '#FFAB91';
    case 'yellow': return '#FFF59D';
    case 'gray': return '#B0BEC5';
    case 'green': return '#CCFF90';
    default: return 'white';
  }
}

function textRiskProfile(value) {
  switch (value) {
    case 'red': return 'Alto riesgo';
    case 'yellow': return 'Mediano riesgo';
    case 'gray': return 'No registra información en el mes';
    case 'green': return 'Sin riesgo';
    default: return '---';
  }
}

const VACIO = { dni: '', ap: '', am: '', nom: '', sexo: 'M', cel: '', correo: '', rubro: '', telefono: '', n_hijos: '', grado_inst: '', fec_nac: '', estado_civil: '', tipo: '', direc: '', lugar_nac: '', client_type: '', risk_profile_id: '' };

export default function Clientes() {
  const [data, setData] = useState([]);
  const [search, setSearch] = useState('');
  const [crear, setCrear] = useState(false);
  const [editar, setEditar] = useState(null);
  const [form, setForm] = useState(VACIO);
  const [msg, setMsg] = useState('');

  function cargar(q) {
    clienteService.buscar(q || '').then((r) => setData(r.data || [])).catch(() => setData([]));
  }

  useEffect(() => { cargar(''); }, []);

  useEffect(() => {
    const t = setTimeout(() => cargar(search.trim()), 400);
    return () => clearTimeout(t);
  }, [search]);

  function filtrados() {
    const s = search.trim().toLowerCase();
    if (!s) return data;
    const partes = s.split(' ');
    return data.filter((i) => {
      const nombre = `${i.ap || ''} ${i.am || ''} ${i.nom || ''}`.toLowerCase();
      return partes.every((v) => nombre.includes(v)) || (i.dni || '').startsWith(search.trim());
    });
  }

  async function buscarDni() {
    if ((form.dni || '').length !== 8) {
      setMsg('DNI de 8 dígitos para autocompletar.');
      return;
    }
    try {
      const r = await dniService.consultar(form.dni);
      setForm({ ...form, ap: r.data.father_first_surname || form.ap, am: r.data.mother_first_surname || form.am, nom: r.data.name || form.nom });
    } catch (err) {
      setMsg(err.message);
    }
  }

  async function guardar(e) {
    e.preventDefault();
    try {
      if (editar) {
        await clienteService.actualizar({ ...form, idCG: editar.idCG });
        setMsg('Cliente actualizado.');
      } else {
        await clienteService.crear(form);
        setMsg('Cliente registrado con éxito!!');
      }
      setCrear(false);
      setEditar(null);
      setForm(VACIO);
      cargar(search.trim());
    } catch (err) {
      setMsg(err.message);
    }
  }

  function exportar() {
    const rows = filtrados();
    const head = ['idCG', 'dni', 'ap', 'am', 'nom', 'cel', 'direc'];
    const esc = (v) => `"${String(v ?? '').replace(/"/g, '""')}"`;
    const csv = [head.join(','), ...rows.map((r) => head.map((h) => esc(r[h])).join(','))].join('\n');
    const a = document.createElement('a');
    a.href = URL.createObjectURL(new Blob([csv], { type: 'text/csv;charset=utf-8' }));
    a.download = 'clientes.csv';
    a.click();
  }

  const lista = filtrados();

  return (
    <div className="panel panel-info">
      <div className="panel-heading">
        <div className="pull-right">
          <button className="btn btn-warning" style={{ marginRight: 15 }} onClick={exportar}>Exportar a excel</button>
          <Link className="btn btn-primary" style={{ marginRight: 10 }} to={lista[0] ? `/clientes/${lista[0].idCG}` : '/clientes'}><i className="fa fa-map-marker"></i> Ver en el mapa</Link>
          <button type="button" className="btn btn-info" onClick={() => { setForm(VACIO); setEditar(null); setCrear(true); }}>
            <span className="glyphicon glyphicon-plus"></span> Nuevo Cliente
          </button>
        </div>
        <h4><i className="glyphicon glyphicon-search"></i> Buscar Clientes <small style={{ color: 'black' }}>del Centro de Tecnología y Créditos del Perú</small></h4>
      </div>
      <div className="panel-body">
        <div style={{ display: 'flex', justifyContent: 'center', alignItems: 'center', padding: 20 }}>
          <label style={{ marginRight: 20 }}>Buscar</label>
          <input type="text" className="form-control" style={{ maxWidth: 300 }} value={search} onChange={(e) => setSearch(e.target.value)} />
        </div>
        {msg !== '' && <div className="alert alert-info">{msg}</div>}
        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(300px, 1fr))', gap: 20 }}>
          {lista.map((customer) => (
            <div key={customer.idCG} style={{ border: '1px solid #e7eaec', display: 'flex', flexDirection: 'column' }}>
              <div style={{ flex: '1 1 auto', display: 'flex', flexDirection: 'column', alignItems: 'center', padding: 20, background: colorRiskProfile(customer.risk_profile?.description) }}>
                <div>
                  <img className="img-circle" src={customer.sexo === 'F' ? './img/profile2.jpg' : './img/profile.jpg'} width="70" height="70" alt="" />
                </div>
                <h3 style={{ textAlign: 'center', marginTop: 10 }}>{`${customer.ap || ''} ${customer.am || ''} ${customer.nom || ''}`}</h3>
                <div className="font-bold"><i className="fa fa-address-card-o"></i> <span>{customer.dni}</span></div>
                <div><span style={{ fontWeight: 'bold' }}>Código:</span> <span>{customer.idCG}</span></div>
                <div style={{ textAlign: 'center' }}><span style={{ fontWeight: 'bold' }}>Perfil de riesgo:</span> <span>{textRiskProfile(customer.risk_profile?.description)}</span></div>
                {customer.cel && <div><i className="fa fa-phone"> </i> <span>{customer.cel}</span></div>}
                {customer.direc && <div><i className="fa fa-map-marker"></i> <span>{customer.direc}</span></div>}
              </div>
              <div style={{ display: 'flex', flexDirection: 'column', alignItems: 'center', padding: '10px 20px', borderTop: '1px solid #e7eaec' }}>
                <div className="m-t-xs btn-group">
                  <Link to="/formatos" className="btn btn-xs btn-default"><i className="fa fa-file-pdf-o" style={{ color: 'black' }}></i><span style={{ marginLeft: 5 }}>No adeudo</span></Link>
                  <Link to="/cobrar" className="btn btn-xs btn-default"><i className="fa fa-money"></i><span style={{ marginLeft: 5 }}>Compromiso</span></Link>
                  <Link to={`/clientes/${customer.idCG}`} title="Datos del cliente" className="btn btn-xs btn-white"><i className="fa fa-linode"></i> Datos</Link>
                </div>
                <div className="m-t-xs btn-group">
                  <button className="btn btn-xs btn-default" title="Editar cliente" onClick={() => { setForm({ ...VACIO, ...customer }); setEditar(customer); setCrear(true); }}>
                    <i className="glyphicon glyphicon-edit"></i> Editar
                  </button>
                  {(customer.coordinate_lat && customer.coordinate_lng) && (
                    <Link to={`/clientes/${customer.idCG}`} className="btn btn-xs btn-default" title="Ver ubicación"><i className="fa fa-map-marker"></i> Ubicación</Link>
                  )}
                </div>
              </div>
            </div>
          ))}
        </div>
        {(crear) && (
          <div style={{ position: 'fixed', inset: 0, zIndex: 3000, background: 'rgba(0,0,0,0.5)', padding: 20, overflow: 'auto' }}>
            <div style={{ border: '1px solid #e7eaec', background: 'white', maxWidth: 700, margin: '0 auto', borderRadius: 5 }}>
              <div style={{ padding: '10px 20px', display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                <h4>{editar ? 'Editar cliente' : 'Nuevo cliente'}</h4>
                <button className="btn btn-xs btn-default" onClick={() => { setCrear(false); setEditar(null); }}>X</button>
              </div>
              <form onSubmit={guardar} style={{ padding: '0 20px 20px', display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 10 }}>
                {[['dni', 'DNI'], ['ap', 'Apellido paterno'], ['am', 'Apellido materno'], ['nom', 'Nombres'], ['cel', 'Celular'], ['correo', 'Correo'], ['rubro', 'Rubro'], ['telefono', 'Dirección de negocio'], ['n_hijos', 'N° hijos'], ['grado_inst', 'Grado Inst.'], ['fec_nac', 'Fecha nacimiento'], ['lugar_nac', 'Lugar nacimiento'], ['direc', 'Dirección domiciliaria']].map(([f, ph]) => (
                  <div key={f}><label>{ph}</label><input type={f === 'fec_nac' ? 'date' : 'text'} className="form-control" value={form[f] || ''} onChange={(e) => setForm({ ...form, [f]: e.target.value })} /></div>
                ))}
                <div><label>Sexo</label><select className="form-control" value={form.sexo || ''} onChange={(e) => setForm({ ...form, sexo: e.target.value })}><option value="M">M</option><option value="F">F</option></select></div>
                <div><label>Estado civil</label><select className="form-control" value={form.estado_civil || ''} onChange={(e) => setForm({ ...form, estado_civil: e.target.value })}><option value="">Seleccione</option><option value="S">Soltero</option><option value="C">Casado</option><option value="V">Viudo</option><option value="D">Divorciado</option><option value="Conv">Conviviente</option><option value="Sep">Separado</option></select></div>
                <div><label>Vivienda</label><select className="form-control" value={form.tipo || ''} onChange={(e) => setForm({ ...form, tipo: e.target.value })}><option value="">Seleccione</option><option value="Propia">Propia</option><option value="Familiar">Familiar</option><option value="Alquilada">Alquilada</option></select></div>
                <div style={{ gridColumn: '1 / -1' }}>
                  <button className="btn btn-info" type="button" onClick={buscarDni}>Autocompletar por DNI</button>{' '}
                  <button className="btn btn-success" type="submit">{editar ? 'Actualizar' : 'Registrar'}</button>
                </div>
              </form>
            </div>
          </div>
        )}
      </div>
    </div>
  );
}
