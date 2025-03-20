<?php

namespace Zahzah\ModuleTransaction\Models\Price;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Zahzah\LaravelHasProps\Concerns\HasProps;
use Zahzah\LaravelSupport\Models\BaseModel;

class ModelHasVoucher extends BaseModel {
    use HasUlids,HasProps;

    public $incrementing  = false;
    protected $keyType    = 'string';
    protected $primaryKey = 'id';
    protected $list       = [
        'id', 'model_id', 'model_type', 'voucher_id','props'
    ];
    protected static function booted(): void{
        parent::booted();
    }

    public function reference(){return $this->morphTo();}
    public function voucher(){return $this->belongsToModel("Voucher");}
}
