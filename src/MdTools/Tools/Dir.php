<?php
/**
 * Fonctions utilitaires sur les répertoires
 *
 * @author jeromeklam
 * @package System
 * @category Tools
 */
namespace MdTools\Tools;

/**
 * Fonctions utilitaires sur les répertoires
 * @author jeromeklam
 */
class Dir
{

    /**
     * Création récursive d'un chemin
     *
     * @param string $path
     *
     * @return boolean
     */
    public static function mkpath($path)
    {
        if (@mkdir($path) || file_exists($path)) {
            return true;
        }
        return (self::mkpath(dirname($path)) && mkdir($path));
    }

    /**
     * Suppression récursive d'un chemin
     *
     * @param string $target
     *
     * @todo return
     */
    public static function remove($target)
    {
        if (is_dir($target)) {
            $files = glob($target . '*', GLOB_MARK);
            foreach ($files as $file) {
                self::remove($file);
            }
            @rmdir($target);
        } else {
            if (is_file($target)) {
                @unlink($target);
            }
        }
    }
}
