<?php

namespace Hanafalah\ModuleTransaction\Enums\Billing;

enum Status: string
{
    case DRAFT     = 'DRAFT';
    case REPORTED  = 'REPORTED';
    case CANCELLED = 'CANCELLED';
}
