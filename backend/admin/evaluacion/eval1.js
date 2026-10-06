//------------------------------
//identificadores de 1ra evaluacion
//--------------------------------
$(document).ready(function() {
  setInterval(function () {
    fechaEva1();
    //swal("hola");
  }, 100);
});
//function verificaci
function vacio(dato)
{
  var resul=dato;

  if(dato=='')
  {
    resul=0;
  }
  resul=parseFloat(resul);
  return resul;
}
/////fechas
function fechaEva1()
{
  var fecha1=$('#fecha1').val();
  //fecha
  $('#txtf1').val(fecha1);
  var fecha2=$('#fecha2').val();
  //fecha
  $('#txtf2').val(fecha2);
  var fecha3=$('#fecha3').val();
  //fecha
  $('#txtf3').val(fecha3);
}
//suma de INVENTARIO
function sumaInve(dato1,dato2,dato3,dato4)
{
  var resul="";
  resul=((vacio(dato1))+(vacio(dato2))+(vacio(dato3))+(vacio(dato4))).toFixed(2);
  return resul;
}
//VENTAS
function ventaDia(bueno,malo)
{
  var resul=((vacio(bueno)+vacio(malo))/2).toFixed(2);
  return resul;
}
//corto plazo
function corto(dispo,porCobra,inven)
{
  var resul=((vacio(dispo))+(vacio(porCobra))+(vacio(inven))).toFixed(2);
  return resul;
}
//costo de mercado
function costoMerca(pCom,pVen)
{
  var resul=((vacio(pCom)/vacio(pVen))).toFixed(2);
  if(vacio(pVen)==0)
  {
    resul=0;
  }
  return resul;
}
//costo mercado/prod
function costoMercaTotal(costo1,costo2,costo3,costo4,costo5,costo6,costo7)
{
  var cn=0;
  costo1=vacio(costo1);
  if(costo1>0)
  {
    cn++;
  }
  costo2=vacio(costo2);
  if(costo2>0)
  {
    cn++;
  }
  costo3=vacio(costo3);
  if(costo3>0)
  {
    cn++;
  }
  costo4=vacio(costo4);
  if(costo4>0)
  {
    cn++;
  }
  costo5=vacio(costo5);
  if(costo5>0)
  {
    cn++;
  }
  costo6=vacio(costo6);
  if(costo6>0)
  {
    cn++;
  }
  costo7=vacio(costo7);
  if(costo7>0)
  {
    cn++;
  }

  var resul=0;
  if(cn!=0)
  {
      resul=((costo1+costo2+costo3+costo4+costo5+costo6+costo7)/cn).toFixed(2);
  }
  return resul;
}
//otros ingresos
function otroIngreso(dato1,dato2,dato3)
{
  dato1=vacio(dato1);
  dato2=vacio(dato2);
  dato3=vacio(dato3);
  var resul=dato1+dato2+dato3;
  resul=(resul).toFixed(2);
  return resul;
}
//fucntion costo mercado producto
function cosMerProd(dato1,dato2)
{
  dato1=vacio(dato1);
  dato2=vacio(dato2);
  var resul=dato1*dato2;
  resul=(resul).toFixed(2);
  return resul;
}
//utilidad BRUTA
function utilidadBruta(dato1,dato2)
{
  dato1=vacio(dato1);
  dato2=vacio(dato2);
  var resul=dato1-dato2;
  resul=(resul).toFixed(2);
  return resul;
}
//cobertura - otros costoss
function cobOtrosCostos(dato1,dato2)
{
  dato1=vacio(dato1);
  dato2=vacio(dato2);
  var resul=dato1*dato2;
  resul=(resul).toFixed(2);
  return resul;
}
//excedente de la evaluacion
function excedente(dato1,dato2)
{
  dato1=vacio(dato1);
  dato2=vacio(dato2);
  var resul=dato1-dato2;
  resul=(resul).toFixed(2);
  return resul;
}

//evluacion 1
function eval1()
{
  //---------- INVENTARIO
  var inv1=$('#txtinve1a').val();
  var inv1m=$('#txtinve1a1').val();

  var inv2=$('#txtinve2a').val();
  var inv2m=$('#txtinve2a2').val();

  var inv3=$('#txtinve3a').val();
  var inv3m=$('#txtinve3a3').val();

  var inv4=$('#txtinve4a').val();
  var inv4m=$('#txtinve4a4').val();

  var ope=sumaInve(inv1m,inv2m,inv3m,inv4m);
  $('#txttotalInventarioa1').val(ope);
  var invtotal=$('#txttotalInventarioa1').val();

  $('#txttotinven1').val(ope);

//CUENTAS
var disponible=$('#txtdiponible1').val();
var porCobrar=$('#txtporcobrar1').val();
var totInventario=$('#txttotinven1').val();
var toCorto=corto(disponible,porCobrar,totInventario);
$('#txttotalCorto1').val(toCorto);
var totCorto=$('#txttotalCorto1').val();
  //----------------VENTAS

  var bueno=$('#txtbueno1').val();
  var malo=$('#txtmalo1').val();
  //tottal
  var vtota=ventaDia(bueno,malo);
  $('#txtestimacion1').val(vtota);
  var estimacion=$('#txtestimacion1').val();

  $('#txtventaDiaria1').val(vtota);
  var ventaDiaria=$('#txtventaDiaria1').val();

  //--------------costo de mercado

  var prod1=$('#txtcosto1a1').val();
  var preCompra1=$('#txtcosto1Precio1').val();
  var preVenta1=$('#txtcosto1Venta1').val();
  //tot
  var topro1=costoMerca(preCompra1,preVenta1);
  $('#txtcosto1Cr1').val(topro1);

  //--------------//
  var prod2=$('#txtcosto1a2').val();
  var preCompra2=$('#txtcosto1Precio2').val();
  var preVenta2=$('#txtcosto1Venta2').val();
  //tot
  var topro2=costoMerca(preCompra2,preVenta2);
  $('#txtcosto1Cr2').val(topro2);

  //--------------//
  var prod3=$('#txtcosto1a3').val();
  var preCompra3=$('#txtcosto1Precio3').val();
  var preVenta3=$('#txtcosto1Venta3').val();
  //tot
  var topro3=costoMerca(preCompra3,preVenta3);
  $('#txtcosto1Cr3').val(topro3);

  //--------------//
  var prod4=$('#txtcosto1a4').val();
  var preCompra4=$('#txtcosto1Precio4').val();
  var preVenta4=$('#txtcosto1Venta4').val();
  //tot
  var topro4=costoMerca(preCompra4,preVenta4);
  $('#txtcosto1Cr4').val(topro4);

  //--------------//
  var prod5=$('#txtcosto1a5').val();
  var preCompra5=$('#txtcosto1Precio5').val();
  var preVenta5=$('#txtcosto1Venta5').val();
  //tot
  var topro5=costoMerca(preCompra5,preVenta5);
  $('#txtcosto1Cr5').val(topro5);

  //--------------//
  var prod6=$('#txtcosto1a6').val();
  var preCompra6=$('#txtcosto1Precio6').val();
  var preVenta6=$('#txtcosto1Venta6').val();
  //tot
  var topro6=costoMerca(preCompra6,preVenta6);
  $('#txtcosto1Cr6').val(topro6);

  //--------------//
  var prod7=$('#txtcosto1a7').val();
  var preCompra7=$('#txtcosto1Precio7').val();
  var preVenta7=$('#txtcosto1Venta7').val();
  //tot
  var topro7=costoMerca(preCompra7,preVenta7);
  $('#txtcosto1Cr7').val(topro7);


  //total
  var cr1=$('#txtcosto1Cr1').val();
  var cr2=$('#txtcosto1Cr2').val();
  var cr3=$('#txtcosto1Cr3').val();
  var cr4=$('#txtcosto1Cr4').val();
  var cr5=$('#txtcosto1Cr5').val();
  var cr6=$('#txtcosto1Cr6').val();
  var cr7=$('#txtcosto1Cr7').val();
  var totCosto=costoMercaTotal(cr1,cr2,cr3,cr4,cr5,cr6,cr7);
  $('#txtcosto1Prom1').val(totCosto);
  var totalCosto=$('#txtcosto1Prom1').val();
  //------------------otros INGRESOS
  var otroingreso1=$('#txtingreso1').val();
  var otroingreso2=$('#txtingreso2').val();
  var otroingreso3=$('#txtingreso3').val();
  var totalOtro=otroIngreso(otroingreso1,otroingreso2,otroingreso3);
  $('#txttotalOtroIngreso').val(totalOtro);
  var totalOtroIngreso=$('#txttotalOtroIngreso').val();
  ///------------resumen de la primera ACTIVIDAD


  $('#txtv1').val(ventaDiaria);
  var eva1VE=$('#txtv1').val();
  $('#txto1').val(totalOtro);
  var eva1OI=$('#txto1').val();

  //tot
  var eva1TOTAL=(vacio(eva1VE)+vacio(eva1OI)).toFixed(2);
  $('#txtti1').val(eva1TOTAL);
  var eva1TOTI=$('#txtti1').val();

  //costo mercado producto
  var costoMercado=cosMerProd(eva1TOTI,totalCosto);
  $('#txtcmp1').val(costoMercado);
  var eva1CM=$('#txtcmp1').val();

  //utilidad bruta
  var utiBruta=utilidadBruta(eva1TOTI,eva1CM);
  $('#txtub1').val(utiBruta);
  var eva1UB=$('#txtub1').val();

  //cobertura - otros costos
  //----tipo vivienda
  var vivienda=$('#tipoVivienda').val();
  var cobe=cobOtrosCostos(eva1UB,vivienda);
  $('#txtcoc1').val(cobe);
  var eva1OC=$('#txtcoc1').val();

  //excedente
  var exede=excedente(eva1UB,eva1OC);
  $('#txtec1').val(exede);
  var eva1EC=$('#txtec1').val();
}
//----------------------//
//----evaluacion 2
//----------------------//

function eval2()
{
  //---------- INVENTARIO
  var inv1=$('#txtinve1a2').val();
  var inv1m=$('#txtinve1a12').val();

  var inv2=$('#txtinve2a21').val();
  var inv2m=$('#txtinve2a22').val();

  var inv3=$('#txtinve3a2').val();
  var inv3m=$('#txtinve3a32').val();

  var inv4=$('#txtinve4a2').val();
  var inv4m=$('#txtinve4a42').val();

  var ope=sumaInve(inv1m,inv2m,inv3m,inv4m);
  $('#txttotalInventarioa2').val(ope);
  var invtotal=$('#txttotalInventarioa2').val();

  $('#txttotinven2').val(ope);

//CUENTAS
var disponible=$('#txtdiponible2').val();
var porCobrar=$('#txtporcobrar2').val();
var totInventario=$('#txttotinven2').val();
var toCorto=corto(disponible,porCobrar,totInventario);
$('#txttotalCorto2').val(toCorto);
var totCorto=$('#txttotalCorto2').val();
  //----------------VENTAS

  var bueno=$('#txtbueno2').val();
  var malo=$('#txtmalo2').val();
  //tottal
  var vtota=ventaDia(bueno,malo);
  $('#txtestimacion2').val(vtota);
  var estimacion=$('#txtestimacion2').val();

  $('#txtventaDiaria2').val(vtota);
  var ventaDiaria=$('#txtventaDiaria2').val();

  //--------------costo de mercado

  var prod1=$('#txtcosto1a12').val();
  var preCompra1=$('#txtcosto1Precio12').val();
  var preVenta1=$('#txtcosto1Venta12').val();
  //tot
  var topro1=costoMerca(preCompra1,preVenta1);
  $('#txtcosto1Cr12').val(topro1);

  //--------------//
  var prod2=$('#txtcosto1a22').val();
  var preCompra2=$('#txtcosto1Precio22').val();
  var preVenta2=$('#txtcosto1Venta22').val();
  //tot
  var topro2=costoMerca(preCompra2,preVenta2);
  $('#txtcosto1Cr22').val(topro2);

  //--------------//
  var prod3=$('#txtcosto1a32').val();
  var preCompra3=$('#txtcosto1Precio32').val();
  var preVenta3=$('#txtcosto1Venta32').val();
  //tot
  var topro3=costoMerca(preCompra3,preVenta3);
  $('#txtcosto1Cr32').val(topro3);

  //--------------//
  var prod4=$('#txtcosto1a42').val();
  var preCompra4=$('#txtcosto1Precio42').val();
  var preVenta4=$('#txtcosto1Venta42').val();
  //tot
  var topro4=costoMerca(preCompra4,preVenta4);
  $('#txtcosto1Cr42').val(topro4);

  //--------------//
  var prod5=$('#txtcosto1a52').val();
  var preCompra5=$('#txtcosto1Precio52').val();
  var preVenta5=$('#txtcosto1Venta52').val();
  //tot
  var topro5=costoMerca(preCompra5,preVenta5);
  $('#txtcosto1Cr52').val(topro5);

  //--------------//
  var prod6=$('#txtcosto1a62').val();
  var preCompra6=$('#txtcosto1Precio62').val();
  var preVenta6=$('#txtcosto1Venta62').val();
  //tot
  var topro6=costoMerca(preCompra6,preVenta6);
  $('#txtcosto1Cr62').val(topro6);

  //--------------//
  var prod7=$('#txtcosto1a72').val();
  var preCompra7=$('#txtcosto1Precio72').val();
  var preVenta7=$('#txtcosto1Venta72').val();
  //tot
  var topro7=costoMerca(preCompra7,preVenta7);
  $('#txtcosto1Cr72').val(topro7);


  //total
  var cr1=$('#txtcosto1Cr12').val();
  var cr2=$('#txtcosto1Cr22').val();
  var cr3=$('#txtcosto1Cr32').val();
  var cr4=$('#txtcosto1Cr42').val();
  var cr5=$('#txtcosto1Cr52').val();
  var cr6=$('#txtcosto1Cr62').val();
  var cr7=$('#txtcosto1Cr72').val();
  var totCosto=costoMercaTotal(cr1,cr2,cr3,cr4,cr5,cr6,cr7);
  $('#txtcosto1Prom12').val(totCosto);
  var totalCosto=$('#txtcosto1Prom12').val();

  //------------------otros INGRESOS
  var otroingreso1=$('#txtingreso12').val();
  var otroingreso2=$('#txtingreso22').val();
  var otroingreso3=$('#txtingreso32').val();
  var totalOtro=otroIngreso(otroingreso1,otroingreso2,otroingreso3);
  $('#txttotalOtroIngreso2').val(totalOtro);
  var totalOtroIngreso=$('#txttotalOtroIngreso2').val();
  ///------------resumen de la primera ACTIVIDAD


  $('#txtv2').val(ventaDiaria);
  var eva1VE=$('#txtv2').val();
  $('#txto2').val(totalOtro);
  var eva1OI=$('#txto2').val();

  //tot
  var eva1TOTAL=(vacio(eva1VE)+vacio(eva1OI)).toFixed(2);
  $('#txtti2').val(eva1TOTAL);
  var eva1TOTI=$('#txtti2').val();

  //costo mercado producto
  var costoMercado=cosMerProd(eva1TOTI,totalCosto);
  $('#txtcmp2').val(costoMercado);
  var eva1CM=$('#txtcmp2').val();

  //utilidad bruta
  var utiBruta=utilidadBruta(eva1TOTI,eva1CM);
  $('#txtub2').val(utiBruta);
  var eva1UB=$('#txtub2').val();

  //cobertura - otros costos
  //----tipo vivienda
  var vivienda=$('#tipoVivienda').val();
  var cobe=cobOtrosCostos(eva1UB,vivienda);
  $('#txtcoc2').val(cobe);
  var eva1OC=$('#txtcoc2').val();

  //excedente
  var exede=excedente(eva1UB,eva1OC);
  $('#txtec2').val(exede);
  var eva1EC=$('#txtec2').val();
}
//----------------------//
//----evaluacion 3
//----------------------//

function eval3()
{
  //---------- INVENTARIO
  var inv1=$('#txtinve1a3').val();
  var inv1m=$('#txtinve1a13').val();

  var inv2=$('#txtinve2a3').val();
  var inv2m=$('#txtinve2a23').val();

  var inv3=$('#txtinve3a31').val();
  var inv3m=$('#txtinve3a33').val();

  var inv4=$('#txtinve4a3').val();
  var inv4m=$('#txtinve4a43').val();

  var ope=sumaInve(inv1m,inv2m,inv3m,inv4m);
  $('#txttotalInventarioa3').val(ope);
  var invtotal=$('#txttotalInventarioa3').val();

  $('#txttotinven3').val(ope);

//CUENTAS
var disponible=$('#txtdiponible3').val();
var porCobrar=$('#txtporcobrar3').val();
var totInventario=$('#txttotinven3').val();
var toCorto=corto(disponible,porCobrar,totInventario);
$('#txttotalCorto3').val(toCorto);
var totCorto=$('#txttotalCorto3').val();
  //----------------VENTAS

  var bueno=$('#txtbueno3').val();
  var malo=$('#txtmalo3').val();
  //tottal
  var vtota=ventaDia(bueno,malo);
  $('#txtestimacion3').val(vtota);
  var estimacion=$('#txtestimacion3').val();

  $('#txtventaDiaria3').val(vtota);
  var ventaDiaria=$('#txtventaDiaria3').val();

  //--------------costo de mercado

  var prod1=$('#txtcosto1a13').val();
  var preCompra1=$('#txtcosto1Precio13').val();
  var preVenta1=$('#txtcosto1Venta13').val();
  //tot
  var topro1=costoMerca(preCompra1,preVenta1);
  $('#txtcosto1Cr13').val(topro1);

  //--------------//
  var prod2=$('#txtcosto1a23').val();
  var preCompra2=$('#txtcosto1Precio23').val();
  var preVenta2=$('#txtcosto1Venta23').val();
  //tot
  var topro2=costoMerca(preCompra2,preVenta2);
  $('#txtcosto1Cr23').val(topro2);

  //--------------//
  var prod3=$('#txtcosto1a33').val();
  var preCompra3=$('#txtcosto1Precio33').val();
  var preVenta3=$('#txtcosto1Venta33').val();
  //tot
  var topro3=costoMerca(preCompra3,preVenta3);
  $('#txtcosto1Cr33').val(topro3);

  //--------------//
  var prod4=$('#txtcosto1a43').val();
  var preCompra4=$('#txtcosto1Precio43').val();
  var preVenta4=$('#txtcosto1Venta43').val();
  //tot
  var topro4=costoMerca(preCompra4,preVenta4);
  $('#txtcosto1Cr43').val(topro4);

  //--------------//
  var prod5=$('#txtcosto1a53').val();
  var preCompra5=$('#txtcosto1Precio53').val();
  var preVenta5=$('#txtcosto1Venta53').val();
  //tot
  var topro5=costoMerca(preCompra5,preVenta5);
  $('#txtcosto1Cr53').val(topro5);

      //--------------//
    var prod6=$('#txtcosto1a63').val();
    var preCompra6=$('#txtcosto1Precio63').val();
    var preVenta6=$('#txtcosto1Venta63').val();
    //tot
    var topro6=costoMerca(preCompra6,preVenta6);
    $('#txtcosto1Cr63').val(topro6);

    //--------------//
      var prod7=$('#txtcosto1a73').val();
      var preCompra7=$('#txtcosto1Precio73').val();
      var preVenta7=$('#txtcosto1Venta73').val();
      //tot
      var topro7=costoMerca(preCompra7,preVenta7);
      $('#txtcosto1Cr73').val(topro7);

  //total
  var cr1=$('#txtcosto1Cr13').val();
  var cr2=$('#txtcosto1Cr23').val();
  var cr3=$('#txtcosto1Cr33').val();
  var cr4=$('#txtcosto1Cr43').val();
  var cr5=$('#txtcosto1Cr53').val();
  var cr6=$('#txtcosto1Cr63').val();
  var cr7=$('#txtcosto1Cr73').val();
  var totCosto=costoMercaTotal(cr1,cr2,cr3,cr4,cr5,cr6,cr7);
  $('#txtcosto1Prom13').val(totCosto);
  var totalCosto=$('#txtcosto1Prom13').val();

  //------------------otros INGRESOS
  var otroingreso1=$('#txtingreso13').val();
  var otroingreso2=$('#txtingreso23').val();
  var otroingreso3=$('#txtingreso33').val();
  var totalOtro=otroIngreso(otroingreso1,otroingreso2,otroingreso3);
  $('#txttotalOtroIngreso3').val(totalOtro);
  var totalOtroIngreso=$('#txttotalOtroIngreso3').val();
  ///------------resumen de la primera ACTIVIDAD


  $('#txtv3').val(ventaDiaria);
  var eva1VE=$('#txtv3').val();
  $('#txto3').val(totalOtro);
  var eva1OI=$('#txto3').val();

  //tot
  var eva1TOTAL=(vacio(eva1VE)+vacio(eva1OI)).toFixed(2);
  $('#txtti3').val(eva1TOTAL);
  var eva1TOTI=$('#txtti3').val();

  //costo mercado producto
  var costoMercado=cosMerProd(eva1TOTI,totalCosto);
  $('#txtcmp3').val(costoMercado);
  var eva1CM=$('#txtcmp3').val();

  //utilidad bruta
  var utiBruta=utilidadBruta(eva1TOTI,eva1CM);
  $('#txtub3').val(utiBruta);
  var eva1UB=$('#txtub3').val();

  //cobertura - otros costos
  //----tipo vivienda
  var vivienda=$('#tipoVivienda').val();
  var cobe=cobOtrosCostos(eva1UB,vivienda);
  $('#txtcoc3').val(cobe);
  var eva1OC=$('#txtcoc3').val();

  //excedente
  var exede=excedente(eva1UB,eva1OC);
  $('#txtec3').val(exede);
  var eva1EC=$('#txtec3').val();
}
function imprime()
{
  impri("juve");
}
function vali(dato)
{
  if(dato!='')
  {
    dato=parseFloat(dato).toFixed(2);
  }
  return dato;
}
function impri(dato)
{
  var valor="";
  valor+="d1="+$('#fecha1').val();
  valor+="& d2="+$('#fecha2').val();
  valor+="& d3="+$('#fecha3').val();

  valor+="& d4="+vali($('#txtdiponible1').val());
  valor+="& d5="+vali($('#txtdiponible2').val());
  valor+="& d6="+vali($('#txtdiponible3').val());

  valor+="& d7="+vali($('#txtporcobrar1').val());
  valor+="& d8="+vali($('#txtporcobrar2').val());
  valor+="& d9="+vali($('#txtporcobrar3').val());

  valor+="& d10="+vali($('#txttotinven1').val());
  valor+="& d11="+vali($('#txttotinven2').val());
  valor+="& d12="+vali($('#txttotinven3').val());

  valor+="& d13="+vali($('#txttotalCorto1').val());
  valor+="& d14="+vali($('#txttotalCorto2').val());
  valor+="& d15="+vali($('#txttotalCorto3').val());

  valor+="& d16="+$('#txtinve1a').val()+" | "+$('#txtinve1a2').val()+" | "+$('#txtinve1a3').val();
  valor+="& d17="+vali($('#txtinve1a1').val());
  valor+="& d18="+vali($('#txtinve1a12').val());
  valor+="& d19="+vali($('#txtinve1a13').val());

  valor+="& d20="+$('#txtinve2a').val()+" | "+$('#txtinve2a21').val()+" | "+$('#txtinve2a3').val();
  valor+="& d21="+vali($('#txtinve2a2').val());
  valor+="& d22="+vali($('#txtinve2a22').val());
  valor+="& d23="+vali($('#txtinve2a23').val());

  valor+="& d24="+$('#txtinve3a').val()+" | "+$('#txtinve3a2').val()+" | "+$('#txtinve3a31').val();
  valor+="& d25="+vali($('#txtinve3a3').val());
  valor+="& d26="+vali($('#txtinve3a32').val());
  valor+="& d27="+vali($('#txtinve3a33').val());

  valor+="& d28="+$('#txtinve4a').val()+" | "+$('#txtinve4a2').val()+" | "+$('#txtinve4a3').val();
  valor+="& d29="+vali($('#txtinve4a4').val());
  valor+="& d30="+vali($('#txtinve4a42').val());
  valor+="& d31="+vali($('#txtinve4a43').val());

  valor+="& d32="+vali($('#txttotalInventarioa1').val());
  valor+="& d33="+vali($('#txttotalInventarioa2').val());
  valor+="& d34="+vali($('#txttotalInventarioa3').val());

  valor+="& d35="+vali($('#txtventaDiaria1').val());
  valor+="& d36="+vali($('#txtventaDiaria2').val());
  valor+="& d37="+vali($('#txtventaDiaria3').val());

  valor+="& d38="+vali($('#txtbueno1').val());
  valor+="& d39="+vali($('#txtbueno2').val());
  valor+="& d40="+vali($('#txtbueno3').val());

  valor+="& d41="+vali($('#txtmalo1').val());
  valor+="& d42="+vali($('#txtmalo2').val());
  valor+="& d43="+vali($('#txtmalo3').val());

  valor+="& d44="+vali($('#txtestimacion1').val());
  valor+="& d45="+vali($('#txtestimacion2').val());
  valor+="& d46="+vali($('#txtestimacion3').val());

  valor+="& d47="+($('#txtcosto1a1').val());
  valor+="& d48="+vali($('#txtcosto1Precio1').val());
  valor+="& d49="+vali($('#txtcosto1Venta1').val());
  valor+="& d50="+vali($('#txtcosto1Cr1').val());
  valor+="& d51="+vali($('#txtcosto1Prom1').val());

  valor+="& d52="+($('#txtcosto1a2').val());
  valor+="& d53="+vali($('#txtcosto1Precio2').val());
  valor+="& d54="+vali($('#txtcosto1Venta2').val());
  valor+="& d55="+vali($('#txtcosto1Cr2').val());

  valor+="& d56="+($('#txtcosto1a3').val());
  valor+="& d57="+vali($('#txtcosto1Precio3').val());
  valor+="& d58="+vali($('#txtcosto1Venta3').val());
  valor+="& d59="+vali($('#txtcosto1Cr3').val());

  valor+="& d60="+($('#txtcosto1a4').val());
  valor+="& d61="+vali($('#txtcosto1Precio4').val());
  valor+="& d62="+vali($('#txtcosto1Venta4').val());
  valor+="& d63="+vali($('#txtcosto1Cr4').val());

  valor+="& d64="+($('#txtcosto1a12').val());
  valor+="& d65="+vali($('#txtcosto1Precio12').val());

  valor+="& d66="+vali($('#txtcosto1Venta12').val());
  valor+="& d67="+vali($('#txtcosto1Cr12').val());
  valor+="& d68="+vali($('#txtcosto1Prom12').val());

  valor+="& d69="+($('#txtcosto1a22').val());
  valor+="& d70="+vali($('#txtcosto1Precio22').val());
  valor+="& d71="+vali($('#txtcosto1Venta22').val());
  valor+="& d72="+vali($('#txtcosto1Cr22').val());

  valor+="& d73="+($('#txtcosto1a32').val());
  valor+="& d74="+vali($('#txtcosto1Precio32').val());
  valor+="& d75="+vali($('#txtcosto1Venta32').val());
  valor+="& d76="+vali($('#txtcosto1Cr32').val());

  valor+="& d77="+($('#txtcosto1a42').val());
  valor+="& d78="+vali($('#txtcosto1Precio42').val());
  valor+="& d79="+vali($('#txtcosto1Venta42').val());
  valor+="& d80="+vali($('#txtcosto1Cr42').val());

//--
  valor+="& d81="+($('#txtcosto1a13').val());
  valor+="& d82="+vali($('#txtcosto1Precio13').val());
  valor+="& d83="+vali($('#txtcosto1Venta13').val());
  valor+="& d84="+vali($('#txtcosto1Cr13').val());
  valor+="& d85="+vali($('#txtcosto1Prom13').val());

  valor+="& d86="+($('#txtcosto1a23').val());
  valor+="& d87="+vali($('#txtcosto1Precio23').val());
  valor+="& d88="+vali($('#txtcosto1Venta23').val());
  valor+="& d89="+vali($('#txtcosto1Cr23').val());

  valor+="& d90="+($('#txtcosto1a33').val());
  valor+="& d91="+vali($('#txtcosto1Precio33').val());
  valor+="& d92="+vali($('#txtcosto1Venta33').val());
  valor+="& d93="+vali($('#txtcosto1Cr33').val());

  valor+="& d94="+($('#txtcosto1a43').val());
  valor+="& d95="+vali($('#txtcosto1Precio43').val());
  valor+="& d96="+vali($('#txtcosto1Venta43').val());
  valor+="& d97="+vali($('#txtcosto1Cr43').val());


  valor+="& d98="+vali($('#txttotalOtroIngreso').val());
  valor+="& d99="+$('#txtingresoD1').val()+", "+$('#txtingresoD2').val()+", "+$('#txtingresoD3').val();
  valor+="& d100="+vali($('#txttotalOtroIngreso2').val());
  valor+="& d101="+$('#txtingresoD12').val()+", "+$('#txtingresoD22').val()+", "+$('#txtingresoD32').val();
  valor+="& d102="+vali($('#txttotalOtroIngreso3').val());
  valor+="& d103="+$('#txtingresoD13').val()+", "+$('#txtingresoD23').val()+", "+$('#txtingresoD33').val();

  valor+="& d104="+$('#txtf1').val();
  valor+="& d105="+$('#txtf2').val();
  valor+="& d106="+$('#txtf3').val();

  valor+="& d107="+$('#txtv1').val();
  valor+="& d108="+$('#txtv2').val();
  valor+="& d109="+$('#txtv3').val();

  valor+="& d110="+$('#txto1').val();
  valor+="& d111="+$('#txto2').val();
  valor+="& d112="+$('#txto3').val();

  valor+="& d113="+$('#txtti1').val();
  valor+="& d114="+$('#txtti2').val();
  valor+="& d115="+$('#txtti3').val();

  valor+="& d116="+$('#txtcmp1').val();
  valor+="& d117="+$('#txtcmp2').val();
  valor+="& d118="+$('#txtcmp3').val();

  valor+="& d119="+$('#txtub1').val();
  valor+="& d120="+$('#txtub2').val();
  valor+="& d121="+$('#txtub3').val();

  valor+="& d122="+$('#txtcoc1').val();
  valor+="& d123="+$('#txtcoc2').val();
  valor+="& d124="+$('#txtcoc3').val();

  valor+="& d125="+$('#txtec1').val();
  valor+="& d126="+$('#txtec2').val();
  valor+="& d127="+$('#txtec3').val();


  window.open("evalPrint.php?"+valor,"JUVE","width=200","height=200");
}
