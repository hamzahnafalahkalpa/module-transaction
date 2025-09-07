<?php

use Hanafalah\ModuleTransaction\Commands as ModuleTransactionCommands;

return [
    'namespace' => 'Hanafalah\\ModuleTransaction',
    'app' => [
        'contracts' => [
            //ADD YOUR CONTRACTS HERE
        ],
    ],
    'libs'       => [
        'model' => 'Models',
        'contract' => 'Contracts',
        'schema' => 'Schemas',
        'database' => 'Database',
        'data' => 'Data',
        'resource' => 'Resources',
        'migration' => '../assets/database/migrations'
    ],
    'database'   => [
        'models' => [

        ]
    ],
    'commands' => [
        ModuleTransactionCommands\InstallMakeCommand::class
    ],
    'transaction_types'=> [
        //THIS KEY SAME WITH MODEL NAME USING SNAKE CASE
        'submission' => [
            'schema' => 'Submission',
        ]
    ],
    'author' => 'User',
    'payment_summary' => null,
    'payment_detail' => null,
    'consument' => null
];
