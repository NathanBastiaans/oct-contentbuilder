<?php

return [
    'plugin' => [
        'name' => 'Content Builder',
        'description' => 'Replaces existing fields with a content builder'
    ],
    'components' => [
        'contentRenderer' => [
            'name' => 'Content Renderer',
            'description' => 'Provides helping methods to render the content'
        ]
    ],
    'builder' => [
        'blocks' => [
            'editor' => [
                'name'           => 'Editor block',
                'block_editor'   => 'Editor',
                'editor_content' => 'Content'
            ],
            'markdown' => [
                'name'             => 'Markdown block',
                'block_markdown'   => 'Markdown',
                'markdown_content' => 'Content'
            ],
            'image' => [
                'name'                => 'Image block',
                'block_image'         => 'Image',
                'image_description'   => 'Description',
                'image_path'          => 'Image',
                'image_alt'           => 'Alternative',
                'is_image_full_width' => 'Display image full width'

            ],
            'quote' => [
                'name'          => 'Quote block',
                'block_quote'   => 'Quote',
                'quote_content' => 'Quote',
                'quote_author'  => 'Author'
            ],
            'video' => [
                'name'               => 'Video block',
                'block_video'        => 'Video',
                'video_type'         => 'Video type',
                'video_url'          => 'Video URL',
                'video_type_options' => [
                    'youtube' => 'YouTube',
                    'vimeo'   => 'Vimeo',
                    'empty'   => 'Select a video type'
                ]
            ]
        ],
        'misc' => [
            'repeater_prompt' => 'Add another block',
            'default_tab'     => 'Content builder',
            'default_label'   => 'Content builder'
        ]
    ],
    'validation' => [
        'editor_content' => [
            'required' => 'The content in the Editor block is required'
        ],
        'image_path' => [
            'required' => 'The image in the Image block is required'
        ],
        'image_alt' => [
            'required' => 'The alt in the Image block is required'
        ],
        'quote_content' => [
            'required' => 'The quote in the Quote block is required'
        ],
        'video_type' => [
            'required' => 'The video type in the Video block is required'
        ],
        'video_url' => [
            'required' => 'The video url in the Video block is required'
        ]
    ]
];