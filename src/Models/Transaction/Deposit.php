<?php

namespace Zahzah\ModuleTransaction\Models\Transaction;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Zahzah\LaravelHasProps\Concerns\HasProps;
use Zahzah\LaravelSupport\Models\BaseModel;
use Zahzah\ModuleTransaction\Resources\Deposit\ShowDeposit;
use Zahzah\ModuleTransaction\Resources\Deposit\ViewDeposit;

class Deposit extends BaseModel{
    use HasUlids, HasProps, SoftDeletes;

    public $incrementing = false;
    protected $keyType   = "string";
    protected $primaryKey = 'id';
    protected $list      = ['id','reference_type','reference_id','reported_at','total','props'];
    protected $show      = [];

    public $casts = [
        'reported_at' => 'datetime',
    ];

    protected static function booted(): void{
        parent::booted();
        static::creating(function($query){
            if (!isset($query->deposit_code)){
                $query->deposit_code = static::hasEncoding('DEPOSIT'); 
            }
        });
    }

    public function toShowApi(){
        return new ShowDeposit($this);
    }

    public function toViewApi(){
        return new ViewDeposit($this);
    }

    public function reference(){return $this->morphTo();}
}
