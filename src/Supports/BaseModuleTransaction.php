<?php

namespace Hanafalah\ModuleTransaction\Supports;

use Hanafalah\LaravelSupport\Supports\PackageManagement;

class BaseModuleTransaction extends PackageManagement
{
    /** @var array */
    protected $__module_transaction_config = [];

    /**
     * A description of the entire PHP function.
     *
     * @param Container $app The Container instance
     * @throws Exception description of exception
     * @return void
     */
    public function __construct()
    {
        $this->setConfig('module-transaction', $this->__module_transaction_config);
    }
}
