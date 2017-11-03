<?php
/**
 * Classe de gestion du merge des fichiers markdown
 *
 * @author jeromeklam
 * @package Markdown
 * @category Tools
 */
namespace MdTools\Command\Markdown;

/**
 * Use...
 */
use \MdTools\Console\Input\ParameterInput;
use \MdTools\Console\Output\ConsoleOutput;
use MdTools;

/**
 * Commande merge
 * @author jeromeklam
 */
class Merge
{

    /**
     * Gestion de la function de plusieurs fichiers markdown
     * Liste des paramètres
     *     * source : répertoire de base (obligatoire)
     *     * output : fichier de destination, par défaut output-merged.md
     *     * links  : par défaut à vrai, pour remplacer les liens à l'intérieur des fichiers md
     *
     * @param \MdTools\Console\Input\ParameterInput $p_input
     * @param \MdTools\Console\Output\ConsoleOutput $p_output
     */
    public function process(ParameterInput $p_input, ConsoleOutput $p_output)
    {
        $mdFiles = array();
        $params  = $p_input->getParams();
        if (!array_key_exists('source', $params)) {
            throw new \InvalidArgumentException(sprintf('%s parameter is required !', 'source'));
        }
        $source = $params['source'];
        if (!is_dir($source)) {
            throw new \InvalidArgumentException(sprintf('%s parameter must be a directory !', 'source'));
        }
        if (!array_key_exists('destination', $params)) {
            throw new \InvalidArgumentException(sprintf('%s parameter is required !', 'destination'));
        }
        $destination = $params['destination'];
        if (is_dir($destination)) {
            throw new \InvalidArgumentException(sprintf('%s directory must be empty !', 'destination'));
        }
        \MdTools\Tools\Dir::mkpath($destination);
        if (!is_dir($destination)) {
            throw new \InvalidArgumentException(sprintf('Can\'t create %s directory !', $destination));
        }
        // Possibilité de fournir une tdm...
        $tdm = false;
        if (array_key_exists('tdm', $params)) {
            $tdmFile = $params['tdm'];
            if (is_file($tdmFile)) {
                $tdm = preg_split("/(\r\n|\n|\r)/", file_get_contents($tdmFile));
            }
        }
        // Je vais rechercher tous les fichiers .md, pour les mettre dans un tableau
        if ($tdm === false) {
            $p_output->write('    * Processing source directory for markdown files...', true);
            $directory = new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS);
            $filter    = new \MdTools\Tools\FilenameFilter($directory, '/\.(?:md|MD)$/');
            foreach(new \RecursiveIteratorIterator($filter) as $file) {
                //@todo : détecter des fomats relatifs / absoluts.
                $mdFiles[] = [
                    'original' => $file,
                    'md5'      => md5_file($file),
                    'simple'   => str_replace($source, '', $file),
                    'filename' => $file->getFilename(),
                    'path'     => $file->getPath()
                ];
            }
        } else {
            $p_output->write('    * Processing source directory for markdown files from tdm...', true);
            foreach ($tdm as $idx => $oneFile) {
                $file      = new \SplFileInfo(rtrim($source, '/') . '/' . $oneFile);
                $mdFiles[] = [
                    'original' => $file,
                    'md5'      => md5_file($file),
                    'simple'   => str_replace($source, '', $file),
                    'filename' => $file->getFilename(),
                    'path'     => $file->getPath()
                ];
            }
        }
        foreach ($mdFiles as $idxF => $oneFile) {
            // Parcours des liens
            $file    = $oneFile['original'];
            $content = file_get_contents($oneFile['original']);
            $links   = [];
            // Images links
            // Détecter plus de cas et éventuellement reproduire convertir en vrai markdown les <img ...
            $result  = preg_match_all('#\<img (.*?)src="(.*?)"(.*?)\/\>#sim', $content, $matches);
            if ($result && count($matches) > 0) {
                foreach ($matches[0] as $idx => $match) {
                    //var_dump($match);
                    $links[] = [
                        'full'  => $match,
                        'link'  => $matches[2][$idx],
                        'check' => ''
                    ];
                }
            }
            // markdown links
            $result  = preg_match_all('#\[(.*?)]\((.*?)\)#sim', $content, $matches);
            if ($result && count($matches) > 0) {
                foreach ($matches[0] as $idx => $match) {
                    //var_dump($match);
                    $links[] = [
                        'full'  => $match,
                        'link'  => $matches[2][$idx],
                        'check' => ''
                    ];
                }
            }
            // First line... : Title :
            $ff   = file($file);
            $line = trim(str_replace('#', '', $ff[0]));
            // Ajout dans les fichiers à traiter
            $mdFiles[$idxF]['links'] = $links;
            $mdFiles[$idxF]['line']  = $line;
        }
        // Pour chaque fichier trouvé on va générer la sortie
        $cptr = 0;
        foreach ($mdFiles as $idx => $oneFile) {
            $fName   = str_replace('/', '_', $oneFile['simple']);
            $spl     = $oneFile['original'];
            $content = file_get_contents($spl->getPathName());
            //
            $toCheck = [];
            foreach ($oneFile['links'] as $idxL => $oneLink) {
                $baseDir = $oneFile['original']->getPath();
                if (strpos($oneLink['link'], './') === 0) {
                    $oneLink['check'] = $baseDir . substr($oneLink['link'], 1);
                    $toCheck[]        = $oneLink;
                } else {
                    if (strpos($oneLink['link'], '../') === 0) {
                        $oneLink['check'] = $baseDir . '/' . $oneLink['link'];
                        $toCheck[]        = $oneLink;
                    } else {
                        if (strpos($oneLink['link'], 'http') === false) {
                            $oneLink['check'] = $baseDir . '/' . $oneLink['link'];
                            $toCheck[]        = $oneLink;
                        }
                    }
                }
            }
            //@todo : optimiser le remplacement des liens
            foreach ($toCheck as $idxC => $oneLink) {
                $assetFile = $oneLink['check'];
                if (is_file($assetFile)) {
                    if (strtolower(substr($assetFile, -3)) == '.md') {
                        //echo 'ici';
                        $md5 = md5_file($assetFile);
                        foreach ($mdFiles as $idx2 => $file2) {
                            if ($file2['md5'] == $md5) {
                                $orig    = $oneLink['full'];
                                $newFile = str_replace('/', '_', $file2['simple']);
                                $new     = str_replace($oneLink['link'], $newFile, $orig);
                                $lnk     = str_replace(' ', '-', MdTools\Tools\Str::removeAccents($file2['line']));
                                $new     = str_replace($oneLink['link'], '#' . strtolower($lnk) , $orig);
                                $content = str_replace($orig, $new, $content);
                                break;
                            }
                        }
                    } else {
                        $parts = pathinfo($assetFile);
                        // Simple copy....
                        $cptr++;
                        copy($assetFile, $destination . '/asset_' . $cptr . '_' . $parts['basename']);
                        $orig    = $oneLink['full'];
                        $new     = str_replace($oneLink['link'], 'asset_' . $cptr . '_' . $parts['basename'], $orig);
                        $content = str_replace($orig, $new, $content);
                    }
                } else {
                    echo $assetFile;
                }
            }
            file_put_contents($destination . '/' . $fName, $content);
        }
        $arrPath = [];
        $arrFile = [];
        foreach ($mdFiles as $idx => $oneFile) {
            $arrPath[] = $oneFile['path'];
            $arrFile[] = $oneFile['filename'];
        }
        // On ne tri que si il n'y a pas de tdm
        if ($tdm === false) {
            array_multisort($arrPath, SORT_ASC, SORT_STRING, $arrFile, SORT_ASC, SORT_STRING, $mdFiles);
        }
        // Génération du fichier unique
        $output = '';
        foreach ($mdFiles as $idx => $oneFile) {
            $file    = str_replace('/', '_', $oneFile['simple']);
            $output .= file_get_contents($destination . '/' . $file) . "\n";
        }
        file_put_contents($destination . '/output.md', $output);
        $p_output->write('    * output.md generated...', true);
        //@todo : ménage ...
        //@todo : landement de la commande pandoc : pdf / epub
    }
}
