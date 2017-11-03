<?php
namespace MdTools\Console\Output;

/**
 *
 * @author jeromeklam
 */
class ConsoleOutput extends \MdTools\Console\Output\AbstractOutput
{

    /**
     * Constructeur
     *
     * @param string $p_verbosity
     */
    public function __construct($p_verbosity = self::VERBOSITY_NORMAL)
    {
        parent::__construct($this->openOutputStream(), $p_verbosity);
    }

    /**
     * Support stdout ?
     *
     * @return boolean
     */
    private function hasStdoutSupport()
    {
        return true;
    }

    /**
     * @return resource
     */
    private function openOutputStream()
    {
        if (!$this->hasStdoutSupport()) {
            return fopen('php://output', 'w');
        }
        return @fopen('php://stdout', 'w') ?: fopen('php://output', 'w');
    }
}
