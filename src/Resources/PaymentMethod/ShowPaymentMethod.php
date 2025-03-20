<?php

namespace Zahzah\ModuleTransaction\Resources\PaymentMethod;

use Zahzah\LaravelSupport\Resources\ApiResource;
use Zahzah\ModuleTransaction\Resources\TransactionItem\ShowTransactionItem;

class ShowPaymentMethod extends ViewPaymentMethod{

    /**
     * Transform the resource into an array.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [];
        $arr = $this->mergeArray(parent::toArray($request), $arr);
        
        return $arr;
    }
}
