<?php

namespace Zahzah\ModuleTransaction\Models\Transaction;

use Zahzah\ModuleTransaction\Concerns\HasTransaction;

class BillingDeferred extends Invoice{
    protected $table = 'invoices';
}
