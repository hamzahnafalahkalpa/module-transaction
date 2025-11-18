<?php

namespace Hanafalah\ModuleTransaction\Resources\MasterReport;

use Hanafalah\LaravelSupport\Resources\Unicode\ViewUnicode;

class ViewMasterReport extends ViewUnicode
{
  /**
   * Transform the resource into an array.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
   */
  public function toArray(\Illuminate\Http\Request $request): array
  {
    $arr = [
      'id' => $this->id,
      'name' => $this->name,
      'label' => $this->label,
      'data_type' => $this->data_type,
      'filters' => $this->filters
    ];
    return $arr;
  }
}
