<?php echo $this->fetch('library/new_page_header.lbi'); ?>
<header class="dis-box header-menu b-color color-whie"><a class="" href="javascript:history.go(-1)"><i class="iconfont icon-jiantou"></i></a>
	<h3 class="box-flex"><?php echo $this->_var['title']; ?></h3>
	<a class="" href="javascript:;"><i class="iconfont icon-pailie j-nav-box"></i></a>
</header>
<div class="j-nav-content">
	<ul class="dis-box new-footer-box">
		<li class="box-flex">
			<a href="<?php echo url('index/index');?>" class="nav-cont"><i class="nav-box"><img src="__TPL__/statics/img/home.png"></i><span>首页</span></a>
		</li>
		<li class="box-flex">
			<a href="<?php echo url('category/top_all');?>" class="nav-cont"><i class="nav-box"><img src="__TPL__/statics/img/cate.png"></i><span>分类</span></a>
			<li class="box-flex"><a href="javascript:;" class="nav-cont j-search-input"><i class="nav-box"><img src="__TPL__/statics/img/search.png"></i><span>搜索</span></a></li>
			<li class="box-flex"><a href="<?php echo url('flow/cart');?>" class="nav-cont"><i class="nav-box"><img src="__TPL__/statics/img/flow.png"></i><span>购物车</span></a></li>
			<li class="box-flex"><a href="<?php echo url('user/index');?>" class="nav-cont"><i class="nav-box"><img src="__TPL__/statics/img/user.png"></i><span>用户中心</span></a></li>
	</ul>
</div>

<body class="iscroll">
	<div id="page">
		<div class="panel-body" id="panel-body">
			<div class="m_msg J_Status show">
				<div class="msg-item msg-lst">
					<div class="msg-info m-top10"> <s class="arrow"></s>
						<div class="dis-box">
							<a  href="<?php echo url('goods/index', array('id'=>$this->_var['goods']['goods_id']));?>" target="_blank"><div class="n-detail-box"><img src="<?php echo $this->_var['goods']['goods_thumb']; ?>"></div></a>
							<dl class="box-flex n-aft-detail-box">
								<dt>
                        <h4><a href="<?php echo url('goods/index', array('id'=>$this->_var['goods']['goods_id']));?>"><?php echo $this->_var['goods']['goods_name']; ?><?php echo $this->_var['goods']['goods_attr']; ?></a></h4>
                      </dt>
								<dd class="dd-price"><b><?php echo $this->_var['lang']['shop_price']; ?><?php echo $this->_var['goods']['goods_price']; ?> <?php echo $this->_var['lang']['number_to']; ?>：<?php echo $this->_var['goods']['goods_number']; ?></b></dd>
								<dd class="dd-price"><b class="pull-left"><?php echo $this->_var['lang']['ws_subtotal']; ?>：<span class="ect-colory"><?php echo $this->_var['goods']['subtotal']; ?></span></b></dd>
							</dl>
						</div>
						<div class="cutoff-line"></div>
						<div class="desc">
							<div class="desc-i"><?php echo $this->_var['lang']['aftermarket_type']; ?>：<span><?php echo $this->_var['return']['service_name']; ?></span></div>
							<div class="desc-i"><?php echo $this->_var['lang']['aftermarket_status']; ?>：<b><?php echo $this->_var['return']['return_status']; ?></b></div>
							<div class="desc-i"><?php echo $this->_var['lang']['refund_status']; ?>：<b><?php echo $this->_var['return']['refund_status']; ?></b></div>
							<div class="desc-i"><?php echo $this->_var['lang']['aftermarket_number']; ?>：<span><?php echo $this->_var['return']['service_sn']; ?></span> </div>
							<div class="desc-i"><?php echo $this->_var['lang']['order_addtime']; ?>：<span><?php echo $this->_var['return']['formated_add_time']; ?></span></div>
							<div class="cutoff-line cutoff-line2"></div>
							<div class="additional"> <?php echo $this->_var['lang']['return_num']; ?>：<b><?php echo $this->_var['goods']['back_num']; ?></b>: <?php echo $this->_var['lang']['should_return']; ?>：<b class="ect-colory"><?php echo $this->_var['return']['formated_should_return']; ?></b> </div>
						</div>
					</div>
				</div>
				<?php if ($this->_var['return']['cert']): ?>
                			<div class="m_msg conversation">
                				<div class="msg-item  msg-itm-img">
                					<div class="p-info">
                						<div class="title"><?php echo $this->_var['lang']['cert_img']; ?></div>
                						<div class="p-img">
                							<ul>
                								<?php $_from = $this->_var['return']['cert']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'cert');if (count($_from)):
    foreach ($_from AS $this->_var['cert']):
?>
                								<li> <img src="<?php echo $this->_var['cert']['img_url']; ?>"></li>
                								<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                							</ul>
                						</div>
                					</div>
                				</div>
                			</div>
                			<?php endif; ?>
			</div>
			<div class="m_msg J_Status show">
				<div class="msg-item msg-lst" style="left: 0px; opacity: 1;">

					<div class="filter-btn dis-box" style="position:relative">
						<?php if ($this->_var['return']['handler']): ?>
						<div type="button" class="btn-cart n-but-ls box-flex n-iphone5-top1 j-goods-attr j-show-div"><?php echo $this->_var['return']['handler']; ?></div>
						<?php endif; ?>
						<a href="<?php echo url('user/aftermarket_done', array('rec_id'=>$this->_var['return']['rec_id']));?>" type="button" class="btn-submit box-flex n-iphone5-top1 j-goods-attr j-show-div"><?php echo $this->_var['lang']['aftermarket_action']; ?></a>
					</div>

				</div>
			</div>

			<?php if ($this->_var['return']['back_shipping_name']): ?>
			<div class="m_msg J_Status show">
				<div style="left: 0px; opacity: 1;" class="msg-item msg-lst">
					<div class="msg-info">
						<div class="title">买家寄出</div>
						<div class="desc">
							<div class="desc-i"><?php echo $this->_var['lang']['shipping_method']; ?>：<span><?php echo $this->_var['return']['back_shipping_name']; ?></span></div>
							<div class="desc-i"><?php echo $this->_var['lang']['shipping_number']; ?>：<span><?php echo $this->_var['return']['back_invoice_no']; ?></span></div>
						</div>
						<div class="cutoff-line cutoff-line2"></div>
						<div class="title">商家寄出</div>
						<div class="desc">
							<div class="desc-i"><?php echo $this->_var['lang']['shipping_method']; ?>：<span><?php echo $this->_var['return']['out_shipping_name']; ?></span></div>
							<div class="desc-i"><?php echo $this->_var['lang']['shipping_number']; ?>：<span><?php echo $this->_var['return']['out_invoice_no']; ?></span></div>
						</div>
					</div>
				</div>
			</div>
			<?php endif; ?>


		</div>

	</div>
	<div id="mask"></div>
	<div id="popup" class="bottom-side"></div>
	<?php echo $this->fetch('library/new_search.lbi'); ?>
	<?php echo $this->fetch('library/new_page_footer.lbi'); ?>
	<script type="text/javascript" src="__PUBLIC__/js/jquery.min.js"></script>
	<script type="text/javascript" src="__PUBLIC__/js/jquery.json.js"></script>
</body>