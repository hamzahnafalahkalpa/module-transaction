<?php

namespace Hanafalah\ModuleTransaction\Resources\Transaction;

class ShowTransaction extends ViewTransaction
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
            'reference' => $this->relationValidation('reference', function () {
                return $this->propNil($this->reference->toShowApi()->resolve(),'transaction');
            }),
            'transaction_items' => $this->relationValidation('transactionItems', function () {
                return $this->transactionItems->map(function ($item) {
                    return $item->toViewApi();
                });
            })
        ];
        $arr = $this->mergeArray(parent::toArray($request), $arr);
        return $arr;
    }
}
