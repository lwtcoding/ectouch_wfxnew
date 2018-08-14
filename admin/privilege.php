<?php

/**
 * ECSHOP 管理员信息以及权限管理程序
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: privilege.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECTOUCH', true);

require(dirname(__FILE__) . '/includes/init.php');

/* act操作项的初始化 */
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'login';
}
else
{
    $_REQUEST['act'] = trim($_REQUEST['act']);
}

/* 初始化 $exc 对象 */
$exc = new exchange($ecs->table("admin_user"), $db, 'user_id', 'user_name');

/*------------------------------------------------------ */
//-- 退出登录
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'logout')
{
    /* 清除cookie */
    setcookie('ECSCP[admin_id]',   '', 1);
    setcookie('ECSCP[admin_pass]', '', 1);

    $sess->destroy_session();

    $_REQUEST['act'] = 'login';
}

/*------------------------------------------------------ */
//-- 登陆界面
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'login')
{
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");

    if ((intval($_CFG['captcha']) & CAPTCHA_ADMIN) && gd_version() > 0)
    {
        $smarty->assign('gd_version', gd_version());
        $smarty->assign('random',     mt_rand());
    }

    $smarty->display('login.htm');
}

/*------------------------------------------------------ */
//-- 验证登陆信息
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'signin')
{
    if (intval($_CFG['captcha']) & CAPTCHA_ADMIN)
    {
        // include_once(ROOT_PATH . 'includes/cls_captcha.php');

        /* 检查验证码是否正确 */
        $validator = new captcha();
        if (!empty($_POST['captcha']) && !$validator->check_word($_POST['captcha']))
        {
            sys_msg($_LANG['captcha_error'], 1);
        }
    }

    $_POST['username'] = isset($_POST['username']) ? trim($_POST['username']) : '';
    $_POST['password'] = isset($_POST['password']) ? trim($_POST['password']) : '';

    $sql="SELECT `ec_salt` FROM ". $ecs->table('admin_user') ."WHERE user_name = '" . $_POST['username']."'";
    $ec_salt =$db->getOne($sql);
    if(!empty($ec_salt))
    {
         /* 检查密码是否正确 */
         $sql = "SELECT user_id, user_name, password, last_login, action_list, last_login,suppliers_id,ec_salt".
            " FROM " . $ecs->table('admin_user') .
            " WHERE user_name = '" . $_POST['username']. "' AND password = '" . md5(md5($_POST['password']).$ec_salt) . "'";
    }
    else
    {
         /* 检查密码是否正确 */
         $sql = "SELECT user_id, user_name, password, last_login, action_list, last_login,suppliers_id,ec_salt".
            " FROM " . $ecs->table('admin_user') .
            " WHERE user_name = '" . $_POST['username']. "' AND password = '" . md5($_POST['password']) . "'";
    }
    $row = $db->getRow($sql);
    if ($row)
    {
        // 检查是否为供货商的管理员 所属供货商是否有效
        if (!empty($row['suppliers_id']))
        {
            $supplier_is_check = suppliers_list_info(' is_check = 1 AND suppliers_id = ' . $row['suppliers_id']);
            if (empty($supplier_is_check))
            {
                sys_msg($_LANG['login_disable'], 1);
            }
        }

        // 登录成功
        set_admin_session($row['user_id'], $row['user_name'], $row['action_list'], $row['last_login']);
        $_SESSION['suppliers_id'] = $row['suppliers_id'];
		if(empty($row['ec_salt']))
	    {
			$ec_salt=rand(1,9999);
			$new_possword=md5(md5($_POST['password']).$ec_salt);
             $db->query("UPDATE " .$ecs->table('admin_user').
                 " SET ec_salt='" . $ec_salt . "', password='" .$new_possword . "'".
                 " WHERE user_id='$_SESSION[admin_id]'");
		}

        if($row['action_list'] == 'all' && empty($row['last_login']))
        {
            $_SESSION['shop_guide'] = true;
        }

        // 更新最后登录时间和IP
        $db->query("UPDATE " .$ecs->table('admin_user').
                 " SET last_login='" . gmtime() . "', last_ip='" . real_ip() . "'".
                 " WHERE user_id='$_SESSION[admin_id]'");

        if (isset($_POST['remember']))
        {
            $time = gmtime() + 3600 * 24 * 365;
            setcookie('ECSCP[admin_id]',   $row['user_id'],                            $time);
            setcookie('ECSCP[admin_pass]', md5($row['password'] . $_CFG['hash_code']), $time);
        }

        // 清除购物车中过期的数据
        clear_cart();

        ecs_header("Location: ./index.php\n");

        exit;
    }
    else
    {
        sys_msg($_LANG['login_faild'], 1);
    }
}

/*------------------------------------------------------ */
//-- 管理员列表页面
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'list')
{
    /* 模板赋值 */
    $smarty->assign('ur_here',     $_LANG['admin_list']);
    $smarty->assign('action_link', array('href'=>'privilege.php?act=add', 'text' => $_LANG['admin_add']));
    $smarty->assign('full_page',   1);
    $smarty->assign('admin_list',  get_admin_userlist());

    /* 显示页面 */
    assign_query_info();
    $smarty->display('privilege_list.htm');
}

/*------------------------------------------------------ */
//-- 查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $smarty->assign('admin_list',  get_admin_userlist());

    make_json_result($smarty->fetch('privilege_list.htm'));
}

/*------------------------------------------------------ */
//-- 添加管理员页面
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add')
{
    /* 检查权限 */
    admin_priv('admin_manage');
    $user = array(  'rank_points'   => $_CFG['register_points'],
        'pay_points'    => $_CFG['register_points'],
        'sex'           => 0,
        'credit_line'   => 0
    );
    /* 取出注册扩展字段 */
    $sql = 'SELECT * FROM ' . $ecs->table('reg_fields') . ' WHERE type < 2 AND display = 1 AND id != 6 ORDER BY dis_order, id';
    $extend_info_list = $db->getAll($sql);
    $smarty->assign('extend_info_list', $extend_info_list);
     /* 模板赋值 */

    $smarty->assign('ur_here',     $_LANG['admin_add']);
    $smarty->assign('action_link', array('href'=>'privilege.php?act=list', 'text' => $_LANG['admin_list']));
    $smarty->assign('form_act',    'insert');
    $smarty->assign('action',      'add');
    $smarty->assign('countries',        get_regions());
    $smarty->assign('default_country',  $_CFG['shop_country']);
    $smarty->assign('select_role',  get_role_list());
    $smarty->assign('associate_user',             $user);
    $smarty->assign('special_ranks',    get_rank_list(true));
    /* 显示页面 */
    assign_query_info();
    $smarty->display('privilege_info.htm');
}

/*------------------------------------------------------ */
//-- 添加管理员的处理
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'insert')
{
    admin_priv('admin_manage');
    if($_POST['token']!=$_CFG['token'])
    {
         sys_msg('add_error', 1);
    }
    /* 判断管理员是否已经存在 */
    if (!empty($_POST['user_name']))
    {
        $is_only = $exc->is_only('user_name', $_POST['user_name']);

        if (!$is_only)
        {
            sys_msg(sprintf($_LANG['user_name_exist'], $_POST['user_name']), 1);
        }
    }

    /* Email地址是否有重复 */
    if (!empty($_POST['email']))
    {
        $is_only = $exc->is_only('email', stripslashes($_POST['email']));

        if (!$is_only)
        {
            sys_msg(sprintf($_LANG['email_exist'], stripslashes($_POST['email'])), 1);
        }
    }
    $proxy_user = 0;
    $proxy_default = 0;
    $proxy_region = null;
    $proxy_user_id = null;
    $proxy_vip_user_rank = 0;
    if (!empty($_POST['proxy_user']))
    {
        $proxy_user = $_POST['proxy_user'];
    }
    /* 添加选定的城市和地区 */
    if (isset($_POST['proxy_region']) && is_array($_POST['proxy_region']))
    {
        foreach ($_POST['proxy_region'] AS $key => $val)
        {
            $proxy_region = $val;
        }
    }
    if (!empty($_POST['proxy_user_id']))
    {
        $proxy_user_id = $_POST['proxy_user_id'];
    }
    if (!empty($_POST['proxy_default']))
    {
        $proxy_default = $_POST['proxy_default'];
    }
    if (!empty($_POST['proxy_vip_user_rank']))
    {
        $proxy_vip_user_rank = $_POST['proxy_vip_user_rank'];
    }
    if ($proxy_user==1) {
        $username = empty($_POST['associate_username']) ? '' : trim($_POST['associate_username']);
        $password = empty($_POST['associate_password']) ? '' : trim($_POST['associate_password']);
        $email = empty($_POST['associate_email']) ? '' : trim($_POST['associate_email']);
        $sex = empty($_POST['associate_sex']) ? 0 : intval($_POST['associate_sex']);
        $sex = in_array($sex, array(0, 1, 2)) ? $sex : 0;
        $birthday = $_POST['associate_birthdayYear'] . '-' .  $_POST['associate_birthdayMonth'] . '-' . $_POST['associate_birthdayDay'];
        $rank = empty($_POST['associate_user_rank']) ? 0 : intval($_POST['associate_user_rank']);
        $credit_line = empty($_POST['associate_credit_line']) ? 0 : floatval($_POST['associate_credit_line']);

        $nick_name = empty($_POST['associate_nick_name']) ? '' : trim($_POST['associate_nick_name']);
        $identity = empty($_POST['associate_identity']) ? '' : trim($_POST['associate_identity']);
        $address = empty($_POST['associate_address']) ? '' : trim($_POST['associate_address']);
        $country = empty($_POST['associate_country']) ? '' : trim($_POST['associate_country']);
        $province = empty($_POST['associate_province']) ? '' : trim($_POST['associate_province']);
        $city = empty($_POST['associate_city']) ? '' : trim($_POST['associate_city']);
        $districts = empty($_POST['associate_districts']) ? '' : trim($_POST['associate_districts']);
        $mobile_phone = empty($_POST['associate_mobile_phone']) ? '' : trim($_POST['associate_mobile_phone']);

        $users =& init_users();

        if (!$users->add_user($username, $password, $email))
        {
            /* 插入会员数据失败 */
            if ($users->error == ERR_INVALID_USERNAME)
            {
                $msg = $_LANG['username_invalid'];
            }
            elseif ($users->error == ERR_USERNAME_NOT_ALLOW)
            {
                $msg = $_LANG['username_not_allow'];
            }
            elseif ($users->error == ERR_USERNAME_EXISTS)
            {
                $msg = $_LANG['username_exists'];
            }
            elseif ($users->error == ERR_INVALID_EMAIL)
            {
                $msg = $_LANG['email_invalid'];
            }
            elseif ($users->error == ERR_EMAIL_NOT_ALLOW)
            {
                $msg = $_LANG['email_not_allow'];
            }
            elseif ($users->error == ERR_EMAIL_EXISTS)
            {
                $msg = $_LANG['email_exists'];
            }
            else
            {
                //die('Error:'.$users->error_msg());
            }
            sys_msg($msg, 1);
        }

        $sql = "SELECT user_id FROM " . $ecs->table('users') . " WHERE user_name = '".$username."'";
        $row = $db->getRow($sql);
        $proxy_user_id = $row['user_id'];
        $action_list = $row['action_list'];

        /* 注册送积分 */
        if (!empty($GLOBALS['_CFG']['register_points']))
        {
            log_account_change($_SESSION['user_id'], 0, 0, $GLOBALS['_CFG']['register_points'], $GLOBALS['_CFG']['register_points'], $_LANG['register_points']);
        }

        /*把新注册用户的扩展信息插入数据库*/
        $sql = 'SELECT id FROM ' . $ecs->table('reg_fields') . ' WHERE type = 0 AND display = 1 ORDER BY dis_order, id';   //读出所有扩展字段的id
        $fields_arr = $db->getAll($sql);

        $extend_field_str = '';    //生成扩展字段的内容字符串
        $user_id_arr = $users->get_profile_by_name($username);
        foreach ($fields_arr AS $val)
        {
            $extend_field_index = 'extend_field' . $val['id'];
            if(!empty($_POST[$extend_field_index]))
            {
                $temp_field_content = strlen($_POST[$extend_field_index]) > 100 ? mb_substr($_POST[$extend_field_index], 0, 99) : $_POST[$extend_field_index];
                $extend_field_str .= " ('" . $user_id_arr['user_id'] . "', '" . $val['id'] . "', '" . $temp_field_content . "'),";
            }
        }
        $extend_field_str = substr($extend_field_str, 0, -1);

        if ($extend_field_str)      //插入注册扩展数据
        {
            $sql = 'INSERT INTO '. $ecs->table('reg_extend_info') . ' (`user_id`, `reg_field_id`, `content`) VALUES' . $extend_field_str;
            $db->query($sql);
        }

        /* 更新会员的其它信息 */
        $other =  array();
        $other['is_proxy'] = 1;
        $other['credit_line'] = $credit_line;
        $other['user_rank']  = $rank;
        $other['sex']        = $sex;
        $other['birthday']   = $birthday;
        $other['reg_time'] = local_strtotime(local_date('Y-m-d H:i:s'));

        $other['msn'] = isset($_POST['extend_field1']) ? htmlspecialchars(trim($_POST['extend_field1'])) : '';
        $other['qq'] = isset($_POST['extend_field2']) ? htmlspecialchars(trim($_POST['extend_field2'])) : '';
        $other['office_phone'] = isset($_POST['extend_field3']) ? htmlspecialchars(trim($_POST['extend_field3'])) : '';
        $other['home_phone'] = isset($_POST['extend_field4']) ? htmlspecialchars(trim($_POST['extend_field4'])) : '';
        $other['mobile_phone'] = isset($_POST['extend_field5']) ? htmlspecialchars(trim($_POST['extend_field5'])) : '';


        $other['nick_name']   = $nick_name;
        $other['identity']   = $identity;
        $other['address']   = $address;
        $other['country']   = $country;
        $other['province']   = $province;
        $other['city']   = $city;
        $other['districts']   = $districts;
        $other['mobile_phone']   = $mobile_phone;

        $db->autoExecute($ecs->table('users'), $other, 'UPDATE', "user_name = '$username'");

        //开启分销商店
        $enable_drp_shop = [];
        $enable_drp_shop['shop_name'] = $username.'的商店';
        $enable_drp_shop['real_name'] = $username;
        $enable_drp_shop['shop_mobile'] = $other['mobile_phone'] ;
        $enable_drp_shop['user_id'] = $proxy_user_id;
        $enable_drp_shop['create_time'] = gmtime();
        $enable_drp_shop['audit'] = 1;
        $enable_drp_shop['open'] = 1;

        $db->autoExecute($ecs->table('drp_shop'), $enable_drp_shop, 'INSERT');

    }

    /* 获取添加日期及密码 */
    $add_time = gmtime();
    
    $password  = md5($_POST['password']);
    $role_id = '';
    $action_list = '';
    if (!empty($_POST['select_role']))
    {
        $sql = "SELECT action_list FROM " . $ecs->table('role') . " WHERE role_id = '".$_POST['select_role']."'";
        $row = $db->getRow($sql);
        $action_list = $row['action_list'];
        $role_id = $_POST['select_role'];
    }

        $sql = "SELECT nav_list FROM " . $ecs->table('admin_user') . " WHERE action_list = 'all'";
        $row = $db->getRow($sql);
    //总代理只能设置一个
    if ($proxy_default == 1) {
        $sql = "SELECT count(*) FROM " . $ecs->table('admin_user') . " WHERE proxy_user =1 and proxy_default=1";
        $count = $db->getOne($sql);
        if ($count>0) {
            sys_msg('总部代理只能设置1个！', 1);
        }
    }
    $sql = "INSERT INTO ".$ecs->table('admin_user')." (user_name, email, password, add_time, nav_list, action_list, role_id, proxy_user, proxy_region, proxy_user_id, proxy_vip_user_rank, proxy_default) ".
           "VALUES ('".trim($_POST['user_name'])."', '".trim($_POST['email'])."', '$password', '$add_time', '$row[nav_list]', '$action_list', '$role_id', '$proxy_user', '$proxy_region', '$proxy_user_id', '$proxy_vip_user_rank', '$proxy_default')";

    $db->query($sql);
    //添加关联用户

    /* 转入权限分配列表 */
    $new_id = $db->Insert_ID();

    /*添加链接*/
    $link[0]['text'] = $_LANG['go_allot_priv'];
    $link[0]['href'] = 'privilege.php?act=allot&id='.$new_id.'&user='.$_POST['user_name'].'';

    $link[1]['text'] = $_LANG['continue_add'];
    $link[1]['href'] = 'privilege.php?act=add';

    sys_msg($_LANG['add'] . "&nbsp;" .$_POST['user_name'] . "&nbsp;" . $_LANG['action_succeed'],0, $link);

    /* 记录管理员操作 */
    admin_log($_POST['user_name'], 'add', 'privilege');
 }

/*------------------------------------------------------ */
//-- 编辑管理员信息
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit')
{
    /* 不能编辑demo这个管理员 */
    if ($_SESSION['admin_name'] == 'demo')
    {
       $link[] = array('text' => $_LANG['back_list'], 'href'=>'privilege.php?act=list');
       sys_msg($_LANG['edit_admininfo_cannot'], 0, $link);
    }

    $_REQUEST['id'] = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;

    /* 查看是否有权限编辑其他管理员的信息 */
    if ($_SESSION['admin_id'] != $_REQUEST['id'])
    {
        admin_priv('admin_manage');
    }

    /* 获取管理员信息 */
    $sql = "SELECT user_id, user_name, email, password, agency_id, role_id, proxy_user, proxy_region, proxy_user_id, proxy_vip_user_rank, proxy_default FROM " .$ecs->table('admin_user').
           " WHERE user_id = '".$_REQUEST['id']."'";
    $user_info = $db->getRow($sql);


    /* 取得该管理员负责的办事处名称 */
    if ($user_info['agency_id'] > 0)
    {
        $sql = "SELECT agency_name FROM " . $ecs->table('agency') . " WHERE agency_id = '$user_info[agency_id]'";
        $user_info['agency_name'] = $db->getOne($sql);
    }



    /* 获得该管理员的权限 */
    $priv_str = $db->getOne("SELECT action_list FROM " .$ecs->table('admin_user'). " WHERE user_id = '$_GET[id]'");

    /* 如果被编辑的管理员拥有了all这个权限，将不能编辑 */
    if ($priv_str != 'all')
    {
       $smarty->assign('select_role',  get_role_list());
    }
    /* 获得该区域下的所有地区 */
    $regions = array();
    if ($user_info['proxy_region']) {
        $sql = "SELECT r.region_id, r.region_name ".
            "FROM ".$ecs->table('region'). " AS r ".
            "WHERE r.region_id='$user_info[proxy_region]'";
        $res = $db->query($sql);
        while ($arr = $db->fetchRow($res))
        {
            $regions[$arr['region_id']] = $arr['region_name'];
        }
    }

    if ($user_info['proxy_user_id']) {

        $sql = "SELECT u.*, u2.user_name as parent_username".
            " FROM " .$ecs->table('users'). " u LEFT JOIN " . $ecs->table('users') . " u2 ON u.parent_id = u2.user_id WHERE u.user_id='$user_info[proxy_user_id]'";

        $row = $db->GetRow($sql);
        $row['user_name'] = addslashes($row['user_name']);
        $users  =& init_users();
        $user   = $users->get_user_info($row['user_name']);

        $sql = "SELECT u.*, u2.user_name as parent_username".
            " FROM " .$ecs->table('users'). " u LEFT JOIN " . $ecs->table('users') . " u2 ON u.parent_id = u2.user_id WHERE u.user_id='$user_info[proxy_user_id]'";

        $row = $db->GetRow($sql);

        if ($row)
        {
            $user = $row;
            $user['user_id']        = $row['user_id'];
            $user['sex']            = $row['sex'];
            $user['birthday']       = date($row['birthday']);
            $user['pay_points']     = $row['pay_points'];
            $user['rank_points']    = $row['rank_points'];
            $user['user_rank']      = $row['user_rank'];
            $user['user_money']     = $row['user_money'];
            $user['frozen_money']   = $row['frozen_money'];
            $user['credit_line']    = $row['credit_line'];
            $user['formated_user_money'] = price_format($row['user_money']);
            $user['formated_frozen_money'] = price_format($row['frozen_money']);
            $user['parent_id']      = $row['parent_id'];
            $user['parent_username']= $row['parent_username'];
            $user['qq']             = $row['qq'];
            $user['msn']            = $row['msn'];
            $user['office_phone']   = $row['office_phone'];
            $user['home_phone']     = $row['home_phone'];
            $user['mobile_phone']   = $row['mobile_phone'];
        }
        else
        {
            $link[] = array('text' => $_LANG['go_back'], 'href'=>'users.php?act=list');
            sys_msg($_LANG['username_invalid'], 0, $links);
//        $user['sex']            = 0;
//        $user['pay_points']     = 0;
//        $user['rank_points']    = 0;
//        $user['user_money']     = 0;
//        $user['frozen_money']   = 0;
//        $user['credit_line']    = 0;
//        $user['formated_user_money'] = price_format(0);
//        $user['formated_frozen_money'] = price_format(0);
        }

        /* 取出注册扩展字段 */
        $sql = 'SELECT * FROM ' . $ecs->table('reg_fields') . ' WHERE type < 2 AND display = 1 AND id != 6 ORDER BY dis_order, id';
        $extend_info_list = $db->getAll($sql);

        $sql = 'SELECT reg_field_id, content ' .
            'FROM ' . $ecs->table('reg_extend_info') .
            " WHERE user_id = $user[user_id]";
        $extend_info_arr = $db->getAll($sql);

        $temp_arr = array();
        foreach ($extend_info_arr AS $val)
        {
            $temp_arr[$val['reg_field_id']] = $val['content'];
        }

        foreach ($extend_info_list AS $key => $val)
        {
            switch ($val['id'])
            {
                case 1:     $extend_info_list[$key]['content'] = $user['msn']; break;
                case 2:     $extend_info_list[$key]['content'] = $user['qq']; break;
                case 3:     $extend_info_list[$key]['content'] = $user['office_phone']; break;
                case 4:     $extend_info_list[$key]['content'] = $user['home_phone']; break;
                case 5:     $extend_info_list[$key]['content'] = $user['mobile_phone']; break;
                default:    $extend_info_list[$key]['content'] = empty($temp_arr[$val['id']]) ? '' : $temp_arr[$val['id']] ;
            }
        }

        $smarty->assign('extend_info_list', $extend_info_list);

        /* 当前会员推荐信息 */
        $affiliate = unserialize($GLOBALS['_CFG']['affiliate']);
        $smarty->assign('affiliate', $affiliate);

        empty($affiliate) && $affiliate = array();

        if(empty($affiliate['config']['separate_by']))
        {
            //推荐注册分成
            $affdb = array();
            $num = count($affiliate['item']);
            $up_uid = "'$_GET[id]'";
            for ($i = 1 ; $i <=$num ;$i++)
            {
                $count = 0;
                if ($up_uid)
                {
                    $sql = "SELECT user_id FROM " . $ecs->table('users') . " WHERE parent_id IN($up_uid)";
                    $query = $db->query($sql);
                    $up_uid = '';
                    while ($rt = $db->fetch_array($query))
                    {
                        $up_uid .= $up_uid ? ",'$rt[user_id]'" : "'$rt[user_id]'";
                        $count++;
                    }
                }
                $affdb[$i]['num'] = $count;
            }
            if ($affdb[1]['num'] > 0)
            {
                $smarty->assign('affdb', $affdb);
            }
        }
        $smarty->assign('associate_user',             $user);
    }
    /* 模板赋值 */
    $smarty->assign('countries',    get_regions());
    if ($user['country'] > 0)
    {
        $smarty->assign('provinces', get_regions(1, $user['country']));
        if ($user['province'])
        {
            $smarty->assign('cities', get_regions(2, $user['province']));
        }
    }
    $smarty->assign('ur_here',     $_LANG['admin_edit']);
    $smarty->assign('action_link', array('text' => $_LANG['admin_list'], 'href'=>'privilege.php?act=list'));
    $smarty->assign('user',        $user_info);

    $smarty->assign('special_ranks',    get_rank_list(true));
    $smarty->assign('regions',          $regions);
    $smarty->assign('countries',        get_regions());
    $smarty->assign('default_country',  1);
    $smarty->assign('form_act',    'update');
    $smarty->assign('action',      'edit');

    assign_query_info();
    $smarty->display('privilege_info.htm');
}

/*------------------------------------------------------ */
//-- 更新管理员信息
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'update' || $_REQUEST['act'] == 'update_self')
{

    /* 变量初始化 */
    $admin_id    = !empty($_REQUEST['id'])        ? intval($_REQUEST['id'])      : 0;
    $admin_name  = !empty($_REQUEST['user_name']) ? trim($_REQUEST['user_name']) : '';
    $admin_email = !empty($_REQUEST['email'])     ? trim($_REQUEST['email'])     : '';
    $ec_salt=rand(1,9999);
    $password = !empty($_POST['new_password']) ? ", password = '".md5(md5($_POST['new_password']).$ec_salt)."'"    : '';
    if($_POST['token']!=$_CFG['token'])
    {
         sys_msg('update_error', 1);
    }
    if ($_REQUEST['act'] == 'update')
    {
        /* 查看是否有权限编辑其他管理员的信息 */
        if ($_SESSION['admin_id'] != $_REQUEST['id'])
        {
            admin_priv('admin_manage');
        }
        $g_link = 'privilege.php?act=list';
        $nav_list = '';
    }
    else
    {
        $nav_list = !empty($_POST['nav_list'])     ? ", nav_list = '".@join(",", $_POST['nav_list'])."'" : '';
        $admin_id = $_SESSION['admin_id'];
        $g_link = 'privilege.php?act=modif';
    }
    /* 判断管理员是否已经存在 */
    if (!empty($admin_name))
    {
        $is_only = $exc->num('user_name', $admin_name, $admin_id);
        if ($is_only == 1)
        {
            sys_msg(sprintf($_LANG['user_name_exist'], stripslashes($admin_name)), 1);
        }
    }

    /* Email地址是否有重复 */
    if (!empty($admin_email))
    {
        $is_only = $exc->num('email', $admin_email, $admin_id);

        if ($is_only == 1)
        {
            sys_msg(sprintf($_LANG['email_exist'], stripslashes($admin_email)), 1);
        }
    }
    $proxy_user = 0;
    $proxy_default = 0;
    $proxy_region = null;
    $proxy_user_id = null;
    $proxy_vip_user_rank = 0;
    if (!empty($_POST['proxy_user']))
    {
        $proxy_user = $_POST['proxy_user'];
    }
    /* 添加选定的城市和地区 */
    if (isset($_POST['proxy_region']) && is_array($_POST['proxy_region']))
    {
        foreach ($_POST['proxy_region'] AS $key => $val)
        {
            $proxy_region = $val;
        }
    }
    if (!empty($_POST['proxy_user_id']))
    {
        $proxy_user_id = $_POST['proxy_user_id'];
    }
    if (!empty($_POST['proxy_default']))
    {
        $proxy_default = $_POST['proxy_default'];
    }
    if (!empty($_POST['proxy_vip_user_rank']))
    {
        $proxy_vip_user_rank = $_POST['proxy_vip_user_rank'];
    }
    //总代理只能设置一个
    if ($proxy_default == 1) {
        $sql = "SELECT count(*) FROM " . $ecs->table('admin_user') . " WHERE proxy_user =1 and proxy_default=1 and user_id != ".$admin_id;
        $count = $db->getOne($sql);
        if ($count>0) {
            sys_msg('总部代理只能设置1个！', 1);
        }
    }
    //把会员更新为非代理字段
    if ($proxy_user==0 && $proxy_user_id) {
        $other = [];
        $other['is_proxy'] = 0;
        $db->autoExecute($ecs->table('users'), $other, 'UPDATE', "user_id = '$proxy_user_id'");
        $db->autoExecute($ecs->table('admin_user'), ['proxy_default'=>0], 'UPDATE', "user_id = '$admin_id'");

        $db->autoExecute($ecs->table('drp_shop'), ['open'=>0], 'UPDATE', "user_id = '$proxy_user_id'");
    }
    //把会员更新为代理字段
    if ($proxy_user_id) {
        if ($proxy_user==1) {
            $other = [];
            $other['is_proxy'] = 1;
            $db->autoExecute($ecs->table('users'), $other, 'UPDATE', "user_id = '$proxy_user_id'");

            $db->autoExecute($ecs->table('drp_shop'), ['open'=>1], 'UPDATE', "user_id = '$proxy_user_id'");
        }
//        else {
//            $other = [];
//            $other['parent_proxy_user_id'] = 0;
//            $db->autoExecute($ecs->table('users'), $other, 'UPDATE', "user_id = '$proxy_user_id'");
//        }
    }

    //添加用户
    if ($proxy_user==1 && !$proxy_user_id) {
        $username = empty($_POST['associate_username']) ? '' : trim($_POST['associate_username']);
        $password = empty($_POST['associate_password']) ? '' : trim($_POST['associate_password']);
        $email = empty($_POST['associate_email']) ? '' : trim($_POST['associate_email']);
        $sex = empty($_POST['associate_sex']) ? 0 : intval($_POST['associate_sex']);
        $sex = in_array($sex, array(0, 1, 2)) ? $sex : 0;
        $birthday = $_POST['associate_birthdayYear'] . '-' .  $_POST['associate_birthdayMonth'] . '-' . $_POST['associate_birthdayDay'];
        $rank = empty($_POST['associate_user_rank']) ? 0 : intval($_POST['associate_user_rank']);
        $credit_line = empty($_POST['associate_credit_line']) ? 0 : floatval($_POST['associate_credit_line']);

        $nick_name = empty($_POST['associate_nick_name']) ? '' : trim($_POST['associate_nick_name']);
        $identity = empty($_POST['associate_identity']) ? '' : trim($_POST['associate_identity']);
        $address = empty($_POST['associate_address']) ? '' : trim($_POST['associate_address']);
        $country = empty($_POST['associate_country']) ? '' : trim($_POST['associate_country']);
        $province = empty($_POST['associate_province']) ? '' : trim($_POST['associate_province']);
        $city = empty($_POST['associate_city']) ? '' : trim($_POST['associate_city']);
        $districts = empty($_POST['associate_districts']) ? '' : trim($_POST['associate_districts']);
        $mobile_phone = empty($_POST['associate_mobile_phone']) ? '' : trim($_POST['associate_mobile_phone']);

        $users =& init_users();

        if (!$users->add_user($username, $password, $email))
        {
            /* 插入会员数据失败 */
            if ($users->error == ERR_INVALID_USERNAME)
            {
                $msg = $_LANG['username_invalid'];
            }
            elseif ($users->error == ERR_USERNAME_NOT_ALLOW)
            {
                $msg = $_LANG['username_not_allow'];
            }
            elseif ($users->error == ERR_USERNAME_EXISTS)
            {
                $msg = $_LANG['username_exists'];
            }
            elseif ($users->error == ERR_INVALID_EMAIL)
            {
                $msg = $_LANG['email_invalid'];
            }
            elseif ($users->error == ERR_EMAIL_NOT_ALLOW)
            {
                $msg = $_LANG['email_not_allow'];
            }
            elseif ($users->error == ERR_EMAIL_EXISTS)
            {
                $msg = $_LANG['email_exists'];
            }
            else
            {
                //die('Error:'.$users->error_msg());
            }
            sys_msg($msg, 1);
        }

        $sql = "SELECT user_id FROM " . $ecs->table('users') . " WHERE user_name = '".$username."'";
        $row = $db->getRow($sql);
        $proxy_user_id = $row['user_id'];
        $action_list = $row['action_list'];

        /* 注册送积分 */
        if (!empty($GLOBALS['_CFG']['register_points']))
        {
            log_account_change($_SESSION['user_id'], 0, 0, $GLOBALS['_CFG']['register_points'], $GLOBALS['_CFG']['register_points'], $_LANG['register_points']);
        }

        /*把新注册用户的扩展信息插入数据库*/
        $sql = 'SELECT id FROM ' . $ecs->table('reg_fields') . ' WHERE type = 0 AND display = 1 ORDER BY dis_order, id';   //读出所有扩展字段的id
        $fields_arr = $db->getAll($sql);

        $extend_field_str = '';    //生成扩展字段的内容字符串
        $user_id_arr = $users->get_profile_by_name($username);
        foreach ($fields_arr AS $val)
        {
            $extend_field_index = 'extend_field' . $val['id'];
            if(!empty($_POST[$extend_field_index]))
            {
                $temp_field_content = strlen($_POST[$extend_field_index]) > 100 ? mb_substr($_POST[$extend_field_index], 0, 99) : $_POST[$extend_field_index];
                $extend_field_str .= " ('" . $user_id_arr['user_id'] . "', '" . $val['id'] . "', '" . $temp_field_content . "'),";
            }
        }
        $extend_field_str = substr($extend_field_str, 0, -1);

        if ($extend_field_str)      //插入注册扩展数据
        {
            $sql = 'INSERT INTO '. $ecs->table('reg_extend_info') . ' (`user_id`, `reg_field_id`, `content`) VALUES' . $extend_field_str;
            $db->query($sql);
        }

        /* 更新会员的其它信息 */
        $other =  array();
        $other['is_proxy'] = 1;
        $other['credit_line'] = $credit_line;
        $other['user_rank']  = $rank;
        $other['sex']        = $sex;
        $other['birthday']   = $birthday;
        $other['reg_time'] = local_strtotime(local_date('Y-m-d H:i:s'));

        $other['msn'] = isset($_POST['extend_field1']) ? htmlspecialchars(trim($_POST['extend_field1'])) : '';
        $other['qq'] = isset($_POST['extend_field2']) ? htmlspecialchars(trim($_POST['extend_field2'])) : '';
        $other['office_phone'] = isset($_POST['extend_field3']) ? htmlspecialchars(trim($_POST['extend_field3'])) : '';
        $other['home_phone'] = isset($_POST['extend_field4']) ? htmlspecialchars(trim($_POST['extend_field4'])) : '';
        $other['mobile_phone'] = isset($_POST['extend_field5']) ? htmlspecialchars(trim($_POST['extend_field5'])) : '';

        $other['nick_name']   = $nick_name;
        $other['identity']   = $identity;
        $other['address']   = $address;
        $other['country']   = $country;
        $other['province']   = $province;
        $other['city']   = $city;
        $other['districts']   = $districts;
        $other['mobile_phone']   = $mobile_phone;

        $db->autoExecute($ecs->table('users'), $other, 'UPDATE', "user_name = '$username'");

        //开启分销商店
        $enable_drp_shop = [];
        $enable_drp_shop['shop_name'] = $username.'的商店';
        $enable_drp_shop['real_name'] = $username;
        $enable_drp_shop['shop_mobile'] = $other['mobile_phone'] ;
        $enable_drp_shop['user_id'] = $proxy_user_id;
        $enable_drp_shop['create_time'] = gmtime();
        $enable_drp_shop['audit'] = 1;
        $enable_drp_shop['open'] = 1;

        $db->autoExecute($ecs->table('drp_shop'), $enable_drp_shop, 'INSERT');
    }
    //如果要修改密码
    $pwd_modified = false;

    if (!empty($_POST['new_password']))
    {
        /* 查询旧密码并与输入的旧密码比较是否相同 */
        $sql = "SELECT password FROM ".$ecs->table('admin_user')." WHERE user_id = '$admin_id'";
        $old_password = $db->getOne($sql);
		$sql ="SELECT ec_salt FROM ".$ecs->table('admin_user')." WHERE user_id = '$admin_id'";
        $old_ec_salt= $db->getOne($sql);
		if(empty($old_ec_salt))
	    {
			$old_ec_password=md5($_POST['old_password']);
		}
		else
	    {
			$old_ec_password=md5(md5($_POST['old_password']).$old_ec_salt);
		}
        if ($old_password <> $old_ec_password)
        {
           $link[] = array('text' => $_LANG['go_back'], 'href'=>'javascript:history.back(-1)');
           sys_msg($_LANG['pwd_error'], 0, $link);
        }

        /* 比较新密码和确认密码是否相同 */
        if ($_POST['new_password'] <> $_POST['pwd_confirm'])
        {
           $link[] = array('text' => $_LANG['go_back'], 'href'=>'javascript:history.back(-1)');
           sys_msg($_LANG['js_languages']['password_error'], 0, $link);
        }
        else
        {
            $pwd_modified = true;
        }
    }

    $role_id = '';
    $action_list = '';
    if (!empty($_POST['select_role']))
    {
        $sql = "SELECT action_list FROM " . $ecs->table('role') . " WHERE role_id = '".$_POST['select_role']."'";
        $row = $db->getRow($sql);
        $action_list = ', action_list = \''.$row['action_list'].'\'';
        $role_id = ', role_id = '.$_POST['select_role'].' ';
    }
    //更新管理员信息
    if($pwd_modified)
    {
        $sql = "UPDATE " .$ecs->table('admin_user'). " SET ".
               "user_name = '$admin_name', ".
               "email = '$admin_email', ".
               "proxy_user = '$proxy_user', ".
               "proxy_region = '$proxy_region', ".
               "proxy_user_id = '$proxy_user_id', ".
               "proxy_default = '$proxy_default', ".
               "proxy_vip_user_rank = '$proxy_vip_user_rank', ".
               "ec_salt = '$ec_salt' ".
               $action_list.
               $role_id.
               $password.
               $nav_list.
               "WHERE user_id = '$admin_id'";
    }
    else
    {
        $sql = "UPDATE " .$ecs->table('admin_user'). " SET ".
               "user_name = '$admin_name', ".
               "email = '$admin_email', ".
            "proxy_user = '$proxy_user', ".
            "proxy_region = '$proxy_region', ".
            "proxy_user_id = '$proxy_user_id', ".
            "proxy_default = '$proxy_default', ".
            "proxy_vip_user_rank = '$proxy_vip_user_rank' ".

            $action_list.
               $role_id.
               $nav_list.
               "WHERE user_id = '$admin_id'";
    }

   $db->query($sql);
   /* 记录管理员操作 */
   admin_log($_POST['user_name'], 'edit', 'privilege');

   /* 如果修改了密码，则需要将session中该管理员的数据清空 */
   if ($pwd_modified && $_REQUEST['act'] == 'update_self')
   {
       $sess->delete_spec_admin_session($_SESSION['admin_id']);
       $msg = $_LANG['edit_password_succeed'];
   }
   else
   {
       $msg = $_LANG['edit_profile_succeed'];
   }

   /* 提示信息 */
   $link[] = array('text' => strpos($g_link, 'list') ? $_LANG['back_admin_list'] : $_LANG['modif_info'], 'href'=>$g_link);
   sys_msg("$msg<script>parent.document.getElementById('header-frame').contentWindow.document.location.reload();</script>", 0, $link);

}

/*------------------------------------------------------ */
//-- 编辑个人资料
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'modif')
{
    /* 不能编辑demo这个管理员 */
    if ($_SESSION['admin_name'] == 'demo')
    {
       $link[] = array('text' => $_LANG['back_admin_list'], 'href'=>'privilege.php?act=list');
       sys_msg($_LANG['edit_admininfo_cannot'], 0, $link);
    }

    include_once('includes/inc_menu.php');
    include_once('includes/inc_priv.php');

    /* 包含插件菜单语言项 */
    $sql = "SELECT code FROM ".$ecs->table('plugins');
    $rs = $db->query($sql);
    while ($row = $db->FetchRow($rs))
    {
        /* 取得语言项 */
        if (file_exists(ROOT_PATH.'plugins/'.$row['code'].'/languages/common_'.$_CFG['lang'].'.php'))
        {
            include_once(ROOT_PATH.'plugins/'.$row['code'].'/languages/common_'.$_CFG['lang'].'.php');
        }

        /* 插件的菜单项 */
        if (file_exists(ROOT_PATH.'plugins/'.$row['code'].'/languages/inc_menu.php'))
        {
            include_once(ROOT_PATH.'plugins/'.$row['code'].'/languages/inc_menu.php');
        }
    }

    foreach ($modules AS $key => $value)
    {
        ksort($modules[$key]);
    }
    ksort($modules);

    foreach ($modules AS $key => $val)
    {
        if (is_array($val))
        {
            foreach ($val AS $k => $v)
            {
                if (is_array($purview[$k]))
                {
                    $boole = false;
                    foreach ($purview[$k] as $action)
                    {
                         $boole = $boole || admin_priv($action, '', false);
                    }
                    if (!$boole)
                    {
                        unset($modules[$key][$k]);
                    }
                }
                elseif (! admin_priv($purview[$k], '', false))
                {
                    unset($modules[$key][$k]);
                }
            }
        }
    }

    /* 获得当前管理员数据信息 */
    $sql = "SELECT user_id, user_name, email, nav_list ".
           "FROM " .$ecs->table('admin_user'). " WHERE user_id = '".$_SESSION['admin_id']."'";
    $user_info = $db->getRow($sql);

    /* 获取导航条 */
    $nav_arr = (trim($user_info['nav_list']) == '') ? array() : explode(",", $user_info['nav_list']);
    $nav_lst = array();
    foreach ($nav_arr AS $val)
    {
        $arr              = explode('|', $val);
        $nav_lst[$arr[1]] = $arr[0];
    }

    /* 模板赋值 */
    $smarty->assign('lang',        $_LANG);
    $smarty->assign('ur_here',     $_LANG['modif_info']);
    $smarty->assign('action_link', array('text' => $_LANG['admin_list'], 'href'=>'privilege.php?act=list'));
    $smarty->assign('user',        $user_info);
    $smarty->assign('menus',       $modules);
    $smarty->assign('nav_arr',     $nav_lst);

    $smarty->assign('form_act',    'update_self');
    $smarty->assign('action',      'modif');

    /* 显示页面 */
    assign_query_info();
    $smarty->display('privilege_info.htm');
}

/*------------------------------------------------------ */
//-- 为管理员分配权限
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'allot')
{
    include_once(BASE_PATH . 'languages/' .$_CFG['lang']. '/admin/priv_action.php');

    admin_priv('allot_priv');
    if ($_SESSION['admin_id'] == $_GET['id'])
    {
        admin_priv('all');
    }

    /* 获得该管理员的权限 */
    $priv_str = $db->getOne("SELECT action_list FROM " .$ecs->table('admin_user'). " WHERE user_id = '$_GET[id]'");

    /* 如果被编辑的管理员拥有了all这个权限，将不能编辑 */
    if ($priv_str == 'all')
    {
       $link[] = array('text' => $_LANG['back_admin_list'], 'href'=>'privilege.php?act=list');
       sys_msg($_LANG['edit_admininfo_cannot'], 0, $link);
    }

    /* 获取权限的分组数据 */
    $sql_query = "SELECT action_id, parent_id, action_code,relevance FROM " .$ecs->table('admin_action').
                 " WHERE parent_id = 0";
    $res = $db->query($sql_query);
    while ($rows = $db->FetchRow($res))
    {
        $priv_arr[$rows['action_id']] = $rows;
    }

    /* 按权限组查询底级的权限名称 */
    $sql = "SELECT action_id, parent_id, action_code,relevance FROM " .$ecs->table('admin_action').
           " WHERE parent_id " .db_create_in(array_keys($priv_arr));
    $result = $db->query($sql);
    while ($priv = $db->FetchRow($result))
    {
        $priv_arr[$priv["parent_id"]]["priv"][$priv["action_code"]] = $priv;
    }

    // 将同一组的权限使用 "," 连接起来，供JS全选
    foreach ($priv_arr AS $action_id => $action_group)
    {
        $priv_arr[$action_id]['priv_list'] = join(',', @array_keys($action_group['priv']));

        foreach ($action_group['priv'] AS $key => $val)
        {
            $priv_arr[$action_id]['priv'][$key]['cando'] = (strpos($priv_str, $val['action_code']) !== false || $priv_str == 'all') ? 1 : 0;
        }
    }

    /* 赋值 */
    $smarty->assign('lang',        $_LANG);
    $smarty->assign('ur_here',     $_LANG['allot_priv'] . ' [ '. $_GET['user'] . ' ] ');
    $smarty->assign('action_link', array('href'=>'privilege.php?act=list', 'text' => $_LANG['admin_list']));
    $smarty->assign('priv_arr',    $priv_arr);
    $smarty->assign('form_act',    'update_allot');
    $smarty->assign('user_id',     $_GET['id']);

    /* 显示页面 */
    assign_query_info();
    $smarty->display('privilege_allot.htm');
}

/*------------------------------------------------------ */
//-- 更新管理员的权限
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'update_allot')
{
    admin_priv('admin_manage');
    if($_POST['token']!=$_CFG['token'])
    {
         sys_msg('update_allot_error', 1);
    }
    /* 取得当前管理员用户名 */
    $admin_name = $db->getOne("SELECT user_name FROM " .$ecs->table('admin_user'). " WHERE user_id = '$_POST[id]'");

    /* 更新管理员的权限 */
    $act_list = @join(",", $_POST['action_code']);
    $sql = "UPDATE " .$ecs->table('admin_user'). " SET action_list = '$act_list', role_id = '' ".
           "WHERE user_id = '$_POST[id]'";

    $db->query($sql);
    /* 动态更新管理员的SESSION */
    if ($_SESSION["admin_id"] == $_POST['id'])
    {
        $_SESSION["action_list"] = $act_list;
    }

    /* 记录管理员操作 */
    admin_log(addslashes($admin_name), 'edit', 'privilege');

    /* 提示信息 */
    $link[] = array('text' => $_LANG['back_admin_list'], 'href'=>'privilege.php?act=list');
    sys_msg($_LANG['edit'] . "&nbsp;" . $admin_name . "&nbsp;" . $_LANG['action_succeed'], 0, $link);

}

/*------------------------------------------------------ */
//-- 删除一个管理员
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    check_authz_json('admin_drop');

    $id = intval($_GET['id']);

    /* 获得管理员用户名 */
    $admin_name = $db->getOne('SELECT user_name FROM '.$ecs->table('admin_user')." WHERE user_id='$id'");

    /* demo这个管理员不允许删除 */
    if ($admin_name == 'demo')
    {
        make_json_error($_LANG['edit_remove_cannot']);
    }

    /* ID为1的不允许删除 */
    if ($id == 1)
    {
        make_json_error($_LANG['remove_cannot']);
    }

    /* 管理员不能删除自己 */
    if ($id == $_SESSION['admin_id'])
    {
        make_json_error($_LANG['remove_self_cannot']);
    }

    if ($exc->drop($id))
    {
        $sess->delete_spec_admin_session($id); // 删除session中该管理员的记录

        admin_log(addslashes($admin_name), 'remove', 'privilege');
        clear_cache_files();
    }

    $url = 'privilege.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}

/* 获取管理员列表 */
function get_admin_userlist()
{
    $list = array();
    $sql  = 'SELECT au.*, r.region_name AS proxy_region_name '.
            'FROM ' .$GLOBALS['ecs']->table('admin_user'). ' AS au '.
        ' LEFT JOIN '.$GLOBALS['ecs']->table('region').' AS r ON au.proxy_region = r.region_id '.
        ' ORDER BY user_id DESC';
    $list = $GLOBALS['db']->getAll($sql);

    foreach ($list AS $key=>$val)
    {
        $list[$key]['add_time']     = local_date($GLOBALS['_CFG']['time_format'], $val['add_time']);
        $list[$key]['last_login']   = local_date($GLOBALS['_CFG']['time_format'], $val['last_login']);
    }

    return $list;
}

/* 清除购物车中过期的数据 */
function clear_cart()
{
    /* 取得有效的session */
    $sql = "SELECT DISTINCT session_id " .
            "FROM " . $GLOBALS['ecs']->table('cart') . " AS c, " .
                $GLOBALS['ecs']->table('sessions') . " AS s " .
            "WHERE c.session_id = s.sesskey ";
    $valid_sess = $GLOBALS['db']->getCol($sql);

    // 删除cart中无效的数据
    $sql = "DELETE FROM " . $GLOBALS['ecs']->table('cart') .
            " WHERE session_id NOT " . db_create_in($valid_sess);
    $GLOBALS['db']->query($sql);
}

/* 获取角色列表 */
function get_role_list()
{
    $list = array();
    $sql  = 'SELECT role_id, role_name, action_list '.
            'FROM ' .$GLOBALS['ecs']->table('role');
    $list = $GLOBALS['db']->getAll($sql);
    return $list;
}

?>
