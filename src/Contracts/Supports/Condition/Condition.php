<?php

namespace Hanafalah\ModuleTransaction\Contracts\Supports\Condition;

use Illuminate\Database\Eloquent\Model;
use Hanafalah\LaravelSupport\Contracts\Supports\DataManagement;

interface Condition extends DataManagement
{
    public function validation(Model &$voucher, array &$attributes): array;
}
