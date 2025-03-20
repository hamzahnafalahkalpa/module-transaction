<?php

namespace Hanafalah\ModuleTransaction\Models\Payment;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Hanafalah\LaravelHasProps\Concerns\HasProps;
use Hanafalah\LaravelSupport\Models\BaseModel;
use Hanafalah\ModuleTransaction\Concerns\HasInvoice;
use Hanafalah\ModuleTransaction\Resources\PaymentMethod\{
    ShowPaymentMethod,
    ViewPaymentMethod
};

class PaymentMethod extends BaseModel
{
    use HasUlids, HasProps, SoftDeletes;

    const PAYMENT_METHOD_DEFERRED = "DEFERRED";

    public $incrementing  = false;
    protected $keyType    = "string";
    protected $primaryKey = 'id';
    protected $list       = ['id', 'name', 'props'];

    protected static function booted(): void
    {
        parent::booted();
    }

    public function toShowApi()
    {
        return new ShowPaymentMethod($this);
    }

    public function toViewApi()
    {
        return new ViewPaymentMethod($this);
    }
}
