<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 28.08.18
 * Time: 13:12
 */

namespace GenBM\Domain\Service;

use GenBM\Domain\Entity\Configuration\Configuration;
use Symfony\Component\Filesystem\Filesystem;
use GenBM\Domain\Helper\FileHelper;
use Symfony\Component\Finder\Finder;
use GenBM\Domain\Helper\FileZip;

class ZipService
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

    private function run()
    {
        $this->fileHelper->AddZipLastVersion();
        $this->AddZipUpdateVersion();
        echo "The project zipped!\n";
    }

    private function AddZipUpdateVersion()
    {
        $source=$this->fileHelper->getTargetName([
            $this->Configuration->getPath()->getResult(),
            "version",
        ]);
        $iterator = new Finder();
        $iterator
            ->ignoreDotFiles(false)
            ->directories()->in($source)
            ->exclude('init')
            ->sort(function (\SplFileInfo $a, \SplFileInfo $b) {
                return strcmp(
                    $b->getRealpath() ?: $b->getPathname(),$a->getRealpath() ?: $a->getPathname()
                );
            })
            ->depth('== 0')
        ;

        $first=true;

        foreach ($iterator as $director)
        {
            $archiveName=$this->Configuration->getPath()->getResult().'archives/'.$director->getFilename();
            if(strlen($archiveName)>0 && $archiveName[strlen($archiveName)-1]=="/")
                $archiveName=substr($archiveName, 0, -1);
            $archiveName.='.zip';
            if (!$this->fileSystem->exists($archiveName) || $first)
            {
                $this->fileSystem->remove([$archiveName]);
                FileZip::CreateArchive($director->getPathname(), $archiveName,$director->getFilename());
            }
            $first=false;
        }
    }

}