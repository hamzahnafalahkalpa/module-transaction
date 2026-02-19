# CLAUDE.md - Module Transaction

This document provides guidance for working with the `hanafalah/module-transaction` package.

## Overview

Module Transaction provides a comprehensive transaction management system for the Wellmed healthcare platform. It handles transaction tracking, transaction items, submissions, journal entries, and reporting functionality with polymorphic relationships.

**Namespace:** `Hanafalah\ModuleTransaction`

## Dependencies

```json
{
    "hanafalah/module-service": "dev-main",
    "hanafalah/laravel-has-props": "dev-main",
    "hanafalah/laravel-support": "dev-main"
}
```

## Architecture

### Service Provider

The `ModuleTransactionServiceProvider` extends `BaseServiceProvider` from `laravel-support`:

```php
class ModuleTransactionServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        $this->registerMainClass(ModuleTransaction::class)
            ->registerCommandService(Providers\CommandServiceProvider::class)
            ->registers(['*']);
    }
}
```

> **WARNING: BaseServiceProvider Usage**
>
> This module extends `Hanafalah\LaravelSupport\Providers\BaseServiceProvider`. When modifying the service provider:
> - Do NOT override the `boot()` method without calling `parent::boot()`
> - The `registers(['*'])` call auto-registers all contracts, schemas, and data classes
> - The `dir()` method must return the correct base path for asset resolution
> - Migrations are loaded from `assets/database/migrations/`

### Directory Structure

```
src/
├── Commands/                    # Artisan commands
│   ├── InstallMakeCommand.php
│   └── EnvironmentCommand.php
├── Concerns/                    # Traits for models
│   ├── HasTransaction.php       # Adds transaction relationship to models
│   ├── HasTransactionItem.php   # Adds transaction item relationship
│   └── HasJournalEntry.php      # Adds journal entry support
├── Contracts/                   # Interfaces
│   ├── Data/                    # Data transfer object contracts
│   ├── Schemas/                 # Schema contracts
│   └── ModuleTransaction.php
├── Data/                        # Spatie Data DTOs
│   ├── TransactionData.php
│   ├── TransactionItemData.php
│   ├── SubmissionData.php
│   └── MasterReportData.php
├── Enums/
│   └── Transaction/
│       └── Status.php           # DRAFT, ACTIVE, SUSPENDED, CANCELED, COMPLETED
├── Facades/
│   └── ModuleTransaction.php
├── Models/
│   ├── Transaction/
│   │   ├── Transaction.php      # Main transaction model
│   │   └── TransactionItem.php  # Transaction line items
│   ├── Submission.php           # Submission model with HasTransaction
│   └── MasterReport.php         # Report model extending Unicode
├── Providers/
│   └── CommandServiceProvider.php
├── Resources/                   # API resources (View/Show)
│   ├── Transaction/
│   ├── TransactionItem/
│   ├── Submission/
│   └── MasterReport/
├── Schemas/                     # Business logic schemas
│   ├── Transaction.php
│   ├── TransactionItem.php
│   ├── Submission.php
│   ├── MasterReport.php
│   └── ReportTransaction.php
├── Supports/
│   └── BaseModuleTransaction.php
├── ModuleTransaction.php        # Main class
└── ModuleTransactionServiceProvider.php
```

## Core Concepts

### Transaction Status Enum

```php
namespace Hanafalah\ModuleTransaction\Enums\Transaction;

enum Status: string
{
    case DRAFT      = 'DRAFT';
    case ACTIVE     = 'ACTIVE';
    case SUSPENDED  = 'SUSPENDED';
    case CANCELED   = 'CANCELED';
    case COMPLETED  = 'COMPLETED';
}
```

### Transaction Model

The `Transaction` model uses:
- **ULIDs** for primary keys
- **HasProps** for flexible JSON properties
- **SoftDeletes** for safe deletion
- **Polymorphic relationships** via `reference_type` and `reference_id`

Key relationships:
- `reference()` - Polymorphic morph-to relationship
- `transactionItems()` - Has many transaction items
- `paymentSummaries()` - Has many payment summaries
- `paymentDetails()` - Has many payment details
- `consument()` - Consumer through transaction
- `journalEntry()` - Has one journal entry

### Transaction Item Model

Transaction items represent line items within a transaction:
- Links to a parent `Transaction`
- Has polymorphic `item` relationship for the actual item
- Has polymorphic `reference` relationship
- Supports payment detail association

### Using the HasTransaction Trait

Add transaction capability to any model:

```php
use Hanafalah\ModuleTransaction\Concerns\HasTransaction;

class YourModel extends Model
{
    use HasTransaction;

    // Automatically creates transaction on model creation
    // Adds transaction() relationship
    // Adds reporting(), journalReporting(), canceling() methods
}
```

The trait automatically:
1. Creates a transaction record when the model is created
2. Stores transaction view data in `prop_transaction`
3. Provides `reporting()`, `journalReporting()`, and `canceling()` methods

### Using the HasJournalEntry Trait

For models that require journal entry integration:

```php
use Hanafalah\ModuleTransaction\Concerns\HasJournalEntry;

class YourModel extends Model
{
    use HasJournalEntry; // Includes HasTransaction

    // Adds isHasJournalEntry() method returning true
    // Adds journalEntry() relationship
}
```

### Using the HasTransactionItem Trait

For models that can be transaction items:

```php
use Hanafalah\ModuleTransaction\Concerns\HasTransactionItem;

class YourItemModel extends Model
{
    use HasTransactionItem;

    // Adds transactionItem() relationship
}
```

## Configuration

Configuration file: `assets/config/config.php`

```php
return [
    'namespace' => 'Hanafalah\\ModuleTransaction',
    'libs' => [
        'model' => 'Models',
        'contract' => 'Contracts',
        'schema' => 'Schemas',
        'database' => 'Database',
        'data' => 'Data',
        'resource' => 'Resources',
        'migration' => '../assets/database/migrations'
    ],
    'transaction_types' => [
        // Key must be snake_case of model name
        'submission' => [
            'schema' => 'Submission',
        ]
    ],
    'author' => 'User',
    'payment_summary' => null,  // Configure in app
    'payment_detail' => null,   // Configure in app
    'consument' => null         // Configure in app
];
```

### Registering Transaction Types

To add custom transaction types, extend the config:

```php
// In your application config or service provider
config(['module-transaction.transaction_types.your_model' => [
    'schema' => 'YourModelSchema',
]]);
```

## Schema Usage

### Creating Transactions

```php
use Hanafalah\ModuleTransaction\Facades\ModuleTransaction;

// Using the schema
$transactionSchema = app(config('app.contracts.Transaction'));
$transaction = $transactionSchema->prepareStoreTransaction(
    $dto->requestDTO(config('app.contracts.TransactionData'), [
        'reference_type' => 'YourModel',
        'reference_id' => $model->getKey(),
        'reference_model' => $model,
        'transaction_items' => [...],
        'props' => [...]
    ])
);
```

### Creating Transaction Items

```php
$transactionItemSchema = app(config('app.contracts.TransactionItem'));
$transactionItem = $transactionItemSchema->prepareStoreTransactionItem(
    $dto->requestDTO(config('app.contracts.TransactionItemData'), [
        'transaction_id' => $transaction->getKey(),
        'item_type' => 'Product',
        'item_id' => $product->getKey(),
        'name' => $product->name,
        'props' => [...]
    ])
);
```

## Database Schema

### transactions table

| Column | Type | Description |
|--------|------|-------------|
| id | ULID | Primary key |
| uuid | string(36) | UUID identifier |
| transaction_code | string(100) | Auto-generated code |
| parent_id | ULID | Self-referential parent |
| reference_type | string(50) | Polymorphic type |
| reference_id | string(36) | Polymorphic ID |
| status | enum | DRAFT, ACTIVE, SUSPENDED, CANCELED, COMPLETED |
| props | json | Flexible properties |
| reported_at | timestamp | When reported |
| canceled_at | timestamp | When canceled |
| journal_reported_at | timestamp | When journal reported |
| created_at | timestamp | Creation time |
| updated_at | timestamp | Last update |
| deleted_at | timestamp | Soft delete |

### transaction_items table

| Column | Type | Description |
|--------|------|-------------|
| id | ULID | Primary key |
| transaction_id | ULID | Parent transaction |
| parent_id | ULID | Self-referential parent |
| reference_type | string | Polymorphic type |
| reference_id | string | Polymorphic ID |
| item_type | string | Item polymorphic type |
| item_id | string | Item polymorphic ID |
| name | string | Item name |
| props | json | Flexible properties |

## Journal Entry Integration

When a transaction's `journal_reported_at` is updated and the reference model has journal entry support:

```php
// Automatic trigger on Transaction::updated event
if ($reference->isHasJournalEntry() && $query->isJournalReported()) {
    app(config('app.contracts.JournalEntry'))->prepareStoreJournalEntry(...);
}
```

## Best Practices

1. **Always use DTOs** - Use `TransactionData` and `TransactionItemData` for data transfer
2. **Configure payment models** - Set `payment_summary`, `payment_detail`, and `consument` in config
3. **Use traits appropriately** - `HasTransaction` for transactable models, `HasJournalEntry` for accounting-aware models
4. **Transaction types** - Register custom transaction types in config with proper schema mapping
5. **Status management** - Use the `Status` enum for consistent status handling

## Octane Considerations

When running under Laravel Octane:
- Transaction models use ULIDs which are stateless
- The `HasProps` trait stores data in JSON columns, avoiding static state
- Ensure any caching respects tenant isolation
- The schema's `$__cache` configuration provides cache tagging for proper invalidation

## Related Modules

- `hanafalah/module-payment` - Payment detail and summary integration
- `hanafalah/module-service` - Base service functionality
- `hanafalah/laravel-has-props` - JSON property management
- `hanafalah/laravel-support` - Base models, schemas, and utilities
