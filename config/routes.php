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
            'pattern' => '/entries',
            'controller' => 'entry.controller:getEntriesAction',
            'method' => [
                'get',
            ],
        ],
        [
            'pattern' => '/entries',
            'controller' => 'entry.controller:createEntryAction',
            'method' => [
                'post',
            ],
        ],
        [
            'pattern' => '/images/',
            'controller' => 'image.controller:postImageAction',
            'method' => [
                'post'
            ],
        ],
        [
            'pattern' => '/images/{id}',
            'controller' => 'image.controller:getImageAction',
            'method' => [
                'get'
            ],
        ],
    ],
];
