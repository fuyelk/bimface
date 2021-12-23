<?php


require_once "../src/Api.php";
require_once "../src/File.php";
require_once "../src/View.php";
require_once "../src/BimfaceException.php";


try {

    $file = new \fuyelk\bimface\File();
    $res = $file->getFileList();
//    $res = $file->getFileInfo('10000719720023');
//    $res = $file->getFileUploadStatus('10000719720023');
//    $res = $file->getSupport();
//    $res = $file->download('10000714577102');
//    $res = $file->delete('10000714577102');
//    $res = $file->getUploadPolicy('百事可乐.rvt');
//    $res = $file->uploadFromServer('D:/Projects/api_upload_demo.rvt','测试接口上传2.rvt');
//    $res = $file->uploadFromUrl('https://www.example.com/upload_from_url_demo.rvt','测试URL上传.rvt');
//    $res = $file->translate('https://www.example.com?from=bimface','测试接口上传2.rvt',false,'10000719735182');
//    $res = $file->getTranslateStatus('10000719735182');

//    $view = new \fuyelk\bimface\View();
//    $res = $view->getViewToken('10000719735182');

} catch (\fuyelk\bimface\BimfaceException $e) {
    echo $e->getMessage();
}
print_r($res);

echo "\nfinished";