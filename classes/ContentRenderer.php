<?php
use Illuminate\Support\Facades\Event;

/**
 * Class ContentRenderer
 */
class ContentRenderer {

    /**
     * @param $content
     * @return mixed
     */
    public function renderContent($content)
    {
        // If it's no array it's probably no contentbuilder
        if (!is_array($content)) {
            return $content;
        }

        // You can hook into this event to do your own custom parsing
        Event::fire('nathan.contentbuilder.beforeParse', [&$content]);

        $parsed_content = [];

        // Loop through all the blocks
        foreach ($content as $block) {

            if (array_get($block, '_group') == "block_video") {

                if (array_get($block, 'video_type') == 'youtube') {
                    $block['video_id'] = $this->parseYoutubeUrl(array_get($block, 'video_url'));
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
            "@content_builder.htm",
            [
                'content' => $parsed_content
            ]
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
            $url, $match)
        ) {
            return $match[1];
        } else {
            return false;
        }
    }

}