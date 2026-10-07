<?php
class Validator
{
    public static function required(array $input, array $fields): ?string
    {
        foreach ($fields as $f) {
            if (!isset($input[$f]) || $input[$f] === '' || $input[$f] === null) {
                return "El campo '{$f}' es obligatorio.";
            }
        }
        return null;
    }
}
