<?php

namespace Zahzah\ModuleTransaction\Schemas;

use Zahzah\LaravelSupport\Supports\PackageManagement;
use Zahzah\ModuleTransaction\Contracts\PriceComponent as ContractsPriceComponent;
use Zahzah\ModuleTransaction\Resources\PriceComponent\{
    ViewPriceComponent, ShowPriceComponent
};

class PriceComponent extends PackageManagement implements ContractsPriceComponent{
    protected array $__guard   = ['id','model_type','model_id'];
    protected array $__add     = ['tariff_component_id','price'];
    protected string $__entity = 'PriceComponent';
    public static $price_component;
    public static $price = 0;

    protected array $__resources = [
        'view' => ViewPriceComponent::class,
        'show' => ShowPriceComponent::class
    ];

    public function prepareStorePriceComponent(mixed $attributes=null){
        $attributes ??= request()->all();

        //GET EXISTING TARIFF COMPONENTS
        $model = $this->pricecomponent()->where([
            ['model_id', $attributes['model_id']],
            ['model_type', $attributes['model_type']]
        ])->when(isset($attributes['service_id']),function($query) use ($attributes){
            $query->where('service_id',$attributes['service_id']);
        });

        $tariffComponents = $model->get();
        $tariff_component_ids = array_column($tariffComponents->toArray(),'id');
        if (isset($attributes['tariff_components'])){
            $keep  = [];
            $price = 0;
            $service = [];
            if (isset($attributes['service_id'])){
                $service['service_id'] = $attributes['service_id'];
            }
            foreach ($attributes['tariff_components'] as $tariff) {
                $tariff = $this->pricecomponent()->updateOrCreate($this->mergeArray($service,[
                    'model_id'   => $attributes['model_id'],
                    'model_type' => $attributes['model_type'],
                    $this->TariffComponentModel()->getForeignKey() => $tariff['id']
                ]),[
                    'price' => $tariff['price']
                ]);
                $keep[] = $tariff->getKey();
                $price += $tariff['price'] ?? 0;
            }
            static::$price = $price;
            $remove = array_diff($tariff_component_ids, $keep);
            if (isset($attributes['service'])){
                $attributes['service']->price = $price;
                $attributes['service']->save();
            }
            if (count($remove) > 0) $this->pricecomponent()->whereIn('id', $remove)->delete();
        }else{
            $model->delete();
        }
    }

    public function getPrice(): int{
        return static::$price;
    }

    public function addOrChange(? array $attributes=[]): self{
        if(isset($attributes['parent_model'])) {
            $attributes['parent_model']->load("treatment");
            $service = $attributes['parent_model']->treatment;
            $service->price = $attributes['price'];
            $service->save();

            $attributes = [
                "model_id"            => $attributes['parent_model']->id,
                "model_type"          => $attributes['parent_model']->getMorphClass(),
                "tariff_component_id" => $attributes['id'],
                "price"               => $attributes['price']
            ];
        } else {
            if(!isset($attributes['id'])) throw new \Exception("id required");
            if(!isset($attributes['id'])) throw new \Exception("price required");

            $attributes = [
                "model_id"            => $attributes['parent_model']->id,
                "model_type"          => $attributes['parent_model']->getMorphClass(),
                "tariff_component_id" => $attributes['id'],
                "price"               => $attributes['price']
            ];
        }

        $this->PriceComponentModel()->updateOrCreate([
            'model_id'            => $attributes['model_id'],
            'model_type'          => $attributes['model_type'],
            'tariff_component_id' => $attributes['tariff_component_id'],
        ],[
            'service_id'          => isset($attributes['service_id']) ? $attributes['service_id'] : null,
            'price'               => $attributes['price']
        ]);
        // $this->updateOrCreate($attributes);
        return $this;
    }

    public function getPriceComponent(): mixed{
        return static::$price_component;
    }

    public function pricecomponent(mixed $conditionals=null){
        $this->booting();
        return $this->PriceComponentModel()->withParameters()->conditionals($conditionals);
    }
}
