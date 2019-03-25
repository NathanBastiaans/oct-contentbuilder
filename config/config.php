<?php

return [
    'default_builder_yaml' => '$/nathan/contentbuilder/assets/builder.yaml',

    'models' => [
        // For example:
        [
            'model_class' => \Nathan\Contact\Models\Message::class,
            'builders' => [
                'comment' => [
                    'tab'   => 'Comment builder',
                    'label' => 'Lekker comments bouwen'
                ],

            ]
        ],
        [
            'model_class' => \RainLab\Pages\Classes\Page::class,
            'builders'    => [
                'markup' => [
                    'tab' => 'hi',
                    'label' => 'party time',
                ]
            ]
        ],
    ]
];