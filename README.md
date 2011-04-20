SahiClient
==========

Driver to [Sahi](http://sahi.co.in/w/sahi) JS test automation tool.

Usage
-----

1. First, install & configure Sahi as [described here](http://sahi.co.in/w/using-sahi)
2. After that, you could write/run initial script like this:

    ``` php
    <?php

    require_once '/sahi/driver/path/autoload.php.dist';

    use Behat\SahiClient\Client;

    $client = new Client();
    $client->start('firefox');
    ....
    ```

3. And now, you can work with `$browser` object as with Sahi remote controll:

    ``` php
    <?php
    ...

    $client->navigateTo('http://some_page.loc');

    $link = $client->findLink('Search!');
    $previousLinkText = $link->getText();
    $link->click();

    $h1Text = $client->findHeader(2)->getText();
    ....
    ```

4. After tests, turn browser off:

    ``` php
    <?php
    ...
    $client->stop();
    ```

Copyright
---------

SahiClient Copyright (c) 2011 Konstantin Kudryashov (ever.zet). See LICENSE for details.

Contributors
------------

* Konstantin Kudryashov [everzet](http://github.com/everzet) [lead developer]
