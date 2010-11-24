<?php

namespace Everzet\SahiDriver;

use Everzet\SahiDriver\Accessor;

/*
 * This file is part of the SahiDriver.
 * (c) 2010 Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Browser Accessor API methods.
 */
class Browser
{
    /**
     * Sahi Driver instance.
     *
     * @var     Driver
     */
    protected $con;

    /**
     * Initialize Accessor.
     *
     * @param   Connection  $connection Sahi Connection
     */
    public function __construct(Connection $con)
    {
        $this->con = $con;
    }

    /**
     * Create & return new DomElement tied to current connection.
     *
     * $browser->selectLink()
     * $browser->selectLink(12)
     * $browser->selectImage('sahi_id')
     * $browser->selectLabel('agree')
     * $browser->selectDiv('/.*_div/')
     *
     * @param   string  $func       function name
     * @param   string  $arguments  function arguments
     *
     * @return  DomElement
     */
    public function __call($func, $arguments)
    {
        if (preg_match('/^select(.*)$/', $func, $matches)) {
            return new Accessor\DomElement(
                lcfirst($matches[1]),
                isset($arguments[0]) ? $arguments[0] : null,
                isset($arguments[1]) ? $arguments[1] : array(),
                $this->con
            );
        } else {
            throw new \InvalidArgumentException(
                sprintf('Unknown method called %s::%s', get_class($this), $func)
            );
        }
    }

    /**
     * Return browser title.
     *
     * @return  string
     */
    public function getTitle()
    {
        return $this->con->executeJavascript('_sahi._title()');
    }

    /**
     * Navigates to the given URL.
     *
     * @param   string  $url    URL
     */
    public function navigateTo($url)
    {
        $this->con->executeStep(sprintf('_sahi._navigateTo("%s")', $url));
    }

    /**
     * Set speed of execution (in milli seconds).
     *
     * @param   integer $speed  speed in milli seconds
     */
    public function setSpeed($speed)
    {
        $this->con->executeCommand('setSpeed', array('speed' => $speed));
    }

    /**
     * Get last browser alert message.
     *
     * @return  string|null
     */
    public function getLastAlert()
    {
        return $this->con->executeJavascript('_sahi._lastAlert()');
    }

    /**
     * Clear last browser alert message.
     */
    public function clearLastAlert()
    {
        $this->con->executeStep('_sahi._clearLastAlert()');
    }

    /**
     * Get last browser confirm message.
     *
     * @return  string|null
     */
    public function getLastConfirm()
    {
        return $this->con->executeJavascript('_sahi._lastConfirm()');
    }

    /**
     * Clear last browser confirm message.
     */
    public function clearLastConfirm()
    {
        $this->con->executeStep('_sahi._clearLastConfirm()');
    }

    /**
     * Set an expectation to press OK (true) or Cancel (false) for specific confirm message.
     *
     * @param   string  $message    confirm message
     * @param   string  $input      OK|Cancel
     */
    public function expectConfirm($message, $input)
    {
        $this->con->executeStep(sprintf('_sahi._expectConfirm(\'%s\', %s)', $message, $input));
    }

    /**
     * Get last browser prompt message.
     *
     * @return  string|null
     */
    public function getLastPrompt()
    {
        return $this->con->executeJavascript('_sahi._lastPrompt()');
    }

    /**
     * Clear last browser prompt message.
     */
    public function clearLastPrompt()
    {
        $this->con->executeStep('_sahi._clearLastPrompt()');
    }

    /**
     * Set an expectation to press OK (true) or Cancel (false) for specific prompt message.
     *
     * @param   string  $message    prompt message
     * @param   string  $input      OK|Cancel
     */
    public function expectPrompt($message, $input)
    {
        $this->con->executeStep(sprintf('_sahi._expectPrompt(\'%s\', %s)', $message, $input));
    }

    /**
     * Return last downloaded file's filename.
     *
     * @return  string|null
     */
    public function getLastDownloadedFilename()
    {
        return $this->con->executeJavascript('_sahi._lastDownloadedFileName()');
    }

    /**
     * Clear last browser downloaded file's filename.
     */
    public function clearLastDownloadedFilename()
    {
        $this->con->executeStep('_sahi._lastDownloadedFileName()');
    }

    /**
     * Save last browser downloaded file at path.
     */
    public function saveLastDownloadedFile($path)
    {
        $this->con->executeStep(sprintf('_sahi._saveDownloadedAs(\'%s\')', $path));
    }
}
