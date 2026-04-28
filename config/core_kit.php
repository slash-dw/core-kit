<?php

return [
    'tracing' => [
        'headers' => ['X-Trace-Id'],
        'fallback_header' => 'X-Trace-Id',
    ],

    'pagination' => [
        'per_page_options' => [30, 50, 75, 100],
    ],
];
