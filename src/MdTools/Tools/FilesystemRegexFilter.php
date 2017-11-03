<?php
namespace MdTools\Tools;

abstract class FilesystemRegexFilter extends \RecursiveRegexIterator {

    /**
     * Expression régulière
     * @var mixed
     */
    protected $regex;

    /**
     * Constructeur
     *
     * @param \RecursiveIterator $p_it
     * @param string             $p_regex
     */
    public function __construct(\RecursiveIterator $p_it, $p_regex) {
        $this->regex = $p_regex;
        parent::__construct($p_it, $p_regex);
    }
}
