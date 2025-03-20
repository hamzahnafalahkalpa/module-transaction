<?php

namespace Zahzah\ModuleTransaction\Resources\VoucherRule;

use Zahzah\LaravelSupport\Resources\ApiResource;

class ViewVoucherRule extends ApiResource
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [
            'id'        => $this->id,
            'name'      => $this->name,
            'condition' => $this->condition,
            'is_valid'  => $this->is_valid ?? false
        ];

        $props = $this->getPropsData() ?? [];
        foreach ($props as $key => $prop) {
            $arr[$key] = $prop;
        }
        
        return $arr;
    }
}
