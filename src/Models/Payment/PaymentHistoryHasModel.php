<?php

namespace Hanafalah\ModuleTransaction\Models\Payment;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Hanafalah\LaravelSupport\Models\BaseModel;

class PaymentHistoryHasModel extends BaseModel
{
    use HasUlids;

    public $incrementing   = false;
    protected $keyType     = 'string';
    protected $primaryKey  = 'id';

    protected $list        = [
        'id',
        'payment_history_id',
        'model_type',
        'model_id'
    ];

    public function paymentHistory()
    {
        return $this->belongsToModel('PaymentHistory');
    }
    public function model()
    {
        return $this->morphTo();
    }
    public function paymentSummary()
    {
        return $this->morphOneModel('PaymentSummary', 'reference');
    }
}
