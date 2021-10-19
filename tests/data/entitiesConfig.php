<?php

use EnoffSpb\EntityManager\Tests\Entity\Example;

return [
    Example::class => [
        'tableName' => 'php_em_example',
        'primaryKey' => 'id',
        'mapping' => [
            'id' => [
                'getter' => 'getId',
                'setter' => 'setId'
            ],
            'name' => [],
            'order' => [
                'getter' => 'getOrder',
                'setter' => 'setOrder'
            ]
        ]
    ]
];
