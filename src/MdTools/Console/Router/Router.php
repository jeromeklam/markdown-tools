<?php
/**
 * Router en mode console
 *
 * @author jeromeklam
 * @package Routing
 * @category Console
 */
namespace MdTools\Console\Router;

/**
 * Router console
 * @author jeromeklam
 */
class Router
{

    /**
     * Instance
     *
     * @var Router
     */
    protected static $instance = null;

    /**
     * Les routes
     * @var array
     */
    protected $routes = array();

    /**
     * La requête
     * @var \MdTools\Console\Request
     */
    protected $request = null;

    /**
     * Retourne une instance
     *
     * @param array $p_config
     *
     * @return \MdTools\Console\Router\Router
     */
    public static function getInstance(array $p_config = null)
    {
        if (self::$instance === null) {
            self::$instance = new self();
            if (is_array($p_config) && array_key_exists('routes', $p_config)) {
                self::$instance->addRoutes($p_config['routes']);
            }
        }
        return self::$instance;
    }

    /**
     * Ajout de routes
     *
     * @param array $p_routes
     *
     * @return \MdTools\Console\Router\Router
     */
    public function addRoutes($p_routes)
    {
        foreach ($p_routes as $name => $oneRoute) {
            if (is_array($oneRoute)) {
                $route = new \MdTools\Console\Router\Route();
                $route->setName($name);
                if (array_key_exists('description', $oneRoute)) {
                    $route->setDescription($oneRoute['description']);
                }
                if (array_key_exists('controller', $oneRoute)) {
                    $route->setController($oneRoute['controller']);
                }
                $this->routes[] = $route;
            }
        }
        return $this;
    }

    /**
     * Affectation de la requête
     *
     * @param \MdTools\Console\Request $p_request
     *
     * @return \MdTools\Console\Router\Router
     */
    public function setRequest(\MdTools\Console\Request $p_request)
    {
        $this->request = $p_request;
        return $this;
    }

    /**
     * Retourne la requête
     *
     * @return \MdTools\Console\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Recherche d'une route correspondante
     *
     * @param string $p_command
     * @param array  $p_params
     * @param string $p_requestMethod
     *
     * @return mixed (boolean | \MdTools\Console\Router\Route)
     */
    protected function match($p_command, $p_params, $p_requestMethod = 'CMD')
    {
        foreach ($this->routes as $route) {
            if (!$route->hasMethod($p_requestMethod)) {
                continue;
            }
            if ($route->getName() == $p_command) {
                $route->setParameters($p_params);
                return $route;
            }
        }
        return false;
    }

    /**
     * Préparation de la recherche de la route
     *
     * @todo : gérer les paramètres de la forme --name=value
     *
     * @return mixed (boolean | \MdTools\Console\Router\Route)
     */
    public function matchCurrentRequest()
    {
        $request = $this->getRequest();
        if ($request instanceof \MdTools\Console\Request) {
            $command = $request->getCommand();
            if ($command !== false) {
                return $this->match($command, $request->getAttributes());
            }
        }
        return false;
    }
}
