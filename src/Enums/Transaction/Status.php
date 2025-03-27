<?php

namespace Hanafalah\ModuleTransaction\Enums\Transaction;

enum Status: string
{
    case DRAFT      = 'DRAFT';
    case ACTIVE     = 'ACTIVE';
    case SUSPENDED  = 'SUSPENDED';
    case CANCELED   = 'CANCELED';
    case COMPLETED  = 'COMPLETED';
}
