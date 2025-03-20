<?php

namespace Hanafalah\ModuleTransaction\Models\Transaction;

use Hanafalah\ModuleTransaction\Concerns\HasTransaction;

class BillingDeferred extends Invoice
{
    protected $table = 'invoices';
}
