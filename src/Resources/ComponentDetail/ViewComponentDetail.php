<?php

namespace Zahzah\ModuleTransaction\Resources\ComponentDetail;

use Zahzah\LaravelSupport\Resources\ApiResource;

class ViewComponentDetail extends ApiResource
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
            'name' => $this->flag,
        ];
        if (isset($this->jurnal)) {
            $arr['jurnal'] = $this->jurnal;
        }
        
        return $arr;
    }
}
