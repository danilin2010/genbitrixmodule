<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 14.08.18
 * Time: 14:01
 */

namespace GenBM\Domain\Service;

use GenBM\Domain\Entity\Configuration\Configuration;
use GenBM\Exception as Ex;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use GenBM\Domain\Helper\FileHelper;

class InitService
{

    /**
     * @var Configuration
     */
    private $Configuration;

    /**
     * @var Filesystem $fileSystem
     */
    private $fileSystem;

    /**
     * @var FileHelper $fileHelper
     */
    private $fileHelper;


    public function __construct($Configuration)
    {
        $this->Configuration=$Configuration;
        $this->fileSystem = new Filesystem();
        $this->fileHelper = new FileHelper($Configuration,$this->fileSystem);
        $this->run();
    }

    /**
     * @throws Ex\GenerationException
     * @throws Ex\InitServiceException
     */
    private function run()
    {
        $this->copyFile();
        $this->initComposer();
        $this->fileSystem->mirror(
            $this->fileHelper->getTargetName([
                $this->Configuration->getPath()->getResult(),
                '.last_version'
            ]),
            $this->fileHelper->getTargetName([
                $this->Configuration->getPath()->getResult(),
                'version/init'
            ]),
            null,
            ['override'=>true]
        );
        echo "The project is initialized!\n";
    }

    private function initComposer()
    {
        if($this->Configuration->getComposer()->getUse())
        {
            $newFile=$this->fileHelper->getTargetName([
                $this->Configuration->getPath()->getResult(),
                '.last_version',
            ]);
            $path=realpath($newFile);
            echo `{$this->Configuration->getComposer()->getCommand()} install -d {$path}`;
        }
    }


    /**
     * @throws Ex\InitServiceException
     * @throws Ex\GenerationException
     */
    private function copyFile()
    {
        $iterator = new Finder();

        $is_empty = count(glob($this->Configuration->getPath()->getResult()."{,.}[!.,!..]*",GLOB_MARK|GLOB_BRACE)) ? false : true;
        if(!$is_empty)
            throw new Ex\InitServiceException("The resulting directory must be empty");

        $iterator
                ->ignoreDotFiles(false)
                ->files()->in($this->Configuration->getPath()->getSource())
                ;

        if($this->Configuration->getComposer()->getUse())
            $iterator->exclude($this->Configuration->getComposer()->getPath());

        foreach ($iterator as $file)
        {
            $newFile=$this->fileHelper->getTargetName([
                $this->Configuration->getPath()->getResult(),
                '.last_version',
                $file->getrelativePath()
            ]);
            $this->fileSystem->copy($file->getpathName(),$newFile.$file->getfileName());
            $this->fileHelper->fileConvert($newFile,$file->getfileName());
        }

    }




}