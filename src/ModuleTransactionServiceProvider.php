<?php

declare(strict_types=1);

namespace Hanafalah\ModuleTransaction;

use Hanafalah\LaravelSupport\Providers\BaseServiceProvider;

class ModuleTransactionServiceProvider extends BaseServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return $this
     */
    public function register()
    {
        $this->registerMainClass(ModuleTransaction::class)
            ->registerCommandService(Providers\CommandServiceProvider::class)
            ->registers([
                '*',
                'Services' => function () {
                    $this->binds([
                        Contracts\ModuleTransaction::class            => ModuleTransaction::class,
                        Contracts\Voucher\Condition::class            => Supports\Condition\Condition::class,
                        Contracts\Voucher\DateCondition::class        => Supports\Condition\DateCondition::class,
                        Contracts\Voucher\UsageCondition::class       => Supports\Condition\UsageCondition::class,
                        Contracts\Voucher\TransactionCondition::class => Supports\Condition\TransactionCondition::class,
                        Contracts\Voucher\Benefit::class              => Supports\Benefit\Benefit::class,
                        Contracts\Voucher\Benefit\Billing::class      => Supports\Benefit\Billing::class
                    ]);
                }
            ]);
    }

    /**
     * Get the base path of the package.
     *
     * @return string
     */
    protected function dir(): string
    {
        return __DIR__ . '/';
    }

    protected function migrationPath(string $path = ''): string
    {
        return database_path($path);
    }
}
