<?php

namespace Zahzah\ModuleTransaction\Models\Price;

use Illuminate\Database\Eloquent\Relations\Relation;
use Zahzah\LaravelSupport\Models\BaseModel;
use Zahzah\ModuleTransaction\Resources\PriceComponent\ShowPriceComponent;
use Zahzah\ModuleTransaction\Resources\PriceComponent\ViewPriceComponent;

class PriceComponent extends BaseModel{
    protected $fillable = ['id', 'service_id', 'model_type', 'model_id', 'tariff_component_id', 'price'];

    protected static function booted(): void{
        parent::booted();
        static::creating(function($query){
            if (!isset($query->service_id)){
                $relation = Relation::morphMap()[$query->model_type];
                if (!isset($relation)) throw new \Exception('Relation not found');
                $relation = app($relation)->findOrFail($query->model_id);
                if (\method_exists($relation,'service')){
                    $service = $relation->service;
                    if (isset($service)){
                        $query->service_id = $service->getKey();
                    }
                }
            }
        });
    }

    public function toViewApi(){
        return new ViewPriceComponent($this);
    }

    public function toShowApi(){
        return new ShowPriceComponent($this);
    }

    public function model(){return $this->morphTo();}
    public function tariffComponent(){return $this->belongsToModel('TariffComponent');}
    public function service(){return $this->belongsToModel('Service');}
}
    