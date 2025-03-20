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
                        Contracts\Transaction::class                  => Schemas\Transaction::class,
                        Contracts\TransactionItem::class              => Schemas\TransactionItem::class,
                        Contracts\POSTransaction::class               => Schemas\POSTransaction::class,
                        Contracts\ReportTransaction::class            => Schemas\ReportTransaction::class,
                        Contracts\Billing::class                      => Schemas\Billing::class,
                        Contracts\SplitBill::class                    => Schemas\SplitBill::class,
                        Contracts\PaymentHistory::class               => Schemas\PaymentHistory::class,
                        Contracts\PaymentHistoryDetail::class         => Schemas\PaymentHistoryDetail::class,
                        Contracts\Refund::class                       => Schemas\Refund::class,
                        Contracts\TariffComponent::class              => Schemas\TariffComponent::class,
                        Contracts\PriceComponent::class               => Schemas\PriceComponent::class,
                        Contracts\PaymentDetail::class                => Schemas\PaymentDetail::class,
                        Contracts\Bank::class                         => Schemas\Bank::class,
                        Contracts\Voucher::class                      => Schemas\Voucher::class,
                        Contracts\VoucherRule::class                  => Schemas\VoucherRule::class,
                        Contracts\PaymentMethod::class                => Schemas\PaymentMethod::class,
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
