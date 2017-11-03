<?php
/**
 * Classe de gestion d'une route
 *
 * @author jeromeklam
 * @package Routing
 * @category Console
 */
namespace MdTools\Console\Router;

/**
 * Une route simplifiée
 * @author jeromeklam
 */
class Route {

    /**
     * Types disponibles
     * @var string
     */
    const TYPE_CMD = 'CMD';

    /**
     * Nom de la route
     * @var string
     */
    protected $name = null;

    /**
     * Description
     * @var string
     */
    protected $description = null;

    /**
     * Le controlleur
     * @var string
     */
    protected $controller = null;

    /**
     * Méthodes autorisées
     * @var array
     */
    protected $methods = array(self::TYPE_CMD);

    /**
     * Paramètres
     * @var array
     */
    protected $parameters = array();

    /**
     * Retourne la liste des méthodes disponibles
     *
     * @return array
     */
    protected static function getAllMethods()
    {
        return array(self::TYPE_CMD);
    }

    /**
     * Affectation du nom de la route
     *
     * @return \MdTools\Console\Router\Route
     */
    public function setName($p_name)
    {
        $this->name = $p_name;
        return $this;
    }

    /**
     * Retoune le nom de la route
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Affectation de la description
     *
     * @param string $p_desc
     *
     * @return \MdTools\Console\Router\Route
     */
    public function setDescription($p_desc)
    {
        $this->description = $p_desc;
        return $this;
    }

    /**
     * Retourne la description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Affectation du controlleur
     *
     * @param string $p_controller
     *
     * @return \MdTools\Console\Router\Route
     */
    public function setController($p_controller)
    {
        $this->controller = $p_controller;
        return $this;
    }

    /**
     * Retourne le controlleur
     *
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Affectation des paramètres
     *
     * @param array $p_parameters
     *
     * @return \MdTools\Console\Router\Route
     */
    public function setParameters($p_parameters)
    {
        $this->parameters = $p_parameters;
        return $this;
    }

    /**
     * Retourne les paramètres
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Affectation des méthodes
     *
     * @param array $p_methods
     *
     * @return \MdTools\Console\Router\Route
     */
    public function setMethods($p_methods)
    {
        $this->methods = [];
        foreach ($p_methods as $idx => $name) {
            if (in_array($name, self::getAllMethods())) {
                $this->methods[] = $name;
            }
        }
        return $this;
    }

    /**
     * Retourne les méthodes
     *
     * @return array
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * Dispose de la méthode ?
     *
     * @param string $p_method
     *
     * @return boolean
     */
    public function hasMethod($p_method)
    {
        if (in_array($p_method, $this->methods)) {
            return true;
        }
        return false;
    }

    /**
     * Dispatch action
     *
     * @return boolean
     */
    public function dispatch(\MdTools\Console\Request $p_request)
    {
        $ctrl = $this->getController();
        if (class_exists($ctrl)) {
            $class = new $ctrl();
            if (method_exists($class, 'process')) {
                $input  = new \MdTools\Console\Input\ParameterInput();
                $output = new \MdTools\Console\Output\ConsoleOutput();
                $output->write($this->getDescription(), true);
                $input->setParams($p_request->getAttributes());
                $class->process($input, $output);
            } else {
                throw new \Exception(sprintf('Method %s doesn\'t exists in %s class!', 'process', $ctrl));
            }
        } else {
            throw new \Exception(sprintf('Class %s doesn\'t exists !', $ctrl));
        }
        return true;
    }
}
