<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dictionary extends Model
{
    use SoftDeletes;

    protected $table = 'dictionaries';
    public $timestamps = false;

    // Mismo blindaje que Proposal (SoftDeletes + Carbon 2.58 + PHP 8.2).
    protected function asDateTime($value)
    {
        try {
            return parent::asDateTime($value);
        } catch (\Throwable $e) {
            error_log('Dictionary::asDateTime fecha inválida: ' . var_export($value, true));
            return new \DateTimeImmutable('1970-01-01 00:00:00');
        }
    }
}
