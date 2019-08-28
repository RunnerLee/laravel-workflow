<?php
/**
 * @author: RunnerLee
 * @email: runnerleer@gmail.com
 * @time: 2019-08
 */

return [
    'demo' => [
        'supports' => [stdClass::class],
        'single_status' => true,
        'marking_property' => 'status',
        'places' => [
            'created',
            'submitted',
            'reviewed',
        ],
        'transitions' => [
            'to_submit' => [
                'from' => 'created',
                'to' => 'submitted',
                'metadata' => [],
            ],
            'to_review' => [
                'from' => 'submitted',
                'to' => 'reviewed',
            ],
        ],
    ],
];
