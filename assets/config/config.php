<?php

use Hanafalah\ModuleTransaction\Commands as ModuleTransactionCommands;

return [
    'commands' => [
        ModuleTransactionCommands\InstallMakeCommand::class
    ],
    'libs' => [
        'model' => 'Models',
        'contract' => 'Contracts',
        'schema' => 'Schemas'
    ],
    'app' => [
        'contracts'  => [
        ],
    ],
    'database'   => [
        'models' => [

        ]
    ],
    'author' => \App\Models\User::class
];
