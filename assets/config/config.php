<?php

use Hanafalah\ModuleTransaction\Commands as ModuleTransactionCommands;

return [
    'commands' => [
        ModuleTransactionCommands\InstallMakeCommand::class
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
    'app' => [
        'contracts'  => [
        ],
    ],
    'database'   => [
        'models' => [

        ]
    ],
    'author' => 'User'
];
