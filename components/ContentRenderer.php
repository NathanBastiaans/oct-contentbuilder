<?php namespace Nathan\ContentBuilder\Components;

use Cms\Classes\ComponentBase;
use Illuminate\Support\Facades\Event;

class ContentRenderer extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'nathan.contentbuilder::lang.components.contentRenderer.name',
            'description' => 'nathan.contentbuilder::lang.components.contentRenderer.description'
        ];
    }

    /**
     * @param $content
     * @return mixed
     */
    public function renderContent($content = '')
    {
        // If it's no array it's probably no content builder
        if (!is_array($content)) {
            try {
                $content = json_decode($content, true);
            } catch (\Exception $e) {
                return $content;
            }
        }

        // You can hook into this event to do your own custom parsing
        Event::fire('nathan.contentbuilder.beforeParse', [&$content]);

        $parsed_content = [];

        // Loop through all the blocks
        foreach ($content as $block) {
            if (array_get($block, '_group') == "blockVideo") {
                if (array_get($block, 'video_type') == 'youtube') {
                    $block['video_id'] = $this->parseYoutubeUrl(array_get($block, 'video_url'));
                } elseif (array_get($block, 'video_type') == 'vimeo') {
                    $block['video_id'] = $this->parseVimeoUrl(array_get($block, 'video_url'));
                }

                // The video id is false if the URL wasn't parsed correctly so we shouldn't add the block
                if (!$block['video_id']) {
                    continue;
                }
            }

            $parsed_content[] = $block;
        }

        // You can hook into this event to do our own custom parsing after the default parsing
        Event::fire('nathan.contentbuilder.afterParse', [&$parsed_content]);

        return $this->renderPartial(
            '@content_builder.htm',
            ['content' => $parsed_content]
        );
    }

    /**
     * Convert the video block data for the front-end
     *
     * @param string $url The URL to parse
     *
     * @return mixed
     */
    protected function parseYoutubeUrl($url)
    {
        if (preg_match(
            '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i',
            $url,
            $match
        )
        ) {
            return $match[1];
        } else {
            return false;
        }
    }

    /**
     * Convert the video block data for the front-end
     *
     * @param string $url The URL to parse
     *
     * @return mixed
     */
    protected function parseVimeoUrl($url)
    {
        if (preg_match(
            '/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/',
            $url,
            $match
        )
        ) {
            return $match[1];
        } else {
            return false;
        }
    }
}