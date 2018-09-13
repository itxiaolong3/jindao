<?php
/**
 * 金导旅游模块微站定义
 *
 * @author panshikj
 * @url http://www.zunyangkj.com
 */
defined('IN_IA') or exit('Access Denied');

class Jindao_travelModuleSite extends WeModuleSite
{


    //入口，配置信息
    public function doWebSite()
    {
        global $_W, $_GPC;
        $table = "jindao_config";
        $code = "site";
        $sql = "SELECT * FROM " . tablename($table) . " WHERE uniacid = :uniacid AND code = :code";
        $params = array(':uniacid' => $_W['uniacid'], ':code' => $code);
        $setting = pdo_fetch($sql, $params);
        $item = iunserializer($setting['value']);
        if ($_W['ispost']) {

            $data = array();
            $data['uniacid'] = $_W['uniacid'];
            $data['code'] = $code;
            $data['value'] = iserializer($_POST);
            if (empty($setting)) {
                pdo_insert($table, $data);
            } else {
                pdo_update($table, $data, array('id' => $setting['id']));
            }

            message('提交成功', referer(), success);
        }

        include $this->template('site');
    }

    //商家列表
    public function doWebList()
    {
        global $_W, $_GPC;
        $table = "jindao_stores";
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $sql = "select * from " . tablename($table) . " where `uniacid`=:uniacid order by addtime desc limit " . ($pindex - 1) * $psize . ',' . $psize;
        $params = array(
            ':uniacid' => $_W['uniacid'],
        );
        $result_list = pdo_fetchall($sql, $params);

        $sql2 = "select count(*) from " . tablename($table) . " where `uniacid`=:uniacid";
        $total = pdo_fetchcolumn($sql2, $params);
        $pager = pagination($total, $pindex, $psize);
        include $this->template("list");

    }

    public function doWebAddseller()
    {
        global $_W, $_GPC;
        $tablename = "jindao_stores";
        if (!$_W['ispost']) {
            include $this->template('addseller');
        } else {

            $arr = array();
            $arr['uniacid'] = $_W['uniacid'];
            if (empty($_GPC['s_name'])) {
                message('商家名称不能为空', referer(), 'error');
            } else {
                $arr['s_name'] = $_GPC['s_name'];
            }
            if (empty($_GPC['s_address'])) {
                message('商家地址不能为空', referer(), 'error');
            } else {
                $arr['s_address'] = $_GPC['s_address'];
            }
            if (empty($_GPC['s_compername'])) {
                message('公司不能为空', referer(), 'error');
            } else {
                $arr['s_compername'] = $_GPC['s_compername'];
            }
            if (empty($_GPC['s_headname'])) {
                message('负责人不能为空', referer(), 'error');
            } else {
                $arr['s_headname'] = $_GPC['s_headname'];
            }
            if (empty($_GPC['s_headphone'])) {
                message('负责人电话不能为空', referer(), 'error');
            } else {
                $arr['s_headphone'] = $_GPC['s_headphone'];
            }
            if (empty($_GPC['s_img'])) {
                message('商家logo不能为空', referer(), 'error');
            } else {
                $arr['s_img'] = $_GPC['s_img'];
            }
            if (empty($_GPC['yingyeimg'])) {
                message('营业执照不可为空', referer(), 'error');
            } else {
                $arr['yingyeimg'] = $_GPC['yingyeimg'];
            }
            if (empty($_GPC['travelallowimg'])) {
                message('旅游许可证', referer(), 'error');
            } else {
                $arr['travelallowimg'] = $_GPC['travelallowimg'];
            }
            if (empty($_GPC['latitude'])) {
                message('商家位置信息不能为空', referer(), 'error');
            } else {
                $arr['latitude'] = $_GPC['latitude'];
            }
            if (empty($_GPC['longitude'])) {
                message('商家位置信息不能为空', referer(), 'error');
            } else {
                $arr['longitude'] = $_GPC['longitude'];
            }


            $arr['addtime'] = time();
            $add_result = pdo_insert('jindao_stores', $arr);
            if (!empty($add_result)) {
                message('添加成功', $this->createWebUrl('list', array('type' => $arr['type'])), 'success');
            } else {
                message('添加失败', referer(), 'error');
            }
        }
    }


    public function doWebEditseller()
    {
        global $_W, $_GPC;
        $table = "jindao_stores";
        $id = intval($_GPC['id']);
        if ($id == "") {
            message("参数错误", referer(), 'error');
        } else {
            $sql = "select * from " . tablename($table) . " where `uniacid`=:uniacid and `id`=:id";
            $params = array(
                ':uniacid' => $_W['uniacid'],
                ':id' => $id
            );
            $result = pdo_fetch($sql, $params);
            if (empty($result)) {
                message("信息不存在", referer(), 'error');
            } else {
                if ($_W['ispost']) {

                    $arr = array();
                    $arr['uniacid'] = $_W['uniacid'];
                    if (empty($_GPC['s_name'])) {
                        message('商家名称不能为空', referer(), 'error');
                    } else {
                        $arr['s_name'] = $_GPC['s_name'];
                    }
                    if (empty($_GPC['s_address'])) {
                        message('商家地址不能为空', referer(), 'error');
                    } else {
                        $arr['s_address'] = $_GPC['s_address'];
                    }
                    if (empty($_GPC['s_compername'])) {
                        message('公司不能为空', referer(), 'error');
                    } else {
                        $arr['s_compername'] = $_GPC['s_compername'];
                    }
                    if (empty($_GPC['s_headname'])) {
                        message('负责人不能为空', referer(), 'error');
                    } else {
                        $arr['s_headname'] = $_GPC['s_headname'];
                    }
                    if (empty($_GPC['s_headphone'])) {
                        message('负责人电话不能为空', referer(), 'error');
                    } else {
                        $arr['s_headphone'] = $_GPC['s_headphone'];
                    }
                    if (empty($_GPC['s_img'])) {
                        message('商家logo不能为空', referer(), 'error');
                    } else {
                        $arr['s_img'] = $_GPC['s_img'];
                    }
                    if (empty($_GPC['yingyeimg'])) {
                        message('营业执照不可为空', referer(), 'error');
                    } else {
                        $arr['yingyeimg'] = $_GPC['yingyeimg'];
                    }
                    if (empty($_GPC['travelallowimg'])) {
                        message('旅游许可证', referer(), 'error');
                    } else {
                        $arr['travelallowimg'] = $_GPC['travelallowimg'];
                    }
                    if (empty($_GPC['latitude'])) {
                        message('商家位置信息不能为空', referer(), 'error');
                    } else {
                        $arr['latitude'] = $_GPC['latitude'];
                    }
                    if (empty($_GPC['longitude'])) {
                        message('商家位置信息不能为空', referer(), 'error');
                    } else {
                        $arr['longitude'] = $_GPC['longitude'];
                    }

                    //$arr['addtime']=time();
                    $edit_result = pdo_update('jindao_stores', $arr, array('uniacid' => $_W['uniacid'], 'id' => $id));
                    if (!empty($edit_result)) {
                            message('编辑成功', $this->createWebUrl('list'), 'success');
                    } else {
                        message('编辑失败', referer(), 'error');
                    }
                } else {

                    include $this->template('editseller');
                }

            }

        }
    }

    public function doWebDelseller()
    {
        global $_W, $_GPC;
        $table = "jindao_stores";
        $id = intval($_GPC['id']);
        if ($id < 0) {
            message('参数错误', referer(), 'error');
        } else {
            $sql = "select * from " . tablename($table) . " where `uniacid`=:uniacid and `id`=:id";
            $params = array(
                ':uniacid' => $_W['uniacid'],
                ':id' => $id
            );
            $result = pdo_fetch($sql, $params);
            if (empty($result)) {
                message("数据不存在", referer(), 'error');
            } else {
                $del_result = pdo_delete($table, array('uniacid' => $_W['uniacid'], 'id' => $id));
                if ($del_result) {
                    message('删除成功', $this->createWebUrl('list', array('type' => $result['type'])), 'success');
                } else {
                    message("删除失败", referer(), 'error');
                }
            }

        }
    }
    //审核管理
    public function doWebSh(){
        global $_W,$_GPC;
        $table="jindao_stores";
        $s=intval($_GPC['s']);
        $id=intval($_GPC['id']);
        $result=pdo_update($table,array('status'=>$s),array('uniacid'=>$_W['uniacid'],'id'=>$id));
        if($result){
            message('操作成功',referer(),'success');
        }else{
            message('操作失败',referer(),'error');
        }

    }
    public function __construct()
    {
        global $_W, $_GPC;
        if ($_W['os'] == 'mobile') {

        } else {
            $do = $_GPC['do'];
            $doo = $_GPC['doo'];
            $act = $_GPC['act'];
            global $frames;
            if ($_W['user']['type'] < 3) {
                $frames = $this->getModuleFrames();
                $this->_calc_current_frames2($frames);
            } else {
                $frames = $this->getModuleFrames2();
                $this->_calc_current_frames2($frames);
            }

        }
    }

    function getModuleFrames()
    {

        $frames = array();
        $name = "jindao_travel";
        $frames['set']['title'] = '管理中心';
        $frames['set']['active'] = '';
        $frames['set']['items'] = array();
        $frames['set']['items']['site']['url'] = url('site/entry/site', array('m' => $name));
        $frames['set']['items']['site']['title'] = '站点设置';
        $frames['set']['items']['site']['actions'] = array();
        $frames['set']['items']['site']['active'] = '';

        $frames['seller']['title'] = '商家管理';
        $frames['seller']['active'] = '';
        $frames['seller']['items'] = array();

        $frames['seller']['items']['list1']['url'] = url('site/entry/list', array('m' => $name));
        $frames['seller']['items']['list1']['title'] = '商家列表';
        $frames['seller']['items']['list1']['actions'] = array();
        $frames['seller']['items']['list1']['active'] = '';

        $frames['seller']['items']['addseller']['url'] = url('site/entry/addseller', array('m' => $name));
        $frames['seller']['items']['addseller']['title'] = '添加商家';
        $frames['seller']['items']['addseller']['actions'] = array();
        $frames['seller']['items']['addseller']['active'] = '';


        return $frames;
    }

    function _calc_current_frames2(&$frames)
    {
        global $_W, $_GPC, $frames;
        if (!empty($frames) && is_array($frames)) {
            foreach ($frames as &$frame) {
                foreach ($frame['items'] as &$fr) {
                    $query = parse_url($fr['url'], PHP_URL_QUERY);
                    parse_str($query, $urls);
                    if (defined('ACTIVE_FRAME_URL')) {
                        $query = parse_url(ACTIVE_FRAME_URL, PHP_URL_QUERY);
                        parse_str($query, $get);
                    } else {
                        $get = $_GET;
                    }
                    if (!empty($_GPC['a'])) {
                        $get['a'] = $_GPC['a'];
                    }
                    if (!empty($_GPC['c'])) {
                        $get['c'] = $_GPC['c'];
                    }
                    if (!empty($_GPC['do'])) {
                        $get['do'] = $_GPC['do'];
                    }
                    if (!empty($_GPC['doo'])) {
                        $get['doo'] = $_GPC['doo'];
                    }
                    if (!empty($_GPC['op'])) {
                        $get['op'] = $_GPC['op'];
                    }
                    if (!empty($_GPC['m'])) {
                        $get['m'] = $_GPC['m'];
                    }
                    $diff = array_diff_assoc($urls, $get);

                    if (empty($diff)) {
                        $fr['active'] = ' active';
                        $frame['active'] = ' active';
                    }
                }
            }
        }
    }
}