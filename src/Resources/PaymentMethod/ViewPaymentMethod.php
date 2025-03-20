<?php

namespace Zahzah\ModuleTransaction\Resources\PaymentMethod;

use Zahzah\LaravelSupport\Resources\ApiResource;

class ViewPaymentMethod extends ApiResource{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [
            'id'                => $this->id,
            'name'              => $this->name,
        ];
        $props = $this->getPropsData();
        if(isset($props) && count($props) > 0) {
            foreach ($props as $key => $prop) {
                $arr[$key] = $prop;
            }
        }
        
        return $arr;
    }
}
