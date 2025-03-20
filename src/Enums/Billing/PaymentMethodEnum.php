
    <?php

    namespace Hanafalah\ModuleTransaction\Enums\Billing;

    enum PaymentMethodEnum: string
    {
        case EDC      = 'EDC';
        case CASH     = 'CASH';
        case TRANSFER = 'TRANSFER';
        case DITAGIHKAN = 'DITAGIHKAN';
    }
