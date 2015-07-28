<?php
return [
    'hackday-project' => [
        [
            'pattern' => '/',
            'controller' => 'index.controller:getIndexAction',
            'method' => [
                'get'
            ],
        ],
        [
            'pattern' => '/entry',
            'controller' => 'entry.controller:getEntriesAction',
            'method' => [
                'get'
            ],
        ],
    ],
];
