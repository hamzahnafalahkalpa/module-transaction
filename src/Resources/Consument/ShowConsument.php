<?php

namespace Hanafalah\ModuleTransaction\Resources\Consument;

class ShowConsument extends ViewConsument
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [
            'reference'  => $this->relationValidation('reference', function () {
                return $this->reference->toShowApi();
            })
        ];
        $arr = $this->mergeArray(parent::toArray($request), $arr);

        return $arr;
    }
}
