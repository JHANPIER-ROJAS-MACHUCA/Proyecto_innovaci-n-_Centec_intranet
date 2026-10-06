<?php
require_once '../vendor/autoload.php';
require_once '../src/Domain/Database/bootstrap.php';

use CrediSoporte\Domain\Request\Request;
use CrediSoporte\Domain\Models\Customer;
use CrediSoporte\Domain\Models\Proposal;
use CrediSoporte\Domain\Models\Dictionary;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

$request = new Request();

$dictionaries = Dictionary::all();
$dictionariesProducts = $database->table('credit_types')->get();
$dictionariesPayments = $dictionaries->where('type', 'PAYMENT');
$dictionariesModalities = $dictionaries->where('type', 'MODALITY');
$dictionariesClientTypes = $dictionaries->where('type', 'CLIENT_TYPE');
$dictionariesHousingTypes = $dictionaries->where('type', 'HOUSING_TYPE');
$dictionariesRiskProfiles = $dictionaries->where('type', 'RISK_PROFILE');

$messageSuccess = "";
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $validator = Validation::createValidator();

    $spouse = [];
    $spouseInput = [];
    if ($request->post('with_spouse') == 'on') {
        $spouse = [
            'spouse_document' => [
                new Assert\NotBlank(['message' => 'El dni del conyuge no puede estar vacio.']),
                new Assert\Length(['min' => 8, 'max' => 8, 'exactMessage' => 'El dni del conyuge debe tene {{ limit }} digitos.'])
            ],
            'spouse_name' => [
                new Assert\NotBlank(['message' => 'El nombre del conyuge no puede estar vacio.']),
                new Assert\Length(['max' => 50, 'maxMessage' => 'Los nombre del conyuge no debe contener mayor a {{ limit }} caracteres.'])
            ],
            'spouse_surname' => [
                new Assert\NotBlank(['message' => 'El apellido del conyuge no puede estar vacio.']),
                new Assert\Length(['max' => 50, 'maxMessage' => 'Los apellido del conyuge no debe contener mayor a {{ limit }} caracteres.'])
            ],
            'spouse_cell_phone' => new Assert\Length([
                'max' => 20,
                'maxMessage' => 'El telefono del conyuge no debe contener mayor a {{ limit }} caracteres.'
            ])
        ];

        $spouseInput = [
            'spouse_document' => $request->post('spouse_document'),
            'spouse_name' => $request->post('spouse_name'),
            'spouse_surname' => $request->post('spouse_surname'),
            'spouse_cell_phone' => $request->post('spouse_cell_phone')
        ];
    }

    $aval = [];
    $avalInput = [];
    if ($request->post('with_aval') == 'on') {
        $aval = [
            'aval_document' => [
                new Assert\NotBlank(['message' => 'El dni del aval no puede estar vacio.']),
                new Assert\Length(['min' => 8, 'max' => 8, 'exactMessage' => 'El dni del aval debe tene {{ limit }} digitos.'])
            ],
            'aval_name' => [
                new Assert\NotBlank(['message' => 'El nombre del aval no puede estar vacio.']),
                new Assert\Length(['max' => 50, 'maxMessage' => 'Los nombre del aval no debe contener mayor a {{ limit }} caracteres.'])
            ],
            'aval_surname' => [
                new Assert\NotBlank(['message' => 'El apellido del aval no puede estar vacio.']),
                new Assert\Length(['max' => 50, 'maxMessage' => 'Los apellido del aval no debe contener mayor a {{ limit }} caracteres.'])
            ],
            'aval_cell_phone' => new Assert\Length([
                'max' => 20,
                'maxMessage' => 'El telefono del aval no debe contener mayor a {{ limit }} caracteres.'
            ])
        ];

        $avalInput = [
            'aval_document' => $request->post('aval_document'),
            'aval_name' => $request->post('aval_name'),
            'aval_surname' => $request->post('aval_surname'),
            'aval_cell_phone' => $request->post('aval_cell_phone'),
        ];
    }

    $input = [
        'document' => $request->post('document'),
        'name' => $request->post('name'),
        'surname' => $request->post('surname'),
        'cell_phone' => $request->post('cell_phone'),
        'amount' => $request->post('amount'),
        'installment' => $request->post('installment'),
        'rate' => $request->post('rate'),
        'fee' => $request->post('fee'),
    ] + $avalInput + $spouseInput;

    $groups = new Assert\GroupSequence(['Default', 'custom']);

    $constraint = new Assert\Collection([
        'document' => new Assert\NotBlank(['message' => 'El documento no puede estar vacio.']),
        'document' => new Assert\Length(['min' => 8, 'max' => 8, 'exactMessage' => 'El documento debe tene {{ limit }} digitos.']),
        'name' => new Assert\NotBlank(['message' => 'El campo nombre no puede estar vacio.']),
        'name' => new Assert\Length(['max' => 50, 'maxMessage' => 'Los nombres no debe contener mayor a {{ limit }} caracteres.']),
        'surname' => new Assert\NotBlank(['message' => 'El campo apellido no puede estar vacio.']),
        'surname' => new Assert\Length(['max' => 50, 'maxMessage' => 'Los apellidos no debe contener mayor a {{ limit }} caracteres.']),
        'cell_phone' => new Assert\Length(['max' => 20, 'maxMessage' => 'El telefono no debe contener mayor a {{ limit }} caracteres.']),
        'amount' => new Assert\Range([
            'min' => 1,
            'max' => 100000,
            'notInRangeMessage' => 'El monto debe ser mayor a {{ min }} y mayor a {{ max }}.',
            'invalidMessage' => 'El monto no es válido.'
        ]),
        'installment' => new Assert\Range([
            'min' => 1,
            'max' => 60,
            'notInRangeMessage' => 'El plazo debe ser mayor a {{ min }} y menor a {{ max }}.',
            'invalidMessage' => 'El plazo no es válido.'
        ]),
        'rate' => new Assert\Range([
            'min' => 0,
            'max' => 100,
            'notInRangeMessage' => 'La taza debe ser mayor a {{ min }} y menor a {{ max }}.',
            'invalidMessage' => 'La taza no es válido.'
        ]),
        'fee' => new Assert\Range([
            'min' => 1,
            'max' => 100000,
            'notInRangeMessage' => 'La cuota debe ser mayor a {{ min }} y menor a {{ max }}.',
            'invalidMessage' => 'La cuota no es válido.'
        ]),
    ] + $aval + $spouse);

    $violations = $validator->validate($input, $constraint, $groups);

    if (0 !== count($violations)) {
        // hay errores ahora puedes mostrarlos
        $errors = $violations;
    } else {
        $proposalUpdate = Proposal::find($request->get('proposalId'));

        if ($_COOKIE['user1'] == $proposalUpdate->user_id) {
            $surnameInArray = explode(' ', trim($request->post('surname')));
            $customer = Customer::updateOrCreate(
                ['dni' => $request->post('document')],
                [
                    'ap' => trim($surnameInArray[0] ?? ''),
                    'am' => trim($surnameInArray[1] ?? ''),
                    'nom' => trim($request->post('name')),
                    'cel' => trim($request->post('cell_phone')),
                    'client_type' => $request->post('client_type'),
                    'risk_profile_id' => $request->post('risk_profile_id')
                ]
            );

            $aval = null;
            if ($request->post('with_aval') == 'on') {
                $surnameInArray = explode(' ', trim($request->post('aval_surname')));
                $aval = Customer::updateOrCreate(
                    ['dni' => $request->post('aval_document')],
                    [
                        'ap' => trim($surnameInArray[0] ?? ''),
                        'am' => trim($surnameInArray[1] ?? ''),
                        'nom' => trim($request->post('aval_name')),
                        'cel' => trim($request->post('aval_cell_phone')),
                        'risk_profile_id' => trim($request->post('aval_risk_profile_id'))
                    ]
                );
            }

            $spouse = null;
            if ($request->post('with_spouse') == 'on') {
                $surnameInArray = explode(' ', trim($request->post('spouse_surname')));
                $spouse = Customer::updateOrCreate(
                    ['dni' => $request->post('spouse_document')],
                    [
                        'ap' => trim($surnameInArray[0] ?? ''),
                        'am' => trim($surnameInArray[1] ?? ''),
                        'nom' => trim($request->post('spouse_name')),
                        'cel' => trim($request->post('spouse_cell_phone')),
                        'risk_profile_id' => trim($request->post('spouse_risk_profile_id'))
                    ]
                );
            }

            $proposalUpdate->customer_id = $customer->idCG;
            $proposalUpdate->aval_id = $aval !== null ? $aval->idCG : null;
            $proposalUpdate->spouse_id = $spouse !== null ? $spouse->idCG : null;
            $proposalUpdate->credit_type_id = $request->post('credit_type', null);
            $proposalUpdate->modality_id = $request->post('modality', null);
            $proposalUpdate->payment_type_id = $request->post('payment_type', null);
            $proposalUpdate->housing_type_id = $request->post('housing_type', null);
            $proposalUpdate->business_type_id = $request->post('business_type', null);
            $proposalUpdate->amount = $request->post('amount');
            $proposalUpdate->installment = $request->post('installment');
            $proposalUpdate->rate = $request->post('rate');
            $proposalUpdate->fee = $request->post('fee');
            $proposalUpdate->about_business = $request->post('about_business');
            $proposalUpdate->about_destiny = $request->post('about_destiny');
            $proposalUpdate->about_experience = $request->post('about_experience');
            $proposalUpdate->about_family = $request->post('about_family');
            $proposalUpdate->evaluation = $request->post('evaluation');
            $proposalUpdate->guaranty = $request->post('guaranty');
            $proposalUpdate->save();

            $messageSuccess = "Se actualizo la propuesa número " . $request->get('proposalId') . '.';
            unset($_POST);
        }
    }
}

$proposal = Proposal::join('tclie_general', 'proposals.customer_id', 'tclie_general.idCG')
    ->leftJoin('tclie_general as spouse', 'proposals.spouse_id', 'spouse.idCG')
    ->leftJoin('tclie_general as aval', 'proposals.aval_id', 'aval.idCG')
    ->select(
        'proposals.*',
        'tclie_general.dni as document',
        'tclie_general.nom as name',
        'tclie_general.cel as cell_phone',
        'tclie_general.client_type as client_type_id',
        'tclie_general.cel as cell_phone',
        'tclie_general.risk_profile_id',
        'spouse.dni as spouse_document',
        'spouse.nom as spouse_name',
        'spouse.cel as spouse_cell_phone',
        'spouse.risk_profile_id as spouse_risk_profile_id',
        'aval.dni as aval_document',
        'aval.nom as aval_name',
        'aval.cel as aval_cell_phone',
        'aval.risk_profile_id as aval_risk_profile_id'
    )
    ->selectRaw('concat(tclie_general.ap, " ", tclie_general.am) as surname')
    ->selectRaw('concat(spouse.ap, " ", spouse.am) as spouse_surname')
    ->selectRaw('concat(aval.ap, " ", aval.am) as aval_surname')
    ->find($request->get('proposalId'));

if (is_null($proposal)) {
    echo 'No existe la propuesta.';
    die();
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Propuesta de credito</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>

    <style>
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>
</head>

<body>
    <div class="max-w-4xl mx-auto px-4">
        <?php if ($messageSuccess) { ?>
            <div class="text-green-500 font-semibold bg-green-50 p-3 border border-green-500 mt-10">
                <?php echo $messageSuccess ?>
            </div>
        <?php } ?>

        <h1 class="text-4xl font-bold text-center mt-10">
            EVALUACION - PROPUESTA DE CREDITO
        </h1>

        <form action="<?php echo $_SERVER["PHP_SELF"] . '?proposalId=' . $request->get('proposalId'); ?>" method="post" id="form_propuesta">
            <div class="divide-y">
                <div class="py-12">
                    <h4 class="text-2xl font-semibold">Datos del cliente</h4>
                    <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                        <label class="block">
                            <span class="text-gray-700">DNI</span>
                            <input type="number" name="document" class="border-gray-300 rounded-md w-full" id="form_document" value="<?php echo $proposal->document ?>">
                        </label>
                        <label class="block">
                            <span class="text-gray-700">Apellidos</span>
                            <input type="text" name="surname" class="border-gray-300 rounded-md w-full" id="form_surname" value="<?php echo $proposal->surname ?>">
                        </label>
                        <label class="block">
                            <span class="text-gray-700">Nombres</span>
                            <input type="text" name="name" class="border-gray-300 rounded-md w-full" id="form_name" value="<?php echo $proposal->name ?>">
                        </label>
                        <label class="block">
                            <span class="text-gray-700">Telefono</span>
                            <input type="tel" name="cell_phone" class="border-gray-300 rounded-md w-full" id="form_cell_phone" value="<?php echo $proposal->cell_phone ?>">
                        </label>
                        <label class="block">
                            <span class="text-gray-700">Tipo de cliente</span>
                            <select name="client_type" class="border-gray-300 rounded-md w-full">
                                <option value="">Seleccione una opción</option>
                                <?php foreach ($dictionariesClientTypes as $dictionary) { ?>
                                    <option value="<?php echo $dictionary->id ?>" <?php echo $proposal->client_type_id == $dictionary->id ? 'selected' : '' ?>>
                                        <?php echo $dictionary->description ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </label>
                        <label class="block">
                            <span class="text-gray-700">Perfil de riesgo</span>
                            <select name="risk_profile_id" class="border-gray-300 rounded-md w-full" id="form_risk_profile">
                                <option value="">Seleccione una opción</option>
                                <?php foreach ($dictionariesRiskProfiles as $dictionary) { ?>
                                    <option value="<?php echo $dictionary->id ?>" <?php echo $proposal->risk_profile_id == $dictionary->id ? 'selected' : '' ?>>
                                        <?php
                                        switch ($dictionary->description) {
                                            case 'red':
                                                echo 'Alto Riesgo';
                                                break;
                                            case 'yellow':
                                                echo 'Mediano Riesgo';
                                                break;
                                            case 'gray':
                                                echo 'No registra información en el mes';
                                                break;
                                            case 'green':
                                                echo 'Sin Riesgo';
                                                break;
                                        }
                                        ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </label>
                    </div>
                </div>

                <div class="py-12">
                    <h4 class="text-2xl font-semibold">Propuesta de crédito</h4>
                    <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                        <label class="block">
                            <span class="text-gray-700">Monto propuesto</span>
                            <input type="number" name="amount" class="border-gray-300 rounded-md w-full" id="form_amount" value="<?php echo $proposal->amount ?>">
                        </label>
                        <label class="block">
                            <span class="text-gray-700">Tipo de crédito</span>
                            <select name="credit_type" class="border-gray-300 rounded-md w-full">
                                <option value="">Seleccione una opción</option>
                                <?php foreach ($dictionariesProducts as $item) { ?>
                                    <option value="<?php echo $item->id ?>" <?php echo $proposal->credit_type_id == $item->id ? 'selected' : '' ?>>
                                        <?php echo $item->name ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </label>
                        <label class="block">
                            <span class="text-gray-700">Modalidad</span>
                            <select name="modality" class="border-gray-300 rounded-md w-full">
                                <option value="">Seleccione una opción</option>
                                <?php foreach ($dictionariesModalities as $dictionary) { ?>
                                    <option value="<?php echo $dictionary->id ?>" <?php echo $proposal->modality_id == $dictionary->id ? 'selected' : '' ?>>
                                        <?php echo $dictionary->description ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </label>
                        <label class="block">
                            <span class="text-gray-700">Tipo de pago</span>
                            <select name="payment_type" class="border-gray-300 rounded-md w-full">
                                <option value="">Seleccione una opción</option>
                                <?php foreach ($dictionariesPayments as $dictionary) { ?>
                                    <option value="<?php echo $dictionary->id ?>" <?php echo $proposal->payment_type_id == $dictionary->id ? 'selected' : '' ?>>
                                        <?php echo $dictionary->description ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </label>
                        <label class="block">
                            <span class="text-gray-700">Plazo propuesto</span>
                            <input type="number" name="installment" class="border-gray-300 rounded-md w-full" id="form_installment" value="<?php echo $proposal->installment ?>">
                        </label>
                        <label class="block">
                            <span class="text-gray-700">Tasa</span>
                            <input type="number" name="rate" class="border-gray-300 rounded-md w-full" id="form_rate" value="<?php echo $proposal->rate ?>">
                        </label>
                        <label class="block">
                            <span class="text-gray-700">Cuota</span>
                            <input type="number" name="fee" step="any" class="border-gray-300 rounded-md w-full" id="form_fee" value="<?php echo $proposal->fee ?>">
                        </label>
                        <label class="block">
                            <span class="text-gray-700">Situación de domicilio</span>
                            <select name="housing_type" class="border-gray-300 rounded-md w-full">
                                <option value="">Seleccione una opción</option>
                                <?php foreach ($dictionariesHousingTypes as $dictionary) { ?>
                                    <option value="<?php echo $dictionary->id ?>" <?php echo $proposal->housing_type_id == $dictionary->id ? 'selected' : '' ?>>
                                        <?php echo $dictionary->description ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </label>
                        <label class="block">
                            <span class="text-gray-700">Situación de negocio</span>
                            <select name="business_type" class="border-gray-300 rounded-md w-full">
                                <option value="">Seleccione una opción</option>
                                <?php foreach ($dictionariesHousingTypes as $dictionary) { ?>
                                    <option value="<?php echo $dictionary->id ?>" <?php echo $proposal->business_type_id == $dictionary->id ? 'selected' : '' ?>>
                                        <?php echo $dictionary->description ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </label>
                    </div>

                    <label class="block mt-8">
                        <input type="checkbox" name="with_spouse" class="border-gray-300 rounded" id="form_with_spouse" <?php echo $proposal->spouse_id != '' ? 'checked' : '' ?>>
                        <span class="text-gray-700 ml-2">Con conyuge</span>
                    </label>
                    <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6" id="container_spouse" style="<?php echo $proposal->spouse_id == '' ? 'display: none;' : '' ?>">
                        <label class="block">
                            <span class="text-gray-700">DNI</span>
                            <input type="number" name="spouse_document" class="border-gray-300 rounded-md w-full" id="form_spouse_document" value="<?php echo $proposal->spouse_document ?>">
                        </label>
                        <label class="block">
                            <span class="text-gray-700">Apellidos</span>
                            <input type="text" name="spouse_surname" class="border-gray-300 rounded-md w-full" id="form_spouse_surname" value="<?php echo $proposal->spouse_surname ?>">
                        </label>
                        <label class="block">
                            <span class="text-gray-700">Nombres</span>
                            <input type="text" name="spouse_name" class="border-gray-300 rounded-md w-full" id="form_spouse_name" value="<?php echo $proposal->spouse_name ?>">
                        </label>
                        <label class="block">
                            <span class="text-gray-700">Telefono</span>
                            <input type="tel" name="spouse_cell_phone" class="border-gray-300 rounded-md w-full" id="form_spouse_cell_phone" value="<?php echo $proposal->spouse_cell_phone ?>">
                        </label>
                        <label class="block">
                            <span class="text-gray-700">Perfil de riesgo</span>
                            <select name="spouse_risk_profile_id" class="border-gray-300 rounded-md w-full" id="form_spouse_risk_profile">
                                <option value="">Seleccione una opción</option>
                                <?php foreach ($dictionariesRiskProfiles as $dictionary) { ?>
                                    <option value="<?php echo $dictionary->id ?>" <?php echo $proposal->spouse_risk_profile_id == $dictionary->id ? 'selected' : '' ?>>
                                        <?php
                                        switch ($dictionary->description) {
                                            case 'red':
                                                echo 'Alto Riesgo';
                                                break;
                                            case 'yellow':
                                                echo 'Mediano Riesgo';
                                                break;
                                            case 'gray':
                                                echo 'No registra información en el mes';
                                                break;
                                            case 'green':
                                                echo 'Sin Riesgo';
                                                break;
                                        }
                                        ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </label>
                    </div>

                    <label class="block mt-8">
                        <input type="checkbox" name="with_aval" class="border-gray-300 rounded" id="form_with_aval" <?php echo $proposal->aval_id != '' ? 'checked' : '' ?>>
                        <span class="text-gray-700 ml-2">Con aval</span>
                    </label>
                    <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6" id="container_aval" style="<?php echo $proposal->aval_id == '' ? 'display: none;' : '' ?>">
                        <label class="block">
                            <span class="text-gray-700">DNI</span>
                            <input type="number" name="aval_document" class="border-gray-300 rounded-md w-full" id="form_aval_document" value="<?php echo $proposal->aval_document ?>">
                        </label>
                        <label class="block">
                            <span class="text-gray-700">Apellidos</span>
                            <input type="text" name="aval_surname" class="border-gray-300 rounded-md w-full" id="form_aval_surname" value="<?php echo $proposal->aval_surname ?>">
                        </label>
                        <label class="block">
                            <span class="text-gray-700">Nombres</span>
                            <input type="text" name="aval_name" class="border-gray-300 rounded-md w-full" id="form_aval_name" value="<?php echo $proposal->aval_name ?>">
                        </label>
                        <label class="block">
                            <span class="text-gray-700">Telefono</span>
                            <input type="tel" name="aval_cell_phone" class="border-gray-300 rounded-md w-full" id="form_aval_cell_phone" value="<?php echo $proposal->aval_cell_phone ?>">
                        </label>
                        <label class="block">
                            <span class="text-gray-700">Perfil de riesgo</span>
                            <select name="aval_risk_profile_id" class="border-gray-300 rounded-md w-full" id="form_aval_risk_profile">
                                <option value="">Seleccione una opción</option>
                                <?php foreach ($dictionariesRiskProfiles as $dictionary) { ?>
                                    <option value="<?php echo $dictionary->id ?>" <?php echo $proposal->aval_risk_profile_id == $dictionary->id ? 'selected' : '' ?>>
                                        <?php
                                        switch ($dictionary->description) {
                                            case 'red':
                                                echo 'Alto Riesgo';
                                                break;
                                            case 'yellow':
                                                echo 'Mediano Riesgo';
                                                break;
                                            case 'gray':
                                                echo 'No registra información en el mes';
                                                break;
                                            case 'green':
                                                echo 'Sin Riesgo';
                                                break;
                                        }
                                        ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </label>
                    </div>
                </div>
                <div class="py-12">
                    <h4 class="text-2xl font-semibold">Evaluación cualitativa</h4>
                    <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <label class="block">
                            <span class="text-gray-700">Sobre el negocio</span>
                            <textarea name="about_business" rows="3" class="border-gray-300 rounded-md w-full"><?php echo $proposal->about_business ?></textarea>
                        </label>
                        <label class="block">
                            <span class="text-gray-700">Sobre el destino del crédito</span>
                            <textarea name="about_destiny" rows="3" class="border-gray-300 rounded-md w-full"><?php echo $proposal->about_destiny ?></textarea>
                        </label>
                        <label class="block">
                            <span class="text-gray-700">Sobre la experiencia crediticia</span>
                            <textarea name="about_experience" rows="3" class="border-gray-300 rounded-md w-full"><?php echo $proposal->about_experience ?></textarea>
                        </label>
                        <label class="block">
                            <span class="text-gray-700">Sobre el entorno familiar</span>
                            <textarea name="about_family" rows="3" class="border-gray-300 rounded-md w-full"><?php echo $proposal->about_family ?></textarea>
                        </label>
                        <label class="block">
                            <span class="text-gray-700">Evaluación</span>
                            <textarea name="evaluation" rows="3" class="border-gray-300 rounded-md w-full"><?php echo $proposal->evaluation ?></textarea>
                        </label>
                        <label class="block">
                            <span class="text-gray-700">Garantías del crédito</span>
                            <textarea name="guaranty" rows="3" class="border-gray-300 rounded-md w-full"><?php echo $proposal->guaranty   ?></textarea>
                        </label>
                    </div>
                </div>


                <?php if ($_COOKIE['user1'] == $proposal->user_id) { ?>

                    <div class="py-12">
                        <?php if (count($errors) > 0) { ?>
                            <ul class="mb-10">
                                <?php foreach ($errors as $error) { ?>
                                    <li class="text-red-500 font-medium text-sm">- <?php echo $error->getMessage() ?></li>
                                <?php } ?>
                            </ul>
                        <?php } ?>

                        <button type="submit" class="px-3 py-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm w-full rounded-md">
                            ACTUALIZAR PROPUESTA
                        </button>
                    </div>

                <?php } ?>
            </div>
        </form>
    </div>

    <script>
        const formPropuesta = document.getElementById('form_propuesta');
        formPropuesta.onsubmit = e => {
            // e.preventDefault();

            if (confirm('Deseas continuar') == false) {
                e.preventDefault();
            }
        }

        document.getElementById('form_with_spouse').onchange = e => {
            if (e.target.checked) {
                document.getElementById('container_spouse').style = 'display: grid';
            } else {
                document.getElementById('container_spouse').style = 'display: none';
            }
        }

        document.getElementById('form_with_aval').onchange = e => {
            if (e.target.checked) {
                document.getElementById('container_aval').style = 'display: grid';
            } else {
                document.getElementById('container_aval').style = 'display: none';
            }
        }

        // inputs del form propuesta
        const formDocument = document.getElementById('form_document');
        const formDocumentSpouse = document.getElementById('form_spouse_document');
        const formDocumentAval = document.getElementById('form_aval_document');

        formDocument.onkeypress = evt => {
            if (evt.target.value.length > 7) {
                return false;
            }

            return true;
        }

        formDocument.onkeyup = e => {
            const value = e.target.value;

            if (value.length === 8) {
                searchByDocument(
                    'form_document',
                    'form_name',
                    'form_surname',
                    'form_cell_phone',
                    'form_client_type',
                    'form_risk_profile'
                );
            }
        }

        formDocumentSpouse.onkeyup = e => {
            const value = e.target.value;

            if (value.length === 8) {
                searchByDocument(
                    'form_spouse_document',
                    'form_spouse_name',
                    'form_spouse_surname',
                    'form_spouse_cell_phone',
                    'form_spouse_client_type',
                    'form_spouse_risk_profile'
                );
            }
        }

        formDocumentAval.onkeyup = e => {
            const value = e.target.value;

            if (value.length === 8) {
                searchByDocument(
                    'form_aval_document',
                    'form_aval_name',
                    'form_aval_surname',
                    'form_aval_cell_phone',
                    'form_aval_client_type',
                    'form_aval_risk_profile'
                )
            }
        }

        function searchByDocument(tagValue, tagName, tagSurname, tagCellPhone, tagClientType, tagRiskProfile) {
            const tag = document.getElementById(tagValue);
            tag.disabled = true
            const value = tag.value;

            fetch('../app/api/searchCustomer.php?dni=' + value)
                .then(response => response.json())
                .then(data => {
                    if (data.success === true && data.api_origin === 'new') {
                        document.getElementById(tagName).value = data.data.name;
                        document.getElementById(tagSurname).value = data.data.surname;
                        document.getElementById(tagCellPhone).value = '';

                        const fct = document.getElementById(tagClientType);
                        fct && (fct.value = '');

                        document.getElementById(tagRiskProfile).value = '';
                    } else if (data.success === true && data.api_origin === 'recurrent') {
                        document.getElementById(tagName).value = data.data.name;
                        document.getElementById(tagSurname).value = data.data.surname;
                        document.getElementById(tagCellPhone).value = data.data.cell_phone;

                        const fct = document.getElementById(tagClientType);
                        fct && (fct.value = data.data.client_type);

                        document.getElementById(tagRiskProfile).value = data.data.risk_profile_id;
                    }
                })
                .catch(_ => {
                    document.getElementById(tagName).value = '';
                    document.getElementById(tagSurname).value = '';
                    document.getElementById(tagCellPhone).value = '';

                    const fct = document.getElementById(tagClientType);
                    fct && (fct.value = '');

                    document.getElementById(tagRiskProfile).value = 's';
                })
                .finally(_ => tag.disabled = false);
        }

        document.getElementById('form_amount').onkeyup = e => calculateCuota()
        document.getElementById('form_installment').onkeyup = e => calculateCuota()
        document.getElementById('form_rate').onkeyup = e => calculateCuota()

        function calculateCuota() {
            const amount = getFloatVal(document.getElementById('form_amount').value);
            const installment = getFloatVal(document.getElementById('form_installment').value);
            const rate = getFloatVal(document.getElementById('form_rate').value);

            document.getElementById('form_fee').value = ((amount + (amount * rate / 100)) / installment).toFixed(2);
        }

        function getFloatVal(value) {
            if (isNaN(value) || value == '') {
                return 0;
            }

            return parseFloat(value);
        }
    </script>
</body>

</html>