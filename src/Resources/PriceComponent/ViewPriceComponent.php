<?php

namespace Hanafalah\ModuleTransaction\Resources\PriceComponent;

use Hanafalah\LaravelSupport\Resources\ApiResource;

class ViewPriceComponent extends ApiResource
{

    /**
     * Transform the resource into an array.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [
            'id'         => $this->id,
            'price'      => $this->price,
            'tariff_component' => $this->relationValidation('tariffComponent', function () {
                return $this->tariffComponent->toViewApi();
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        return $arr;
    }
}
