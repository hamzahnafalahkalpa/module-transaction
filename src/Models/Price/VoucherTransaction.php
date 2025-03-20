<?php

namespace Hanafalah\ModuleTransaction\Models\Price;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Hanafalah\LaravelHasProps\Concerns\HasProps;
use Hanafalah\LaravelSupport\Models\BaseModel;
use Hanafalah\ModuleTransaction\Concerns\HasTransaction;
use Hanafalah\ModuleTransaction\Resources\VoucherTransaction\{
    ViewVoucherTransaction,
    ShowVoucherTransaction
};

class VoucherTransaction extends BaseModel
{
    use HasUlids, HasProps, HasTransaction;

    const STATUS_DRAFT = 'DRAFT';
    const STATUS_CLAIM = 'CLAIMED';

    public $incrementing  = false;
    protected $keyType    = 'string';
    protected $primaryKey = 'id';
    protected $list       = [
        'id',
        'name',
        'voucher_id',
        'consument_type',
        'consument_id',
        'payment_history_id',
        'ref_transaction_id',
        'reported_at',
        'status',
        'props'
    ];

    protected static function booted(): void
    {
        parent::booted();
        static::creating(function ($query) {
            $query->status = self::STATUS_DRAFT;
        });
        static::updating(function ($query) {
            if ($query->isDirty('reported_at')) $query->status = self::STATUS_CLAIM;
        });
    }

    public function toViewApi()
    {
        return new ViewVoucherTransaction($this);
    }

    public function toShowApi()
    {
        return new ShowVoucherTransaction($this);
    }

    public function consument()
    {
        return $this->morphTo();
    }
    public function voucher()
    {
        return $this->belongsToModel('Voucher');
    }
    public function paymentHistory()
    {
        return $this->belongsToModel('PaymentHistory');
    }
    public function referenceTransaction()
    {
        return $this->belongsToModel('Transaction', 'ref_transaction_id');
    }
}
