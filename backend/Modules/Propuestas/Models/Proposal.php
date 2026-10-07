<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proposal extends Model
{
    use SoftDeletes;

    protected $table = 'proposals';

    public function response()
    {
        return $this->hasOne('App\Models\ProposalResponse');
    }

    // Blindaje Carbon 2.58 + PHP 8.2 (sin composer para actualizar):
    // una fecha malformada en BD tumbaba la API con 500. Se registra y se
    // devuelve época en vez de romper la serialización.
    protected function asDateTime($value)
    {
        try {
            return parent::asDateTime($value);
        } catch (\Throwable $e) {
            error_log('Proposal::asDateTime fecha inválida: ' . var_export($value, true));
            return new \DateTimeImmutable('1970-01-01 00:00:00');
        }
    }
}
