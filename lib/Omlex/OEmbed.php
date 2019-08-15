<?php

/*
 * This file is part of the Omlex library.
 *
 * (c) Michael H. Arieli <excelwebzone@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Omlex;

use Omlex\Provider;

/**
 * Base class for consuming objects
 *
 * <code>
 * <?php
 *
 * // The URL that we'd like to find out more information about.
 * $url = 'http://www.flickr.com/photos/24887479@N06/2656764466/';
 *
 * // The oEmbed API URI. Not all providers support discovery yet so we're
 * // explicitly providing one here. If one is not provided OEmbed
 * // attempts to discover it. If none is found an exception is thrown.
 * $oEmbed = new Omlex\OEmbed($url, 'http://www.flickr.com/services/oembed/');
 * $object = $oEmbed->getObject();
 *
 * // All of the objects have somewhat sane __toString() methods that allow
 * // you to output them directly.
 * echo (string)$object;
 *
 * ?>
 * </code>
 *
 * @author Michael H. Arieli <excelwebzone@gmail.com>
 */
class OEmbed
{
    /**
     * The API's URI
     *
     * If the API is known ahead of time this option can be used to explicitly
     * set it. If not present then the API is attempted to be discovered
     * through the auto-discovery mechanism.
     *
     * @var string
     */
    protected $endpoint = null;

    /**
     * URL of object to get embed information for
     *
     * @var string
     */
    protected $url = null;

    /**
     * Providers
     *
     * @var array
     */
    protected $providers = array();
    /**
     * @var bool
     */
    protected $discovery = true;
    /**
     * @var Discoverer
     */
    protected $discoverer;

    /**
     * Constructor
     *
     * @param string $url       The URL to fetch from
     * @param string $endpoint  The API endpoint
     * @param array  $providers Additional providers
     * @param bool   $useDefaultProviders whether the default providers should be used.
     */
    public function __construct($url = null, $endpoint = null, array $providers = array(), $useDefaultProviders = true)
    {
        if ($url) {
            $this->setURL($url);
        }

        if ($endpoint && $this->validateURL($endpoint)) {
            $this->endpoint = $endpoint;
        }

        if ($useDefaultProviders) {
            $this->registerDefaultProviders();
        }

        foreach ($providers as $provider) {
            if (is_array($provider) || $provider instanceof Provider) {
                $this->addProvider($provider);
            }
        }
    }

    /**
     * Registers the default providers (clears all current providers).
     *
     * @return OEmbed
     */
    public function registerDefaultProviders()
    {
        $this->providers = array(
            new Provider\Flickr(),
            new Provider\Hulu(),
            new Provider\iFixit(),
            new Provider\PollEverywhere(),
            new Provider\Qik(),
            new Provider\Revision3(),
            new Provider\SlideShare(),
            new Provider\SmugMug(),
            new Provider\Viddler(),
            new Provider\Vimeo(),
            new Provider\YouTube(),
        );

        return $this;
    }

    /**
     * Whether discovery should be used if no provider could be found for the given url.
     *
     * @param boolean $discovery true if discovery should be used, false otherwise.
     *
     * @return OEmbed
     */
    public function setDiscovery($discovery)
    {
        $this->discovery = $discovery;

        return $this;
    }

    /**
     * Returns whether discovery is used if no provider could be found for a given url.
     *
     * @return boolean true if discovery is enabled, false otherwise.
     */
    public function getDiscovery()
    {
        return $this->discovery;
    }

    /**
     * Sets the Discoverer that should be used for discovery of oEmbed endpoints.
     * If no discoverer is set, the default Omlex Discoverer is used.
     *
     * @param \Omlex\Discoverer $discoverer the discover that should be used for discovery of oEmbed endpoints.
     *
     * @return OEmbed
     * @see Discoverer
     */
    public function setDiscoverer(Discoverer $discoverer = null)
    {
        $this->discoverer = $discoverer;

        return $this;
    }

    /**
     * Returns the discoverer that is used for discovery of oEmbed endpoints.
     *
     * @return \Omlex\Discoverer
     */
    public function getDiscoverer()
    {
        return $this->discoverer;
    }



    /**
     * Set a URL to fetch from
     *
     * @param string $url The URL to fetch from
     *
     * @throws \InvalidArgumentException If the URL is invalid
     */
    public function setURL($url)
    {
        if (!$this->validateURL($url)) {
            throw new \InvalidArgumentException(sprintf('The URL "%s" is invalid.', $url));
        }

        $this->url = $url;
        $this->endpoint = null;
    }

    /**
     * Add provider
     *
     * @param array|Provider $provider The provider
     */
    public function addProvider($provider)
    {
        if ($provider instanceof Provider) {
            $this->providers[] = $provider;
        }

        if (is_array($provider)) {
            $this->providers[] = new Provider(
                $provider['endpoint'],
                $provider['schemes'],
                $provider['url'],
                $provider['name']
            );
        }
    }

    /**
     * Removes the given provider from the registered providers array.
     *
     * @param Provider $provider the provider to remove.
     *
     * @return OEmbed
     */
    public function removeProvider(Provider $provider)
    {
        $index = array_search($provider, $this->providers);

        if ($index !== false) {
            unset($this->providers[$index]);
        }

        return $this;
    }

    /**
     * Returns the registered providers.
     *
     * @return array array containing the registered Provider instances.
     *
     * @see Provider
     */
    public function getProviders()
    {
        return $this->providers;
    }

    /**
     * Clears all registered providers.
     *
     * @return OEmbed
     */
    public function clearProviders()
    {
        $this->providers = array();

        return $this;
    }

    /**
     * Validate a URL
     *
     * @param string $url The URL
     *
     * @return Boolean True if valid, false if not
     */
    public function validateURL($url)
    {
        $info = parse_url($url);
        if (false === $info) {
            return false;
        }

        return true;
    }

    /**
     * Get the oEmbed response
     *
     * @param array $params Optional parameters for
     *
     * @return object The oEmbed response as an object
     *
     * @throws \RuntimeException         On HTTP errors
     * @throws \InvalidArgumentException when result is not parsable
     */
    public function getObject(array $parameters = array())
    {
        if ($this->url === null) {
            throw new \InvalidArgumentException('Missing URL.');
        }

        if ($this->endpoint === null) {
            $this->endpoint = $this->discover($this->url);
        }

        $sign = '?';
        if ($query = parse_url($this->endpoint, PHP_URL_QUERY)) {
            $sign = '&';

            parse_str($query, $parameters);
        }

        if (!isset($parameters['url'])) {
            $parameters['url'] = $this->url;
        }
        if (!isset($parameters['format'])) {
            $parameters['format'] = 'json';
        }

        $client = new Client(
            sprintf('%s%s%s', $this->endpoint, $sign, http_build_query($parameters))
        );

        $data = $client->send();

        switch ($parameters['format']) {
            case 'json':
                $data = json_decode($data);
                if (!is_object($data)) {
                    throw new \InvalidArgumentException('Could not parse JSON response.');
                }

                break;

            case 'xml':
                libxml_use_internal_errors(true);
                $data = simplexml_load_string($data);
                if (!$data instanceof \SimpleXMLElement) {
                    $errors = libxml_get_errors();
                    $error  = array_shift($errors);
                    libxml_clear_errors();
                    libxml_use_internal_errors(false);
                    throw new \InvalidArgumentException($error->message, $error->code);
                }

                break;
        }

        return OmlexObject::factory($data);
    }

    /**
     * Discover an oEmbed API endpoint
     *
     * @param string $url The URL to attempt to discover Omlex for
     *
     * @return string The oEmbed API endpoint discovered
     *
     * @throws \InvalidArgumentException If not $endpoint was found
     */
    protected function discover($url)
    {
        $endpoint = $this->findEndpointFromProviders($url);

        // if no provider was found, try to discover the endpoint URL
        if ($this->discovery && !$endpoint) {
            $discover = new Discoverer();
            $endpoint = $discover->getEndpointForUrl($url);
        }

        if (!$endpoint) {
            throw new \InvalidArgumentException('No oEmbed links found.');
        }

        return $endpoint;
    }

    /**
     * Finds an endpoint by looping trough the providers array and matching the url against
     * the allowed schemes for each provider.
     *
     * @param string $url the url to find an endpoint for.
     *
     * @return string|null the endpoint if a match was found, null if no suitable provider was found.
     */
    protected function findEndpointFromProviders($url)
    {
        // try to find a provider matching the supplied URL if no one has been supplied
        foreach ($this->providers as $provider) {
            /** @var $provider Provider */
            if ($provider->match($url)) {
                return $provider->getEndpoint();
            }
        }

        return null;
    }
}
