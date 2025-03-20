<?php

namespace Hanafalah\ModuleTransaction\Models\Transaction;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Hanafalah\LaravelHasProps\Concerns\HasProps;
use Hanafalah\LaravelSupport\Models\BaseModel;
use Hanafalah\ModuleTransaction\Concerns\HasInvoice;
use Hanafalah\ModuleTransaction\Concerns\HasTransaction;
use Hanafalah\ModuleTransaction\Resources\SplitBill\{
    ViewSplitBill
};

class SplitBill extends BaseModel
{
    use HasUlids, HasProps, HasInvoice, SoftDeletes, HasTransaction;

    public $incrementing = false;
    protected $keyType   = "string";
    protected $primaryKey = 'id';
    protected $list      = [
        'id',
        'payment_method',
        'billing_id',
        'total_paid',
        'invoice_id',
        'payer_id',
        'payer_type'
    ];
    protected $show      = [];

    protected static function booted(): void
    {
        parent::booted();
        static::creating(function ($query) {
            if (!isset($query->split_bill_code)) {
                $query->split_bill_code = static::hasEncoding('SPLIT_BILL');
            }
        });
    }

    public function toShowApi()
    {
        return new ViewSplitBill($this);
    }

    public function toViewApi()
    {
        return new ViewSplitBill($this);
    }

    public function getPaymentDetails()
    {
        $payment_history = $this->paymentHistory;

        $transaction_id = $payment_history->transaction_id;

        return $this->PaymentDetailModel()
            ->select('qty', 'amount', 'debt', 'tax', 'additional', 'props', 'transaction_item_id', 'id')
            ->with([
                'transactionItem:id,item_name',
                'transactionItem.item'
            ])
            ->whereHas('paymentHistory', function ($query) use ($transaction_id) {
                $query->where('transaction_id', $transaction_id);
            })->get()
            ->each(function ($data) {
                $data->transactionItem->item = $data->transactionItem->item ?? null;
            });
    }

    public function billing()
    {
        return $this->belongsToModel('Billing');
    }
    public function payer()
    {
        return $this->morphTo();
    }
    public function paymentSummary()
    {
        return $this->morphOneModel('PaymentSummary', 'reference', 'reference_type', 'reference_id', 'id');
    } //will delete soon
    public function paymentHistory()
    {
        return $this->morphOneModel('PaymentHistory', 'reference', 'reference_type', 'reference_id', 'id');
    }
}
