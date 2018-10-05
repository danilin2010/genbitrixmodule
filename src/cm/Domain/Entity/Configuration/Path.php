<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 14.08.18
 * Time: 14:14
 */

namespace GenBM\Domain\Entity\Configuration;

use GenBM\Exception as Ex;

/**
 * Class Path
 * @package GenBM\Domain\Entity\Configuration
 */
class Path
{

    /**
     * Path constructor.
     * @param string $source
     * @param string $result
     * @throws Ex\UnknownOptionException
     */
    public function __construct($source,$result)
    {

        if(strlen($source)<=0)
            throw new Ex\UnknownOptionException("Empty parameter :source");

        if(strlen($result)<=0)
            throw new Ex\UnknownOptionException("Empty parameter :result");

        if($source[0]!='/')
            $source="./".$source;
        if($source[strlen($source)-1]!='/')
            $source=$source."/";

        if($result[0]!='/')
            $result="./".$result;
        if($result[strlen($result)-1]!='/')
            $result=$result."/";

        if($source==$result)
            throw new Ex\UnknownOptionException("The source should not be equal to the result");

        $this->source=$source;
        $this->result=$result;
    }

    /**
     * string $source
     */
    private $source;

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param string $source
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     * string $result
     */
    private $result;

    /**
     * @return string
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param string $result
     */
    public function setResult($result)
    {
        $this->result = $result;
    }
}