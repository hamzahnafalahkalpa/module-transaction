<?php

namespace Zahzah\ModuleTransaction\Concerns;

trait HasPriceComponent{
    public function priceComponent(){
        return $this->morphOneModel('PriceComponent','model');
    }

    public function priceComponents(){
        return $this->morphManyModel('PriceComponent','model');
    }

    public function tariffComponents(){
        return $this->belongsToManyModel(
            'TariffComponent','PriceComponent',
            'model_id',$this->TariffComponentModel()->getForeignKey()
        )->where('model_type',$this->getMorphClass())->select('tariff_components.*','price');
    }
}