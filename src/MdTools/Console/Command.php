<?php
/**
 * Classe de base des commandes
 *
 * @autgor jeromeklam
 * @package Command
 */
namespace PawBx\Core\Console;

/**
 * Classe de base d'une commande
 * @author jeromeklam
 */
class Command
{

    /**
     * Comportements
     */
    use \PawBx\Core\Behaviour\DI;
    use \PawBx\Core\Behaviour\LoggerAwareTrait;
}
