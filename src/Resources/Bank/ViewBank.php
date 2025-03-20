<?php

namespace Zahzah\ModuleTransaction\Resources\Bank;

use Zahzah\LaravelSupport\Resources\ApiResource;

class ViewBank extends ApiResource
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
            'name'           => $this->name,
            'account_number' => $this->account_number,
            'account_name'   => $this->account_name,
            'status'         => $this->status,
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at
        ];
        
        return $arr;
    }
}
