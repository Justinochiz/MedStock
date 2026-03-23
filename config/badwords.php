<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Bad Words Filter Configuration
    |--------------------------------------------------------------------------
    |
    | List of bad words and patterns to filter from user input
    |
    */

    'enabled' => true,

    'words' => [
        'fuck',
        'shit',
        'bitch',
        'asshole',
        'damn',
        'putangina',
        
    ],

    // Fields to apply the filter to
    'fields' => [
        'reviews' => true,
        'comments' => true,
        'descriptions' => true,
        'names' => false,
    ],
];
