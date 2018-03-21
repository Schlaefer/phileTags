<?php
/**
 * @link https://github.com/Schlaefer/phileTagsm
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Plugin\Siezi\PhileTags
 */

namespace Phile\Plugin\Siezi\PhileTags;

use Phile\Gateway\EventObserverInterface;
use Phile\Plugin\AbstractPlugin;

/**
 * PhileTags
 */
class Plugin extends AbstractPlugin implements EventObserverInterface
{
    /** @var boolean current page is tag-page */
    private $isTagPage;

    /** @var string current tag to show */
    private $currentTag;

    protected $events = [
        'after_read_file_meta' => 'onAfterReadFileMeta',
        'request_uri' => 'onRequestUri',
        'template_engine_registered' => 'setTwigVars',
        'after_response_created' => 'setResponse',
    ];

    /**
     * Parses tags in content pages.
     */
    protected function onAfterReadFileMeta($data)
    {
        if (empty($data['meta']['tags'])) {
            return;
        }
        $raw = $data['meta']['tags'];
        if (is_array($raw)) {
            // YAML style array tags
            $tags = $raw;
        } elseif (!empty($raw) && is_string($raw)) {
            $tags = mb_split($this->settings['tag_separator'], $raw);
        }
        $tags = array_map('trim', $tags);
        asort($tags);

        $data['meta']['tags_array'] = $tags;
    }

    /**
     * Checks if tag-page is requested with which tag.
     */
    protected function onRequestUri($data)
    {
        $uri = $data['uri'];
        $this->isTagPage = (dirname($uri) === 'tag');

        if ($this->isTagPage) {
            $this->currentTag = urldecode(basename($uri));
        }
    }

    /**
     * Sets template variables for showing tag page.
     */
    protected function setTwigVars($data)
    {
        if (!$this->isTagPage) {
            return;
        }
        $data['data']['meta']['title'] = '#' . $this->currentTag;
        $data['data']['meta']['template'] = $this->settings['tag_template'];
        $data['data']['current_tag'] = $this->currentTag;
    }

    /**
     * Removes 404 HTML status code for tag page.
     */
    protected function setResponse($data)
    {
        if (!$this->isTagPage) {
            return;
        }
        $data['response'] = $data['response']->withStatus(200);
    }
}
