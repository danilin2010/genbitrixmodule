<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 15.08.18
 * Time: 17:42
 */

namespace GenBM\Domain\Repository;

use GenBM\Domain\Entity\TimeGenerator;

class TimeGeneratorRepository
{
    /**
     * @var string $file
     */
    private $file="time.json";

    /**
     * @var string $patch
     */
    private $patch="./";

    /**
     * TimeGeneratorRepository constructor.
     * @param string $patch
     * @param string $file
     */
    public function __construct($patch = null,$file = null)
    {
        if(!is_null($file))
            $this->file = $file;
        if(!is_null($patch))
            $this->patch = $patch;
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param string $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * @return string
     */
    public function getPatch()
    {
        return $this->patch;
    }

    /**
     * @param string $patch
     */
    public function setPatch($patch)
    {
        $this->patch = $patch;
    }

    /**
     * @return TimeGenerator
     */
    public function get(){
        $j = file_get_contents($this->patch.$this->file);
        $data = json_decode($j);
        $TimeGenerator=new TimeGenerator($data->updateTime,$data->generationTime);
        return $TimeGenerator;
    }

    /**
     * @param TimeGenerator $TimeGenerator
     */
    public function set(TimeGenerator $TimeGenerator){
        $data=[
            'generationTime'=>$TimeGenerator->getGenerationTime(),
            'updateTime'=>$TimeGenerator->getUpdateTime()
        ];
        file_put_contents($this->patch.$this->file,json_encode($data));
    }
}