<?php

namespace CrediSoporte\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proposal extends Model
{
    use SoftDeletes;

    protected $table = 'proposals';

    public function response()
    {
        return $this->hasOne('CrediSoporte\Domain\Models\ProposalResponse');
    }
}
