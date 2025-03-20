<?php

namespace Hanafalah\ModuleTransaction\Resources\PaymentMethod;

use Hanafalah\LaravelSupport\Resources\ApiResource;
use Hanafalah\ModuleTransaction\Resources\TransactionItem\ShowTransactionItem;

class ShowPaymentMethod extends ViewPaymentMethod
{

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
