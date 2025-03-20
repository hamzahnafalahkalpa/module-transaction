<?php

namespace Hanafalah\ModuleTransaction\Models\Transaction;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Hanafalah\LaravelHasProps\Concerns\HasProps;
use Hanafalah\LaravelSupport\Models\BaseModel;
use Hanafalah\ModuleTransaction\Concerns\HasPaymentSummary;
use Hanafalah\ModuleTransaction\Concerns\HasTransaction;
use Hanafalah\ModuleTransaction\Resources\Invoice\ShowInvoice;
use Hanafalah\ModuleTransaction\Resources\Invoice\ViewInvoice;

class Invoice extends BaseModel
{
    use HasUlids, HasProps, HasPaymentSummary, HasTransaction;

    public $incrementing  = false;
    protected $primaryKey = 'id';
    protected $keyType    = "string";
    protected $list       = [
        'id',
        'author_id',
        'author_type',
        'consument_id',
        'consument_type',
        'paid_at',
        'generated_at',
        'billing_at',
        'props'
    ];
    protected $show  = ['invoice_code'];

    protected static function booted(): void
    {
        parent::booted();
        static::addGlobalScope('transaction_billing_deferred', function ($query) {
            $query->with('transactionBillingDeferred');
        });
        static::creating(function ($query) {
            if (!isset($query->generated_at)) $query->generated_at = now();
            if (!isset($query->invoice_code))  $query->invoice_code = static::hasEncoding('INVOICE');
        });
        static::created(function ($query) {
            $transaction                 = $query->transaction;
            $consument                   = $query->consument;
            $transaction->consument_name = $consument->name ?? '-';
        });
        static::updating(function ($query) {
            if ($query->isDirty('billing_at')) {
                if (!isset($query->invoice_code)) {
                    //                    $query->invoice_code = static::hasEncoding('INVOICE');
                }
            }
        });
        static::updated(function ($query) {
            if ($query->isDirty('billing_at')) {
                $consument = $query->consument;
                $consument->invoice()->firstOrCreate();

                $billing_deferred = app(config('database.models.BillingDeferred'))->find($query->getKey());
                $transaction      = $billing_deferred->transaction()->firstOrCreate([
                    'parent_id'   => $query->transaction->getKey()
                ]);
                $payment_summary  = $billing_deferred->paymentSummary()->firstOrCreate();
                $payment_summary->transaction_id = $transaction->getKey();
                $payment_summary->save();
            }
        });
    }

    public function toShowApi()
    {
        return new ShowInvoice($this);
    }

    public function toViewApi()
    {
        return new ViewInvoice($this);
    }

    public function scopeDraft($builder)
    {
        return $builder->whereNull('billing_at');
    }
    public function scopeDeferred($builder)
    {
        return $builder->whereNotNull('billing_at')->whereNull('paid_at');
    }
    public function scopePaid($builder)
    {
        return $builder->whereNotNull('paid_at');
    }

    public function author()
    {
        return $this->morphTo('author');
    }
    public function consument()
    {
        return $this->morphTo('consument');
    }
    public function paymentSummary()
    {
        return $this->morphOneModel('PaymentSummary', 'reference');
    }
    public function listPaymentSummary()
    {
        return $this->paymentSummary()->with('childs');
    }
    //    public function transaction(){return $this->hasOneModel('Transaction');}
    //    public function transactions(){return $this->hasManyModel('Transaction');}
    public function transactionBillingDeferred()
    {
        return $this->hasOneModel('Transaction', 'reference_id', 'id')->where('reference_type', $this->BillingDeferredModelMorph());
    }
}
