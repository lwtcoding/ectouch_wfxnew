<?php

/**
 * ECSHOP 管理中心权限管理模块语言文件
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

/* 字段信息 */
$_LANG['user_id'] = '编号';
$_LANG['user_name'] = '用户名';
$_LANG['email'] = 'Email地址';
$_LANG['password'] = '密  码';
$_LANG['join_time'] = '加入时间';
$_LANG['last_time'] = '最后登录时间';
$_LANG['last_ip'] = '最后访问的IP';
$_LANG['action_list'] = '操作权限';
$_LANG['nav_list'] = '导航条';
$_LANG['language'] = '使用的语言';
$_LANG['proxy_user'] = '区域代理';
$_LANG['proxy_region'] = '代理城市';
$_LANG['nickname'] = '姓名';
$_LANG['identity'] = '身份证';
$_LANG['province'] = '省份';
$_LANG['country'] = '国家';
$_LANG['city'] = '城市';
$_LANG['address'] = '地址';

$_LANG['allot_priv'] = '分派权限';
$_LANG['allot_list'] = '权限列表';
$_LANG['go_allot_priv'] = '设置管理员权限';

$_LANG['view_log'] = '查看日志';

$_LANG['back_home'] = '返回首页';
$_LANG['forget_pwd'] = '您忘记了密码吗?';
$_LANG['get_new_pwd'] = '找回管理员密码';
$_LANG['pwd_confirm'] = '确认密码';
$_LANG['new_password'] = '新密码';
$_LANG['old_password'] = '旧密码';
$_LANG['agency'] = '负责的办事处';
$_LANG['self_nav'] = '个人导航';
$_LANG['all_menus'] = '所有菜单';
$_LANG['add_nav'] = '增加';
$_LANG['remove_nav'] = '移除';
$_LANG['move_up'] = '上移';
$_LANG['move_down'] = '下移';
$_LANG['continue_add'] = '继续添加管理员';
$_LANG['back_list'] = '返回管理员列表';
$_LANG['select_role'] = '角色选择';
$_LANG['select_please'] = '请选择...';

$_LANG['admin_edit'] = '编辑管理员';
$_LANG['edit_pwd'] = '修改密码';

$_LANG['back_admin_list'] = '返回管理员列表';

/* 提示信息 */
$_LANG['js_languages']['user_name_empty'] = '管理员用户名不能为空!';
$_LANG['js_languages']['password_invaild'] = '密码必须同时包含字母及数字且长度不能小于6!';
$_LANG['js_languages']['email_empty'] = 'Email地址不能为空!';
$_LANG['js_languages']['email_error'] = 'Email地址格式不正确!';
$_LANG['js_languages']['password_error'] = '两次输入的密码不一致!';
$_LANG['js_languages']['captcha_empty'] = '您没有输入验证码!';
$_LANG['action_succeed'] = '操作成功!';
$_LANG['edit_profile_succeed'] = '您已经成功的修改了个人帐号信息!';
$_LANG['edit_password_succeed'] = '您已经成功的修改了密码，因此您必须重新登录!';
$_LANG['user_name_exist'] = '该管理员已经存在!';
$_LANG['email_exist'] = 'Email地址已经存在!';
$_LANG['captcha_error'] = '您输入的验证码不正确。';
$_LANG['login_faild'] = '您输入的帐号信息不正确。';
$_LANG['login_disable'] = '您输入的帐号暂时不可用。';
$_LANG['user_name_drop'] = '已被成功删除!';
$_LANG['pwd_error'] = '输入的旧密码错误!';
$_LANG['old_password_empty'] = '如果要修改密码,必须先输入你的旧密码!';
$_LANG['edit_admininfo_error'] = '只能编辑自己的个人资料!';
$_LANG['edit_admininfo_cannot'] = '您不能对此管理员的权限进行任何操作!';
$_LANG['edit_remove_cannot'] = '您不能删除demo这个管理员!';
$_LANG['remove_self_cannot'] = '您不能删除自己!';
$_LANG['remove_cannot'] = '此管理员您不能进行删除操作!';

$_LANG['modif_info'] = '编辑个人资料';
$_LANG['edit_navi'] = '设置个人导航';

/* 帮助信息 */
$_LANG['password_notic'] = '如果要修改密码,请先填写旧密码,如留空,密码保持不变';
$_LANG['email_notic'] = '输入管理员的Email邮箱,必须为Email格式';
$_LANG['confirm_notic'] = '输入管理员的确认密码,两次输入必须一致';

/* 登录表单 */
$_LANG['label_username'] = '管理员用户名';
$_LANG['label_password'] = '管理员密码';
$_LANG['label_captcha'] = '验证码';
$_LANG['click_for_another'] = '看不清？点击更换另一个验证码。';
$_LANG['signin_now'] = '进入管理中心';
$_LANG['remember'] = '请保存我这次的登录信息。';
/* JS 语言项 */
$_LANG['js_languages']['no_username'] = '没有输入用户名。';
$_LANG['js_languages']['invalid_email'] = '没有输入邮件地址或者输入了一个无效的邮件地址。';
$_LANG['js_languages']['no_password'] = '没有输入密码。';
$_LANG['js_languages']['less_password'] = '输入的密码不能少于六位。';
$_LANG['js_languages']['passwd_balnk'] = '密码中不能包含空格';
$_LANG['js_languages']['no_confirm_password'] = '没有输入确认密码。';
$_LANG['js_languages']['password_not_same'] = '输入的密码和确认密码不一致。';
$_LANG['js_languages']['invalid_pay_points'] = '消费积分数不是一个整数。';
$_LANG['js_languages']['invalid_rank_points'] = '等级积分数不是一个整数。';
$_LANG['js_languages']['password_len_err'] = '新密码和确认密码的长度不能小于6';
$_LANG['js_languages']['region_exists'] = '选定的地区已经存在。';

$_LANG['sex'][0] = '保密';
$_LANG['sex'][1] = '男';
$_LANG['sex'][2] = '女';


$_LANG['proxy_user_option'][0] = '否';
$_LANG['proxy_user_option'][1] = '是';
$_LANG['associate_user_label'] = '关联会员';


/* 表单相关语言项 */
$_LANG['password'] = '登录密码';
$_LANG['username'] = '用户名';
$_LANG['confirm_password'] = '确认密码';
$_LANG['newpass'] = '新密码';
$_LANG['question'] = '密码提示问题';
$_LANG['answer'] = '密码提示问题答案';
$_LANG['gender'] = '性别';
$_LANG['birthday'] = '出生日期';
$_LANG['sex'][0] = '保密';
$_LANG['sex'][1] = '男';
$_LANG['sex'][2] = '女';
$_LANG['pay_points'] = '消费积分';
$_LANG['rank_points'] = '等级积分';
$_LANG['user_money'] = '可用资金';
$_LANG['frozen_money'] = '冻结资金';
$_LANG['credit_line'] = '信用额度';
$_LANG['user_rank'] = '会员等级';
$_LANG['not_special_rank'] = '非特殊等级';
$_LANG['proxy_default_label'] = '总部代理';

$_LANG['proxy_default_options'][0] = '否';
$_LANG['proxy_default_options'][1] = '是';
$_LANG['view_detail_account'] = '查看明细';
$_LANG['parent_user'] = '推荐人';
$_LANG['parent_remove'] = '脱离推荐关系';
$_LANG['affiliate_user'] = '推荐会员';
$_LANG['show_affiliate_users'] = '查看推荐详细名单';
$_LANG['show_affiliate_orders'] = '查看推荐订单详情';
$_LANG['affiliate_lever'] = '等级';
$_LANG['affiliate_num'] = '人数';
$_LANG['page_note'] = '此列表显示用户推荐的全部会员信息，';
$_LANG['how_many_user'] = '个会员。';
$_LANG['back_note'] = '返回会员编辑页面';
$_LANG['affiliate_level'] = '推荐等级';
$_LANG['button_excel'] = '导出ECxel';

$_LANG['msn'] = 'MSN';
$_LANG['qq'] = 'QQ';
$_LANG['home_phone'] = '家庭电话';
$_LANG['office_phone'] = '办公电话';
$_LANG['mobile_phone'] = '手机';

$_LANG['notice_pay_points'] = '消费积分是一种站内货币，允许用户在购物时支付一定比例的积分。';
$_LANG['notice_rank_points'] = '等级积分是一种累计的积分，系统根据该积分来判定用户的会员等级。';
$_LANG['notice_user_money'] = '用户在站内预留下的金额';

/* 提示信息 */
$_LANG['username_exists'] = '已经存在一个相同的用户名。';
$_LANG['email_exists'] = '该邮件地址已经存在。';
$_LANG['edit_user_failed'] = '修改会员资料失败。';
$_LANG['invalid_email'] = '输入了非法的邮件地址。';
$_LANG['update_success'] = '编辑用户信息已经成功。';
$_LANG['still_accounts'] = '该会员有余额或欠款\n';
$_LANG['remove_confirm'] = '您确定要删除该会员账号吗？';
$_LANG['list_still_accounts'] = '选中的会员账户中仍有余额或欠款\n';
$_LANG['list_remove_confirm'] = '您确定要删除所有选中的会员账号吗？';
$_LANG['remove_order_confirm'] = '该会员账号已经有订单存在，删除该会员账号的同时将清除订单数据。<br />您确定要删除吗？';
$_LANG['remove_order'] = '是，我确定要删除会员账号及其订单数据';
$_LANG['remove_cancel'] = '不，我不想删除该会员账号了。';
$_LANG['remove_success'] = '会员账号 %s 已经删除成功。';
$_LANG['add_success'] = '会员账号 %s 已经添加成功。';
$_LANG['batch_remove_success'] = '已经成功删除了 %d 个会员账号。';
$_LANG['no_select_user'] = '您现在没有需要删除的会员！';
$_LANG['register_points'] = '注册送积分';
$_LANG['username_not_allow'] = '用户名不允许注册';
$_LANG['username_invalid'] = '无效的用户名';
$_LANG['email_invalid'] = '无效的email地址';
$_LANG['email_not_allow'] = '邮件不允许';
$_LANG['proxy_vip_level'] = '加盟商会员级别';

/* JS 语言项 */
$_LANG['js_languages']['no_username'] = '没有输入用户名。';
$_LANG['js_languages']['no_nickname'] = '没有输入用户姓名。';
$_LANG['js_languages']['no_identity'] = '没有输入身份证。';
$_LANG['js_languages']['no_mobile_phone'] = '没有输入手机号。';
$_LANG['js_languages']['no_address'] = '没有输入详细地址。';
$_LANG['js_languages']['no_country'] = '没有选择国家。';
$_LANG['js_languages']['no_province'] = '没有选择省份。';
$_LANG['js_languages']['no_city'] = '没有选择城市。';
$_LANG['js_languages']['invalid_email'] = '没有输入邮件地址或者输入了一个无效的邮件地址。';
$_LANG['js_languages']['no_password'] = '没有输入密码。';
$_LANG['js_languages']['less_password'] = '输入的密码不能少于六位。';
$_LANG['js_languages']['passwd_balnk'] = '密码中不能包含空格';
$_LANG['js_languages']['no_confirm_password'] = '没有输入确认密码。';
$_LANG['js_languages']['password_not_same'] = '输入的密码和确认密码不一致。';
$_LANG['js_languages']['invalid_pay_points'] = '消费积分数不是一个整数。';
$_LANG['js_languages']['invalid_rank_points'] = '等级积分数不是一个整数。';
$_LANG['js_languages']['password_len_err'] = '新密码和确认密码的长度不能小于6';

?>