<?php
/**
 * Injecteur de dépendances
 *
 * @author jeromeklam
 * @package DI
 * @category Console
 */
namespace MdTools;

/**
 * Old school DI ;)
 * @author jeromeklam
 */
class DI {

    private static $di_instance = null;

    /**
     * Ressources
     * @var array
     */
    protected $shared = array();

    /**
     * Constructeur
     */
    protected function __construct()
    {
    }

    /**
     * Retourne une instance
     *
     * @return \MdTools\DI
     */
    public static function getDI()
    {
        if (self::$di_instance === null) {
            self::$di_instance = new self();
        }
        return self::$di_instance;
    }

    /**
     * Ajout d'une ressource
     *
     * @param string $p_name
     * @param object $p_resource
     *
     * @return \MdTools\DI
     */
    public function setShared($p_name, $p_resource)
    {
        $this->shared[$p_name] = $p_resource;
        return $this;
    }

    /**
     * Retourne une ressource
     *
     * @param string $p_name
     *
     * @return boolean | object
     */
    public function getShared($p_name)
    {
        if (array_key_exists($p_name, $this->shared)) {
            $this->shared[$p_name];
        }
        return false;
    }
}
