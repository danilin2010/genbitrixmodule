<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 14.08.18
 * Time: 14:21
 */

namespace GenBM\Domain\Repository;

use GenBM\Domain\Entity\Configuration\Configuration;
use GenBM\Domain\Entity\Configuration\Path;
use GenBM\Domain\Entity\Configuration\Composer;

/**
 * Class ConfigurationRepository
 * @package GenBM\Domain\Repository
 */
class ConfigurationRepository
{
    /**
     * return Configuration
     */
    public function getConfiguration(){
        $Configuration=new Configuration();
        $j = file_get_contents("./cm.json");
        $data = json_decode($j);
        $Configuration->setPath(new Path($data->path->source,$data->path->result));
        $Configuration->setComposer(new Composer($data->composer->use,$data->composer->path,$data->composer->command));
        return $Configuration;
    }
}