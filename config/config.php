<?php

return [
    'default_builder_yaml' => '$/nathan/contentbuilder/assets/builder.yaml',

    'models' => [
        // For example:
        //[
        //    'model_class' => \Nathan\Contact\Models\Message::class,
        //    'builders' => [
        //        'comment' => [
        //            'tab'   => 'Comment builder',
        //            'label' => 'Cool comment builder!'
        //            'builder_config' => 'Path-To-Custom-Builder'
        //        ]
        //    ]
        //]
        [
            'model_class' => \RainLab\Pages\Classes\Page::class,
            'builders'    => [
                'markup' => [
                    'tab' => 'Content',
                    'label' => 'Default content builder',
                ]
            ]
        ],
    ]
];