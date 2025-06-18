<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'addUpdateCustomerPayment'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['https://colourindigo.com'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'], // Only define this once

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false, // Set to true only if sending cookies

];
