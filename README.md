Omlex
=====

This is a lightweight PHP 5.3 library for handling oEmbed services.

```php
<?php

require_once 'Omlex/ClassLoader.php';
Omlex\ClassLoader::register();

$ombed = new Omlex\OEmbed(
    'http://www.flickr.com/photos/24887479@N06/2656764466/', // URL
    'http://www.flickr.com/services/oembed/',                // API (optional)
);
$object = $ombed->getObject();

echo $object->__toString();
```

Here is the result for <code>print_r($object)</code>:

```
Omlex\Object\Photo Object
(
    [required:protected] => Array
        (
            [0] => url
            [1] => width
            [2] => height
            [3] => version
        )

    [object:protected] => stdClass Object
        (
            [version] => 1.0
            [type] => photo
            [author_url] => http://www.flickr.com/photos/24887479@N06/
            [cache_age] => 3600
            [provider_name] => Flickr
            [provider_url] => http://www.flickr.com/
            [title] => Torrie Wilson
            [author_name] => jtellolopez
            [width] => 411
            [height] => 500
            [url] => http://farm4.static.flickr.com/3245/2656764466_afa90677e1.jpg
        )

)
```

Omlex is preset with a list of providers (see http://oembed.com/#section7).
The following is an example of how to add additional providers:

```php
<?php

$ombed->addProvider(
    new Omlex\Provider(
        'http://lab.viddler.com/services/oembed/',
        array(
            'http://*.viddler.com/*',
        ),
        'http://www.viddler.com',
        'Viddler'
    )
);

// or

$ombed->addProvider(
    array(
        'endpoint' => 'http://qik.com/api/oembed.json', // or xml
        'schemes'  => array(
            'http://qik.com/video/*',
            'http://qik.com/*',
        ),
        'url'      => 'http://qik.com',
        'name'     => 'Qik'
    )
);
```


Omlex is tested using PHPUnit. The run the test suite, execute the following
command:

    $ phpunit test/

## Integration

  - **With Symfony2**: Seamless integration with Symfony2 is provided by the [EWZOmlexBundle][1].

[1]: https://github.com/excelwebzone/EWZOmlexBundle
