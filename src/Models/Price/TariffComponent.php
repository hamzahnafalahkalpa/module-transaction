<?php

namespace Zahzah\ModuleTransaction\Models\Price;

use Zahzah\LaravelSupport\Models\BaseModel;
use Zahzah\ModuleTransaction\Enums\TariffComponent\Flag;
use Zahzah\ModuleTransaction\Resources\TariffComponent\ShowTariffComponent;
use Zahzah\ModuleTransaction\Resources\TariffComponent\ViewTariffComponent;

class TariffComponent extends BaseModel{
    protected $fillable = ['id','name'];

    protected $casts = [
        'name' => 'string'
    ];

    public function toViewApi(){
        return new ViewTariffComponent($this);
    }

    public function toShowApi(){
        return new ShowTariffComponent($this);
    }

    public function getFlags(){
        return array_column(Flag::cases(),'value');
    }

    public function componentDetails(){return $this->morphManyModel('ComponentDetail','reference');}
}