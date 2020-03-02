<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 13.08.18
 * Time: 17:54
 */

namespace GenBM;

use GenBM\Exception as Ex;
use GenBM\Domain\Service;
use GenBM\Domain\Repository\ConfigurationRepository;

class Route
{

    private $command=[];


    public function __construct($argc,$argv)
    {

        $this->Run($argc,$argv);

    }

    /**
     * @param int $argc
     * @param string[] $argv
     */
    private function Run($argc,$argv)
    {
        try
        {
            $this->PreparingSettings($argc,$argv);
            $this->PreparingProcess();
        }catch (\Exception $e){
            echo "Error: ".$e->getMessage()."\n";
        }
    }

    /**
     * @param int $argc
     * @param string[] $argv
     * @throws Ex\UnknownOptionException
     */
    private function PreparingSettings($argc,$argv)
    {
        if ($argc>1)
        {

            for ($i=1; $i<$argc; $i++)
            {
                $option = explode("=", $argv[$i]);
                switch ($option[0])
                {

                    case "-i":
                    case "--init":
                        $this->command['init']=true;
                        break;
                    case "-a":
                    case "--add":
                        $this->command['add']=true;
                        break;
                    case "-z":
                    case "--zip":
                        $this->command['zip']=true;
                        break;
                    case "-nl":
                    case "--notChangeLevel":
                        $this->command['notChangeLevel']=true;
                        break;
                    case "-lv":
                    case "--levelVersion":
                        if(in_array($option[1],['minor','major','global']))
                            $this->command['levelVersion']=$option[1];
                        break;
                    default:
                        throw new Ex\UnknownOptionException(
                            "Unknown option: {$argv[$i]}"
                        );
                        break;
                }
            }
        }else{
            throw new Ex\UnknownOptionException(
                "Run without parameters"
            );
        }
    }

    /**
     * @throws Ex\InitServiceException
     */
    private function PreparingProcess()
    {
        $ConfigurationRepository= new ConfigurationRepository();
        $Configuration=$ConfigurationRepository->getConfiguration();
        if(isset($this->command['init']))
        {
            new Service\InitService($Configuration);
        }elseif(isset($this->command['add'])){
            $property=[];
            if(isset($this->command['notChangeLevel']))
                $property['notChangeLevel']=true;
            if(isset($this->command['levelVersion']))
                $property['levelVersion']=$this->command['levelVersion'];
            new Service\AddService($Configuration,$property);
        }

        if(isset($this->command['zip']))
            new Service\ZipService($Configuration);

        return;
    }

}