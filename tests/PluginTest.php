<?php
/**
 * @link https://github.com/Schlaefer/phileTagsm
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Plugin\Siezi\PhileTags
 */

namespace Phile\Plugin\Siezi\PhileTags\Tests;

use Phile\Core\Config;
use Phile\Test\TestCase;

class PluginTest extends TestCase
{
    public function testTag()
    {
        $config = new Config([
            'plugins' => [
                'siezi\\phileTags' => ['active' => true]
            ]
        ]);
        $core = $this->createPhileCore(null, $config);

        $called = 0;
        $core->addBootstrap(function ($eventBus) use (&$called) {
            // fixture
            $eventBus->register('before_read_file_meta', function ($key, $data) {
                $data['meta']->set('tags', ' foo   , bar  ');
            });
            // in additional addBootstrap: put on eventBus plugin's event handling
            $eventBus->register('template_engine_registered', function ($key, $data) use (&$called) {
                $called++;

                $this->assertEquals('foo', $data['data']['current_tag']);
                $this->assertEquals(
                    ['foo', 'bar'],
                    $data['data']['current_page']->getMeta()->get('tags_array')
                );
            });
        });

        $request = $this->createServerRequestFromArray(['REQUEST_URI' => '/tag/foo' ]);
        $response = $this->createPhileResponse($core, $request);

        // make sure tests in event are triggered
        $this->assertEquals(1, $called);
        $this->assertSame(200, $response->getStatusCode());
    }
}
