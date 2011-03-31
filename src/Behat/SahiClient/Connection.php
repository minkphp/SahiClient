<?php

namespace Behat\SahiClient;

use Buzz;

use Behat\SahiClient\Exception;

/*
 * This file is part of the Behat\SahiClient.
 * (c) 2010 Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Sahi Connection Driver.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class Connection
{
    /**
     * Sahi SID
     *
     * @var     integer
     */
    private     $sid;
    /**
     * Sahi proxy hostname
     *
     * @var     string
     */
    private     $host;
    /**
     * Sahi proxy port number
     *
     * @var     integer
     */
    private     $port;

    /**
     * HTTP Browser instance.
     *
     * @var     BrowserInterface
     */
    protected   $browser;

    /**
     * Initialize Sahi Driver.
     *
     * @param   string      $sid        Sahi SID
     * @param   string      $host       Sahi proxy host
     * @param   integer     $port       Sahi proxy port
     * @param   BuzzBrowser $browser    HTTP browser instance
     */
    public function __construct($sid, $host = 'localhost', $port = 9999, Buzz\Browser $browser = null)
    {
        $this->sid  = $sid;
        $this->host = $host;
        $this->port = $port;

        if (null === $browser) {
            $client = new Buzz\Client\Curl();
            $this->browser = new Buzz\Browser($client);
        } else {
            $this->browser = $browser;
        }
    }

    /**
     * Starts browser.
     *
     * @param   string  $browserName    (firefox, ie, safari, chrome, opera)
     */
    public function start($browserName)
    {
        $this->executeCommand('launchPreconfiguredBrowser', array('browserType' => $browserName));
    }

    /**
     * Stop browser.
     */
    public function stop()
    {
        $this->executeCommand('kill');
    }

    /**
     * Checks whether Sahi proxy were started.
     *
     * @return  Boolean
     */
    public function isProxyStarted()
    {
        return 200 === $this->post(
            sprintf('http://%s:%d/_s_/spr/blank.htm', $this->host, $this->port)
        )->getStatusCode();
    }

    /**
     * Checks whether connection is ready.
     *
     * @return  Boolean
     */
    public function isReady()
    {
        return 'true' === $this->executeCommand('isReady');
    }

    /**
     * Return HTTP Browser instance.
     *
     * @return  Browser
     */
    public function getBrowser()
    {
        return $this->browser;
    }

    /**
     * Execute Sahi command & returns its response.
     *
     * @param   string  $command        Sahi command
     * @param   array   $parameters     parameters
     *
     * @return  string                  command response
     */
    public function executeCommand($command, array $parameters = array())
    {
        return $this->post(
            sprintf('http://%s:%d/_s_/dyn/Driver_%s', $this->host, $this->port, $command),
            array_merge($parameters, array('sahisid' => $this->sid))
        )->getContent();
    }

    /**
     * Execute Sahi step.
     *
     * @param   string  $step       step command
     *
     * @throws  BrowserException    if step execution has errors
     */
    public function executeStep($step)
    {
        $this->executeCommand('setStep', array('step' => $step));

        for ($i = 0; $i < 500; $i++) {
            usleep(100000);

            $check = $this->executeCommand('doneStep');

            if ('true' === $check) {
                return;
            } elseif (0 === mb_strpos($check, 'error:')) {
                throw new Exception\ConnectionException($check);
            }
        }
    }

    /**
     * Execute JS expression on the browser and get it value.
     *
     * @param   string  $expression JS expression
     *
     * @return  string|null
     */
    public function executeJavascript($expression)
    {
        $key = '___lastValue___' . uniqid();
        $this->executeStep(
            sprintf("_sahi.setServerVarPlain(%s, %s)", "'" . $key . "'", $expression)
        );

        $resp = $this->executeCommand('getVariable', array('key' => $key));

        return 'null' === $resp ? null : $resp;
    }

    /**
     * Send POST request to specified URL.
     *
     * @param   string  $url    URL
     * @param   array   $query  POST query parameters
     *
     * @return  string          response
     */
    private function post($url, array $query = array())
    {
        return $this->browser->post($url, array(), $this->prepareQueryString($query));
    }

    /**
     * Convert array parameters to POST parameters.
     *
     * @param   array   $query  parameters
     *
     * @return  string          query string (key1=val1&key2=val2)
     */
    private function prepareQueryString(array $query)
    {
        $items = array();
        foreach ($query as $key => $val) {
            $items[] = $key . '=' . urlencode($val);
        }

        return implode('&', $items);
    }
}
