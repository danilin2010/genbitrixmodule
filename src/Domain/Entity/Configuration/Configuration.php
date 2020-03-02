<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 14.08.18
 * Time: 14:13
 */

namespace GenBM\Domain\Entity\Configuration;

/**
 * Class Configuration
 * @package GenBM\Domain\Entity\Configuration
 */
class Configuration
{
    /**
     * Path $Path
     */
    private $Path;

    /**
     * @return Path
     */
    public function getPath()
    {
        return $this->Path;
    }

    /**
     * @param Path $Path
     */
    public function setPath($Path)
    {
        $this->Path = $Path;
    }

    /**
     * Composer $Composer
     */
    private $Composer;

    /**
     * @return Composer
     */
    public function getComposer()
    {
        return $this->Composer;
    }

    /**
     * @param Composer $Composer
     */
    public function setComposer($Composer)
    {
        $this->Composer = $Composer;
    }


}