<?php

namespace Zahzah\ModuleTransaction\Resources\Deposit;

use Zahzah\LaravelSupport\Resources\ApiResource;

class ShowDeposit extends ViewDeposit
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
            'reference' => $this->relationValidation('reference',function(){
                return $this->reference->toShowApi();
            })
        ];
        
        return $arr;
    }
}
