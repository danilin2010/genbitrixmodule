<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 14.08.18
 * Time: 18:14
 */

namespace GenBM\Domain\Helper;

use GenBM\Domain\Entity\Configuration\Configuration;
use Symfony\Component\Filesystem\Filesystem;
use GenBM\Exception as Ex;

class FileHelper
{

    /**
     * @var Filesystem $fileSystem
     */
    private $fileSystem;

    /**
     * @var Configuration
     */
    private $Configuration;

    /**
     * FileHelper constructor.
     * @param Configuration $Configuration
     * @param Filesystem $fileSystem
     */
    public function __construct($Configuration,$fileSystem)
    {
        if(!$fileSystem)
            $fileSystem=new Filesystem();
        $this->fileSystem = $fileSystem;
        $this->Configuration=$Configuration;
    }

    /**
     * @param string[] $arr
     * @return string
     */
    public function getTargetName($arr)
    {
        $res="";
        foreach ($arr as $val)
        {
            $res.=$val;
            if($res[strlen($res)-1]!="/")
                $res.="/";
        }
        return $res;
    }


    public function fileConvert($dirOld,$file)
    {
        $dir=$dirOld;

        if(strlen($dir)>0 && $dir[strlen($dir)-1]=="/")
            $dir=substr($dir, 0, -1);

        $dire=$this->getTargetName([
            $this->Configuration->getPath()->getResult(),
            ".last_version"
        ]);

        if(strlen($dire)>0 && $dire[strlen($dire)-1]=="/")
            $dire=substr($dire, 0, -1);

        $filePatch=$this->fileSystem->makePathRelative(
            realpath($dir),
            realpath($dire)
        );

        if(strlen($filePatch)<=0 || $filePatch=="./" || $filePatch=="..")
            $fileControl=$file;
        else
            $fileControl=$filePatch.$file;

        if(stristr($fileControl, 'lang/ru') === FALSE) {
            if(!$this->is_image($dirOld.$file))
            {
                $fileAllPatch=$dirOld.$file;
                $file_string = file_get_contents ($dirOld.$file);
                preg_match("/[а-яё]/i",$file_string,$matches);
                if(count($matches)>0){
                    throw new Ex\GenerationException(
                        "Cyrillic in base files: {$fileAllPatch}"
                    );
                }
            }
        }else{
            $file_string = file_get_contents ($dirOld.$file);
            $file_string = iconv("UTF-8", "WINDOWS-1251", $file_string);
            file_put_contents ($dirOld.$file, $file_string);
        }
    }

    public function IsDistinguishFiles($file1,$file2)
    {
        if(stristr($file1, 'lang/ru') === FALSE) {
            if(md5_file($file1) != md5_file($file2))
                return true;
        }else{
            if(!$this->is_image($file1))
            {
                $file_string1 = file_get_contents ($file1);
                $file_string2 = file_get_contents ($file2);
                $file_string1 = iconv("UTF-8", "WINDOWS-1251", $file_string1);
                if(md5($file_string1) != md5($file_string2))
                    return true;
            }else
                if(md5_file($file1) != md5_file($file2))
                    return true;
        }
        return false;
    }

    private function is_image($filename) {
        $is = @getimagesize($filename);
        if (!$is)
            return false;
        elseif ( !in_array($is[2], array(1,2,3)) )
            return false;
        else
            return true;
    }

    public function AddZipLastVersion()
    {
        $source=$this->getTargetName([
            $this->Configuration->getPath()->getResult(),
            ".last_version",
        ]);
        $archiveName=$this->Configuration->getPath()->getResult().'archives/'.".last_version";
        if(strlen($archiveName)>0 && $archiveName[strlen($archiveName)-1]=="/")
            $archiveName=substr($archiveName, 0, -1);
        $archiveName.='.zip';
        $this->fileSystem->remove([$archiveName]);
        FileZip::CreateArchive($source, $archiveName,'.last_version');
    }

    public function AddZipUpdateVersion()
    {
        $source=$this->getTargetName([
            $this->Configuration->getPath()->getResult(),
            "version",
        ]);
    }

}