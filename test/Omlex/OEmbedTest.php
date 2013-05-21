<?php

namespace Omlex;

use Omlex\OEmbed;
use Omlex\Object;
use Omlex\Exception\ObjectException;

class OEmbedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Array of test objects to fetch and validate
     *
     * @var array
     */
    protected $objects = array(
        'photo' => array(
            'url' => 'http://www.flickr.com/photos/jtellolopez/2656764466/',
            'api' => 'http://www.flickr.com/services/oembed/',
            'expected' => array(
                'version'       => '1.0',
                'type'          => 'photo',
                'author_url'    => 'http://www.flickr.com/photos/24887479@N06/',
                'cache_age'     => 3600,
                'provider_name' => 'Flickr',
                'provider_url'  => 'http://www.flickr.com/',
                'title'         => 'Torrie Wilson',
                'author_name'   => 'jtellolopez',
                'width'         => '842',
                'height'        => '1024',
                'url'           => 'http://farm4.staticflickr.com/3245/2656764466_afa90677e1_b.jpg',
            )
        ),
        'video' => array(
            'url' => 'http://www.youtube.com/watch?v=ReSxgDpAJwk',
            'api' => 'http://www.youtube.com/oembed/',
            'expected' => array(
                'provider_url'     => 'http://www.youtube.com/',
                'title'            => 'torrie wilson vs trish stratus',
                'html'             => '<iframe width="459" height="344" src="http://www.youtube.com/embed/ReSxgDpAJwk?feature=oembed" frameborder="0" allowfullscreen></iframe>',
                'author_name'      => 'johnnyg08',
                'height'           => 344,
                'thumbnail_width'  => 480,
                'width'            => 459,
                'version'          => '1.0',
                'author_url'       => 'http://www.youtube.com/user/johnnyg08',
                'provider_name'    => 'YouTube',
                'thumbnail_url'    => 'http://i3.ytimg.com/vi/ReSxgDpAJwk/hqdefault.jpg',
                'type'             => 'video',
                'thumbnail_height' => 360,
            )
        )
    );

    /**
     * An invalid object to test
     *
     * @var array
     */
    protected $error = array(
        'url' => 'http://www.flickr.com/photos/jtellolopez/2656764466/',
        'api' => 'http://www.flickr.com/services/oembed/'
    );

    /**
     * An object that does not exist
     *
     * @var array
     */
    protected $notFound = array(
        'url' => 'http://www.flickr.com/photos/jtellolopez/265676446621323/',
        'api' => 'http://www.flickr.com/services/oembed/'
    );

    /**
     * Test fetching all of the objects
     *
     * @return void
     */
    public function testGetObjects()
    {
        foreach ($this->objects as $type => $test) {
            $object = $this->getObject($test);

            $expectedObject = '\\Omlex\\Object\\' . ucfirst($type);
            $this->assertInstanceof($expectedObject, $object);

            foreach ($test['expected'] as $key => $val) {
                $this->assertEquals($val, $object->$key, sprintf('Unexpected %s value for object type %s', $key, $type));
            }
        }
    }

    /**
     * Test the error
     *
     * @return void
     */
    public function testError()
    {
        $this->markTestSkipped('Object seems to be valid');

        try {
            $object = $this->getObject($this->error);
        } catch (ObjectException $e) {
            return;
        }

        $this->fail('An expected exception has not been raised.');
    }

    /**
     * @expectedException RuntimeException
     */
    public function testNotFoundError()
    {
        $object = $this->getObject($this->notFound);
    }

    public function testRemoveProvider()
    {
        $omlex = new OEmbed();
        $providers = $omlex->getProviders();

        $providerCount = count($providers);
        $omlex->removeProvider($providers[0]);
        $this->assertEquals($providerCount - 1, count($omlex->getProviders()));
    }

    public function testAddProvider()
    {
        $omlex = new OEmbed(null, null, array(), false);
        $yt = new \Omlex\Provider\YouTube();
        $omlex->addProvider($yt);

        $providers = $omlex->getProviders();
        $this->assertEquals(1, count($providers));
        $this->assertEquals($yt, $providers[0]);
    }

    public function testClearProviders()
    {
        $omlex = new OEmbed();
        $this->assertNotEmpty($omlex->getProviders());
        $omlex->clearProviders();
        $this->assertEmpty($omlex->getProviders());
    }

    public function testDiscovery()
    {
        $omlex = new OEmbed();
        $this->assertEquals(true, $omlex->getDiscovery());
        $omlex->setDiscovery(false);

        $this->assertEquals(false, $omlex->getDiscovery());
    }

    public function testDiscoverer()
    {
        $omlex = new OEmbed();
        $this->assertNull($omlex->getDiscoverer());

        $discoverer = new Discoverer();
        $omlex->setDiscoverer($discoverer);

        $this->assertEquals($discoverer, $omlex->getDiscoverer());
    }

    /**
     * Get the Omlex object
     *
     * @param array $test The test object to fetch
     *
     * @return object Instance of Object
     */
    protected function getObject(array $test)
    {
        $omlex = new OEmbed($test['url'], $test['api']);

        return $omlex->getObject();
    }
}
