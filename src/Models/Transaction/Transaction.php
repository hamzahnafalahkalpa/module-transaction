<?php

namespace Hanafalah\ModuleTransaction\Models\Transaction;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Hanafalah\LaravelHasProps\Concerns\HasProps;
use Hanafalah\LaravelSupport\Models\BaseModel;
use Hanafalah\ModuleTransaction\Enums\Transaction\Status;
use Hanafalah\ModuleTransaction\Resources\Transaction\{
    ShowTransaction,
    ViewTransaction
};

class Transaction extends BaseModel
{
    use HasUlids, HasProps, SoftDeletes;

    public $incrementing  = false;
    protected $keyType    = "string";
    protected $list       = [
        'id',
        'uuid',
        'transaction_code',
        'reference_type',
        'reference_id',
        'status',
        'reported_at',
        'canceled_at'
    ];
    protected $show       = ['parent_id', 'props'];
    protected $primaryKey = 'id';
    protected $casts = [
        'reported_at' => 'datetime',
        'canceled_at' => 'datetime',
        'name'        => 'string'
    ];

    public function getPropsQuery(): array{
        return [
            'name' => 'props->prop_reference->name'
        ];
    }

    protected static function booted(): void{
        parent::booted();
        static::creating(function ($query) {
            if (!isset($query->transaction_code)) {
                $query->transaction_code = static::hasEncoding('TRANSACTION');
            }
            $query->status = Status::ACTIVE->value;
        });
    }

    public function viewUsingRelation(): array{
        return [];
    }

    public function showUsingRelation(): array{
        return ['reference'];
    }

    public function getViewResource(){
        return ViewTransaction::class;
    }

    public function getShowResource(){
        return ShowTransaction::class;
    }

    public function reference(){return $this->morphTo();}    
    public function transactionHasConsument(){return $this->hasOneModel('TransactionHasConsument');}
    public function consument(){
        $consument_model = $this->ConsumentModel();
        $transaction_consument = $this->TransactionHasConsumentModel();
        return $this->hasOneThroughModel(
            'Consument',
            'TransactionHasConsument',
            $this->getForeignKey(),
            $consument_model->getKeyName(),
            $this->getKeyName(),
            $consument_model->getForeignKey()
        )->select($transaction_consument->getTable().'.*',$consument_model->getTable().'.*', $consument_model->getTable().'.id as id');
    }
}
