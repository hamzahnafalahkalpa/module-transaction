<?php

namespace Hanafalah\ModuleTransaction\Enums\Transaction;

enum TransactionStatus: int
{
    case DRAFT      = 0;
    case ACTIVE     = 1;
    case SUSPENDED  = 2;
    case CANCELED   = 3;
    case COMPLETED  = 4;
}
