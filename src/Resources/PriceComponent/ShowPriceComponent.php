<?php

namespace Hanafalah\ModuleTransaction\Resources\PriceComponent;

class ShowPriceComponent extends ViewPriceComponent
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
            'tariff_component' => $this->relationValidation('tariffComponent', function () {
                return $this->tariffComponent->toShowApi();
            })
        ];
        $arr = $this->mergeArray(parent::toArray($request), $arr);

        return $arr;
    }
}
