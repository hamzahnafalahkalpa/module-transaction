<?php

namespace Zahzah\ModuleTransaction\Models\Payment;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Zahzah\LaravelHasProps\Concerns\HasProps;
use Zahzah\LaravelSupport\Models\BaseModel;
use Zahzah\ModuleTransaction\Resources\PaymentSummary\ShowPaymentSummary;
use Zahzah\ModuleTransaction\Resources\PaymentSummary\ViewPaymentSummary;

class PaymentSummary extends BaseModel{
    use HasUlids, HasProps, SoftDeletes;

    public $incrementing  = false;
    protected $keyType    = "string";
    protected $primaryKey = 'id';
    protected $list       = ['id','transaction_id','reference_type','total_amount','total_cogs','total_discount','total_debt','props'];
    protected $show       = ['parent_id','reference_id','total_tax','total_paid','total_additional'];

    protected $casts = [
        'generated_at'     => 'datetime',
    ];

    public function getPropsQuery(): array{
        return [
            'generated_at'     => 'created_at',
        ];
    }

    protected static function booted(): void{
        parent::booted();
        static::created(function($query){
            static::recalculating($query);
        });
        static::updated(function($query){
            if ($query->isDirty('total_debt') || $query->isDirty('total_amount') || $query->isDirty('parent_id')) {
                static::recalculating($query);
            }
        });
        static::deleted(function($query){
            static::recalculating($query,true);
        });
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

    protected static function recalculating($query,$is_deleting = false, $is_update_parent = true){
        if ($query->isDirty('parent_id') && !$is_update_parent){
            $previous_parent = $query->parent;
            $is_deleting = true;
        }else{
            if(!$query->isDirty('parent_id')) {
                $query->load('parent');
            }
        }
        $parent_payment_summary = $query->parent;
        if (isset($parent_payment_summary)){
            $rate_names = ['total_debt','total_amount','total_tax','total_additional','total_discount','total_cogs'];
            foreach ($rate_names as $rate_name) {
                if (!$is_update_parent){
                    $parent_payment_summary->{$rate_name} += $query->{$rate_name};
                }else{
                    $parent_payment_summary->{$rate_name} += static::calculateCurrent($query,$rate_name,$is_deleting);
                }
            }
            $parent_payment_summary->save();
        }
        if (isset($previous_parent)){
            $query->load('parent');
            self::recalculating($query,false,false);
        }
    }

    public function toShowApi(){
        return new ShowPaymentSummary($this);
    }

    public function toViewApi(){
        return new ViewPaymentSummary($this);
    }

    public function scopeDebtNotZero($builder){
        return $builder->gt('total_debt',0);
    }

    public function reference(){return $this->morphTo();}
    public function tariffComponent(){return $this->belongsToModel('TariffComponent');}
    public function paymentDetail(){return $this->hasOneModel('PaymentDetail');}
    public function paymentDetails(){return $this->hasManyModel('PaymentDetail');}
    public function transaction(){return $this->belongsToModel('Transaction');}
    public function recursiveInvoiceChilds(){return $this->hasManyModel('PaymentSummary','parent_id')->with(['paymentDetails.transactionItem','recursiveInvoiceChilds'])->where('total_amount','>',0);}
    public function recursiveChilds(){
        return $this->hasManyModel('PaymentSummary','parent_id')->debtNotZero()
            ->with([
                'paymentDetails' => function($query){
                    $query->with('transactionItem')->debtNotZero();
                },'recursiveChilds'
            ]);

    }
    public function recursiveParent(){return $this->belongsToModel('PaymentSummary','parent_id')->with('recursiveParent');}
    public function paymentHistoryHasModel(){return $this->morphOneModel('PaymentHistoryHasModel','model');}
    public function transactionItem(){return $this->morphOneModel('TransactionItem','item');}
}
