<?php

namespace Hanafalah\ModuleTransaction\Resources\Deposit;

use Hanafalah\LaravelSupport\Resources\ApiResource;

class ViewDeposit extends ApiResource
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
            'id'             => $this->id,
            'deposit_code'   => $this->deposit_code,
            'reference_type' => $this->reference_type,
            'reference_id'   => $this->reference_id,
            'reported_at'    => $this->reported_at,
            'total'          => $this->total,
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at
        ];

        return $arr;
    }
}
