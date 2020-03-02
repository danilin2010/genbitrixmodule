<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 28.08.18
 * Time: 13:30
 */

namespace GenBM\Domain\Service;

use GenBM\Domain\Entity\Configuration\Configuration;
use Symfony\Component\Filesystem\Filesystem;
use GenBM\Domain\Helper\FileHelper;
use Symfony\Component\Finder\Finder;

class AddService
{
    private $notChangeVersion=false;

    private $levelVersion='minor';

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

    private $version;

    public function __construct($Configuration,$property)
    {
        $this->Configuration=$Configuration;
        $this->fileSystem = new Filesystem();
        $this->fileHelper = new FileHelper($Configuration,$this->fileSystem);

        if(isset($property['notChangeLevel']))
            $this->notChangeVersion=true;

        if(isset($property['levelVersion']))
            $this->levelVersion=$property['levelVersion'];

        $this->run();
    }

    private function run()
    {
        $this->getVersion();
        $this->addVersion();
        $this->copyFile();
        $this->delFile();
        $this->checkDelFile();
        $this->initComposer();
        $this->createDescription();
        $this->saveVersion();
        $this->updateLastVersion();
        echo "The update added!\n";
    }

    private function addVersion()
    {
        if(!$this->notChangeVersion)
        {
            $Version=explode('.',$this->version["VERSION"]);

            switch($this->levelVersion){
                case "major":
                    $Version[1]++;
                    $Version[2]=0;
                    break;
                case "global":
                    $Version[0]++;
                    $Version[1]=0;
                    $Version[2]=0;
                    break;
                case "minor":
                default:
                    $Version[2]++;
                    break;

            }
            $this->version["VERSION"]=implode('.',$Version);
        }
    }

    private function getVersion()
    {
        $patch=$this->fileHelper->getTargetName([
            $this->Configuration->getPath()->getSource(),
            'install/'
        ]).'version.php';
        include($patch);
        $this->version=$arModuleVersion;
    }

    private function copyFile()
    {

        $iterator = new Finder();
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
            $versionFile=$this->fileHelper->getTargetName([
                $this->Configuration->getPath()->getResult(),
                'version/'.$this->version["VERSION"],
                $file->getrelativePath()
            ]);
            if (!$this->fileSystem->exists($newFile.$file->getfileName()) || $this->fileHelper->IsDistinguishFiles($file->getpathName(),$newFile.$file->getfileName()))
            {
                $this->fileSystem->copy($file->getpathName(),$versionFile.$file->getfileName(),true);
                $this->fileHelper->fileConvert($versionFile,$file->getfileName());
            }
        }
    }

    private function delFile()
    {
        $delfile=$this->fileHelper->getTargetName([
            $this->Configuration->getPath()->getResult(),
            'version/'.$this->version["VERSION"]
        ]).'delfile.json';

        if ($this->fileSystem->exists($delfile))
        {
            $j = file_get_contents($delfile);
            $arrDel = json_decode($j);
        }else{
            $arrDel=[];
        }

        $iterator = new Finder();
        $iterator
            ->ignoreDotFiles(false)
            ->files()->in(
                $this->fileHelper->getTargetName([
                    $this->Configuration->getPath()->getResult(),
                    '.last_version',
                ])
            )
        ;
        if($this->Configuration->getComposer()->getUse())
            $iterator->exclude($this->Configuration->getComposer()->getPath());

        foreach ($iterator as $file)
        {
            $oldFile=$this->fileHelper->getTargetName([
                $this->Configuration->getPath()->getSource(),
                $file->getrelativePath()
            ]);
            if (!$this->fileSystem->exists($oldFile.$file->getfileName()))
            {
                if(!in_array($file->getrelativePath().$file->getfileName(),$arrDel))
                    $arrDel[]=$file->getrelativePath().$file->getfileName();
                $this->fileSystem->remove([$this->fileHelper->getTargetName([
                        $this->Configuration->getPath()->getResult(),
                        '.last_version',
                        $file->getrelativePath()
                    ]).$file->getfileName()]);
            }

        }

        if(count($arrDel)>0)
        {
            file_put_contents($delfile,json_encode($arrDel));
            foreach ($arrDel as $del){
                $this->fileSystem->remove([
                    $this->fileHelper->getTargetName([
                        $this->Configuration->getPath()->getResult(),
                        '.last_version'
                    ]).$del
                ]);
            }
        }

    }

    private function checkDelFile()
    {
        $delfile=$this->fileHelper->getTargetName([
                $this->Configuration->getPath()->getResult(),
                'version/'.$this->version["VERSION"]
            ]).'delfile.json';
        if ($this->fileSystem->exists($delfile))
        {
            $j = file_get_contents($delfile);
            $arrDel = json_decode($j);
            $arrNewDel=[];
            foreach ($arrDel as $del){
                $file=$this->fileHelper->getTargetName([
                    $this->Configuration->getPath()->getSource()
                ]).$del;
                if($this->fileSystem->exists($file))
                    $arrNewDel[]=$del;
            }
            if($arrNewDel!=$arrDel)
            {
                if(count($arrNewDel)>0)
                    file_put_contents($delfile,json_encode($arrNewDel));
                else
                    $this->fileSystem->remove([$delfile]);
            }
        }
    }

    private function initComposer()
    {
        if($this->Configuration->getComposer()->getUse())
        {
            $newDir=$this->fileHelper->getTargetName([
                $this->Configuration->getPath()->getResult(),
                'version/'.$this->version["VERSION"],
            ]);
            $newFile=$newDir."composer.json";
            if ($this->fileSystem->exists($newFile))
            {
                $path=realpath($newDir);
                echo `{$this->Configuration->getComposer()->getCommand()} install -d {$path}`;
            }
        }
    }

    private function createDescription()
    {
        $Dir=$this->fileHelper->getTargetName([
                $this->Configuration->getPath()->getResult(),
                'version/'.$this->version["VERSION"]
            ]);
        if (!$this->fileSystem->exists($Dir.'description.ru'))
            file_put_contents($Dir.'description.ru',"");
        if (!$this->fileSystem->exists($Dir.'updater.php'))
            file_put_contents($Dir.'updater.php',"");
    }

    private function saveVersion()
    {
        $Date=new \DateTime();
        $this->version["VERSION_DATE"]=$Date->format('Y-m-d H:i:s');
        $contents = var_export($this->version, true);
        $fileVersion=$this->fileHelper->getTargetName([
            $this->Configuration->getPath()->getResult(),
            'version/'.$this->version["VERSION"]
        ]).'install/version.php';
        file_put_contents($fileVersion, "<?\n \$arModuleVersion={$contents};\n?>");
        $fileSourse=$this->fileHelper->getTargetName([
                $this->Configuration->getPath()->getSource(),
            ]).'install/version.php';
        file_put_contents($fileSourse, "<?\n \$arModuleVersion={$contents};\n?>");
    }

    private function updateLastVersion()
    {
        $iterator = new Finder();
        $iterator
            ->ignoreDotFiles(false)
            ->files()->in(
                $this->fileHelper->getTargetName([
                    $this->Configuration->getPath()->getResult(),
                    'version/'.$this->version["VERSION"],
                ])
            )
        ;

        foreach ($iterator as $file)
        {
            $NewFile=$this->fileHelper->getTargetName([
                $this->Configuration->getPath()->getResult(),
                '.last_version',
                $file->getrelativePath()
            ]);
            if(in_array($file->getrelativePath().'/'.$file->getfileName(),[
                "/updater.php",
                "/delfile.json",
                "/description.ru",
            ]))
                continue;
            $this->fileSystem->copy($file->getpathName(),$NewFile.$file->getfileName(),true);
        }
    }

}