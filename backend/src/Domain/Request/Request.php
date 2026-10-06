<?php

namespace CrediSoporte\Domain\Request;

use CrediSoporte\Domain\Models\User;

class Request
{
    protected $user = null;

    public function __construct()
    {
        $json_str = file_get_contents('php://input');
        $json_obj = json_decode($json_str);

        if (gettype($json_obj) === 'object') {
            foreach ($json_obj as $key => $value) {
                if ($value) {
                    $this->$key = $value;
                } else {
                    $this->$key = null;
                }
            }
        }

        foreach ($_REQUEST as $key => $value) {
            if ($value) {
                $this->$key = $value;
            } else {
                $this->$key = null;
            }
        }
    }

    public function __get($atributo)
    {

        /* confirmaremos si un atributo existe al momento de querer acceder al mismo */
        if (property_exists($this, $atributo)) {
            return $this->$atributo; /* si existe regresara el valor que tenga guardado en dicho atributo/propiedad */
        } else {
            return null; /* y si no existe solo regresara un mensaje indicandolo */
        }
    }

    /* para la funcion __set() es necesario darle 2 parametros, el atributo que queremos modificar o crear,
      y el valor que va a recibir dicho atributo */
    public function __set($atributo, $valor)
    {
        $this->$atributo = $valor;
    }

    public function validate(array $rules, $messages = [], $helps = [])
    {
        $errors = [];
        $rulesAll = ['nullable', 'required', 'numeric', 'min', 'max', 'digits_between', 'digits', 'unique', 'in', 'exists', 'date'];

        foreach ($rules as $attribute => $value) {
            $r = explode('|', $value);
            $errs = [];

            foreach ($r as $rule) {

                if (count($errs) > 0) {
                    break;
                }

                $exists = false;
                foreach ($rulesAll as $ruleAllItem) {
                    if (!$exists && strpos($rule, $ruleAllItem) === 0) {
                        $messageKey = $attribute . '.' . $ruleAllItem;
                        $exists = true;

                        switch ($ruleAllItem) {
                            case 'required':
                                if (!isset($this->$attribute) || empty($this->$attribute)) {
                                    $messageText = 'El campo :attribute es obligatorio.';

                                    if (isset($messages[$messageKey])) {
                                        array_push($errs, str_replace([':attribute'], [$attribute], $messages[$messageKey]));
                                    } else {
                                        array_push($errs, str_replace([':attribute'], [$attribute], $messageText));
                                    }
                                }
                                break;

                            case 'min':
                                $a = explode(':', $rule);

                                if (
                                    !in_array("string", $r) &&
                                    is_numeric($this->$attribute) &&
                                    (float) $this->$attribute < $a[1]
                                ) {

                                    if (isset($messages[$messageKey])) {
                                        array_push($errs, str_replace([':attribute', ':min'], [$attribute, $a[1]], $messages[$messageKey]));
                                    } else {
                                        $messageText = 'El campo :attribute debe ser al menos :min.';
                                        array_push($errs, str_replace([':attribute', ':min'], [$attribute, $a[1]], $messageText));
                                    }
                                } else if (
                                    gettype($this->$attribute) === 'string' &&
                                    strlen($this->$attribute) < $a[1]
                                ) {

                                    if (isset($messages[$messageKey])) {
                                        array_push($errs, str_replace([':attribute'], [$attribute], $messages[$messageKey]));
                                    } else {
                                        $messageText = 'El campo :attribute debe tener al menos :min caracteres.';
                                        array_push($errs, str_replace([':attribute', ':min'], [$attribute, $a[1]], $messageText));
                                    }
                                }

                                break;

                            case 'max':
                                $a = explode(':', $rule);

                                if (
                                    !in_array("string", $r) &&
                                    is_numeric($this->$attribute) &&
                                    (float) $this->$attribute > $a[1]
                                ) {
                                    if (isset($messages[$messageKey])) {
                                        array_push($errs, str_replace([':attribute', ':max'], [$attribute, $a[1]], $messages[$messageKey]));
                                    } else {
                                        $messageText = 'El campo :attribute no debe ser mayor a :max.';
                                        array_push($errs, str_replace([':attribute', ':max'], [$attribute, $a[1]], $messageText));
                                    }
                                } else if (
                                    gettype($this->$attribute) === 'string' &&
                                    strlen($this->$attribute) > $a[1]
                                ) {
                                    if (isset($messages[$messageKey])) {
                                        array_push($errs, str_replace([':attribute', ':max'], [$attribute, $a[1]], $messages[$messageKey]));
                                    } else {
                                        $messageText = 'El campo :attribute no debe tener más de :max caracteres.';
                                        array_push($errs, str_replace([':attribute', ':max'], [$attribute, $a[1]], $messageText));
                                    }
                                }

                                break;

                            case 'digits':
                                $a = explode(':', $rule);

                                if (strlen($this->$attribute) != $a[1]) {
                                    if (isset($messages[$messageKey])) {
                                        array_push($errs, str_replace([':attribute', ':digits'], [$attribute, $a[1]], $messages[$messageKey]));
                                    } else {
                                        $messageText = 'El campo :attribute debe tener :digits digitos.';
                                        array_push($errs, str_replace([':attribute', ':digits'], [$attribute, $a[1]], $messageText));
                                    }
                                }

                                break;

                            case 'unique':

                                if ($helps[$attribute . '.' . $rule]) {
                                    if (isset($messages[$messageKey])) {
                                        array_push($errs, str_replace([':attribute'], [$attribute], $messages[$messageKey]));
                                    } else {
                                        $messageText = 'El campo :attribute ya está tomado.';
                                        array_push($errs, str_replace([':attribute'], [$attribute], $messageText));
                                    }
                                }

                                break;

                            case 'nullable':
                                if (!isset($this->$attribute) || empty($this->$attribute)) {
                                    break 3;
                                }

                                break;

                            case 'in':
                                $a = explode(':', $rule);
                                $b = explode(',', $a[1]);

                                if (!in_array($this->$attribute, $b)) {
                                    if (isset($messages[$messageKey])) {
                                        array_push($errs, str_replace([':attribute'], [$attribute], $messages[$messageKey]));
                                    } else {
                                        $messageText = 'El valor seleccionado para el campo :attribute no es válido.';
                                        array_push($errs, str_replace([':attribute'], [$attribute], $messageText));
                                    }
                                }

                                break;

                            case 'exists':
                                if (!$helps[$attribute . '.' . $rule]) {
                                    if (isset($messages[$messageKey])) {
                                        array_push($errs, str_replace([':attribute'], [$attribute], $messages[$messageKey]));
                                    } else {
                                        $messageText = 'El valor seleccionado para el campo :attribute no es válido.';
                                        array_push($errs, str_replace([':attribute'], [$attribute], $messageText));
                                    }
                                }

                                break;

                            case 'digits_between':
                                $a = explode(':', $rule);
                                $minmax = explode(',', $a[1]);
                                $size = strlen($this->$attribute);

                                if ($size < $minmax[0] || $size > $minmax[1]) {
                                    if (isset($messages[$messageKey])) {
                                        array_push($errs, str_replace([':attribute', ':min', ':max'], [$attribute, $minmax[1], $minmax[1]], $messages[$messageKey]));
                                    } else {
                                        $messageText = 'El campo :attribute debe tener entre :min y :max dígitos.';
                                        array_push($errs, str_replace([':attribute', ':min', ':max'], [$attribute, $minmax[0], $minmax[1]], $messageText));
                                    }
                                }
                                break;

                            case 'numeric':
                                if (!is_numeric($this->$attribute)) {
                                    if (isset($messages[$messageKey])) {
                                        array_push($errs, str_replace([':attribute'], [$attribute], $messages[$messageKey]));
                                    } else {
                                        $messageText = 'El campo :attribute debe ser númerico.';
                                        array_push($errs, str_replace([':attribute'], [$attribute], $messageText));
                                    }
                                }

                                break;

                            case 'date':
                                $dateToValidate = explode('-', $this->$attribute);

                                if (
                                    count($dateToValidate) != 3 ||
                                    !checkdate(
                                        (int) ($dateToValidate[1]), // month
                                        (int) ($dateToValidate[2]), // day
                                        (int) ($dateToValidate[0]) // year
                                    )
                                ) {
                                    if (isset($messages[$messageKey])) {
                                        array_push($errs, str_replace([':attribute'], [$attribute], $messages[$messageKey]));
                                    } else {
                                        $messageText = 'El campo :attribute no es una fecha válida.';
                                        array_push($errs, str_replace([':attribute'], [$attribute], $messageText));
                                    }
                                }

                                break;
                        }
                    }
                }
            }

            if (count($errs) > 0) {
                $errors[$attribute] = $errs;
            }
        }

        return $errors;
    }

    public function only($attributes = [])
    {
        $arrs = [];
        foreach ($attributes as $attribute) {
            if (!isset($this->$attribute)) {
                $arrs[$attribute] = null;
            } else {
                $arrs[$attribute] = $this->$attribute;
            }
        }


        return $arrs;
    }

    public function post($nameAttribute, $defautValue = '')
    {
        if (isset($_POST[$nameAttribute]) && !empty($_POST[$nameAttribute])) {
            return $_POST[$nameAttribute];
        }

        return $defautValue;
    }

    public function get($nameAttribute, $defautValue = '')
    {
        if (isset($_GET[$nameAttribute]) && !empty($_GET[$nameAttribute])) {
            return $_GET[$nameAttribute];
        }

        return $defautValue;
    }

    public function old($value)
    {
        if (isset($_SESSION['flash']['inputs'][$value])) {
            return $_SESSION['flash']['inputs'][$value];
        }

        return '';
    }

    public function user()
    {

        if (isset($_COOKIE['user1']) && is_null($this->user)) {
            $this->user = User::where('idU', $_COOKIE['user1'])
                ->where('estadoU', '1')
                ->first();
        }

        return $this->user;
    }
}
