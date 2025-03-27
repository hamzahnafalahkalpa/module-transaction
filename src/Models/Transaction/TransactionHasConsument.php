<?php

namespace Hanafalah\ModuleTransaction\Models\Transaction;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Hanafalah\LaravelSupport\Models\BaseModel;

class TransactionHasConsument extends BaseModel
{
    use HasUlids;

    protected $list = ['id', 'transaction_id', 'consument_id'];

    public $incrementing  = false;
    protected $primaryKey = 'id';
    protected $keyType    = 'string';

    public function transaction(){return $this->belongsToModel('Transaction');}
    public function consument(){return $this->belongsToModel('Consument');}
}
