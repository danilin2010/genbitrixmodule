<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 14.08.18
 * Time: 16:12
 */

namespace GenBM\Domain\Entity\Configuration;

use GenBM\Exception as Ex;

/**
 * Class Composer
 * @package GenBM\Domain\Entity\Configuration
 */
class Composer
{

    /**
     * Composer constructor.
     * @param bool $use
     * @param string $path
     * string $command
     * @throws Ex\UnknownOptionException
     */
    public function __construct($use,$path,$command)
    {

        if($use && strlen($path)<=0)
            throw new Ex\UnknownOptionException("Empty parameter :path");

        if($use && $path[strlen($path)-1]!='/')
            $path=$path."/";

        $this->use=$use;
        $this->path=$path;
        $this->command=$command;
    }


    /**
     * @var bool $use
     */
    private $use;

    /**
     * @return bool
     */
    public function getUse()
    {
        return $this->use;
    }

    /**
     * @param bool $use
     */
    public function setUse($use)
    {
        $this->use = $use;
    }

    /**
     * @var string $path
     */
    private $path;

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @var string $command
     */
    private $command;

    /**
     * @return string
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @param string $command
     */
    public function setCommand($command)
    {
        $this->command = $command;
    }


}