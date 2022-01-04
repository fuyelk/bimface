<?php
// +---------------------------------------------------
// | 广联达BIMFACE接口基类
// +---------------------------------------------------
// | @author fuyelk@fuyelk.com
// +---------------------------------------------------
// | @date 2021/12/13 09:49
// +---------------------------------------------------

namespace fuyelk\bimface;


class Api
{
    /**
     * @var string
     */
    private static $APP_KEY = '';

    /**
     * @var string
     */
    private static $APP_SECRET = '';

    /**
     * @var string
     */
    private static $ACCESS_TOKEN = '';

    /**
     * @var string
     */
    private static $CONFIG_PATH = __DIR__ . '/config.json';

    /**
     * Api constructor.
     * @throws BimfaceException
     */
    public function __construct()
    {
        $config = self::getConfig();

        if (false === $config) {
            // 初始化配置
            self::setConfig([
                'app_key' => '',
                'app_secret' => '',
                'access_token' => '',
                'access_token_expire_time' => '',
            ]);
        }

        // 配置为空
        if (empty($config['app_key']) || empty($config['app_secret'])) {
            throw new BimfaceException('请完成bimface参数配置:' . self::$CONFIG_PATH);
        }

        self::$APP_KEY = $config['app_key'];
        self::$APP_SECRET = $config['app_secret'];
        self::$ACCESS_TOKEN = $config['access_token'];

        // 检查是否需要刷新AccessToken
        if (empty($config['access_token']) ||
            empty($config['access_token_expire_time']) ||
            $config['access_token_expire_time'] < (time() + 3600)) // TOKEN过期预留1小时时间
        {
            $this->refreshAccessToken();
        }
    }

    /**
     * 刷新AccessToken
     * @throws BimfaceException
     * @author fuyelk <fuyelk@fuyelk.com>
     */
    private function refreshAccessToken()
    {
        // 刷新前清空ACCESS_TOKEN缓存
        self::$ACCESS_TOKEN = "";

        $url = 'https://api.bimface.com/oauth2/token';
        $Authorization = sprintf('Authorization:Basic %s', base64_encode(self::$APP_KEY . ':' . self::$APP_SECRET));
        $res = $this->httpRequest($url, 'POST', [], 1, [$Authorization]);

        // 数据示例：
        //{
        //    "code": "success",
        //    "message": null,
        //    "data": {
        //        "expireTime": "2021-12-15 19:50:22",
        //        "token": "cn-abcdefghij-d224-48be-94a0-abcdefghi"
        //    }
        //}

        // 更新配置
        $config = self::getConfig();
        $config['access_token'] = $res['data']['token'];
        $config['access_token_expire_time'] = strtotime($res['data']['expireTime']);
        self::setConfig($config);

        self::$ACCESS_TOKEN = $config['access_token'];
    }

    /**
     * http请求
     * @param string $url http地址
     * @param string $method 请求方式
     * @param array $data 请求数据：
     * <pre>
     *  $data = [
     *      'image' => new \CURLFile($filePath),
     *      'access_token' => 'this-is-access-token'
     *       ...
     *  ]
     * </pre>
     * @param int $dataType 数据传输方式 [0:form-data,1:json,2:x-www-form-urlencoded]
     * @param string[] $addHeader 添加请求头
     * <pre>
     *  $addHeader = [
     *      'origin:https://www.example.com',
     *      'accept-language:en,zh-CN;q=0.9,zh;q=0.8',
     *       ...
     *  ]
     * </pre>
     * @param bool $checkResult [是否校验结果，默认校验]
     * @return array|bool|string
     * @throws BimfaceException
     * @author fuyelk <fuyelk@fuyelk.com>
     * @date 2021/07/07 13:39
     */
    protected function httpRequest(string $url, string $method = 'GET', array $data = [], int $dataType = 1, array $addHeader = [], bool $checkResult = true)
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_ACCEPT_ENCODING => 'gzip,deflate',
            CURLOPT_URL => $url,
            CURLOPT_CUSTOMREQUEST => strtoupper($method), // 请求方式
            CURLOPT_USERAGENT => 'Mozilla / 5.0 (Windows NT 10.0; Win64; x64)',// 模拟常用浏览器的useragent
            CURLOPT_RETURNTRANSFER => true,   // 获取的信息以文件流的形式返回，而不是直接输出
            CURLOPT_SSL_VERIFYPEER => false,  // https请求不验证证书
            CURLOPT_SSL_VERIFYHOST => false,  // https请求不验证hosts
            CURLOPT_MAXREDIRS => 10,          // 最深允许重定向级数
            CURLOPT_CONNECTTIMEOUT => 10,// 最长等待连接成功时间
            CURLOPT_TIMEOUT => 50,      // 最长等待响应完成时间
        ]);

        if (self::$ACCESS_TOKEN) {
            array_push($addHeader, sprintf('Authorization: Bearer %s', self::$ACCESS_TOKEN));
        }

        // 发送请求数据
        if ($data) {
            switch ($dataType) {
                case 1: // json
                    $data = json_encode($data, JSON_UNESCAPED_UNICODE);
                    array_push($addHeader, 'Content-type:application/json');
                    break;
                case 2: // x-www-form-urlencoded
                    $data = http_build_query($data);
                    array_push($addHeader, 'Content-type:application/x-www-form-urlencoded');
                    break;
                default:
                    array_push($addHeader, 'Content-Type:multipart/form-data');
            }
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER, $addHeader); // 设置请求头

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) throw new BimfaceException($err);
        if (!$checkResult) return $response;

        // 检查结果
        $response = json_decode($response, true);
        if (empty($response) || 'success' != strtolower($response['code'] ?? '')) {
            throw new BimfaceException($response['message'] ?? '接口出错');
        }

        return $response;
    }

    /**
     * 读取配置信息
     * @param string $name 键名
     * @param string $file [配置文件]
     * @return bool|mixed
     * @author fuyelk <fuyelk@fuyelk.com>
     */
    protected static function getConfig(string $name = '', string $file = '')
    {
        $file = $file ?: self::$CONFIG_PATH;

        if (!is_file($file)) return false;

        $data = file_get_contents($file);
        if (empty($data)) return false;

        $config = json_decode($data, true);
        if (!empty($name)) {
            if (array_key_exists($name, $config)) {
                return $config[$name];
            }
            return null;
        }
        return $config;
    }

    /**
     * 创建配置文件
     * @param array $content 配置内容,$$:开头的值原样输出
     * @param string $file [配置文件]
     * @return bool
     * @author fuyelk <fuyelk@fuyelk.com>
     */
    protected static function setConfig(array $content, string $file = '')
    {
        $file = $file ?: self::$CONFIG_PATH;

        if (!is_dir(dirname($file))) {
            mkdir(dirname($file), 0755, true);
        }

        $fp = fopen($file, 'w');
        fwrite($fp, json_encode($content, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        fclose($fp);
        return true;
    }
}