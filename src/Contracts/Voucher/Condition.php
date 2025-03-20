<?php

namespace Hanafalah\ModuleTransaction\Contracts\Voucher;

use Illuminate\Database\Eloquent\Model;
use Hanafalah\LaravelSupport\Contracts\DataManagement;

interface Condition extends DataManagement
{
    public function validation(Model &$voucher, array &$attributes): array;
}
