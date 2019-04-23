<?php

return [
    'plugin' => [
        'name' => 'Content Builder',
        'description' => 'Vervang bestaande velden met een content builder'
    ],
    'components' => [
        'contentRenderer' => [
            'name' => 'Content Renderer',
            'description' => 'Levert de methodes om de content te renderen'
        ]
    ],
    'builder' => [
        'blocks' => [
            'editor' => [
                'name'           => 'Editor blok',
                'block_editor'   => 'Editor',
                'editor_content' => 'Inhoud'
            ],
            'markdown' => [
                'name'             => 'Markdown blok',
                'block_markdown'   => 'Markdown',
                'markdown_content' => 'Inhoud'
            ],
            'image' => [
                'name'                => 'Afbeelding blok',
                'block_image'         => 'Afbeelding',
                'image_description'   => 'Beschrijving',
                'image_path'          => 'Afbeelding',
                'image_alt'           => 'Alternatief',
                'is_image_full_width' => 'Toon afbeelding in volledige breedte'

            ],
            'quote' => [
                'name'          => 'Citaat blok',
                'block_quote'   => 'Citaat',
                'quote_content' => 'Citaat',
                'quote_author'  => 'Auteur'
            ],
            'video' => [
                'name'               => 'Video blok',
                'block_video'        => 'Video',
                'video_type'         => 'Video type',
                'video_url'          => 'Video URL',
                'video_type_options' => [
                    'youtube' => 'YouTube',
                    'vimeo'   => 'Vimeo',
                    'empty'   => 'Selecteer een video type'
                ]
            ]
        ],
        'misc' => [
            'repeater_prompt' => 'Voeg nog een blok toe',
            'default_tab'     => 'Content builder',
            'default_label'   => 'Content builder'
        ]
    ],
    'validation' => [
        'editor_content' => [
            'required' => 'De inhoud in het Editor blok is verplicht'
        ],
        'image_path' => [
            'required' => 'De afbeelding in het Afbeelding blok is verplicht'
        ],
        'image_alt' => [
            'required' => 'De alternatieve tekst in het Afbeelding blok is verplicht'
        ],
        'quote_content' => [
            'required' => 'Het citaat in het Citaat blok is verplicht'
        ],
        'video_type' => [
            'required' => 'Het video type in het Video blok is verplicht'
        ],
        'video_url' => [
            'required' => 'De video url in het Video blok is verplicht'
        ]
    ]
];