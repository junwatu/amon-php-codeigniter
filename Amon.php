<?php
require_once "Amon/AmonConfig.php";
require_once "Amon/AmonData.php";
require_once "Amon/PhpException.php";
require_once "Amon/AmonRemote.php";
require_once "Amon/AmonPhpNotice.php";
require_once "Amon/AmonPhpStrict.php";
require_once "Amon/AmonPhpWarning.php";

class Amon
{
    protected $exceptions;
    protected $previous_exception_handler;
    protected $previous_error_handler;
    protected $controller;
    protected $action;
    protected $config_array;
    protected $amonremote;

    public function __construct()
    {
        $this->amonremote = new AmonRemote();
    }

    /**
     *  Overwrite the default configuration
     *  Amon::config(array('port', 'host', 'application_key'))
     *
     */
    public function config($array)
    {
        $this->config_array = (object)$array;
        // Construct the url
        $this->config_array->url = sprintf("%s:%d", $this->config_array->host, $this->config_array->port);
    }

    /** Check for the config array or default to /etc/amon.conf */
    private function _get_config_object()
    {
        if (empty($this->config_array)) {
            $config = new AmonConfig();
        }
        else
        {
            $config = $this->config_array;
        }
        return $config;
    }

    /**
     * Log!
     *
     * @param string $message
     * @param string $level
     *
     * @return void
     */
    public function log($message, $tags = '')
    {
        $data = array(
            'message' => $message,
            'tags' => $tags
        );
        $config = $this->_get_config_object();
        $log_url = sprintf("%s/api/log", $config->url);
        if ($config->application_key) {
            $log_url = sprintf("%s/%s", $log_url, $config->application_key);
        }

        $this->amonremote->request($log_url, $data);
    }

    public function shutdown()
    {
        if ($e = error_get_last()) {
            $this->handle_error($e["type"], $e["message"], $e["file"], $e["line"]);
        }
    }

    public function handle_error($errno, $errstr, $errfile, $errline)
    {
        if (!(error_reporting() & $errno)) {
            return;
        }

        switch ($errno) {
            case E_NOTICE:
            case E_USER_NOTICE:
                $ex = new AmonPhpNotice($errstr, $errno, $errfile, $errline);
                break;

            case E_WARNING:
            case E_USER_WARNING:
                $ex = new AmonPhpWarning($errstr, $errno, $errfile, $errline);
                break;

            case E_STRICT:
                $ex = new AmonPhpStrict($errstr, $errno, $errfile, $errline);
                break;

            case E_PARSE:
                $ex = new AmonPhpParse($errstr, $errno, $errfile, $errline);
                break;

            default:
                $ex = new AmonPhpError($errstr, $errno, $errfile, $errline);
        }

        $this->handle_exception($ex, false);

        if ($this->previous_error_handler) {
            call_user_func($this->previous_error_handler, $errno, $errstr, $errfile, $errline);
        }
    }

    /*
    * Exception handle class. Pushes the current exception onto the exception
    * stack and calls the previous handler, if it exists. Ensures seamless
    * integration.
    */
    function handle_exception($exception, $call_previous = true)
    {
        $config = $this->_get_config_object();
        $exception_url = sprintf("%s/api/exception", $config->url);
        if ($config->application_key) {
            $exception_url = sprintf("%s/%s", $exception_url, $config->application_key);
        }

        $this->exceptions[] = $exception;

        $data = new AmonData($exception);
        $this->amonremote->request($exception_url, $data->data);

        // if there's a previous exception handler, we call that as well
        if ($call_previous && $this->previous_exception_handler) {
            call_user_func($this->previous_exception_handler, $exception);
        }
    }

    public function setup_exception_handler()
    {
        $this->exceptions = array();
        $this->action = "";
        $this->controller = "";

        // set exception handler & keep old exception handler around
        $this->previous_exception_handler = set_exception_handler(
            array($this, "handle_exception")
        );

        $this->previous_error_handler = set_error_handler(
            array($this, "handle_error")
        );

        register_shutdown_function(
            array($this, "shutdown")
        );
    }
}


