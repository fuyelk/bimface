<?php


require_once "Api.php";
require_once "File.php";
require_once "View.php";
require_once "Model.php";
require_once "BimfaceException.php";


try {

//    $file = new \fuyelk\bimface\File();
//    $res = $file->getFileList();
//    $res = $file->getFileInfo('10000719720023');
//    $res = $file->getFileUploadStatus('10000719720023');
//    $res = $file->getSupport();
//    $res = $file->download('10000714577102');
//    $res = $file->getUploadPolicy('百事可乐.rvt');
//    $res = $file->uploadDirect('测试接口上传2.rvt','D:/Projects/PHP/github/bimface/src/api_upload_demo.rvt');
//    $res = $file->translate('https://www.milinger.com?from=bimface','测试接口上传2.rvt',false,'10000719735182');

//    $model = new \fuyelk\bimface\Model();

    $view = new \fuyelk\bimface\View();
    $res = $view->getViewToken('10000719735182');

}catch (\fuyelk\bimface\BimfaceException $e) {
    echo $e->getMessage();
}
print_r($res);

echo "finished";