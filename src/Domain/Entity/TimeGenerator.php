<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 15.08.18
 * Time: 17:34
 */

namespace GenBM\Domain\Entity;

use DateTime;

class TimeGenerator
{
    /**
     * @var DateTime $updateTime
     */
    private $updateTime;

    /**
     * @var DateTime $generationTime
     */
    private $generationTime;

    /**
     * TimeGenerator constructor.
     * @param DateTime $updateTime
     * @param DateTime $generationTime
     */
    public function __construct(DateTime $updateTime = null, DateTime $generationTime = null)
    {
        if(is_null($updateTime))
            $updateTime=new DateTime();
        if(is_null($generationTime))
            $generationTime=new DateTime();
        $this->updateTime = $updateTime;
        $this->generationTime = $generationTime;
    }

    /**
     * @return DateTime
     */
    public function getUpdateTime()
    {
        return $this->updateTime;
    }

    /**
     * @param DateTime $updateTime
     */
    public function setUpdateTime($updateTime)
    {
        $this->updateTime = $updateTime;
    }

    /**
     * @return DateTime
     */
    public function getGenerationTime()
    {
        return $this->generationTime;
    }

    /**
     * @param DateTime $generationTime
     */
    public function setGenerationTime($generationTime)
    {
        $this->generationTime = $generationTime;
    }

}