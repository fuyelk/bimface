<?php
// +---------------------------------------------------
// | 广联达BIMFACE接口 文件接口
// +---------------------------------------------------
// | @author fuyelk@fuyelk.com
// +---------------------------------------------------
// | @date 2021/12/13 09:49
// +---------------------------------------------------

namespace fuyelk\bimface;


class File extends Api
{
    /**
     * 获取文件列表
     * @param string $startTime 起始日期，格式为 yyyy-MM-dd
     * @param string $endTime 截止日期，格式为 yyyy-MM-dd
     * @param int $offset 查询结果偏移，从查询结果的第offset条开始返回数据
     * @param int $rows 查询结果数，默认为100，最大500
     * @param string $status 文件状态[uploading:上传中,success:成功,failure:失败的]
     * @param string $suffix 文件后缀
     * @return array
     * @throws BimfaceException
     * @author fuyelk <fuyelk@fuyelk.com>
     */
    public function getFileList($startTime = '', $endTime = '', $offset = 0, $rows = 100, $status = '', $suffix = '')
    {
        $query = [];
        if ($startTime) {
            $query['startTime'] = $startTime;
        }
        if ($endTime) {
            $query['endTime'] = $endTime;
        }
        if ($offset) {
            $query['offset'] = $offset;
        }
        if ($rows) {
            $query['rows'] = $rows;
        }
        if ($status) {
            $query['status'] = $status;
        }
        if ($suffix) {
            $query['suffix'] = $suffix;
        }

        $url = 'https://file.bimface.com/files?' . http_build_query($query);
        return $this->http_request($url);

        //{
        //  "code" : "success",
        //  "data" : [ {
        //    "createTime" : "2017-11-09 13:25:03",
        //    "etag" : "19349858cjs98ericu989",
        //    "fileId" : 1938888813662976,
        //    "length" : 39044,
        //    "name" : "BIMFACE示例.rvt",
        //    "status" : "success",
        //    "suffix" : "rvt"
        //  } ],
        //  "message" : null
        //}
    }

    /**
     * 获取一个文件的信息
     * @param string $fileId 文件ID
     * @return array
     * @throws BimfaceException
     * @author fuyelk <fuyelk@fuyelk.com>
     */
    public function getFileInfo($fileId)
    {
        $url = 'https://file.bimface.com/files/' . $fileId;
        return $this->http_request($url);

        //{
        //  "code" : "success",
        //  "data" : {
        //    "createTime" : "2017-11-09 13:25:03",
        //    "etag" : "19349858cjs98ericu989",
        //    "fileId" : 1938888813662976,
        //    "length" : 39044,
        //    "name" : "BIMFACE示例.rvt",
        //    "status" : "success",
        //    "suffix" : "rvt"
        //  },
        //  "message" : null
        //}
    }

    /**
     * 获取文件上传状态信息
     * @param string $fileId 文件ID
     * @return array|bool|string
     * @throws BimfaceException
     * @author fuyelk <fuyelk@fuyelk.com>
     */
    public function getFileUploadStatus($fileId)
    {
        $url = "https://file.bimface.com/files/{$fileId}/uploadStatus";
        return $this->http_request($url);

        //{
        //  "code" : "success",
        //  "data" : {
        //    "failedReason" : "input.stream.read.error",
        //    "fileId" : 1938888813662976,
        //    "name" : "BIMFACE示例.rvt",
        //    "status" : "failure"
        //  },
        //  "message" : null
        //}
    }

    /**
     * 获取应用支持的文件类型
     * @return array|bool|string
     * @throws BimfaceException
     * @author fuyelk <fuyelk@fuyelk.com>
     */
    public function getSupport()
    {
        $url = 'https://file.bimface.com/support';
        return $this->http_request($url);

        //{
        //  "code" : "success",
        //  "data" : {
        //    "length" : 1073741824,
        //    "types" : [ "rvt", "rfa", "dwg", "dxf", "skp", "ifc", "dgn", "obj", "stl", "3ds", "dae", "ply", "igms", "zip", "gtj", "bfcatzip" ]
        //  },
        //  "message" : null
        //}
    }

    /**
     * 源文件下载
     * @param string $fileId 文件ID
     * @param string $fileName 下载文件名
     * @return string
     * @throws BimfaceException
     * @author fuyelk <fuyelk@fuyelk.com>
     */
    public function download($fileId, $fileName = '')
    {
        $query['fileId'] = $fileId;

        if ($fileName) {
            $query['fileName'] = $fileName;
        }
        $url = 'https://file.bimface.com/download/url?' . http_build_query($query);
        return $this->http_request($url, 'GET');

        //{
        //  "code" : "success",
        //  "data" : "https://bf-prod-srcfile.oss-cn-beijing.aliyuncs.com/d6eba6e8B.rvt",
        //  "message" : null
        //}
    }

    /**
     * 源文件删除
     * @param string $fileId 文件ID
     * @return string
     * @throws BimfaceException
     * @author fuyelk <fuyelk@fuyelk.com>
     */
    public function delete($fileId)
    {
        $url = 'https://file.bimface.com/file?fileId=' . $fileId;
        return $this->http_request($url, 'GET');

        //{
        //  "code" : "success",
        //  "data" : null,
        //  "message" : null
        //}
    }

    /**
     * 获取文件上传凭证
     * @param string $name 文件的全名，使用URL编码（UTF-8），最多256个字符
     * @param string $sourceId 调用方的文件源ID，不能重复
     * @return array|bool|string
     * @throws BimfaceException
     * @author fuyelk <fuyelk@fuyelk.com>
     */
    public function getUploadPolicy($name, $sourceId = '')
    {
        $query['name'] = urlencode($name);

        if ($sourceId) {
            $query['sourceId'] = $sourceId;
        }
        $url = 'https://file.bimface.com/upload/policy?' . http_build_query($query);
        return $this->http_request($url);

        //{
        //  "code" : "success",
        //  "data" : {
        //    "accessId" : "QLYNXu7B9OTjErYR",
        //    "callbackBody" : "eyJjYWxsYmFja0JvZHlUeXBlIjoiYXBwbGljYXRpb24veC13d3ctZm9ybS11cmxlbmNvZGVkIiwiY2FsbGJhY2tIb3N0IjoiZmlsZS5iaW1mYWNlLmNvbSIsImNhbGxiYWNrVXJsIjoiaHR0cHM6Ly8xMTYuMjI4LjE5NS4xOC9vc3MvcmVjZWl2ZSIsImNhbGxiYWNrQm9keSI6Im9iamVjdD0ke29iamVjdH0mc2l6ZT0ke3NpemV9JmV0YWc9JHtldGFnfSZuYW1lPXRlc3QucGRmJmZpbGVJZD0xNDgzMDY1NTc0NzU0NTI4JmFwcGtleT1hRGxQZjEzVXRpR3M3eXVIQ2Q4ZUhTTEhiSEpUVThTZCZzb3VyY2VJZD0mZmlsZUJ1Y2tldD1iZi1kZXYtc3JjZmlsZSJ9",
        //    "expire" : 1542792319,
        //    "host" : "https://bf-dev-srcfile.oss-cn-shanghai.aliyuncs.com",
        //    "objectKey" : "2f15df1c430b4ad3b0644029111b703a",
        //    "policy" : "eyJleHBpcmF0aW9uIjoiMjAxOC0xMS0yMVQwOToyNToxOS45OTZaIiwiY29uZGl0aW9ucyI6W1siY29udGVudC1sZW5ndGgtcmFuZ2UiLDAsNTM2ODcwOTEyMF0sWyJzdGFydHMtd2l0aCIsIiRrZXkiLCIiXV19",
        //    "signature" : "q4NrZ1By/msuHOHlgpgX56mMUhY=",
        //    "sourceId" : "17193a84311d4be6bbd68b52a1d9d699"
        //  },
        //  "message" : ""
        //}
    }

    /**
     * 将服务器文件上传至平台（建议通过前端页面实现此功能，而不是使用此接口上传）
     * @param string $name 文件名
     * @param string $filePath 文件路径
     * @return array|bool|string
     * @throws BimfaceException
     * @author fuyelk <fuyelk@fuyelk.com>
     */
    public function uploadDirect($name, $filePath)
    {
        if (!is_file($filePath)) {
            throw new BimfaceException('文件不存在');
        }

        try {
            $policyResult = $this->getUploadPolicy($name);
        } catch (BimfaceException $e) {
            throw new BimfaceException('获取上传凭据失败');
        }
        $policyData = $policyResult['data'];

        $data = [
            'name' => $name,
            'key' => $policyData['objectKey'],
            'policy' => $policyData['policy'],
            'OSSAccessKeyId' => $policyData['accessId'],
            'callback' => $policyData['callbackBody'],
            'signature' => $policyData['signature'],
            'success_action_status' => 200,
            'file' => new \CURLFile($filePath),
        ];
        return $this->http_request($policyData['host'], 'POST', $data, 0);

        //{
        //    "code": "success",
        //    "message": null,
        //    "data": {
        //        "fileId": 1671948932908448,
        //        "name": "uploadTest_20190516.rvt",
        //        "status": "success",
        //        "etag": "85BECD325859F9F715F9FE9E4C3FBD04",
        //        "suffix": "rvt",
        //        "length": 5124105,
        //        "createTime": "2019-08-15 14:06:21"
        //    }
        //}
    }

    /**
     * 发起文件转换
     * @param string $callback 接收转换成功后通知的URL
     * @param string $rootName 主文件名
     * @param bool $compressed 是否为压缩包资源
     * @param string $fileId 文件ID
     * @param string $config 转换配置项
     * @return array|bool|string
     * @throws BimfaceException
     * @author fuyelk <fuyelk@fuyelk.com>
     * @date 2021/12/14 15:09
     */
    public function translate($callback, $rootName, $compressed, $fileId, $config = [])
    {
        $url = 'https://api.bimface.com/translate';
        $data = [
            'callback' => $callback,
            'source' => [
                'rootName' => $rootName,
                'fileId' => $fileId,
                'compressed' => $compressed
            ],
            'config' => $config ?: [
                'toBimtiles' => true
            ]
        ];
        return $this->http_request($url, 'PUT', $data, 1);
    }

    /**
     * 获取文件转换状态
     * @param string $fileId 文件ID
     * @return array|bool|string
     * @throws BimfaceException
     * @author fuyelk <fuyelk@fuyelk.com>
     */
    public function getTranslateStatus($fileId)
    {
        $url = 'https://api.bimface.com/translate?fileId=' . $fileId;
        return $this->http_request($url, 'GET', [], 1);
    }
}