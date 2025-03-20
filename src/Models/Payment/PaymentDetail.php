<?php

namespace Zahzah\ModuleTransaction\Models\Payment;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Zahzah\LaravelHasProps\Concerns\HasProps;
use Zahzah\LaravelSupport\Models\BaseModel;
use Zahzah\ModuleTransaction\Concerns\HasInvoice;
use Zahzah\ModuleTransaction\Resources\PaymentDetail\{
    ShowPaymentDetail, ViewPaymentDetail
};

class PaymentDetail extends BaseModel{
    use HasUlids, HasProps, SoftDeletes, HasInvoice;

    public $incrementing  = false;
    protected $keyType    = "string";
    protected $primaryKey = 'id';
    protected $list       = ['id','payment_summary_id','qty','payment_history_id','transaction_item_id','is_loan'];
    protected $show       = ['parent_id','cogs','amount','debt','price','discount','paid','tax','additional','refund_item_id'];

    protected static function booted(): void{
        parent::booted();
        static::created(function($query){
            if (!$query->isDirty('payment_history_id')){
                static::recalculating($query);
            }
            static::setSettled($query);
        });
        static::updated(function($query){
            if ($query->isDirty('debt') || $query->isDirty('payment_history_id') || $query->isDirty('payment_summary_id')){
                static::recalculating($query);
            }
            static::setSettled($query);
        });
        static::deleted(function($query){
            static::recalculating($query,true);
        });
    }

    private static function setSettled($query){
        $settled = $query->debt == 0;
        $transaction_item = $query->transactionItem;
        $item = $transaction_item->item;
        if(isset($item)) {
            if (static::isInArray('is_settled',$item) || static::isInArray('props',$item)){
                $item->is_settled = $settled;
                $item->save();
            }
        }
    }

    private static function isInArray(string $column, Model $model){
        return in_array($column,$model->getFillable());
    }

    private static function calculateCurrent($query,$field,$is_deleting = false){
        $value = $query->{$field};
        if ($is_deleting) {
            $value *= -1;
        }else{
            $original_value = $query->getOriginal($field) ?? 0;
        }
        return ($value ?? 0) - ($original_value ?? 0);
    }

    protected static function recalculating($query,$is_deleting = false,$to_debt = true){
        $prefix = '';
        $update_transaction_total_debt = false;
        if (!$to_debt){
            $query->load('paymentHistory');
            $model = $query->paymentHistory;
            $prefix = 'total_';
        }else{
            $query->load('parent');
            $parent_payment_detail = $query->parent;
            if (isset($parent_payment_detail)){
                $model = $parent_payment_detail;
            }else{
                $query->load('paymentSummary');
                $model = $query->paymentSummary;
                $prefix = 'total_';
                $update_transaction_total_debt = true;
            }
        }
        $rate_names = ['debt','amount','paid','discount'];
        foreach ($rate_names as $rate_name) {
            if (!$to_debt || $query->isDirty('payment_summary_id')){
                $value = $query->{$rate_name};
            }else{
                $value = static::calculateCurrent($query,$rate_name,$is_deleting);
            }
            $model->{$prefix.$rate_name} += $value;
        }
        $model->save();
        if ($update_transaction_total_debt){
            $query->load([
                'transactionItem'=>fn($q)=>$q->withoutGlobalScopes()
            ]);
            $transaction_item          = $query->transactionItem;
            $transaction               = $transaction_item->transaction;
            $transaction->total_debt ??= 0;
            $transaction->total_debt  += static::calculateCurrent($query,'debt',$is_deleting);
            $transaction->is_settled   = $transaction->total_debt == 0;
            $transaction->save();
        }
        if ($to_debt && $query->isDirty('payment_history_id')){
            self::recalculating($query,false,false);
        }
    }

    public function toShowApi(){
        return new ShowPaymentDetail($this);
    }

    public function toViewApi(){
        return new ViewPaymentDetail($this);
    }

    public function scopeDebtNotZero($builder){
        return $builder->gt('debt',0);
    }

    public function paymentSummary(){return $this->belongsToModel('PaymentSummary');}
    public function transactionItem(){return $this->belongsToModel('TransactionItem');}
    public function paymentHistory(){return $this->belongsToModel('PaymentHistory');}
    public function recursiveParent(){return $this->parent()->with('recursiveParent');}
    public function refundItem(){return $this->hasOneModel('RefundItem');}
}
