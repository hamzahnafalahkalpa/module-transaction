<?php

namespace Hanafalah\ModuleTransaction\Models\Transaction;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Hanafalah\LaravelHasProps\Concerns\HasProps;
use Hanafalah\LaravelSupport\Models\BaseModel;

class TransactionItem extends BaseModel
{
    use HasUlids, HasProps, SoftDeletes;

    public $incrementing  = false;
    protected $keyType    = "string";
    protected $primaryKey = 'id';
    protected $list       = ['id', 'transaction_id', 'item_type', 'item_id', 'item_name'];
    protected $show       = ['parent_id'];

    protected static function booted(): void
    {
        parent::booted();
        static::created(function ($query) {
            $item = $query->item;
            if (
                static::isInArray('is_settled', $item) ||
                static::isInArray('props', $item)
            ) {
                $item->is_settled = false;
                $item->save();
            }
        });
        static::deleted(function ($query) {
            if ($this->PaymentDetailModel() !== null){
                $query->paymentDetail->delete();
            }
        });
    }

    private static function isInArray(string $column, Model $model){
        return in_array($column, $model->getFillable());
    }

    public function reference(){return $this->morphTo();}
    public function transaction(){return $this->belongsToModel('Transaction');}
    public function item(){return $this->morphTo();}
    public function paymentDetail(){return $this->hasOneModel('PaymentDetail');}
}
