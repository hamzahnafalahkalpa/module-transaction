<?php

namespace Zahzah\ModuleTransaction\Models\Price;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Zahzah\LaravelSupport\Models\BaseModel;
use Zahzah\LaravelHasProps\Concerns\HasProps;
use Zahzah\ModuleTransaction\Resources\ComponentDetail\ViewComponentDetail;
use Zahzah\ModuleTransaction\Resources\TariffComponent\ShowComponentDetail;

class ComponentDetail extends BaseModel{
    
    use HasUlids, HasProps;

    public $timestamps    = false;
    public $incrementing  = false;
    protected $primaryKey = 'id';
    protected $keyType    = 'string';

    protected $fillable = ['id','reference_type','reference_id','flag'];

    protected $casts = [
        'name' => 'string'
    ];

    public function toViewApi(){
        return new ViewComponentDetail($this);
    }

    public function toShowApi(){
        return new ShowComponentDetail($this);
    }

    public function reference(){return $this->morphTo();}
}