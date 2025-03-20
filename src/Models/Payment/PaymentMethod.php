<?php

namespace Zahzah\ModuleTransaction\Models\Payment;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Zahzah\LaravelHasProps\Concerns\HasProps;
use Zahzah\LaravelSupport\Models\BaseModel;
use Zahzah\ModuleTransaction\Concerns\HasInvoice;
use Zahzah\ModuleTransaction\Resources\PaymentMethod\{
    ShowPaymentMethod, ViewPaymentMethod
};

class PaymentMethod extends BaseModel{
    use HasUlids, HasProps, SoftDeletes;

    const PAYMENT_METHOD_DEFERRED = "DEFERRED";

    public $incrementing  = false;
    protected $keyType    = "string";
    protected $primaryKey = 'id';
    protected $list       = ['id','name','props'];

    protected static function booted(): void{
        parent::booted();
    }

    public function toShowApi(){
        return new ShowPaymentMethod($this);
    }

    public function toViewApi(){
        return new ViewPaymentMethod($this);
    }

}