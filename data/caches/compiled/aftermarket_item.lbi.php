<div id="J_ModId_5">
  <div id="J_Conversation" class="m_msg conversation"> 
    <?php $_from = $this->_var['action_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'action_0_28017900_1533888056');if (count($_from)):
    foreach ($_from AS $this->_var['action_0_28017900_1533888056']):
?>
    <div class="msg-item    msg-owner-mine    ">
      <div class="p-info">
        <div class="time"><?php echo $this->_var['action_0_28017900_1533888056']['action_time']; ?></div>
        <div class="other"></div>
      </div>
      <div class="msg-info"> <s class="arrow"></s>
        <div class="title">
          <div class="itm"><?php echo $this->_var['action_0_28017900_1533888056']['return_status']; ?></div>
          <div class="itm"><?php echo $this->_var['action_0_28017900_1533888056']['refund_status']; ?></div>
        </div>
        <div class="cutoff-line"></div>
        <div class="desc">
          <div class="desc-i"> <?php echo $this->_var['action_0_28017900_1533888056']['action_info']; ?> </div>
        </div>
      </div>
    </div>
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
  </div>
</div>

<div id="J_ModId_6" style="display:none">
  <div class="m_msg status J_Status show">
    <div style="left: 0px; opacity: 1;" class="msg-item msg-owner-xiaoer J_MsgOwnerHers msg-type-2">
      <div class="p-info"> 
        <div class="time"><?php echo $this->_var['return']['action_time']; ?></div>
        <div class="other"></div>
      </div>
      <div class="msg-info"> <s class="arrow"></s>
        <div class="title">待卖家处理</div>
        <div class="cutoff-line"></div>
        <div class="desc">
          <div class="desc-i">如卖家同意，请按照给出的退货地址退货</div>
          <div class="desc-i">如卖家拒绝，您可修改申请后再次发起，卖家会重新处理</div>
         <!-- <div class="desc-i">如卖家在<span class="J_Countdown countdown" data-config='{"leftTime":"431999","type":"countdown"}'><i class="iconfont icon-time"></i><span class="text">剩</span><s class="hand hand-d"><s class="digital digital-0 ">0</s><s class="digital digital-4 ">4</s></s>天<s class="hand hand-h"><s class="digital digital-2 ">2</s><s class="digital digital-3 ">3</s></s>时<s class="hand hand-m"><s class="digital digital-5 ">5</s><s class="digital digital-9 ">9</s></s>分</span>内未处理，申请将自动达成，请按系统给出的地址退货</div>-->
          <div class="cutoff-line cutoff-line2"></div>
          <div class="additional"> 编号: <?php echo $this->_var['return']['service_sn']; ?> </div>
        </div>
      </div>
    </div>
  </div>
</div>


<div id="J_ModId_15" style="display:none">
  <div class="m_msg status J_Status show">
    <div style="left: 0px; opacity: 1;" class="msg-item msg-owner-xiaoer J_MsgOwnerHers msg-type-2">
      <div class="p-info"> 
        <!--<div class="time">04-16 11:22</div>-->
        <div class="other"></div>
      </div>
      <div class="msg-info"> <s class="arrow"></s>
        <div class="title">审核通过</div>
        <div class="cutoff-line"></div>
        <div class="desc">
          <div class="desc-i">您的申请已通过审核</div>
          <div class="cutoff-line cutoff-line2"></div>
          
          <div class="additional"><a href="<?php echo Url('user/to_return',array('ret_id'=>$this->_var['return']['ret_id']));?>">去退货</a></div>
          
        </div>
      </div>
    </div>
  </div>
</div>

<div id="J_ModId_15" style="display:none">
  <div class="m_msg status J_Status show">
    <div style="left: 0px; opacity: 1;" class="msg-item msg-owner-xiaoer J_MsgOwnerHers msg-type-2">
      <div class="p-info">
        <!--<div class="owner"><b></b>淘宝网系统</div>-->
        <div class="time">04-16 11:22</div>
        <div class="other"></div>
      </div>
      <div class="msg-info"> <s class="arrow"></s>
        <div class="title">退款关闭</div>
        <div class="cutoff-line"></div>
        <div class="desc">
          <div class="desc-i">因您撤消退款申请，退款已关闭</div>
          <div class="cutoff-line cutoff-line2"></div>
          <div class="additional"> 编号: 39220159728720 </div>
        </div>
      </div>
    </div>
  </div>
</div>
