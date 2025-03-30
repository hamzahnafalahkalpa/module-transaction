<?php

namespace Hanafalah\ModuleTransaction\Resources\Transaction;

use Hanafalah\LaravelSupport\Resources\ApiResource;

class ViewTransaction extends ApiResource
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
            'id'                => $this->id,
            'uuid'              => $this->uuid,
            'transaction_code'  => $this->transaction_code,
            'reference_type'    => $this->reference_type,
            'reference'         => $this->relationValidation('reference', function () {
                return $this->reference->toViewApi();
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
        return $arr;
    }
}
