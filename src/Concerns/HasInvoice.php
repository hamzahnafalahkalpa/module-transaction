<?php

namespace Hanafalah\ModuleTransaction\Concerns;

trait HasInvoice
{
    public function invoice()
    {
        return $this->belongsToModel('Invoice');
    }
}
