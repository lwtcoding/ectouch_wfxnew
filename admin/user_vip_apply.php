<?php

/**
 * ECSHOP 加盟商申请管理
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: user_msg.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECTOUCH', true);

require(dirname(__FILE__) . '/includes/init.php');
/* 权限判断 */
admin_priv('user_vip_apply');
/*初始化数据交换对象 */
$exc = new exchange($ecs->table("user_vip_apply"), $db, 'apply_id', 'user_id');
if ($_REQUEST['act']=='list')
{
    assign_query_info();
    $msg_list = apply_list();

    $smarty->assign('msg_list',     $msg_list['apply_list']);
    $smarty->assign('filter',       $msg_list['filter']);
    $smarty->assign('record_count', $msg_list['record_count']);
    $smarty->assign('page_count',   $msg_list['page_count']);
    $smarty->assign('full_page',    1);
    $smarty->assign('sort_msg_id', '<img src="images/sort_desc.gif">');

    $smarty->assign('ur_here',      $_LANG['08_unreply_msg']);
    $smarty->assign('full_page',    1);
    $smarty->display('user_vip_apply_list.htm');
}
/*------------------------------------------------------ */
//-- 发送留言
/*------------------------------------------------------ */
if ($_REQUEST['act']=='add')
{
    $user_id = empty($_GET['user_id']) ? 0 : intval($_GET['user_id']);
    $order_id = empty($_GET['order_id']) ? 0 : intval($_GET['order_id']);
    $order_sn = $db->getOne("SELECT order_sn FROM "  . $ecs->table('order_info') . " WHERE order_id = '$order_id'");

    /* 获取关于订单所有信息 */
    $sql = "SELECT msg_id, user_name, msg_title, msg_type, msg_time, msg_content".
           " FROM " . $ecs->table('feedback').
           " WHERE user_id ='$user_id' AND order_id = '$order_id'";

    $msg_list = $db->getAll($sql);
    foreach($msg_list as $key=>$val)
    {
        $msg_list[$key]['msg_time'] = local_date($GLOBALS['_CFG']['time_format'], $val['msg_time']);
    }

    assign_query_info();
    $smarty->assign('ur_here',      sprintf($_LANG['msg_for_order'], $order_sn));
    $smarty->assign('action_link',  array('text'=>$_LANG['order_detail'], 'href'=>'order.php?act=info&order_id=' . $order_id));
    $smarty->assign('msg_list', $msg_list);
    $smarty->assign('order_id', $_GET['order_id']);
    $smarty->assign('user_id',  $_GET['user_id']);
    $smarty->display('msg_add.htm');
}

if ($_REQUEST['act']=='insert')
{
    $sql = "INSERT INTO " . $ecs->table('feedback') . "(parent_id, user_id, user_name, user_email, msg_title, msg_type, msg_content, msg_time, message_img, order_id)" .
            " VALUES (0, '$_POST[user_id]', '$_SESSION[admin_name]', ' ', ".
            " '$_POST[msg_title]', 5, '$_POST[msg_content]', '" . gmtime() . "', '', '$_POST[order_id]')";

    $db->query($sql);

    ecs_header("Location: user_msg.php?act=add&order_id=$_POST[order_id]&user_id=$_POST[user_id]\n");
    exit;
}

if ($_REQUEST['act'] == 'remove_msg')
{
    $msg_id = empty($_GET['msg_id']) ? 0 : intval($_GET['msg_id']);
    $order_id = empty($_GET['order_id']) ? 0 : intval($_GET['order_id']);
    $user_id = empty($_GET['user_id']) ? 0 : intval($_GET['user_id']);
    $sql = "SELECT user_id, order_id, message_img FROM " . $ecs->table('feedback') . " WHERE msg_id='$msg_id'";
    $row = $db->getRow($sql);
    if ($row)
    {
        if ($row['user_id'] == $user_id && $row['order_id'] == $order_id)
        {
            if ($row['message_img'])
            {
                @unlink(ROOT_PATH. DATA_DIR . '/attached/feedbackimg/' . $row['message_img']);
            }
            $sql = "DELETE FROM " . $ecs->table('feedback') . " WHERE msg_id=$msg_id LIMIT 1";
            $db->query($sql);
        }
    }

    ecs_header("Location: user_msg.php?act=add&order_id=$_GET[order_id]&user_id=$_GET[user_id]\n");
    exit;
}
/*------------------------------------------------------ */
//-- 更新留言的状态为显示或者禁止
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'check')
{
    if ($_REQUEST['check'] == 'allow')
    {
//        $sql = "SELECT uva.*, u.user_name " .
//            "FROM " . $GLOBALS['ecs']->table('user_vip_apply') . " AS uva ".
//            "LEFT JOIN " . $GLOBALS['ecs']->table('users') . " AS u ON u.user_id = uva.user_id ".
//
//
//            "WHERE uva.apply_id = '$_REQUEST[id]'";
//        $apply= $db->GetRow($sql);
//
//        //判断上级是否代理，并且找出会员级别
//        $sql = "SELECT au.proxy_vip_user_rank " .
//
//
//            "FROM " . $GLOBALS['ecs']->table('admin_user') . " AS au ".
//            "LEFT JOIN " . $GLOBALS['ecs']->table('users') . " AS pu ON au.proxy_user_id = pu.user_id ".
//            "LEFT JOIN " . $GLOBALS['ecs']->table('users') . " AS u ON u.parent_id = pu.user_id ".
//
//            "WHERE au.proxy_user = 1 AND u.user_id = '$apply[user_id]'";
//        $user = $db->GetRow($sql);
//
//        if ($user) {
//            $sql = "UPDATE " .$ecs->table('user_vip_apply'). " SET status = 2 WHERE apply_id = '$_REQUEST[id]'";
//            $db->query($sql);
//
//            $sql = "UPDATE " .$ecs->table('users'). " SET is_vip = 1,user_rank = '$user[proxy_vip_user_rank]' WHERE user_id = '$apply[user_id]'";
//            $db->query($sql);
//        } else {
//            //设置为总代理的加盟商等级
//
//            $sql = "SELECT au.proxy_vip_user_rank " .
//
//
//                "FROM " . $GLOBALS['ecs']->table('admin_user') . " AS au ".
//
//                "WHERE au.proxy_user = 1 AND au.proxy_default = 1";
//            $user = $db->GetRow($sql);
//
//            if ($user) {
//                $sql = "UPDATE " . $ecs->table('user_vip_apply') . " SET status = 2 WHERE apply_id = '$_REQUEST[id]'";
//                $db->query($sql);
//
//                $sql = "UPDATE " . $ecs->table('users') . " SET is_vip = 1,user_rank = '$user[proxy_vip_user_rank]' WHERE user_id = '$apply[user_id]'";
//                $db->query($sql);
//            }
//        }
        $sql = "SELECT uva.*, u.user_name " .
            "FROM " . $GLOBALS['ecs']->table('user_vip_apply') . " AS uva ".
            "LEFT JOIN " . $GLOBALS['ecs']->table('users') . " AS u ON u.user_id = uva.user_id ".


            "WHERE uva.apply_id = '$_REQUEST[id]'";
        $apply= $db->GetRow($sql);
        $sql = "UPDATE " . $ecs->table('user_vip_apply') . " SET status = 2 WHERE apply_id = '$_REQUEST[id]'";
                $db->query($sql);

                $sql = "UPDATE " . $ecs->table('users') . " SET is_vip = 1,user_rank = '$_REQUEST[user_rank]' WHERE user_id = '$apply[user_id]'";
                $db->query($sql);

        ecs_header("Location: user_vip_apply.php?act=view&id=$_REQUEST[id]\n");
        exit;
    }
    else if ($_REQUEST['check'] == 'forbid')
    {
        $sql = "UPDATE " .$ecs->table('user_vip_apply'). " SET status = 0 WHERE apply_id = '$_REQUEST[id]'";
        $db->query($sql);

        ecs_header("Location: user_vip_apply.php?act=view&id=$_REQUEST[id]\n");
        exit;
    }
//    else if ($_REQUEST['check'] == 'save')
//    {
//        $sql = "SELECT uva.* " .
//            "FROM " . $GLOBALS['ecs']->table('user_vip_apply') . " AS uva ".
//
//
//            "WHERE uva.apply_id = '$_REQUEST[id]'";
//        $apply= $db->GetRow($sql);
//
//
//        $sql = "UPDATE " .$ecs->table('users'). " SET user_rank = '$_REQUEST[user_rank]' WHERE user_id = '$apply[user_id]'";
//        $db->query($sql);
//
//        ecs_header("Location: user_vip_apply.php?act=view&id=$_REQUEST[id]\n");
//        exit;
//    }
}
/*------------------------------------------------------ */
//-- 列出所有留言
/*------------------------------------------------------ */
if ($_REQUEST['act']=='list_all')
{
    assign_query_info();
//    $msg_list = msg_list();

    $smarty->assign('msg_list',     $msg_list['msg_list']);
    $smarty->assign('filter',       $msg_list['filter']);
    $smarty->assign('record_count', $msg_list['record_count']);
    $smarty->assign('page_count',   $msg_list['page_count']);
    $smarty->assign('full_page',    1);
    $smarty->assign('sort_msg_id', '<img src="images/sort_desc.gif">');

    $smarty->assign('ur_here',      $_LANG['08_unreply_msg']);
    $smarty->assign('full_page',    1);
    $smarty->display('msg_list.htm');
}

/*------------------------------------------------------ */
//-- ajax显示留言列表
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $msg_list = apply_list();

    $smarty->assign('msg_list',     $msg_list['apply_list']);
    $smarty->assign('filter',       $msg_list['filter']);
    $smarty->assign('record_count', $msg_list['record_count']);
    $smarty->assign('page_count',   $msg_list['page_count']);

    $sort_flag  = sort_flag($msg_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('user_vip_apply_list.htm'), '', array('filter' => $msg_list['filter'], 'page_count' => $msg_list['page_count']));
}
/*------------------------------------------------------ */
//-- ajax 删除留言
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    $apply_id = intval($_REQUEST['id']);

    /* 检查权限 */
    check_authz_json('user_vip_apply');


    if ($exc->drop($apply_id))
    {

        $url = 'user_vip_apply.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);
        ecs_header("Location: $url\n");
        exit;
    }
    else
    {
        make_json_error($GLOBALS['db']->error());
    }
}

/*------------------------------------------------------ */
//-- 批量操作删除、允许显示、禁止显示用户评论
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'batch')
{
    admin_priv('feedback_priv');
    $action = isset($_POST['sel_action']) ? trim($_POST['sel_action']) : 'def';

    if (isset($_POST['checkboxes']))
    {
        switch ($action)
        {
           case 'remove':
                $db->query("DELETE FROM " . $ecs->table('feedback') . " WHERE " . db_create_in($_POST['checkboxes'], 'msg_id'));
                $db->query("DELETE FROM " . $ecs->table('feedback') . " WHERE " . db_create_in($_POST['checkboxes'], 'parent_id'));
                break;

           case 'allow' :
               $db->query("UPDATE " . $ecs->table('feedback') . " SET msg_status = 1  WHERE " . db_create_in($_POST['checkboxes'], 'msg_id'));
               break;

           case 'deny' :
               $db->query("UPDATE " . $ecs->table('feedback') . " SET msg_status = 0,msg_area =1  WHERE " . db_create_in($_POST['checkboxes'], 'msg_id'));
               break;

           default :
               break;
        }

        clear_cache_files();
        $action = ($action == 'remove') ? 'remove' : 'edit';
        admin_log('', $action, 'adminlog');

        $link[] = array('text' => $_LANG['back_list'], 'href' => 'user_msg.php?act=list_all');
        sys_msg(sprintf($_LANG['batch_drop_success'], count($_POST['checkboxes'])), 0, $link);
    }
    else
    {
        /* 提示信息 */
        $link[] = array('text' => $_LANG['back_list'], 'href' => 'user_msg.php?act=list_all');
        sys_msg($_LANG['no_select_comment'], 0, $link);
    }
}


/*------------------------------------------------------ */
//-- 回复留言
/*------------------------------------------------------ */
elseif ($_REQUEST['act']=='view')
{
    $smarty->assign('special_ranks',    get_rank_list(true));
    $smarty->assign('send_fail',   !empty($_REQUEST['send_ok']));
    $smarty->assign('msg',         get_detail(intval($_REQUEST['id'])));
    $smarty->assign('ur_here',     $_LANG['detail']);


    assign_query_info();
    $smarty->display('user_vip_apply_info.htm');
}
elseif ($_REQUEST['act']=='action')
{
    if (empty($_REQUEST['parent_id']))
    {
        $sql = "INSERT INTO ".$ecs->table('feedback')." (msg_title, msg_time, user_id, user_name , ".
                    "user_email, parent_id, msg_content) ".
                "VALUES ('reply', '".gmtime()."', '".$_SESSION['admin_id']."', ".
                    "'".$_SESSION['admin_name']."', '".$_POST['user_email']."', ".
                    "'".$_REQUEST['msg_id']."', '".$_POST['msg_content']."') ";
        $db->query($sql);
    }
    else
    {
        $sql = "UPDATE ".$ecs->table('feedback')." SET user_name = '".$_SESSION['admin_name']."', user_email = '".$_POST['user_email']."', msg_content='".$_POST['msg_content']."', msg_time = '".gmtime()."' WHERE msg_id = '".$_REQUEST['parent_id']."'";
        $db->query($sql);
    }

    /* 邮件通知处理流程 */
    if (!empty($_POST['send_email_notice']) or isset($_POST['remail']))
    {
        //获取邮件中的必要内容
        $sql = 'SELECT user_name, user_email, msg_title, msg_content ' .
               'FROM ' .$ecs->table('feedback') .
               " WHERE msg_id ='$_REQUEST[msg_id]'";
        $message_info = $db->getRow($sql);

        /* 设置留言回复模板所需要的内容信息 */
        $template    = get_mail_template('user_message');
        $message_content = $message_info['msg_title'] . "\r\n" . $message_info['msg_content'];

        $smarty->assign('user_name',   $message_info['user_name']);
        $smarty->assign('message_note', $_POST['msg_content']);
        $smarty->assign('message_content', $message_content);
        $smarty->assign('shop_name',   "<a href='".$ecs->url()."'>" . $_CFG['shop_name'] . '</a>');
        $smarty->assign('send_date',   date('Y-m-d'));

        $content = $smarty->fetch('str:' . $template['template_content']);

        /* 发送邮件 */
        if (send_mail($message_info['user_name'], $message_info['user_email'], $template['template_subject'], $content, $template['is_html']))
        {
            $send_ok = 0;
        }
        else
        {
            $send_ok = 1;
        }
    }

    ecs_header("Location: ?act=view&id=".$_REQUEST['msg_id']."&send_ok=$send_ok\n");
    exit;

}

/*------------------------------------------------------ */
//-- 删除会员上传的文件
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'drop_file')
{
    /* 删除上传的文件 */
    $file = $_GET['file'];
    $file = str_replace('/','',$file);
    @unlink('../' . DATA_DIR . '/attached/feedbackimg/'.$file);

    /* 更新数据库 */
    $db->query("UPDATE ".$ecs->table('feedback')." SET message_img = '' WHERE msg_id = '$_GET[id]'");

    ecs_header("Location: user_msg.php?act=view&amp;id=".$_GET['id']."\n");
    exit;
}

/**
 *
 *
 * @access  public
 * @param
 *
 * @return void
 */
function apply_list()
{
    //todo 如果是代理帐号只查询自己下级的
    $where = ' WHERE 1 ';

    $c_admin = admin_info();
    if ($c_admin['proxy_user']) {
        if ($c_admin['proxy_default']) {
            $where .= " AND ( pu.user_id = '$c_admin[proxy_user_id]' OR u.parent_proxy_user_id = 0 ) ";
            $where .= " AND ( u.user_id != '$c_admin[proxy_user_id]' ) ";
        } else {
            $where .= " AND ( pu.user_id = '$c_admin[proxy_user_id]' ) ";
        }
    }
    $filter['keywords']   = empty($_REQUEST['keywords']) ? '' : trim($_REQUEST['keywords']);
    if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
    {
        $filter['keywords'] = json_str_iconv($filter['keywords']);
    }
    $sql = "SELECT count(*) FROM " .$GLOBALS['ecs']->table('user_vip_apply'). " AS uva " .
        "LEFT JOIN " . $GLOBALS['ecs']->table('users') . " AS u ON u.user_id=uva.user_id ".
        "LEFT JOIN " . $GLOBALS['ecs']->table('user_rank') . " AS ur ON ur.rank_id=u.user_rank ".
        "LEFT JOIN " . $GLOBALS['ecs']->table('users') . " AS pu ON pu.user_id=u.parent_id ".
        "LEFT JOIN " . $GLOBALS['ecs']->table('admin_user') . " AS au ON au.proxy_user_id=pu.user_id ".
           "  " . $where;
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);
    $filter['sort_by']    = empty($_REQUEST['sort_by']) ? 'uva.apply_id' : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
    /* 分页大小 */
    $filter = page_and_size($filter);

    $sql = "SELECT uva.*, u.user_name, pu.user_name as parent_user_name, pu.user_id as parent_user_id,u.user_rank,ur.rank_name " .
        "FROM " . $GLOBALS['ecs']->table('user_vip_apply') . " AS uva ".
        "LEFT JOIN " . $GLOBALS['ecs']->table('users') . " AS u ON u.user_id=uva.user_id ".
        "LEFT JOIN " . $GLOBALS['ecs']->table('user_rank') . " AS ur ON ur.rank_id=u.user_rank ".
        "LEFT JOIN " . $GLOBALS['ecs']->table('users') . " AS pu ON pu.user_id=u.parent_id ".
        "LEFT JOIN " . $GLOBALS['ecs']->table('admin_user') . " AS au ON au.proxy_user_id=pu.user_id ".
        $where.

            "ORDER by $filter[sort_by] $filter[sort_order] ".
            "LIMIT " . $filter['start'] . ', ' . $filter['page_size'];

    $msg_list = $GLOBALS['db']->getAll($sql);
    foreach ($msg_list AS $key => $value)
    {   if($value['order_id'] > 0)
        {
            $msg_list[$key]['order_sn'] = $GLOBALS['db']->getOne("SELECT order_sn FROM " . $GLOBALS['ecs']->table('order_info') ." WHERE order_id= " .$value['order_id']);
        }
        $msg_list[$key]['msg_time'] = local_date($GLOBALS['_CFG']['time_format'], $value['msg_time']);
        $msg_list[$key]['msg_type'] = $GLOBALS['_LANG']['type'][$value['msg_type']];
    }
    $filter['keywords'] = stripslashes($filter['keywords']);
    $arr = array('apply_list' => $msg_list, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}
function get_detail($id)
{
    global $ecs, $db, $_CFG;

    $sql = "SELECT uva.*, u.user_name, pu.user_name as parent_user_name, pu.user_id as parent_user_id,u.user_rank,ur.rank_name " .
        "FROM " . $GLOBALS['ecs']->table('user_vip_apply') . " AS uva ".
        "LEFT JOIN " . $GLOBALS['ecs']->table('users') . " AS u ON u.user_id=uva.user_id ".
        "LEFT JOIN " . $GLOBALS['ecs']->table('user_rank') . " AS ur ON ur.rank_id=u.user_rank ".
        "LEFT JOIN " . $GLOBALS['ecs']->table('users') . " AS pu ON pu.user_id=u.parent_id ".

        "WHERE uva.apply_id = '$id'";
    $msg = $db->GetRow($sql);



    return $msg;
}
/**
 * 获得留言的详细信息
 *
 * @param   integer $id
 *
 * @return  array
 */
function get_feedback_detail($id)
{
    global $ecs, $db, $_CFG;

    $sql = "SELECT T1.*, T2.msg_id AS reply_id, T2.user_name  AS reply_name, u.email AS reply_email, ".
                "T2.msg_content AS reply_content , T2.msg_time AS reply_time, T2.user_name AS reply_name ".
            "FROM ".$ecs->table('feedback'). " AS T1 ".
            "LEFT JOIN " .$ecs->table('admin_user'). " AS u ON u.user_id='" .$_SESSION['admin_id']. "' ".
            "LEFT JOIN ".$ecs->table('feedback'). " AS T2 ON T2.parent_id=T1.msg_id ".
            "WHERE T1.msg_id = '$id'";
    $msg = $db->GetRow($sql);

    if ($msg)
    {
        $msg['msg_time']   = local_date($_CFG['time_format'], $msg['msg_time']);
        $msg['reply_time'] = local_date($_CFG['time_format'], $msg['reply_time']);
    }

    return $msg;
}

?>