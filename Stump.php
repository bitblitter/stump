<?php
/**
 * Minimal framework for very small web applications.
 */

namespace Bitblitter;


class Stump
{
    protected $config = array();
    protected $routes = array();
    protected $modules = array();

    function __construct(array $config=array())
    {
        $this->config = $config;
    }

    /**
     * Set up the application.
     *
     */
    public function setup()
    {
    }


    /**
     * Registers a module, a function that can be shared across the application.
     * The function must return a callable, that will be executed each time the
     * module is instanced.
     *
     * @param $moduleName
     * @param $moduleCallable
     * @param array $settings   Application settings to apply before registering.
     */
    public function registerModule($moduleName, $moduleCallable, array $settings = array())
    {
        $this->config = array_merge($this->config, $settings);
        $this->modules[$moduleName] = $moduleCallable;
    }

    /**
     * Calls a registered module
     *
     * @param $moduleName
     * @throws \Exception
     * @return mixed|null
     */
    public function module($moduleName)
    {
        if(isset($this->modules[$moduleName]))
        {
            $moduleCallable = $this->modules[$moduleName];
            if(is_callable($moduleCallable)){
                return call_user_func($moduleCallable, $this);
            } else {
                throw new \Exception('invalid module callable: '.print_r($moduleCallable, true));
            }
        }
        return null;
    }

    /**
     * Retrieve a configuration value
     *
     * @param $key
     * @param null $default
     * @return null
     */
    public function getConfig($key, $default = null)
    {
        $ret = $default;
        if(isset($this->config[$key])){
            $ret = $this->config[$key];
        }
        return $ret;
    }


    /**
     * Specify a configuration value.
     *
     * @param $key
     * @param null $value
     */
    public function setConfig($key, $value = null)
    {
        $this->config[$key] = $value;
    }


    /**
     * Set a route.
     *
     * @param $path string URL path to match.
     * @param $callable Callable to run.
     */
    public function route($path, $callable)
    {
        $this->routes[] = array($path, $callable);
    }

    /**
     * Checks for matching configured routes.
     *
     * @param $path
     * @param array $parameters
     * @return null
     */
    protected function getMatchingRoute($path, &$parameters = array())
    {
        $ret = null;
        foreach($this->routes as $route)
        {
            if(preg_match('#^'.$route[0].'$#', $path, $parameters)){
                $ret = $route;
                array_shift($parameters);
                break;
            }
        }
        return $ret;
    }


    /**
     * Stop the application and optionally show an error message.
     *
     * @param string $error
     * @param int $httpErrorCode
     */
    public function halt($error='unspecified error', $httpErrorCode = 404)
    {
        $errorConfig = $this->getConfig('error_views', null);
        $template = null;
        if(is_array($errorConfig)){
            $template = $errorConfig['default'];
            if(isset($errorConfig[$httpErrorCode])){
                $template = $errorConfig[$httpErrorCode];
            }
        }
        header('X-Error-Message: '.$error, true, $httpErrorCode);
        if($template){
            die($this->render($template, array('message' => $error)));
        } else {
            die($error);
        }
    }

    /**
     * Run the application
     */
    public function run()
    {
        $this->setup();
        $path = $this->getBaseURL();
        $path = preg_replace('/\?.*/', '', $path);
        if($path == '') $path = '/';
        $parameters = array();
        $route = $this->getMatchingRoute($path, $parameters);
        if($route != null){
            $callable = $route[1];
            array_unshift($parameters, $this);
            $ret = call_user_func_array($callable, $parameters);
            echo $ret;
        } else {
            $this->halt('No route found for '.$path);
        }
    }

    /**
     * Check what URL should be used as base.
     * @return mixed
     */
    protected function getBaseURL()
    {
        //using mod_rewrite?
        if(isset($_SERVER['REDIRECT_URL'])){
            $ret = str_replace(dirname($_SERVER['SCRIPT_NAME']), '', $_SERVER['REQUEST_URI']);
        } else {
            $ret = str_replace($_SERVER['SCRIPT_NAME'], '', $_SERVER['REQUEST_URI']);
        }
        return $ret;
    }

    /**
     * Get a query string parameter value.
     *
     * @param $key string parameter name
     * @param null $defaultValue   Default value if parameter not set.
     * @return null
     */
    public function getQuery($key, $defaultValue = null)
    {
        $ret = $defaultValue;
        if(isset($_GET[$key])){
            $ret = $_GET[$key];
        }
        return $ret;
    }

    /**
     * Get a POST parameter value
     *
     * @param $key
     * @param null $defaultValue
     * @return null
     */
    public function getPost($key, $defaultValue = null)
    {
        $ret = $defaultValue;
        if(isset($_POST[$key])) {
            $ret = $_POST[$key];
        }
        return $ret;
    }

    /**
     * Render a PHP file with the variables provided.
     *
     * @param $view string File name
     * @param array $parameters    Variables available to the view.
     * @return string
     */
    public function render($view, array $parameters = array())
    {
        $viewDir = $this->getConfig('view_dir', 'views/');
        $viewPath = $viewDir.$view.'.php';
        if(file_exists($viewPath))
        {
            ob_start();
            extract($parameters);
            require($viewPath);
            $ret = ob_get_contents();
            ob_end_clean();
            return $ret;
        } else {
            $this->halt('View not found', 404);
        }
        return true;
    }
}
