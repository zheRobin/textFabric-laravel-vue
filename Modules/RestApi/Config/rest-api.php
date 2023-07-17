<?php

return [
    'API_KEY'           => env('OPENAI_API_KEY'),
    'API_REQUEST_LIMIT' => env('REST_API_REQUEST_LIMIT', 30000),
];
