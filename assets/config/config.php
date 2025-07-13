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
    'author' => 'User'
];
