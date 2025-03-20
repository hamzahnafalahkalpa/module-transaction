<?php

namespace Zahzah\ModuleTransaction\Models\Transaction;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Zahzah\LaravelHasProps\Concerns\HasProps;
use Zahzah\LaravelSupport\Models\BaseModel;
use Zahzah\ModuleTransaction\Resources\Billing\{
    ShowBilling, ViewBilling
};
use Illuminate\Support\Str;
use Zahzah\ModuleTransaction\Concerns\HasTransaction;
use Zahzah\ModuleTransaction\Enums\Billing\Status;

class Billing extends BaseModel{
    use HasUlids, HasProps, HasTransaction;

    public $incrementing  = false;
    protected $keyType    = "string";
    protected $primaryKey = 'id';
    protected $list       = [
        'id','uuid','billing_code','transaction_id',
        'author_type','author_id',
        'cashier_type','cashier_id',
        'status','reported_at'
    ];
    protected $show  = [];

    protected $casts = [
        'billing_code' => 'string',
    ];

    protected static function booted(): void{
        parent::booted();
        static::creating(function($query){
            if (!isset($query->billing_code)){
                $query->billing_code = static::hasEncoding('BILLING');
            }
            if (!isset($query->uuid)){
                $query->uuid = Str::orderedUuid();
            }
            if (!isset($query->status)) $query->status = Status::DRAFT->value;
        });
    }

    public function toShowApi(){
        return new ShowBilling($this);
    }

    public function toViewApi(){
        return new ViewBilling($this);
    }

    public function reference(){return $this->morphTo();}
    public function paymentHistory(){return $this->morphOneModel('PaymentHistory','reference');}
    public function paymentHistories(){return $this->morphManyModel('PaymentHistory','reference');}
    public function cashier(){return $this->morphTo();}
    public function author(){return $this->morphTo();}
    public function hasTransaction() {return $this->belongsToModel("Transaction");}
    public function splitBill() {return $this->hasOneModel("SplitBill");}
    public function splitBills() {return $this->hasManyModel("SplitBill");}

}
