<?php

namespace Hanafalah\ModuleTransaction\Models\Transaction;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Hanafalah\LaravelHasProps\Concerns\HasProps;
use Hanafalah\LaravelSupport\Models\BaseModel;
use Hanafalah\ModuleTransaction\Concerns\{
    HasInvoice
};
use Hanafalah\ModuleTransaction\Enums\Transaction\Status;
use Hanafalah\ModuleTransaction\Resources\Transaction\{
    ShowTransaction,
    ViewTransaction
};

class Transaction extends BaseModel
{
    use HasUlids, HasProps, HasInvoice, SoftDeletes;

    public $incrementing  = false;
    protected $keyType    = "string";
    protected $list       = [
        'id',
        'uuid',
        'transaction_code',
        'reference_type',
        'reference_id',
        'status',
        'created_at',
        'reported_at',
        'canceled_at'
    ];
    protected $show       = ['parent_id', 'invoice_id', 'props'];
    protected $primaryKey = 'id';

    protected static function booted(): void{
        parent::booted();
        static::creating(function ($query) {
            if (!isset($query->transaction_code)) {
                $query->transaction_code = static::hasEncoding('TRANSACTION');
            }
            $query->status = Status::ACTIVE->value;
        });
    }

    public function getViewResource(){
        return ViewTransaction::class;
    }

    public function getShowResource(){
        return ShowTransaction::class;
    }

    public function reference(){return $this->morphTo();}
    public function billing(){return $this->hasOneModel('Billing');}
    public function paymentHistory(){return $this->hasOneModel('PaymentHistory');}
    public function paymentSummary(){return $this->hasOneModel('PaymentSummary');}
    public function paymentSummaries(){return $this->hasManyModel('PaymentSummary');}
    public function transactionItem(){return $this->hasOneModel('TransactionItem');}
    public function transactionItems(){return $this->hasManyModel('TransactionItem');}
    public function voucherTransaction(){return $this->hasOneModel('VoucherTransaction', 'ref_transaction_id');}
    public function voucherTransactions(){return $this->hasManyModel('VoucherTransaction', 'ref_transaction_id');}
    public function transactionHasConsument(){return $this->hasOneModel('TransactionHasConsument');}
    public function consuments(){return $this->belongsToManyModel('Consument', 'TransactionHasConsument');}

    public function consument()
    {
        $consument_table           = $this->ConsumentModel()->getTable();
        $transaction_has_consument = $this->TransactionHasConsumentModel()->getTable();
        return $this->hasOneThroughModel(
            'Consument',
            'TransactionHasConsument',
            $this->getForeignKey(),
            $this->ConsumentModel()->getKeyName(),
            $this->getKeyName(),
            $this->ConsumentModel()->getForeignKey()
        )->select([
            "$consument_table.*",
            "$transaction_has_consument.*",
            "$consument_table.id as id"
        ]);
    }
}
