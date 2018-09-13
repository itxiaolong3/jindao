<?php
/**
 * 金导旅游模块小程序接口定义
 *
 * @author panshikj
 * @url http://www.zunyangkj.com
 */
defined('IN_IA') or exit('Access Denied');
define("myappid", "wxe4c236378ce4469e");//小程序appid
define("mysecret", "3c36b6e7476337fb59f3d81ef7c7daf8");//小程序密钥
header("Access-Control-Allow-Origin: *");
class Jindao_travelModuleWxapp extends WeModuleWxapp
{

    public function doPageTest()
    {

        global $_GPC, $_W;

        $data = array();
        $getaccesstoken = $this->getAccessToken(myappid, mysecret);
        $data['msg'] = "得到的acctoken" . $getaccesstoken;
        echo $getaccesstoken;

    }

    public function doPageSiteinfo()
    {
        global $_W, $_GPC;
        $table = "zunyang_188che_config";
        $errno = 0;
        $message = '返回消息';
        $data = array();
        $sql = "select * from " . tablename($table) . " where `uniacid`=:uniacid and `code`=:code ";
        $params = array(
            ':uniacid' => $_W['uniacid'],
            ':code' => "site"
        );

        $result = pdo_fetch($sql, $params);
        if (!empty($result)) {

            $data = iunserializer($result['value']);
        } else {
            $errno = 1;
            $message = '参数错误';
        }
        return $this->result($errno, $message, $data);

    }

    public function doPageGetoneseller()
    {
        global $_W, $_GPC;
        $tablename = "zunyang_188che_seller";
        $errno = 0;
        $message = '返回消息';
        $data = array();
        $sql = "select * from " . tablename($tablename) . " where `uniacid`=:uniacid and `status`=:status order by addtime desc";
        $params = array(
            ':uniacid' => $_W['uniacid'],
            ':status' => 1
        );
        $result = pdo_fetch($sql, $params);
        if (empty($result)) {
            $error = 1;
            $message = "暂无";
        } else {
            $result['name'] = $this->cut_str($result['name'], 16);
            $result['address'] = $this->cut_str($result['address'], 16);
            $result['logo'] = tomedia($result['logo']);
            $message = "请求成功";
            $data['result'] = $result;
        }
        return $this->result($errno, $message, $data);
    }

    public function doPageGetnews()
    {
        global $_W, $_GPC;
        $tablename = "zunyang_188che_news";
        $errno = 0;
        $message = '返回消息';
        $data = array();
        $num = intval($_GPC['num']);
        if ($num < 0) {
            $error = 1;
            $message = "参数错误";
        } else {
            $sql = "select * from " . tablename($tablename) . " where `uniacid`=:uniacid order by sort ASC, addtime desc limit 0," . $num;
            $params = array(':uniacid' => $_W['uniacid']);
            $result = pdo_fetchall($sql, $params);
            if (empty($result)) {
                $error = 1;
                $message = "暂无数据";
            } else {
                if (is_array($result)) {
                    foreach ($result as $k => $v) {
                        //$result[$k]['title']=$this->cut_str($result['title'],16);
                        $result[$k]['thumb'] = tomedia($v['thumb']);
                    }
                }
                $message = "成功";
                $data['result'] = $result;
            }
        }
        return $this->result($errno, $message, $data);

    }

    public function doPageGetbanner()
    {
        global $_W, $_GPC;
        $tablename = "zunyang_188che_banner";
        $errno = 0;
        $message = 'banner图的返回消息';
        $data = array();
        $postion = intval($_GPC['postion']);
        if ($postion < 0) {
            $errno = 1;
            $message = "参数错误";
        } else {
            $sql = "select * from " . tablename($tablename) . " where `uniacid`=:uniacid and `postion`=:postion order by sort ASC, addtime desc";
            $params = array(
                ':uniacid' => $_W['uniacid'],
                ':postion' => $postion
            );
            $result = pdo_fetchall($sql, $params);
            if (empty($result)) {
                $errno = 0;
                $data['result'] = array();
                $message = "成功，数据为空";
            } else {
                $errno = 0;
                $message = "成功";
                if (is_array($result)) {
                    foreach ($result as $k => $v) {
                        $result[$k]['thumbs'] = tomedia($v['thumbs']);
                    }
                }
                $data['result'] = $result;
            }
        }
        return $this->result($errno, $message, $data);//以json格式返回
    }


    //检查用户信息是否完整和是否登录
    public function doPageCheckinfo()
    {
        global $_W, $_GPC;
        $table = "zunyang_188che_user";
        $js_code = $_GPC['code'];
        $openid = $this->getOpendId(myappid, mysecret, $js_code);
        $data = array();
        if (!empty($openid)) {

            $sql = "select * from " . tablename($table) . " where `uniacid`=:uniacid and `openId`=:openId order by addtime desc";
            $params = array(
                ':uniacid' => $_W['uniacid'],
                ':openId' => $openid
            );
            $result = pdo_fetch($sql, $params);

            if (empty($result)) {
                $data['result'] = array();
                $data["status"] = -1;
            } else {
                $errno = 1;
                $data["status"] = 1;
                $data['result'] = $result;
            }

        } else {
            $data['status'] = -1;
            $data['msg'] = 'doPageCheckinfo中无法获取openid';
        }
        echo json_encode($data);

    }

    public function doPageGetnewslist()
    {
        global $_W, $_GPC;
        $table = "zunyang_188che_news";
        $errno = 0;
        $message = '成功返回';
        $data = array();
        $sql = "select * from " . tablename($table) . " where `uniacid`=:uniacid order by sort ASC, addtime desc";
        $params = array(':uniacid' => $_W['uniacid']);
        $list = pdo_fetchall($sql, $params);
        foreach ($list as $k => $v) {
            $list[$k]['title'] = $this->cut_str($v['title'], 18);
        }

        $data['list'] = $list;
        return $this->result($errno, $message, $data);
    }

    /*处理发帖上传图片*/
    public function doPagePostuploadimg()
    {

        $message = "请求到服务器";
        global $_GPC, $_W;
        $uptypes = array('image/jpg', 'image/jpeg', 'image/png', 'image/pjpeg', 'image/gif', 'image/bmp', 'image/x-png');
        $max_file_size = 2000000; //上传文件大小限制, 单位BYTE
        $destination_folder = "../attachment/" . $_GPC['m'] . "/" . date('Ymd') . "/"; //上传文件路径
        $arr = array();
        $errno = 0;
        if (!is_uploaded_file($_FILES["file"]['tmp_name'])) //是否存在文件
        {
            $arr['status'] = 0;
            $arr['message'] = '图片不存在!';
            $message = "图片不存在!";
            print_r($message);
            $errno = 1;
            return $this->result($errno, $message, $arr);
            exit;
        }
        $file = $_FILES["file"];
        $arr['file'] = $file;
        if ($max_file_size < $file["size"]) //检查文件大小
        {
            $arr['status'] = 0;
            $arr['message'] = '文件太大';
            $message = "文件太大";
            print_r($message);
            $errno = 1;
            return $this->result($errno, $message, $arr);

            exit;
        }
        if (!in_array($file["type"], $uptypes)) //检查文件类型
        {
            $arr['status'] = 0;
            $message = "文件类型不符!" . $file["type"];
            print_r($message);
            $errno = 1;
            return $this->result($errno, $message, $arr);
            exit;
        }

        if (!file_exists($destination_folder)) {
            mkdir($destination_folder);
        }
        $filename = $file["tmp_name"];
        $pinfo = pathinfo($file["name"]);
        $ftype = $pinfo['extension'];
        $destination = $destination_folder . str_shuffle(time() . rand(111111, 999999)) . "." . $ftype;
        if (file_exists($destination)) {
            $arr['status'] = 0;

            $message = "同名文件已经存在了!" . $file["type"];
            print_r($message);
            $errno = 1;
            return $this->result($errno, $message, $arr);
            exit;
        }
        if (!move_uploaded_file($filename, $destination)) {
            $arr['status'] = 0;
            $message = "移动文件出错";
            print_r($message);
            $errno = 1;
            return $this->result($errno, $message, $arr);
            echo $arr;
            exit;
        }
        $pinfo = pathinfo($destination);
        $fname = $pinfo['basename'];
        $arr['imgname'] = "文件名：" + $fname;
        //echo $fname;
        @require_once(IA_ROOT . '/framework/function/file.func.php');
        @$filename = $fname;
        @file_remote_upload($filename);
        //https://pj.dede1.com/attachment/../attachment/zunyang_chefu188/20180515/9158665524662366.jpg
        $getdealurl = substr($destination, 14);
        $message = "生成的文件路径：" . tomedia($getdealurl);
        $arr['imgpath'] = tomedia($getdealurl);
        //print_r($message);
        // json_encode($arr);
        return $this->result($errno, $message, $arr);

    }

    /*保存门店入驻内容和图片*/
    public function doPagePostshenqing()
    {
        global $_W, $_GPC;
        $table = "jindao_stores";
        $data = array();
        //$imgarray = array();
        $data['uniacid'] = $_W['uniacid'];
        $data['s_name'] = $_GPC['s_name'];
        $data['s_address'] = $_GPC['s_address'];
        $data['s_headname'] = $_GPC['s_headname'];
        $data['s_headphone'] = $_GPC['s_headphone'];
        $data['s_compername'] = $_GPC['s_compername'];
        $data['longitude'] = $_GPC['longitude'];
        $data['latitude'] = $_GPC['latitude'];

//        //把转义符恢复htmlspecialchars_decode
//        $s_img = htmlspecialchars_decode($_GPC['s_img']);
//        //把引号替换
//        $s_img = str_replace('"', '', $s_img);
//        //剔除[ ]
//        $s_img = ltrim($s_img, '[');
//        $s_img = substr($s_img, 0, -1);
//
//        //$data['img'] =$deal;//这里也有可能是数组
//        $data['s_img'] = $s_img;

        //$data['s_img'] = $_GPC['s_img'];
//        $data['s_img'] = ltrim($_GPC['s_img'], 'https://pj.dede1.com/attachment/');
//        $data['yingyeimg'] = ltrim($_GPC['yingyeimg'], 'https://pj.dede1.com/attachment/');
//        $data['travelallowimg'] = ltrim($_GPC['travelallowimg'], 'https://pj.dede1.com/attachment/');
        $data['s_img']=substr($_GPC['s_img'],32);
        $data['yingyeimg']=substr($_GPC['yingyeimg'],32);
        $data['travelallowimg']=substr($_GPC['travelallowimg'],32);
        $arr=array();
        if (empty($_GPC['s_name'])){
            $arr['status'] = 0;
            $arr['message'] = "申请失败，门店名称不可为空";
            echo json_encode($arr);
            die();
        }else if(empty($_GPC['yingyeimg'])){
            $arr['status'] = 0;
            $arr['message'] = "申请失败，营业执照必须有";

            echo json_encode($arr);
            die();
        }else if(empty($_GPC['travelallowimg'])){
            $arr['status'] = 0;
            $arr['message'] = "申请失败，旅游许可证必须有";

            echo $arr;
            die();
        }else if(empty($_GPC['s_img'])){
            $arr['status'] = 0;
            $arr['message'] = "申请失败，公司logo不可为空";

            echo json_encode($arr);
            die();
        }else if(empty($_GPC['s_compername'])){
            $arr['status'] = 0;
            $arr['message'] = "申请失败，公司名不可为空";

            echo json_encode($arr);
            die();
        }else if(empty($_GPC['s_headname'])){
            $arr['status'] = 0;
            $arr['message'] = "申请失败，必须有负责人";

            echo json_encode($arr);
            die();
        }else if(empty($_GPC['s_headphone'])){
            $arr['status'] = 0;
            $arr['message'] = "申请失败，负责人电话不可为空";

            echo json_encode($arr);
            die();
        }else if(empty($_GPC['s_address'])){
            $arr['status'] = 0;
            $arr['message'] = "申请失败，门店地址为空";

            echo json_encode($arr);
            die();
        }else if (empty($_GPC['longitude'])||empty($_GPC['latitude'])){
            $arr['status'] = 0;
            $arr['message'] = "申请失败，经纬度不可为空";

            echo json_encode($arr);
            die();
        }else{
            $data['addtime'] = time();
            $res = pdo_insert($table, $data);
            if ($res) {
                $arr['status'] = 1;
                $arr['message'] = "申请成功";

            } else {
                $arr['status'] = -1;
                $arr['message'] = "申请失败";
            }
        }


        echo json_encode($arr);
    }

    public function doPageGettiewen()
    {
        global $_W, $_GPC;
        //$tablename="zunyang_188che_seller";
        $table = "zunyang_188che_tiewen";
        $errno = 0;
        $message = '贴文成功返回';
        $data = array();
        $id = intval($_GPC['id']);
        if ($id < 0) {
            $errno = 1;
            $message = "参数错误";
        }
        if ($id == "0") {
            $sql = "select * from " . tablename($table) . " where `uniacid`=:uniacid  order by addtime desc";
            $params = array(
                ':uniacid' => $_W['uniacid']
            );
            $result = pdo_fetchall($sql, $params);
            if (empty($result)) {
                $errno = 1;
                $message = "没贴文";

            } else {

                $thumbs = array();


                if (is_array($result)) {
                    foreach ($result as $k => $v) {
                        $arre = explode(",", $v['img']);
                        // $thumbs_list[] = explode("|", $v['img']);
                        foreach ($arre as $kk => $vv) {
                            $v['thumbs'][$kk]['src'] = $vv;
                        }

                        $data[] = $v;
                    }

                }
                $message = "成功";

            }
        } else {
            $sql = "select * from " . tablename($table) . " where `uniacid`=:uniacid and `id`=:id order by addtime desc";
            $params = array(
                ':uniacid' => $_W['uniacid'],
                ':id' => $id
            );
            $result = pdo_fetch($sql, $params);
            if (empty($result)) {
                $errno = 1;
                $message = "没贴文,快抢沙发";
            } else {
                $thumbs = array();
                $thumbs_list = explode(",", $result['img']);
                foreach ($thumbs_list as $k => $v) {
                    if (!empty($v)) {
                        $thumbs[$k] = $v;
                    }

                }
                $message = "成功";
                $data['result'] = $result;
                $data['thumbs'] = $thumbs;
                $result['addtime'] = date("Y-m-d H:m", $result['addtime']);

            }
        }

        return $this->result($errno, $message, $data);
    }


    //数组转xml
    public function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }


    public function doPageGetsellerlist()
    {
        global $_W, $_GPC;
        $table = "zunyang_188che_seller";
        $errno = 0;
        $message = '成功返回';
        $data = array();
        $type = intval($_GPC['type']);
        $latitude = floatval($_GPC['latitude']);
        $longitude = floatval($_GPC['longitude']);
        if (empty($latitude) || empty($longitude)) {
            $errno = 1;
            $message = '获取位置信息失败';
        } else {
            if ($type < 0) {
                $errno = 1;
                $message = "参数错误";
            } else {
                $sql = "select * from " . tablename($table) . " where `uniacid`=:uniacid and `type`=:type and `status`=:status order by addtime desc";
                $params = array(
                    ':uniacid' => $_W['uniacid'],
                    ':type' => $type,
                    ':status' => 1
                );
                $result = pdo_fetchall($sql, $params);
                if (is_array($result)) {
                    foreach ($result as $k => $v) {
                        $result[$k]['logo'] = tomedia($v['logo']);
                        $result[$k]['address'] = $this->cut_str($v['address'], 12);
                        $result[$k]['distance'] = $this->getDistance($latitude, $longitude, $v['latitude'], $v['longitude']);
                    }
                    $flag = array();
                    foreach ($result as $v) {
                        $flag[] = $v['distance'];
                    }
                    array_multisort($flag, SORT_ASC, $result);
                }
                $errno = 0;
                $message = "成功";
                $data['list'] = $result;
            }
            return $this->result($errno, $message, $data);
        }


    }

    //获取商家
    public function doPageGetshangjia()
    {
        global $_W, $_GPC;
        //$tablename="zunyang_188che_seller";
        $table = "jindao_stores";
        $data = array();
        $s_name = $_GPC['s_name'];
        $getnum=$_GPC['num'];
        if (empty($s_name)) {
            //没传参数时，全局搜索
            $sql = "select * from " . tablename($table) . " where `uniacid`=:uniacid and `status`=:status order by addtime";
            ///$sql .= " limit " . 5 * $getnum . ',' . 5;
            $params = array(
                ':uniacid' => $_W['uniacid'],
                ':status' => 1
            );
            $result = pdo_fetchall($sql, $params);

            if (empty($result)) {
                $data["msg"] = "没有数据";
                $data["result"] = $result;
            } else {
                if (is_array($result)) {
                    foreach ($result as $k => $v) {
                        $result[$k]['s_img'] = tomedia($v['s_img']);
                        $result[$k]['yingyeimg'] = tomedia($v['yingyeimg']);
                        $result[$k]['travelallowimg'] = tomedia($v['travelallowimg']);
                    }
                    $data["result"] = $result;
                } else {
                    $result['s_img'] = tomedia($result['s_img']);
                    $result['yingyeimg'] = tomedia($result['yingyeimg']);
                    $result['travelallowimg'] = tomedia($result['travelallowimg']);
                    $data["result"] = $result;
                }
                //$data["result"]=$result;
            }

        } else {
            //指定商店模糊查询
            $sql = "select * from " . tablename($table) . " where `uniacid`=:uniacid and `status`=:status  AND s_name LIKE '%{$s_name}%'";
            $params = array(
                ':uniacid' => $_W['uniacid'],
                ':status' => 1
            );
            $data['sql'] = $sql;
            $result = pdo_fetchall($sql, $params);

            if (empty($result)) {
                $data['status'] = 0;
                $data['msg'] = "没有您要找的门店";
            } else {
                if (is_array($result)) {
                    foreach ($result as $k => $v) {
                        $result[$k]['s_img'] = tomedia($v['s_img']);
                        $result[$k]['yingyeimg'] = tomedia($v['yingyeimg']);
                        $result[$k]['travelallowimg'] = tomedia($v['travelallowimg']);
                    }
                    $data['result'] = $result;
                } else {
                    $result['s_img'] = tomedia($result['s_img']);
                    $result['yingyeimg'] = tomedia($result['yingyeimg']);
                    $result['travelallowimg'] = tomedia($result['travelallowimg']);
                }
                $data['result'] = $result;
            }
        }
        echo json_encode($data);
    }
    
    //地图
    public function doPageMap()
    {
        global $_GPC, $_W;
        $op = $_GPC['op'];
        //$res=pdo_get('zhtc_system',array('uniacid'=>$_W['uniacid']));
        $table = "zunyang_188che_config";

        $data = array();
        $sql = "select * from " . tablename($table) . " where `uniacid`=:uniacid and `code`=:code ";
        $params = array(
            ':uniacid' => $_W['uniacid'],
            ':code' => "site"
        );

        $result = pdo_fetch($sql, $params);
        if (!empty($result)) {

            $data = iunserializer($result['value']);
        }
        $url = "https://apis.map.qq.com/ws/geocoder/v1/?location=" . $op . "&key=" . $data['mapkey'] . "&get_poi=0&coord_type=1";
        $html = file_get_contents($url);
        echo $html;
    }

//计算距离
    function getDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6367000; //approximate radius of earth in meters
        $lat1 = ($lat1 * pi()) / 180;
        $lng1 = ($lng1 * pi()) / 180;
        $lat2 = ($lat2 * pi()) / 180;
        $lng2 = ($lng2 * pi()) / 180;
        $calcLongitude = $lng2 - $lng1;
        $calcLatitude = $lat2 - $lat1;
        $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
        $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
        $calculatedDistance = $earthRadius * $stepTwo;
        return round($calculatedDistance / 1000);
    }

    function cut_str($string, $sublen, $start = 0, $code = 'UTF-8')
    {
        if ($code == 'UTF-8') {
            $pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
            preg_match_all($pa, $string, $t_string);

            if (count($t_string[0]) - $start > $sublen) return join('', array_slice($t_string[0], $start, $sublen)) . "...";
            return join('', array_slice($t_string[0], $start, $sublen));
        } else {
            $start = $start * 2;
            $sublen = $sublen * 2;
            $strlen = strlen($string);
            $tmpstr = '';

            for ($i = 0; $i < $strlen; $i++) {
                if ($i >= $start && $i < ($start + $sublen)) {
                    if (ord(substr($string, $i, 1)) > 129) {
                        $tmpstr .= substr($string, $i, 2);
                    } else {
                        $tmpstr .= substr($string, $i, 1);
                    }
                }
                if (ord(substr($string, $i, 1)) > 129) $i++;
            }
            if (strlen($tmpstr) < $strlen) $tmpstr .= "...";
            return $tmpstr;
        }
    }


    function get_web($url, $data = null, $header = null, $ip = null)
    {
        $https = substr($url, 0, 5);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        if ('https' == $https) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        }
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }

        if (!empty($header)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }

        if (!empty($ip)) {
            $header = array(
                'CLIENT-IP:' . $ip,
                'X-FORWARDED-FOR:' . $ip
            );

            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        $char_a = array('GBK', 'GB2312', 'ASCII', 'UTF-8');
        $encode = mb_detect_encoding($output, $char_a);
        if ('UTF-8' != $encode && in_array($encode, $char_a)) {
            $string = mb_convert_encoding($string, 'UTF-8', $encode);
        }

        curl_close($curl);
        return $output;
    }

    /**
     *
     *  生成app 凭证
     *
     *
     */
    function create_sig($appkey, $mobile, $strRand, $t)
    {
        //return md5($appkey . $mobile);
        $str = "appkey=" . $appkey . "&random=" . $strRand . "&time=" . $t . "&mobile=" . $mobile;
        return bin2hex(hash('sha256', $str, true));
    }

    /**
     *
     *  json to array
     *
     *
     */

    function json2array($json)
    {
        return json_decode($json, true);
    }

    //官方校验微信签名
    public function doPageCheckSignature()
    {
        echo 1;
        die();
        $data = array();
        $table = "zunyang_188che_getinfo";

        $gethtml = file_get_contents('php://input');
        $data['getcon'] = $gethtml;

        pdo_insert($table, $data);

        global $_W, $_GPC;
        $signature = $_GPC["signature"];
        $timestamp = $_GPC["timestamp"];
        $nonce = $_GPC["nonce"];

        $token = 'xiaolong';
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature) {
            echo $_GPC['echostr'];
        } else {
            echo 'xxx';
        }

    }

    //回复消息
    public function responseMsg($getstr)
    {

        $postStr = $getstr;

        if (!empty($postStr) && is_string($postStr)) {
            //禁止引用外部xml实体
            //libxml_disable_entity_loader(true);
            //$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $postArr = json_decode($postStr, true);

            if (!empty($postArr['MsgType']) && $postArr['MsgType'] == 'text') {//文本消息
                $fromUsername = $postArr['FromUserName'];  //发送者openid
                $toUserName = $postArr['ToUserName'];      //小程序id
                $textTpl = array(
                    "ToUserName" => $fromUsername,
                    "FromUserName" => $toUserName,
                    "CreateTime" => time(),
                    "MsgType" => "transfer_customer_service",
                );
                exit(json_encode($textTpl));
            } elseif (!empty($postArr['MsgType']) && $postArr['MsgType'] == 'image') {//图文消息
                $fromUsername = $postArr['FromUserName'];  //发送者openid
                $toUserName = $postArr['ToUserName'];      //小程序id
                $textTpl = array(
                    "ToUserName" => $fromUsername,
                    "FromUserName" => $toUserName,
                    "CreateTime" => time(),
                    "MsgType" => "transfer_customer_service",

                );
                exit(json_encode($textTpl));

            } elseif ($postArr['MsgType'] == 'event' && $postArr['Event'] == 'user_enter_tempsession') {//进入客服动作

                $fromUsername = $postArr['FromUserName'];  //发送者openid

                $content = '您好，有什么能帮助你?';

                $data = array(

                    "touser" => $fromUsername,

                    "msgtype" => "text",

                    "text" => array("content" => $content)

                );

                $json = json_encode($data);

                $access_token = $this->getAccessToken();
                /*POST发送https请求客服接口api*/
                $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=" . $access_token;
                //以'json'格式发送post的https请求
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
                if (!empty($json)) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
                }
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                //curl_setopt($curl, CURLOPT_HTTPHEADER, $headers );
                $output = curl_exec($curl);
                if (curl_errno($curl)) {
                    echo 'Errno' . curl_error($curl);//捕抓异常
                }
                curl_close($curl);
                if ($output == 0) {
                    echo 'success';
                    exit;
                }
            } else {
                exit('aaa');
            }
        } else {
            echo "";
            exit;

        }

    }

    //获取accesstoken保存到文件
//    function getAccessToken($appid,$secret)
//    {
//
//        // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
//        $data = json_decode($this->get_php_file("access_token.php"));
//        if ($data->expire_time < time()) {
//            // 如果是企业号用以下URL获取access_token
//            // $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
//            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$secret;
//            $res = json_decode($this->curl_https($url));
//            $access_token = $res->access_token;
//            if ($access_token) {
//                $data->expire_time = time() + 7000;
//                $data->access_token = $access_token;
//                $this->set_php_file("access_token.php", json_encode($data));
//            }
//
//        } else {
//            $access_token = $data->access_token;
//        }
//        return $access_token;
//    }
    //获取文件
    private function get_php_file($filename)
    {
        return trim(substr(file_get_contents($filename), 15));
    }

    //设置responsemsg到文件
    private function set_php_file($filename, $content)
    {
        $fp = fopen($filename, "w");
        fwrite($fp, "" . $content);
        fclose($fp);
    }

    //发送校验请求
//    function curl_https($url)
//    {
//        $curl = curl_init();
//        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
//        // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
//        // 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
//        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
//        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);
//        curl_setopt($curl, CURLOPT_URL, $url);
//        $res = curl_exec($curl);
//        curl_close($curl);
//        return $res;
//    }

    /**
     * @param $appid 程序appid
     * @param $secret 密钥
     * @param $js_code 获取的code
     * @return mixed openid
     */
    public function getOpendId($appid, $secret, $js_code)
    {

        $urls = "https://api.weixin.qq.com/sns/jscode2session?appid=" . $appid . "&secret=" . $secret . "&js_code=" . $js_code . "&grant_type=authorization_code";

        $html = file_get_contents($urls);

        $getcode = json_decode($html);
        return $getcode->openid;
    }

    //获取accessToken

    /**
     * @return mixed
     */
    public function getAccessToken($appid, $secret)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $appid . "&secret=" . $secret;
        $con = file_get_contents($url);
        $getcode = json_decode($con);
        return $getcode->access_token;

    }


}