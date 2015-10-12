<?php
namespace yii\easyii\helpers;

use Yii;
use yii\web\ServerErrorHttpException;
use yii\web\UploadedFile;
use \yii\web\HttpException;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\helpers\FileHelper;
use yii\helpers\Url;

class Upload
{
    public static $UPLOADS_DIR = 'uploads';

    public static function file(UploadedFile $fileInstance, $dir = '', $namePostfix = true)
    {
        $fileName = Upload::getUploadPath($dir) . DIRECTORY_SEPARATOR . Upload::getFileName($fileInstance, $namePostfix);

        if(!$fileInstance->saveAs($fileName)){
            throw new HttpException(500, 'Cannot upload file "'.$fileName.'". Please check write permissions.');
        }
        return $dir ? $dir . '/' . basename($fileName) : basename($fileName);
    }

    static function getUploadPath($dir = null)
    {
        $uploadPath = Yii::getAlias('@uploads');
        if(!$uploadPath){
            throw new ServerErrorHttpException('Alias `@uploads` is not set.');
        }
        if($dir){
            $uploadPath .= DIRECTORY_SEPARATOR . $dir;
        }
        if(!FileHelper::createDirectory($uploadPath)){
            throw new HttpException(500, 'Cannot create "'.$uploadPath.'". Please check write permissions.');
        }
        return $uploadPath;
    }

    static function getLink($fileName)
    {
        if(!$fileName){
            return '';
        }
        return '/' . implode('/', array_filter([
            Yii::getAlias('@web'),
            basename(Yii::getAlias('@uploads')),
            str_replace('\\', '/', $fileName)
        ]));
    }

    static function getAbsolutePath($fileName)
    {
        if(!$fileName){
            return '';
        }
        if(strpos($fileName, Yii::getAlias('@uploads')) !== false ){
            return $fileName;
        } else {
            return Yii::getAlias('@uploads') . DIRECTORY_SEPARATOR . $fileName;
        }
    }

    static function delete($fileName)
    {
        if($fileName) {
            $filePath = self::getAbsolutePath($fileName);
            if(is_file($filePath)){
                @unlink($filePath);
            }
        }
    }

    static function getFileName(UploadedFile $fileInstance, $namePostfix = true)
    {
        $fileName =  StringHelper::truncate(Inflector::slug($fileInstance->baseName), 32, '');
        if($namePostfix || !$fileName) {
            $fileName .= ($fileName ? '-' : '') . substr(uniqid(md5(rand()), true), 0, 10);
        }
        $fileName .= '.' . strtolower($fileInstance->extension);

        return $fileName;
    }
}