<?php

return [

    'plans' => [

        // what the FREE plan can see/use
        'free' => [
            'contacts',
            'companies',
            'deals.basic',
            'pipelines.basic',
        ],

        // PRO plan
        'pro' => [
            'contacts',
            'companies',
            'deals.basic',
            'deals.advanced',
            'pipelines.basic',
            'pipelines.advanced',
            'reports',
        ],

        // ENTERPRISE
        'enterprise' => [
            'contacts',
            'companies',
            'deals.basic',
            'deals.advanced',
            'pipelines.basic',
            'pipelines.advanced',
            'reports',
            'hr',
            'payroll',
            'accounting',
            'ai',
        ],
    ],

    'default_plan' => 'free',
];
