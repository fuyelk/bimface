<?php
// +---------------------------------------------------
// | 广联达BIMFACE接口 数据转换接口
// +---------------------------------------------------
// | @author fuyelk@fuyelk.com
// +---------------------------------------------------
// | @date 2021/12/13 09:49
// +---------------------------------------------------

namespace fuyelk\bimface;


class View extends Api
{
    /**
     * @var string
     */
    private static $TOKEN_PATH = __DIR__ . '/view_token';

    /**
     * @var array
     */
    private static $VIEW_TOKEN = [];

    public function __construct()
    {
        parent::__construct();
        self::$VIEW_TOKEN = self::getConfig('', self::$TOKEN_PATH) ?: [];
    }

    /**
     * 获取ViewToken
     * @param string $fileId [文件转换ID]
     * @param string $compareId [模型对比ID]
     * @param string $integrateId [集成模型ID]
     * @param string $sceneId [场景ID]
     * @param string $submodelId [子模型ID]
     * @throws BimfaceException
     * @author fuyelk <fuyelk@fuyelk.com>
     */
    public function getViewToken(string $fileId = '', string $compareId = '', string $integrateId = '', string $sceneId = '', string $submodelId = '')
    {
        $query = [];

        if ($fileId) {
            $query['fileId'] = $fileId;
        }
        if ($compareId) {
            $query['compareId'] = $compareId;
        }
        if ($integrateId) {
            $query['integrateId'] = $integrateId;
        }
        if ($sceneId) {
            $query['sceneId'] = $sceneId;
        }
        if ($submodelId) {
            $query['submodelId'] = $submodelId;
        }

        if (empty($query)) {
            throw new BimfaceException('未选择模型,获取ViewToken失败');
        }

        // 构建查询条件
        $key = md5(serialize($query));

        // 检查缓存
        if (isset(self::$VIEW_TOKEN[$key]) &&
            (self::$VIEW_TOKEN[$key]['expire_time'] > (time() + 3600)) // 有效期预留1小时
        ) {
            return self::$VIEW_TOKEN[$key]['token'];
        }

        // 调接口获取
        $url = 'https://api.bimface.com/view/token?' . http_build_query($query);
        $res = $this->httpRequest($url);

        //{
        //  "code" : "success",
        //  "data" : "c6eec7b8b4724da2926959b17c351622",
        //  "message" : ""
        //}

        // 刷新缓存
        self::$VIEW_TOKEN[$key] = [
            'expire_time' => strtotime('+12 hours'),
            'token' => $res['data']
        ];

        self::setConfig(self::$VIEW_TOKEN, self::$TOKEN_PATH);
        return $res['data'];
    }
}