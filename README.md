SahiDriver
==========

Driver to [Sahi](http://sahi.co.in/w/sahi) JS test automation tool.

Usage
-----

1. First, install & configure Sahi as [described here](http://sahi.co.in/w/using-sahi)
2. Second, in proxy-configured browser open "http://sahi.example.com/_s_/dyn/Driver_start?sahisid=@@SAHI_SESSION_ID@@&startUrl=http%3A%2F%2Fsahi.example.com%2F_s_%2Fdyn%2FDriver_initialized". But don't forget to replace "@@SAHI_SESSION_ID@@" in URL with preferred custom sahi session name. It can be any string.
3. After that, you could write/run initial script like this:

    <?php
    
    require_once '/sahi/driver/path/autoload.php.dist';
    
    use Everzet\SahiDriver;
    
    $connection = new SahiDriver\Connection('@@SAHI_SESSION_ID@@'); // replace with your custom session ID
    $browser    = new SahiDriver\Browser($connection);

4. And now, you can work with `$browser` object as with Sahi remote controll:

    <?php
    
    ...
    
    $browser->navigateTo('http://some_page.loc');
    
    $link = $browser->findLink('Search!');
    $previousLinkText = $link->getText();
    $link->click();
    
    $h1Text = $browser->findHeader(2)->getText();


Copyright
---------

SahiDriver Copyright (c) 2010 Konstantin Kudryashov (ever.zet). See LICENSE for details.

Contributors
------------

* Konstantin Kudryashov [everzet](http://github.com/everzet) [lead developer]