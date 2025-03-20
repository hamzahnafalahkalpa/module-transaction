<?php

namespace Zahzah\ModuleTransaction\Resources\Bank;

use Zahzah\LaravelSupport\Resources\ApiResource;

class ShowBank extends ViewBank
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
        ];
        $arr = $this->mergeArray(parent::toArray($request),$arr);
        
        return $arr;
    }
}
