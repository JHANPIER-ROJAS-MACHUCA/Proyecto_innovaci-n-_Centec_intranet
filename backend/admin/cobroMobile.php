<?php

require_once '../vendor/autoload.php';
require_once '../src/Domain/Database/bootstrap.php';

use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Models\Transaction;
use CrediSoporte\Domain\Request\Request;

$request = new Request();

// solo ingresan usuario logeados
if (!$request->user()) {
    header('Location: ./loginMobile.php');
}

// cash
$cash = $database::table('tcaja_usuario')
    ->join('tcaja_oficina', 'tcaja_usuario.idCO', 'tcaja_oficina.idCO')
    ->where('tcaja_usuario.idU', $request->user()->idU)
    ->where('tcaja_oficina.idO', $request->user()->idO)
    ->whereNull('tcaja_usuario.montofin')
    ->whereNull('tcaja_oficina.montof')
    ->first();

// la caja de usuarios normales de verifica de otra manera
if ($cash && $cash->tipo != 1 && !$cash->montoIni && !$cash->hini) {
    $cash = null;
}

$totalCash = 0;
$totalDigital = 0;

if ($cash) {
    if ($cash->tipo == 1) { // caja administracion tiene otros ingresos y egresos
        $otrasCajas = $database->table('tcaja_usuario')
            ->where('idCO', $cash->idCO)
            ->where('idCA', '!=', $cash->idCA)
            ->sum('montoFin');

        $designacionTransactions = Transaction::join('tcaja_usuario', 'tcaja_usu_detal.idCA', 'tcaja_usuario.idCA')
            ->where('tcaja_usuario.idCO', $cash->idCO)
            ->where('tcaja_usu_detal.tipo', '1')
            ->where('tcaja_usu_detal.habilitacion', '4')
            ->sum('tcaja_usu_detal.monto');

        $totalCash += $cash->monto + $otrasCajas - $designacionTransactions;
    }

    $asignacionTransactions = Transaction::where('idCA', $cash->idCA)
        ->where('tipo', 1)
        ->where('estadodt', 2)
        ->where('habilitacion', 4)
        ->sum('monto');

    $cobrosTransactions = Transaction::leftJoin('transaction_details', 'tcaja_usu_detal.idCAD', 'transaction_details.transaction_id')
        ->where('tcaja_usu_detal.idCA', $cash->idCA)
        ->where('tcaja_usu_detal.estadodt', 2)
        ->where('tcaja_usu_detal.tipo', 3)
        ->selectRaw('sum(if(transaction_details.id, tcaja_usu_detal.total,0)) as digital')
        ->selectRaw('sum(if(transaction_details.id, 0,tcaja_usu_detal.total)) as cash')
        ->first();

    $ahorroTransactions = Transaction::join('tahorro_deta', 'tcaja_usu_detal.idCuota', 'tahorro_deta.idAd')
        ->where('tcaja_usu_detal.idCA', $cash->idCA)
        ->where('tcaja_usu_detal.estadodt', 2)
        ->selectRaw('sum(if(tahorro_deta.tipo = 7, tcaja_usu_detal.total, 0)) as income')
        ->selectRaw('sum(if(tahorro_deta.tipo = 8, tcaja_usu_detal.total, 0)) as expense')
        ->first();

    $incomeAndExpense = Transaction::join('tahorro_motivo', 'tcaja_usu_detal.tipo', 'tahorro_motivo.idam')
        ->where('tcaja_usu_detal.idCA', $cash->idCA)
        ->where('tcaja_usu_detal.estadodt', 2)
        ->whereNotIn('tcaja_usu_detal.tipo', [1, 2, 3])
        ->whereNull('tcaja_usu_detal.idCuota')
        ->selectRaw('sum(if(tahorro_motivo.tipoM = 1, tcaja_usu_detal.total, 0)) as income')
        ->selectRaw('sum(if(tahorro_motivo.tipoM = 2, tcaja_usu_detal.total, 0)) as expense')
        ->first();

    $desembolsoTransactions = Transaction::where('tcaja_usu_detal.idCA', $cash->idCA)
        ->where('tcaja_usu_detal.estadodt', 2)
        ->where('tcaja_usu_detal.tipo', 2)
        ->sum('total');

    $totalCash += $asignacionTransactions +
        $cobrosTransactions->cash +
        $ahorroTransactions->income -
        $ahorroTransactions->expense +
        $incomeAndExpense->income -
        $incomeAndExpense->expense -
        $desembolsoTransactions;

    $totalDigital = $cobrosTransactions->digital;
}
// fin cash

$resumenAvance = Credit::join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
    ->join('tpresta_detalle', 'tprestamo.idP', 'tpresta_detalle.idP')
    ->where('tclie_general.idU', $request->user()->idU)
    ->where('tprestamo.estado', 4)
    ->where('tpresta_detalle.expiration_at', date('Y-m-d'))
    ->selectRaw('sum(tpresta_detalle.capital) as capital')
    ->selectRaw('sum(tpresta_detalle.interest) as interest')
    ->selectRaw('sum(tpresta_detalle.capital_payment + tpresta_detalle.interest_payment) as total_pagado')
    ->first();

$resumenAvance->total_pagado = floatval($resumenAvance->total_pagado);

$justifyTypes = $database->table('justify_types')->orderBy('number')->get();

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cobrar</title>
    <script src="./../public/resource/js/tailwind.js"></script>
</head>

<body>
    <div x-data="app">

        <div class="max-w-xl mx-auto h-screen overflow-y-auto" x-data="comment">
            <div class="flex flex-col max-w-xl mx-auto h-screen overflow-y-auto">


                <!-- <div>
                    <button @click="resumenAvance.total_pagado += 40">Aumentar</button>
                    <button @click="resumenAvance.total_pagado -= 40">Disminuir</button>
                </div> -->

                <!-- <div>
                    <div x-text="avanceEnPorcentaje()"></div>
                </div> -->
                <!-- <div x-text="JSON.stringify(resumenAvance)"></div> -->

                <div class="flex justify-between px-4 text-sm bg-yellow-200 py-1">
                    <h6 class="text-yellow-800">Hola, <?php echo ucfirst(mb_strtolower($request->user()->nomU)) ?></h6>
                    <div class="flex divide-x divide-yellow-700">
                        <span class="pr-2 text-yellow-800">E. S/ <span x-text="numberFormat(cash.cash)"></span></span>
                        <span class="pl-2 text-yellow-800">D. S/ <span x-text="numberFormat(cash.digital)"></span></span>
                    </div>
                </div>

                <div class="px-4 mt-3">
                    <div class="flex justify-between items-center">
                        <div class="text-sm text-gray-900 font-medium" x-text="messageAvance"></div>
                        <div class="text-xs text-gray-700" x-text="avanceEnPorcentaje() + ' %'"></div>
                    </div>
                    <div class="rounded-full w-full h-4 mt-1 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-[width] duration-500 ease-out" :class="classColorAvance()" :style="{width: avanceEnPorcentaje() + '%'}"></div>
                    </div>
                </div>

                <div class="px-4 mt-4">
                    <input type="search" class="w-full border-transparent rounded-xl bg-gray-200 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Buscar por nombre o dni" x-model.debounce.300="search">
                </div>

                <div x-show="tab === 'hoy' && search" class="mt-4">
                    <h4 x-show="credits.length > 0" class="px-4">Buscado <b>"<span x-text="search"></span>"</b></h4>
                    <h4 x-show="credits.length === 0" class="px-4">No encontramos resultados para <b>"<span x-text="search"></span>"</b></h4>
                </div>

                <div x-show="tab === 'hoy' && !search" class="mt-4">
                    <h4 class="px-4 text-sm text-gray-600">Todo los cobros hoy</h4>
                    <p x-show="credits.length === 0" class="text-sm text-center mt-4 text-gray-500">No hay nada que mostrar</p>
                </div>

                <div x-show="tab === 'semanal' && !search" class="mt-4">
                    <h4 class="px-4 text-sm text-gray-600">Creditos semanales de hoy</h4>
                    <p x-show="credits.length === 0" class="text-sm text-center mt-4 text-gray-500">No hay nada que mostrar</p>
                </div>

                <div x-show="tab === 'retrasado'" class="mt-4">
                    <h4 class="px-4 text-sm text-gray-600">Creditos vigentes atrasados</h4>
                    <p x-show="credits.length === 0" class="text-sm text-center mt-4 text-gray-500">No hay nada que mostrar</p>
                </div>

                <div class="mt-4 flex-auto overflow-y-auto">
                    <ul>
                        <template x-for="(item, key) in creditsFiltered" :key="item.idP">
                            <li class="hover:bg-gray-50 rounded-md" :class="creditSelected === item.idP ? 'bg-gray-50' : ''" @click="creditSelected = creditSelected === item.idP ? '' : item.idP">
                                <div class="mx-4 py-4" :class="credits.length == key + 1 ? '' : 'border-b'">
                                    <div class="flex space-x-4">
                                        <div class="flex-auto">
                                            <div class="font-medium text-sm text-gray-700" x-text="item.customer"></div>
                                            <div class="text-gray-500 text-sm mt-0.5">
                                                <span>Cuenta:</span>
                                                <span x-text="item.idP"></span>
                                                <span>·</span>
                                                <span>Prestamo:</span>
                                                <span x-text="numberFormat(item.capital)"></span>
                                                <span x-show="item.transactions.length > 0">·</span>
                                                <span x-show="item.transactions.length > 0" class="text-blue-500">Cobrado</span>
                                            </div>
                                            <div x-show="item.comment">
                                                <div class="text-sm text-green-500 font-medium" x-text="item.comment?.category"></div>
                                                <div class="text-sm text-green-500" x-text="item.comment?.description"></div>
                                            </div>
                                        </div>
                                        <div class="flex-none flex items-start space-x-2">
                                            <button class="h-10 px-4 border border-gray-3000 rounded-md bg-white hover:bg-gray-200 focus:ring" @click="handleSelectCredit(item.idP)">
                                                <span class="text-sm text-gray-700 font-medium">Cobrar</span>
                                            </button>
                                        </div>
                                    </div>
                                    <div x-show="creditSelected === item.idP">
                                        <div class="flex space-x-2 mt-4">
                                            <button class="px-3 py-2 border rounded-md bg-white" @click="openModalComment({customerName: item.customer, creditId: item.idP})">
                                                <span class="text-sm font-semibold">Comentar</span>
                                            </button>
                                            <a :href="'./ubicacionCliente.php?customerId=' + item.idCG" target="_blank" class="flex items-center space-x-1 px-3 py-2 bg-white border border-gray-300 rounded-md">
                                                <span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo-alt" viewBox="0 0 16 16">
                                                        <path d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A31.493 31.493 0 0 1 8 14.58a31.481 31.481 0 0 1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3 6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94zM8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10z" />
                                                        <path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm0 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
                                                    </svg>
                                                </span>
                                            </a>
                                            <div x-show="item.cell_phone">
                                                <a :href="'tel:+51' + item.cell_phone" class="inline-flex items-center justify-center border rounded-md hover:bg-gray-200 font-semibold focus:ring px-3 py-2 text-gray-700 bg-white">
                                                    <span class="mr-2">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-telephone" viewBox="0 0 16 16">
                                                            <path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.568 17.568 0 0 0 4.168 6.608 17.569 17.569 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.678.678 0 0 0-.58-.122l-2.19.547a1.745 1.745 0 0 1-1.657-.459L5.482 8.062a1.745 1.745 0 0 1-.46-1.657l.548-2.19a.678.678 0 0 0-.122-.58L3.654 1.328zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511z" />
                                                        </svg>
                                                    </span>
                                                    <span x-text="item.cell_phone"></span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </template>
                    </ul>
                </div>

                <div class="flex shadow">
                    <template x-for="item in [{tab: 'Hoy', value: 'hoy'}, {tab: 'Semanales', value: 'semanal'}, {tab: 'Atrasados', value: 'retrasado'}]">
                        <div class="flex-1" :class="tab === item.value ? 'border-t-2 border-blue-500 text-blue-500 font-semibold' : 'text-gray-500'">
                            <button class="px-3 py-5 w-full text-sm" @click="changeTab(item.value)">
                                <span x-text="item.tab"></span>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <div x-show="loadingSelectCredit" x-cloak>
                <div class="fixed inset-0 bg-white">
                    <div class="text-center text-sm text-gray-500 mt-6">Cargando...</div>
                </div>
            </div>
            <template x-if="credit">
                <div class="fixed inset-0" @keyup.escape.window="itemSelected = '', credit = null">
                    <button class="absolute top-1 left-1 w-10 h-10 flex justify-center items-center" @click="itemSelected = '', credit = null, bodyScroll(true)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" viewBox="0 0 320 512">
                            <path d="M310.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L160 210.7 54.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L114.7 256 9.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L160 301.3 265.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L205.3 256 310.6 150.6z" />
                        </svg>
                    </button>

                    <div class="flex flex-col bg-white max-w-xl mx-auto h-full overflow-hidden">
                        <div class="border-b p-2">
                            <div class="text-center text-blue-600 text-sm font-medium" x-text="credit.customer"></div>
                            <div class="text-center text-gray-500 text-xs">Cliente</div>
                        </div>
                        <div class="flex space-x-2 divide-x border-b">
                            <div class="flex-1 p-2">
                                <div class="text-center text-blue-600 text-sm font-medium" x-text="credit.product"></div>
                                <div class="text-center text-gray-500 text-xs">Producto</div>
                            </div>
                            <div class="flex-1 p-2">
                                <div class="text-center text-blue-600 text-sm font-medium" x-text="credit.id"></div>
                                <div class="text-center text-gray-500 text-xs">Nro Crédito</div>
                            </div>
                        </div>
                        <div class="flex space-x-2 divide-x border-b">
                            <div class="flex-1 p-2">
                                <div class="text-center text-blue-600 text-sm font-medium" x-text="'S/. ' + numberFormat(credit.pendiente + credit.total_mora)"></div>
                                <div class="text-center text-gray-500 text-xs">Pendiente amortización</div>
                            </div>
                            <div class="flex-1 p-2">
                                <div class="text-center text-blue-600 text-sm font-medium" x-text="'S/. ' + numberFormat(credit.total_installment + credit.total_mora)"></div>
                                <div class="text-center text-gray-500 text-xs">Máximo importe a recaudar</div>
                            </div>
                        </div>

                        <div class="flex-auto overflow-auto space-y-3 px-3 pb-3">
                            <template x-for="(item, key) in credit.installments" :key="item.id">
                                <div class="py-2 text-sm border rounded-md" :class="itemSelected === key ? 'ring-2 border-blue-500' : ''">
                                    <div class="grid grid-cols-5" @click="itemSelected = itemSelected === key ? '' : key">
                                        <div class="col-span-2 flex items-center space-x-2 px-2">
                                            <div>
                                                <span x-show="parseFloat(item.pago) + parseFloat(item.pagoMora) > 0 && parseFloat(item.pago) + parseFloat(item.pagoMora) < parseFloat(item.slope) + parseFloat(item.mora)" class="inline-block w-4 h-4 rounded-full bg-orange-500"></span>
                                                <span x-show="parseFloat(item.slope) + parseFloat(item.mora) === parseFloat(item.pago) + parseFloat(item.pagoMora)" class="inline-block w-4 h-4 rounded-full bg-green-500"></span>
                                            </div>
                                            <div>
                                                <div>Cuota <span x-text="item.number"></span></div>
                                                <div x-text="formatDate(item.expiration_at)"></div>
                                            </div>
                                        </div>
                                        <div class="px-2">
                                            <div>Dias</div>
                                            <div x-text="item.days"></div>
                                        </div>
                                        <div class="px-2">
                                            <div>Monto</div>
                                            <div x-text="numberFormat(item.slope + item.mora)"></div>
                                        </div>
                                        <div class="px-2">
                                            <div x-show="key === 0 || parseFloat(credit?.installments[key - 1]?.pago) === parseFloat(credit?.installments[key - 1]?.slope)">
                                                <div>Pago</div>
                                                <div x-text="numberFormat(sum(parseFloat(item.pago), parseFloat(item.pagoMora)))"></div>
                                            </div>
                                            <div x-show="key !== 0 && parseFloat(credit?.installments[key - 1]?.pago) !== parseFloat(credit?.installments[key - 1]?.slope)">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 448 512">
                                                    <path d="M144 144v48H304V144c0-44.2-35.8-80-80-80s-80 35.8-80 80zM80 192V144C80 64.5 144.5 0 224 0s144 64.5 144 144v48h16c35.3 0 64 28.7 64 64V448c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V256c0-35.3 28.7-64 64-64H80z" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                    <div x-show="itemSelected === key">
                                        <template x-if="key === 0 || parseFloat(credit?.installments[key - 1]?.pago) === parseFloat(credit?.installments[key - 1]?.slope)">
                                            <div class="grid grid-cols-3 gap-2 items-center px-2 mt-4">
                                                <button x-show="item.mora === 0" class="flex space-x-2 flex-1 px-3 py-2 border border-gray-100 rounded-md text-gray-500">
                                                    <div>Mora</div>
                                                    <div x-text="numberFormat(item.mora)"></div>
                                                </button>
                                                <button x-show="item.mora > 0" class="flex space-x-2 flex-1 px-3 py-2 border rounded-md" @click="payMora(item.id)" :class="item.mora === item.pagoMora ? 'ring-2 ring-green-200 border-green-500 text-green-600' : 'border-gray-300'">
                                                    <div>Mora</div>
                                                    <div x-text="numberFormat(item.mora)"></div>
                                                </button>

                                                <button x-show="item.slope === 0" class="flex space-x-2 flex-1 px-3 py-2 border border-gray-100 rounded-md">
                                                    <div>Cuota</div>
                                                    <div x-text="numberFormat(item.slope)"></div>
                                                </button>
                                                <button x-show="item.slope > 0" class="flex space-x-2 flex-1 px-3 py-2 border rounded-md" @click="payInstallment(item.id)" :class="item.slope === item.pago ? 'ring-2 ring-green-200 border-green-500 text-green-600' : 'border-gray-300'">
                                                    <div>Cuota</div>
                                                    <div x-text="numberFormat(item.slope)"></div>
                                                </button>

                                                <div class="flex-1">
                                                    <input type="number" class="w-full border-gray-300 rounded-md px-3 py-2 border border-gray-300" :value="item.pago" @input="handleChangePago(item.id, event)" :disabled="item.slope === 0" @focus="$el.select()">
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <div x-show="credit.otras_moras > 0">
                                <div class="flex justify-between border rounded-md px-4 py-4 text-sm" @click="handleSelectOtrasMoras">
                                    <div class="flex items-center">
                                        <span class="inline-block w-4 h-4 bg-green-500 rounded-full mr-2" x-show="credit.pagoMora > 0"></span>
                                        <span>Mora por <span x-text="credit.days"></span> <span x-text="credit.days > 1 ? 'días' : 'día'"></span> de atraso</span>
                                    </div>
                                    <div x-text="numberFormat(credit.otras_moras)"></div>
                                    <div>
                                        <div x-show="pagoMoraInstallment() < credit.mora_installments">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 448 512">
                                                <path d="M144 144v48H304V144c0-44.2-35.8-80-80-80s-80 35.8-80 80zM80 192V144C80 64.5 144.5 0 224 0s144 64.5 144 144v48h16c35.3 0 64 28.7 64 64V448c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V256c0-35.3 28.7-64 64-64H80z" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="shadow border-t border-gray-100 bg-white pt-2">
                            <div class="flex justify-between px-4 text-sm">
                                <span>Subtotal</span>
                                <span x-text="numberFormat(total(),2)"></span>
                            </div>
                            <div class="flex justify-between px-4 text-sm">
                                <span>Mora</span>
                                <span x-text="numberFormat(totalMora(),2)"></span>
                            </div>
                            <div class="flex justify-between px-4 text-sm">
                                <span>Total</span>
                                <span x-text="numberFormat(total() + totalMora(),2)"></span>
                            </div>
                            <div class="max-w-xl mx-auto px-4 flex pb-5 pt-4 space-x-6">
                                <button class="flex-1 py-2 rounded-md bg-green-500 text-white font-semibold focus:ring focus:ring-green-200" :class="total() + totalMora() === 0.0 ? 'bg-opacity-50' : ''" :disabled="parseFloat(total() + totalMora()) === 0.0" @click="modalCobrar = true">
                                    Cobrar
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </template>

            <template x-if="modalCobrar">
                <div class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center p-4">
                    <div class="flex flex-col bg-white w-full max-w-sm rounded-md max-h-full">
                        <div class="flex-auto overflow-x-auto pt-10 pb-4">
                            <div class="px-10">
                                <label>Total a cobrar</label>
                                <div class="border border-blue-700 rounded-md text-lg text-center text-blue-700 py-2 px-3 mt-1 font-medium">
                                    S/ <span x-text="numberFormat(total() + totalMora())"></span>
                                </div>
                            </div>
                            <div class="space-y-4 mt-4 px-10">
                                <div>
                                    <label>Metodo de pago</label>
                                    <select class="w-full rounded-md border border-gray-300 mt-1 px-3 py-2" x-model="payment_method.type">
                                        <option value="efectivo" selected>Efectivo</option>
                                        <option value="yape">Yape</option>
                                        <option value="transferencia bancaria">Transferencia bancaria</option>
                                    </select>
                                </div>
                                <div x-show="payment_method.type != 'efectivo'">
                                    <label>Cuenta</label>
                                    <select class="w-full rounded-md border border-gray-300 mt-1 px-3 py-2" x-mode="payment_method.account">
                                        <option value="">Seleccione</option>
                                        <template x-for="account in getAccounts">
                                            <option :value="account.id" x-text="account.number + ' - ' + account.name"></option>
                                        </template>
                                    </select>
                                </div>
                                <div x-show="payment_method.type == 'transferencia bancaria'">
                                    <label>Referencia</label>
                                    <input type="text" class="w-full rounded-md border border-gray-300 mt-1 px-3 py-2" x-model="payment_method.reference">
                                </div>
                                <div x-show="payment_method.type == 'transferencia bancaria'">
                                    <label>Fecha</label>
                                    <input type="date" class="w-full rounded-md border border-gray-300 mt-1 px-3 py-2" x-model="payment_method.date">
                                </div>
                            </div>
                        </div>
                        <div class="text-right space-x-4 space-x-2 px-10 py-6">
                            <button class="rounded-md bg-gray-100 px-3 py-2" @click="modalCobrar = false">Salir</button>
                            <button class="rounded-md bg-green-500 px-3 py-2 text-white" @click="finalizarCobro" x-text="sendingPayment ? 'Cargando...' : 'Finalizar cobro'">Finalizar cobro</button>
                        </div>
                    </div>
                </div>
            </template>

            <template x-if="success">
                <div class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center p-4">
                    <div class="bg-white w-full max-w-sm text-center p-10 rounded-md">
                        <img src="./../public/resource/sol.png" class="mx-auto w-36" alt="">
                        <h4 class="text-3xl text-gray-800 text-center mt-4 font-medium">Cobro exitoso</h4>

                        <div class="mt-2">
                            <p class="text-gray-500">N° de operación: <span class="font-medium" x-text="success.operation"></span></p>
                            <p class="text-gray-500">Total: <span class="font-medium">S/</span> <span class="font-medium" x-text="numberFormat(success.total)"></span></p>
                        </div>

                        <div class="mt-6">
                            <p class="text-sm text-gray-500">Enviar a whatsapp</p>
                            <div class="mt-1">
                                <input type="tel" class="px-3 py-2 border rounded-md w-32" x-model="success.cell_phone">
                                <button class="px-3 py-2 rounded-md bg-green-500 text-white font-semibold" @click="enviarComprobanteAWhatsapp()">
                                    <span x-show="sendingWhatsApp">Enviando...</span>
                                    <span x-show="!sendingWhatsApp">Enviar</span>
                                </button>
                            </div>
                        </div>

                        <div class="space-x-4 mt-6">
                            <a :href="'./../app/pdf/voucher58.php?operacion=' + success.operation" target="_blank" class="px-3 py-2 border rounded-md text-gray-700">Imprimir</a>
                            <button class="px-3 py-2 bg-green-500 text-white rounded-md" @click="success = null, bodyScroll(true)">Continuar cobrando</button>
                        </div>
                    </div>
                </div>
            </template>

            <template x-if="open">
                <form @submit.prevent="handleSubmit">
                    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center p-4">
                        <div class="bg-white w-full max-w-sm max-h-full rounded-lg overflow-y-auto flex flex-col">

                            <!-- Header -->
                            <div class="px-6 pt-6 pb-4">
                                <h4 class="text-xl text-gray-700">Comentar</h4>
                            </div>

                            <!-- Body -->
                            <div class="overflow-y-auto px-6">
                                <div>
                                    <div class="text-gray-700 font-semibold text-sm">Cliente</div>
                                    <div x-text="customer"></div>
                                </div>

                                <div class="mt-3">
                                    <label class="text-gray-700 text-sm font-semibold">Condición</label>
                                    <select class="px-3 py-2 border border-gray-300 rounded-md w-full" x-model="factory.category_id">
                                        <option value="">Seleccione</option>
                                        <?php foreach ($justifyTypes as $key => $justify) { ?>
                                            <option value="<?php echo $justify->id ?>"><?php echo $justify->description ?></option>
                                        <?php } ?>
                                    </select>
                                    <div class="text-red-500 mt-1" x-show="errors?.category_id" x-text="errors?.category_id"></div>
                                </div>
                                <div class="mt-3">
                                    <label class="text-gray-700 text-sm font-semibold">Comentar · opcional</label>
                                    <textarea class="w-full rounded-md mt-1 border border-gray-300 px-3 py-2" rows="5" x-model="factory.description"></textarea>
                                </div>
                                <div class="mt-3">
                                    <label class="text-gray-700 text-sm font-semibold">Coordenadas · opcional</label>
                                    <div x-data x-text="factory.coordinate_lat + ' ' + factory.coordinate_lng"></div>
                                    <div class="text-red-500" x-text="withErrorLocation ? 'Lo sentimos no se pudo optener tu ubicación' : ''"></div>
                                    <div x-text="loadingLocation ? 'Obteniendo tu ubicación...' : ''"></div>
                                </div>
                                <div class="mt-3">
                                    <!-- <div class="flex items-center justify-between">
                                        <label class="text-sm font-semibold">Foto</label>
                                        <button class="text-blue-500 text-sm hover:underline" type="button" @click="handleOpenModalCamera">Usar camara</button>
                                    </div> -->
                                    <div class="flex items-center justify-between">
                                        <label class="text-gray-700 text-sm font-semibold">Foto · opcional</label>
                                        <button x-show="factory.image" class="text-red-500 text-sm hover:underline" @click="factory.image = null" type="button">Eliminar</button>
                                    </div>
                                    <label class="flex items-center justify-center border aspect-square bg-gray-100">
                                        <input type="file" @change="handleChangeInputFileComment" class="hidden" :disabled="uploadingImage">
                                        <template x-if="factory.image">
                                            <img :src="factory.image.path + factory.image.name" class="max-w-full max-h-full" alt="foto del comentario">
                                        </template>
                                        <div x-show="!factory.image && !uploadingImage" class="text-gray-700 text-sm">Seleccione una imagen</div>
                                        <div x-show="uploadingImage" class="text-green-500 text-sm">Cargando imagen...</div>
                                    </label>
                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="flex justify-end mt-4 px-6 pb-6">
                                <!-- <div>
                                    <button type="button" class="px-3 py-2 bg-red-500 text-white rounded-md" @click="deleteComment">
                                        <span x-show="loadingDeleting">Eliminando...</span>
                                        <span x-show="!loadingDeleting">Eliminar</span>
                                    </button>
                                </div> -->
                                <div class="space-x-2">
                                    <button type="button" class="bg-gray-100 px-3 py-2 rounded-md" @click="closeModalComment">Cancelar</button>
                                    <button type="submit" class="bg-green-500 text-white px-3 py-2 rounded-md" :disabled="sendingDataToCreate || uploadingImage">
                                        <span x-text="sendingDataToCreate ? 'Guardando....' : 'Guardar'"></span>
                                    </button>
                                </div>
                            </div>

                        </div>
                        <!-- <div class="absolute inset-0 bg-gray-900">
                        
                        </div> -->
                    </div>
                </form>
            </template>

        </div>
    </div>



    <script>
        const paymentMethodDefault = {
            type: 'efectivo',
            date: <?php echo json_encode(date('Y-m-d')) ?>,
            account: '',
            reference: ''
        }

        const commentDefault = {
            id: '',
            credit_id: '',
            description: '',
            category_id: '',
            coordinate_lat: '',
            coordinate_lng: '',
            image: null,
        }

        document.addEventListener('alpine:init', () => {
            Alpine.data('app', () => ({
                resumenAvance: <?php echo $resumenAvance ?>,
                cash: <?php echo json_encode(['cash' => $totalCash, 'digital' => $totalDigital]) ?>,
                accounts: <?php echo $database->table('bank_accounts')->get() ?>,
                payment_method: {
                    ...paymentMethodDefault
                },
                credits: [],
                creditsAtrasados: [],
                creditSelected: '',
                loadingSelectCredit: '',
                sendingPayment: false,
                sendingWhatsApp: false,
                itemSelected: '',
                credit: null,
                search: '',
                modalCobrar: false,
                success: null,
                tab: 'hoy', // semanal
                init() {
                    fetch('./../app/apiMobile/listaCobrosHoy.php')
                        .then(response => response.json())
                        .then(data => {
                            this.credits = [...data.data]
                            this.creditsAtrasados = [...data.charges]
                        });

                    this.$watch('search', (newValue) => {
                        fetch('./../app/apiMobile/listaCobrosHoy.php?search=' + newValue)
                            .then(response => response.json())
                            .then(data => {
                                this.credits = [...data.data]
                                this.creditsAtrasados = [...data.charges]
                            })
                    })
                },
                refreshCredits() {
                    fetch('./../app/apiMobile/listaCobrosHoy.php?search=' + this.search)
                        .then(response => response.json())
                        .then(data => {
                            this.credits = [...data.data]
                            this.creditsAtrasados = [...data.charges]
                        })
                },
                get getAccounts() {
                    if (this.payment_method.type === 'yape') {
                        return this.accounts.filter(item => item.type == 'digital_wallet');
                    }

                    return this.accounts.filter(item => item.type == 'bank_account');
                },
                get creditsFiltered() {
                    if (this.tab === 'hoy') {
                        return this.credits
                    } else if (this.tab === 'semanal') {
                        return this.credits.filter(item => item.payment_period === 'weekly')
                    } else if (this.tab === 'retrasado') {
                        return this.creditsAtrasados
                    }

                },
                changeTab(tab) {
                    this.tab = tab
                },
                payInstallment(id) {
                    if (!this.credit) {
                        return;
                    }

                    let v = true
                    const newData = this.credit.installments.map(item => {
                        if (item.id === id) {
                            let value = item.slope;

                            if (item.pago === item.slope) {
                                value = 0;
                            }

                            if (value !== item.slope) {
                                v = false;
                            }

                            return {
                                ...item,
                                pago: value
                            }
                        }

                        if (!v) {
                            return {
                                ...item,
                                pago: 0,
                                pagoMora: 0
                            }
                        }

                        return item;
                    });

                    this.credit.installments = newData;
                },
                payMora(id) {
                    if (!this.credit) {
                        return;
                    }

                    let v = true;
                    const newData = this.credit.installments.map(item => {
                        if (item.id === id) {
                            let value = item.mora;

                            if (parseFloat(item.mora) === parseFloat(item.pagoMora)) {
                                value = 0;
                            }

                            if (value !== item.mora) {
                                v = false;
                            }

                            return {
                                ...item,
                                pagoMora: value
                            }
                        }

                        return item;
                    });

                    if (!v) {
                        this.credit.pagoMora = 0;
                    }

                    this.credit.installments = newData;
                },
                handleChangePago(id, event) {
                    let v = true;
                    this.credit.installments = this.credit.installments.map(item => {
                        if (item.id == id) {
                            let value = event.target.value;

                            if (isNaN(value)) {
                                value = 0;
                            }

                            if (parseFloat(value) > item.slope) {
                                value = item.slope;
                            }

                            if (parseFloat(value) !== parseFloat(item.slope)) {
                                v = false;
                            }

                            return {
                                ...item,
                                pago: value
                            }
                        }

                        if (!v) {
                            return {
                                ...item,
                                pago: 0,
                                pagoMora: 0
                            }
                        }

                        return item;
                    })
                },
                total() {
                    if (!this.credit) {
                        return 0;
                    }

                    const total = this.credit.installments.reduce((total, item) => {
                        if (isNaN(item.pago)) {
                            return total;
                        }

                        return total += parseFloat(item.pago || 0)
                    }, 0);

                    return total;
                },
                totalMora() {
                    if (!this.credit) {
                        return 0;
                    }

                    let moras = this.credit.installments.reduce((total, item) => {
                        if (isNaN(item.pagoMora)) {
                            return total;
                        }

                        return total += parseFloat(item.pagoMora)
                    }, 0);

                    if (!isNaN(this.credit.pagoMora)) {
                        moras += this.credit.pagoMora;
                    }

                    return moras
                },
                handleSelectCredit(id) {
                    const credit = this.credits.find(item => item.id === id);
                    this.credit = {
                        ...credit
                    }
                },
                removeSelectCredit() {
                    this.itemSelected = ''
                    this.credit = null
                },
                finalizarCobro() {
                    if (this.sendingPayment) {
                        return;
                    }

                    this.sendingPayment = true;

                    // fetch(`./../app/apiMobile/cobroMobile.php`, {
                    fetch(`<?php echo $_ENV['API_PATH'] ?>/credits/${this.credit?.id}/pay`, {
                            method: 'post',
                            body: JSON.stringify({
                                amount: this.total(),
                                penalty: this.totalMora(),
                                payment_method_type: this.payment_method.type,
                                payment_method_account: this.payment_method.account,
                                payment_method_date: this.payment_method.date,
                                payment_method_reference: this.payment_method.reference,
                                user_id: <?php echo $request->user()->idU ?>
                            }),
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {

                            if (data.success) {
                                this.refreshCredits();

                                this.success = {
                                    operation: data.transaction_id,
                                    total: data.total,
                                    cell_phone: data.cell_phone,
                                    voucher_path: data.voucher_path,
                                    created_at: data.created_at
                                }

                                if (data.avance > 0) {
                                    this.resumenAvance.total_pagado += data.avance;
                                }

                                if (data.isDigital) {
                                    this.cash = {
                                        ...this.cash,
                                        digital: parseFloat(this.cash.digital) + parseFloat(data.total)
                                    }
                                } else {
                                    this.cash = {
                                        ...this.cash,
                                        cash: parseFloat(this.cash.cash) + parseFloat(data.total)
                                    }
                                }

                                this.credit = null
                                this.payment_method = paymentMethodDefault
                                this.modalCobrar = false
                                this.itemSelected = ''
                            } else {
                                alert(data.message);
                            }

                        })
                        .catch(_ => {
                            alert('Lo sentimos, no se realizo la transacción.');
                        })
                        .finally(_ => this.sendingPayment = false)

                },
                pagoMoraInstallment() {
                    if (!this.credit) {
                        return true;
                    }

                    return this.credit.installments.reduce((total, item) => {
                        if (isNaN(item.pagoMora)) {
                            return total
                        }

                        return total += item.pagoMora
                    }, 0);
                },
                handleSelectOtrasMoras() {
                    if (this.pagoMoraInstallment() < this.credit.mora_installments) {
                        return;
                    }

                    this.credit.pagoMora = this.credit.pagoMora > 0 ? 0 : this.credit.otras_moras
                },
                handleSelectCredit(creditId) {
                    if (this.loadingSelectCredit) {
                        return;
                    }

                    this.loadingSelectCredit = true;

                    fetch(`./../app/apiMobile/creditToPay.php?creditId=${creditId}`)
                        .then(response => response.json())
                        .then(data => {
                            this.credit = data
                        })
                        .finally(_ => this.loadingSelectCredit = false);
                },
                enviarComprobanteAWhatsapp() {
                    if (this.sendingWhatsApp) {
                        return;
                    }

                    this.sendingWhatsApp = true;

                    data = {
                        "messaging_product": "whatsapp",
                        "recipient_type": "individual",
                        "to": `${this.success.cell_phone}`,
                        "type": "template",
                        "template": {
                            name: 'comprobante_de_pago',
                            language: {
                                code: 'es'
                            },
                            components: [{
                                    type: 'header',
                                    parameters: [{
                                        type: 'document',
                                        document: {
                                            // link: 'https://ci.centecp.com/app/pdf/voucher.php?operacion=37988'
                                            link: `<?php echo $_ENV['APP_URL'] ?>/app/pdf/voucher.php?operacion=${this.success.operation}`
                                        }
                                    }]
                                },
                                {
                                    type: 'body',
                                    parameters: [{
                                            type: 'text',
                                            text: this.success.created_at
                                        },
                                        {
                                            type: 'currency',
                                            currency: {
                                                "fallback_value": 'S/' + numberFormat(this.success.total),
                                                "code": "PEN",
                                                "amount_1000": this.success.total * 1000
                                            }
                                        }
                                    ]
                                }
                            ]
                        }
                    }

                    const data2 = {
                        "messaging_product": "whatsapp",
                        "to": `51${this.success.cell_phone}`,
                        "type": "template",
                        template: {
                            name: "hello_world",
                            language: {
                                "code": "en_US"
                            }
                        }
                    }

                    const TOKEN = <?php echo json_encode($_ENV['WHATSAPP_TOKEN'] ?? ''); ?>;
                    const ACCOUNT_ID = '100173789606399'; // test 104185252512148

                    fetch(`https://graph.facebook.com/v15.0/${ACCOUNT_ID}/messages`, {
                            method: 'post',
                            headers: {
                                'Content-Type': 'application/json',
                                Authorization: `Bearer ${TOKEN}`
                            },
                            body: JSON.stringify(data)
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.error) {
                                alert(`Lo sentimos, no se pudo enviar el mensage a +51${data.success.cell_phone}.`);
                            } else {
                                alert('Enviado con exito!!')
                            }
                        })
                        .catch(err => alert('Se produjo un error desconocido. No se envio el mensage.'))
                        .finally(_ => this.sendingWhatsApp = false)
                },
                avanceEnPorcentaje() {
                    return Math.round((parseFloat(this.resumenAvance.total_pagado) * 100) / (parseFloat(this.resumenAvance.capital) + parseFloat(this.resumenAvance.interest)) * 100) / 100
                },
                classColorAvance() {
                    const avance = this.avanceEnPorcentaje()

                    let classColor = 'bg-red-500'

                    if (avance > 75) {
                        classColor = 'bg-green-500'
                    } else if (avance > 50) {
                        classColor = 'bg-yellow-400'
                    } else if (avance > 25) {
                        classColor = 'bg-orange-500'
                    }

                    return classColor
                },
                messageAvance() {
                    const avance = this.avanceEnPorcentaje()

                    let message = 'Redoblar esfuerzo'

                    if (avance > 75) {
                        message = 'Logramos y podemos mejorar'
                    } else if (avance > 50) {
                        message = 'Ya vamos llegando'
                    } else if (avance > 25) {
                        message = 'Sigamos mejorando'
                    }

                    return message
                }
            }))

            Alpine.data('comment', () => ({
                open: false,
                openModalCamera: false,
                customer: '',
                factory: {
                    ...commentDefault
                },
                loading: false,
                loadingDeleting: false,
                withErrorLocation: false,
                loadingLocation: false,
                uploadingImage: false,
                errors: null,
                sendingDataToCreate: false,
                getLocationComment() {
                    this.loadingLocation = true
                    this.withErrorLocation = false;

                    if ("geolocation" in navigator) {
                        navigator.geolocation.getCurrentPosition(position => {
                            this.loadingLocation = false

                            this.factory.coordinate_lat = position.coords.latitude
                            this.factory.coordinate_lng = position.coords.longitude
                        }, error => {
                            this.loadingLocation = false;
                            this.withErrorLocation = true
                        }, {
                            enableHighAccuracy: false,
                            maximumAge: 0,
                            timeout: 5000
                        })
                    } else {
                        alert("Tu navegador no soporta el acceso a la ubicación. Intenta con otro");
                    }
                },
                openModalComment({
                    customerName,
                    creditId
                }) {
                    this.open = true;
                    this.customer = customerName;
                    this.factory.credit_id = creditId;

                    this.getLocationComment()

                    // old open modal
                    // open = true, customer = item.customer, factory.id = item.comment?.id, factory.description = item.comment?.description, factory.credit_id = item.idP
                },
                closeModalComment() {
                    this.open = false
                    this.customer = ''
                    this.factory = {
                        ...commentDefault
                    }
                    this.loading = false
                    this.loadingDeleting = false
                    this.withErrorLocation = false
                    this.loadingLocation = false

                    // old close modal
                    // open = false, factory.id = '', factory.credit_id = '', factory.description = '', customer = ''
                },
                handleChangeInputFileComment(event) {
                    if (this.uploadingImage) {
                        return;
                    }

                    const file = event.target.files[0]

                    if (!file) {
                        return;
                    }

                    // const sizeByte = file.size;

                    // el tipo tiene que ser imagen
                    if (!file.type.startsWith('image/')) {
                        alert("El archivo debe ser una imagen.")
                        return;
                    }

                    const micompresor = new Promise((resolve, reject) => {
                        new Compressor(file, {
                            quality: 0.6,
                            success(result) {
                                resolve(result)
                            },
                            error(err) {
                                reject(err)
                            }
                        })
                    })

                    this.factory.image = null
                    this.uploadingImage = true
                    micompresor.then(result => {
                        const formData = new FormData()
                        formData.append('file', result, result.name)

                        fetch(`../app/api/uploadFile.php`, {
                                method: 'post',
                                body: formData
                            })
                            .then(response => response.json())
                            .then(data => {
                                this.uploadingImage = false
                                if (data.success) {
                                    this.factory.image = data.data
                                }
                            })
                            .finally(_ => {
                                this.uploadingImage = false
                            })
                    }).catch(err => {
                        this.uploadingImage = false
                        alert('Tubimos problemas al mostrar la imagen.')
                    })

                    event.target.value = null
                },
                handleOpenModalCamera() {
                    this.openModalCamera = true
                },
                handleSubmit() {
                    if (this.sendingDataToCreate || this.uploadingImage) {
                        return
                    }

                    this.sendingDataToCreate = true

                    // crear justificacion
                    fetch('./../app/api/justifyNonPayment.php', {
                            method: 'post',
                            body: JSON.stringify({
                                ...this.factory,
                                image: this.factory.image?.name
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                this.factory = {
                                    ...commentDefault
                                }
                                this.open = false
                                this.customer = ''
                                this.refreshCredits();
                                alert('Comentario registrado con exito!!')
                            } else {
                                this.errors = data.errors
                                alert("Tienes errores de validación.")
                            }
                        })
                        .finally(_ => this.sendingDataToCreate = false)

                    return;

                    if (this.factory.id) {
                        // update
                        fetch('./../app/api/updateJustification.php', {
                                method: 'post',
                                body: JSON.stringify(this.factory)
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    this.factory = {
                                        ...commentDefault
                                    }
                                    this.open = false
                                    this.customer = ''
                                    this.refreshCredits();
                                    alert('Comentario actualizado con exito!!')
                                } else {
                                    alert(data.message);
                                }
                            })
                            .finally(_ => this.loading = false)
                    } else {
                        // create
                        fetch('./../app/api/justifyNonPayment.php', {
                                method: 'post',
                                body: JSON.stringify(this.factory)
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    this.factory = {
                                        ...commentDefault
                                    }
                                    this.open = false
                                    this.customer = ''
                                    this.refreshCredits();
                                    alert('Comentario registrado con exito!!')
                                } else {
                                    alert(data.message);
                                }
                            })
                            .finally(_ => this.loading = false)
                    }
                },
                deleteComment() {
                    if (!this.factory?.id) {
                        return;
                    }

                    if (this.loadingDeleting) {
                        return;
                    }

                    this.loadingDeleting = true

                    const result = confirm('¿Segúro que desea eliminar el comentario?')

                    if (result) {
                        fetch(`./../app/api/deleteJustification.php?nonPaymentJustificationId=${this.factory.id}`)
                            .then(response => response.json())
                            .then(data => {
                                this.factory = {
                                    ...commentDefault
                                }
                                this.open = false
                                this.customer = ''

                                alert(data.message)

                                if (data.success) {
                                    this.refreshCredits();
                                }
                            })
                            .finally(_ => {
                                this.loadingDeleting = false;
                            })
                    }
                }
            }))
        });

        const format = new Intl.NumberFormat('es-PE', {
            minimumFractionDigits: 2
        });

        function numberFormat(number) {
            return format.format(number)
        }

        function formatDate(date) {
            return date.split('-').reverse().join('/');
        }

        function sum(...args) {
            let sum = 0;
            for (let arg of args) {
                if (isNaN(arg)) {
                    continue;
                }

                sum += arg
            };
            return sum;
        }

        function bodyScroll(value) {
            document.body.style.overflow = value === false ? 'hidden' : 'auto';
        }
    </script>

    <script src="../public/resource/js/alpine.3.10.3.min.js" defer></script>
    <script src="../public/resource/js/compressor-1.2.1.js"></script>
</body>

</html>