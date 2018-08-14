<!-- $Id: user_account_check.htm 14216 2008-03-10 02:27:21Z testyang $ -->
<?php echo $this->fetch('pageheader.htm'); ?>
<div class="main-div">
<form method="post" action="user_account.php" name="theForm" onsubmit="return validate();">
<table border="0" width="100%">
  <tr>
    <td colspan="2"><strong><?php echo $this->_var['lang']['surplus_info']; ?>：</strong><hr /></td>
  </tr>
  <tr>
    <td colspan="2">
    <strong><?php echo $this->_var['lang']['user_id']; ?>：</strong><?php echo $this->_var['user_name']; ?> &nbsp;&nbsp;<strong><?php echo $this->_var['lang']['surplus_amount']; ?>：</strong><?php echo $this->_var['surplus']['amount']; ?> &nbsp;&nbsp;<strong><?php echo $this->_var['lang']['add_date']; ?>：</strong><?php echo $this->_var['surplus']['add_time']; ?>
    &nbsp;&nbsp;<strong><?php echo $this->_var['lang']['process_type']; ?>：</strong><?php echo $this->_var['process_type']; ?>
    <?php if ($this->_var['surplus']['pay_method']): ?>&nbsp;&nbsp;<strong><?php echo $this->_var['lang']['pay_method']; ?>：</strong><?php echo $this->_var['surplus']['payment']; ?><?php endif; ?>
    </td>
  </tr>
  <tr>
    <td colspan="2"><strong><?php echo $this->_var['lang']['surplus_desc']; ?>：</strong><?php echo $this->_var['surplus']['user_note']; ?><hr /></td>
  </tr>
  <tr>
    <th width="15%" valign="middle" align="right"><?php echo $this->_var['lang']['surplus_notic']; ?>：</th>
    <td width="85%">
      <textarea name="admin_note" cols="55" rows="5"><?php echo $this->_var['surplus']['admin_note']; ?></textarea>
    </td>
  </tr>
  <tr>
    <th width="15%" valign="middle" align="right"><?php echo $this->_var['lang']['status']; ?>：</th>
    <td>
      <input type="radio" name="is_paid" value="0" checked="true" /><?php echo $this->_var['lang']['unconfirm']; ?>
      <input type="radio" name="is_paid" value="1" /><?php echo $this->_var['lang']['confirm']; ?>
    </td>
  </tr>
  <!--<tr>-->
    <!--<th>付款方式</th>-->
    <!--<td><p>-->
      <!--<label><input type="radio" name="pay_code" value="wxpay" />微信付款</label><br>-->
    <!--</p></td>-->
  <!--</tr>-->
  <tr>
    <td>&nbsp;</td>
    <td>
      <input type="hidden" name="act" value="action" />
      <input type="hidden" name="id" value="<?php echo $this->_var['id']; ?>" />
      <input name="submit" type="submit" value="<?php echo $this->_var['lang']['button_submit']; ?>" class="button" />
      <input type="reset" value="<?php echo $this->_var['lang']['button_reset']; ?>" class="button" />
    </td>
  </tr>
</table>
</form>
</div>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,validator.js')); ?>

<script language="JavaScript">
<!--
document.forms['theForm'].elements['admin_note'].focus();

/**
 * 检查表单输入的数据
 */
function validate()
{
    validator = new Validator("theForm");
    validator.required("admin_note",  deposit_notic_empty);
    return validator.passed();
}

onload = function()
{
    // 开始检查订单
    startCheckOrder();
}
//-->
</script>

<?php echo $this->fetch('pagefooter.htm'); ?>