<?php

require_once '../vendor/autoload.php';
require_once '../src/Domain/Database/bootstrap.php';

use CrediSoporte\Domain\Models\Dictionary;
use CrediSoporte\Domain\Models\Proposal;
use CrediSoporte\Domain\Models\ProposalResponse;
use CrediSoporte\Domain\Request\Request;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

$request = new Request();

$proposal = Proposal::find($request->get('proposalId'));

if (is_null($proposal)) {
    echo 'La propuesta no existe';
    die();
}

$dictionaries = Dictionary::all();
$dictionariesProducts = $database->table('credit_types')->get();
$dictionariesPayments = $dictionaries->where('type', 'PAYMENT');
$dictionariesModalities = $dictionaries->where('type', 'MODALITY');
$dictionariesClientTypes = $dictionaries->where('type', 'CLIENT_TYPE');
$dictionariesHousingTypes = $dictionaries->where('type', 'HOUSING_TYPE');

$messageSuccess = "";
$messageError = "";
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $validator = Validation::createValidator();

    $input = [
        'credit_type_id' => $request->post('credit_type_id'),
        'payment_type_id' => $request->post('payment_type_id'),
        'modality_id' => $request->post('modality_id'),
        'amount' => $request->post('amount'),
        'installment' => $request->post('installment'),
        'rate' => $request->post('rate'),
        'fee' => $request->post('fee'),
        'status' => $request->post('status')
    ];

    $groups = new Assert\GroupSequence(['Default', 'custom']);

    $constraint = new Assert\Collection([
        'credit_type_id' => new Assert\NotBlank(['message' => 'El tipo de prestamo no puede estar vacio.']),
        'payment_type_id' => new Assert\NotBlank(['message' => 'El tipo de pago no puede estar vacio.']),
        'modality_id' => new Assert\NotBlank(['message' => 'La modalidad no puede estar vacio.']),
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
        'status' => new Assert\Choice(['choices' => ['APROBADO', 'DESAPROBADO', 'OBSERVADO'], 'message' => 'Seleccione la condición de la propuesta.'])
    ]);

    $violations = $validator->validate($input, $constraint, $groups);

    if (0 !== count($violations)) {
        // hay errores ahora puedes mostrarlos
        $errors = $violations;
    } else {
        // creacion del modelo

        if (ProposalResponse::where('proposal_id', $request->get('proposalId'))->exists()) {
            $messageError = 'La propuesta ' . str_pad($request->get('proposalId'), 2, '0', STR_PAD_LEFT) . ' ya tiene una respuesta.';
        } else if ($_COOKIE['tuser'] == '1' || $_COOKIE['tuser'] == '2') {
            $proposalResponse = new ProposalResponse();
            $proposalResponse->user_id = $_COOKIE['user1'];
            $proposalResponse->proposal_id = $request->get('proposalId');
            $proposalResponse->credit_type_id = $request->post('credit_type_id');
            $proposalResponse->payment_type_id = $request->post('payment_type_id');
            $proposalResponse->modality_id = $request->post('modality_id');
            $proposalResponse->amount = $request->post('amount');
            $proposalResponse->rate = $request->post('rate');
            $proposalResponse->installment = $request->post('installment');
            $proposalResponse->fee = $request->post('fee');
            $proposalResponse->committee_response = $request->post('committee_response');
            $proposalResponse->disbursement_date = $request->post('disbursement_date', null);
            $proposalResponse->status = $request->post('status');
            $proposalResponse->observation = $request->post('status') === 'OBSERVADO' ? $request->post('observation') : null;
            $proposalResponse->save();

            $messageSuccess = 'Propuesta ' . str_pad($request->get('proposalId'), 2, '0', STR_PAD_LEFT) . ' a sido respondido.';
            unset($_POST);
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responder Propuesta</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
</head>

<body>
    <div class="max-w-4xl mx-auto px-4">
        <?php if ($messageError) { ?>
            <div class="text-red-500 font-semibold bg-red-50 p-3 border border-red-500 mt-10">
                <?php echo $messageError ?>
            </div>
        <?php } ?>

        <?php if ($messageSuccess) { ?>
            <div class="text-green-500 font-semibold bg-green-50 p-3 border border-green-500 mt-10">
                <?php echo $messageSuccess ?>
            </div>
        <?php } ?>

        <h1 class="text-4xl font-bold text-center mt-10">
            RESPONDER PROPUESTA NÚMERO <?php echo str_pad($request->get('proposalId'), 2, '0', STR_PAD_LEFT) ?>
        </h1>

        <form action="" method="post">
            <div class="mt-10">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                    <label class="block">
                        <span>Monto aprobado</span>
                        <input type="text" name="amount" id="form_amount" class="border-gray-300 rounded-md w-full" value="<?php echo round($proposal->amount, 2) ?>">
                    </label>
                    <label class="block">
                        <span>Tipo de prestamo</span>
                        <select name="credit_type_id" class="border-gray-300 rounded-md w-full">
                            <option value="">Seleccione una opción</option>
                            <?php foreach ($dictionariesProducts as $item) { ?>
                                <option value="<?php echo $item->id ?>" <?php echo $proposal->credit_type_id == $item->id ? 'selected' : '' ?>>
                                    <?php echo $item->name ?>
                                </option>
                            <?php } ?>
                        </select>
                    </label>
                    <label class="block">
                        <span>Tipo pago</span>
                        <select name="payment_type_id" class="border-gray-300 rounded-md w-full">
                            <option value="">Seleccione una opción</option>
                            <?php foreach ($dictionariesPayments as $dictionary) { ?>
                                <option value="<?php echo $dictionary->id ?>" <?php echo $proposal->payment_type_id == $dictionary->id ? 'selected' : '' ?>>
                                    <?php echo $dictionary->description ?>
                                </option>
                            <?php } ?>
                        </select>
                    </label>
                    <label class="block">
                        <span>Tasa</span>
                        <input type="text" name="rate" id="form_rate" class="border-gray-300 rounded-md w-full" value="<?php echo round($proposal->rate, 2) ?>">
                    </label>
                    <label class="block">
                        <span>Modalidad</span>
                        <select name="modality_id" class="border-gray-300 rounded-md w-full">
                            <option value="">Seleccione una opción</option>
                            <?php foreach ($dictionariesModalities as $dictionary) { ?>
                                <option value="<?php echo $dictionary->id ?>" <?php echo $proposal->modality_id == $dictionary->id ? 'selected' : '' ?>>
                                    <?php echo $dictionary->description ?>
                                </option>
                            <?php } ?>
                        </select>
                    </label>
                    <label class="block">
                        <span>Plazo</span>
                        <input type="text" name="installment" id="form_installment" class="border-gray-300 rounded-md w-full" value="<?php echo round($proposal->installment) ?>">
                    </label>
                    <label class="block">
                        <span>Cuota</span>
                        <input type="text" name="fee" id="form_fee" class="border-gray-300 rounded-md w-full" value="<?php echo round($proposal->fee, 2) ?>">
                    </label>
                    <label class="block">
                        <span>Fecha de desembolso</span>
                        <input type="date" name="disbursement_date" class="border-gray-300 rounded-md w-full">
                    </label>
                </div>

                <div class="mt-6">
                    <label>
                        <span>Propuesta del comité de crédito</span>
                        <textarea name="committee_response" rows="5" class="border-gray-300 rounded-md w-full"><?php echo $request->post('committee_response') ?></textarea>
                    </label>
                </div>

                <div class="mt-5">
                    <h4 class="text-2xl font-semibold">Condición de la propuesta</h4>
                    <div class="md:space-x-5 mt-5">
                        <label class="flex md:inline-flex items-center space-x-2">
                            <input type="radio" name="status" value="APROBADO">
                            <span>APROBADO</span>
                        </label>
                        <label class="flex md:inline-flex items-center space-x-2">
                            <input type="radio" name="status" value="DESAPROBADO">
                            <span>DESAPROBADO</span>
                        </label>
                        <label class="flex md:inline-flex items-center space-x-2">
                            <input type="radio" name="status" id="form_status_observado" value="OBSERVADO">
                            <span>OBSERVADO</span>
                        </label>
                    </div>
                    <div class="mt-5" id="container_observado" style="display: none;">
                        <textarea name="observation" cols="30" rows="2" class="border-gray-300 rounded-md w-full max-w-lg"><?php echo $request->post('observation') ?></textarea>
                    </div>
                </div>

                <?php if (count($errors) > 0) { ?>
                    <ul class="mt-10">
                        <?php foreach ($errors as $error) { ?>
                            <li class="text-red-500 font-medium text-sm">- <?php echo $error->getMessage() ?></li>
                        <?php } ?>
                    </ul>
                <?php } ?>

                <?php if ($_COOKIE['tuser'] == '1' || $_COOKIE['tuser'] == '2') { ?>
                    <div class="mt-8">
                        <button class="py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md w-full">Guardar respuesta</button>
                    </div>
                <?php } ?>
            </div>
        </form>
    </div>

    <script>
        const elements = document.getElementsByName('status');
        elements.forEach(element => {
            element.onchange = observado;
        });

        function observado(e) {
            const checkedValue = document.querySelector('input[name="status"]:checked').value;

            if (checkedValue === 'OBSERVADO') {
                document.getElementById('container_observado').style = 'display: block';
            } else {
                document.getElementById('container_observado').style = 'display: none';
            }

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