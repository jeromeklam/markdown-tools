<?php
namespace MdTools\Tools;

class FilenameFilter extends \MdTools\Tools\FilesystemRegexFilter {

    /**
     * Filter files against the regex
     */
    public function accept()
    {
        return (!$this->isFile() || preg_match($this->regex, $this->getFilename()));
    }
}
