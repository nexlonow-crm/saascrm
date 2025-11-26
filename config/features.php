<?php

return [

    /*
     * Which features are enabled for which plan.
     * You can change these anytime.
     */
    'plans' => [

        'free' => [
            'contacts',
            'companies',
            'deals.basic',
        ],

        'pro' => [
            'contacts',
            'companies',
            'deals.basic',
            'deals.advanced',
            'inventory',
            'reports',
        ],

        'enterprise' => [
            'contacts',
            'companies',
            'deals.basic',
            'deals.advanced',
            'inventory',
            'reports',
            'hr',
            'payroll',
            'accounting',
            'ai',
        ],

    ],

    /*
     * Default plan if not set.
     */
    'default_plan' => 'free',
];
