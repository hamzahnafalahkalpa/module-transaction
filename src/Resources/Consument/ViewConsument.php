<?php

namespace Zahzah\ModuleTransaction\Resources\Consument;

use Zahzah\LaravelSupport\Resources\ApiResource;

class ViewConsument extends ApiResource
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [
            'id'         => $this->id,
            'uuid'       => $this->uuid,
            'name'       => $this->name,
            'phone'      => $this->phone,
            'reference'  => $this->relationValidation('reference',function(){
                return $this->reference->toViewApi();
            }),
            'props'       => $this->getPropsData() ?? null,
            'created_at'  => $this->created_at,
            'updated_at'  => $this->updated_at
        ];

        
        return $arr;
    }
}

