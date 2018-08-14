<?php echo $this->fetch('library/new_page_header.lbi'); ?>
<header class="dis-box header-menu b-color color-whie"><a class="" href="javascript:history.go(-1)"><i class="iconfont icon-jiantou"></i></a>
	<h3 class="box-flex">服务选择</h3>
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
<div style="top: 0px; min-height: 567px; opacity: 1;" id="page">
	<div id="wrapper">
		<div id="scroller">
			<div class="panel panel-entrance m-top10">
				<div class="panel-body">
					<div id="J_ModId_1">
						<ul id="J_Entrance" class="entrance show">
							<?php $_from = $this->_var['service_type']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'service');$this->_foreach['service_type'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['service_type']['total'] > 0):
    foreach ($_from AS $this->_var['service']):
        $this->_foreach['service_type']['iteration']++;
?>
							<li class="arrow J_ListItem" data-value="3">
								<a href="<?php echo $this->_var['service']['url']; ?>"> 
                   <?php if (($this->_foreach['service_type']['iteration'] - 1) == 0): ?>
                  <span class="iconfont icon-7tianwuliyoutuihuo n-user-lanbg"></span>
                  <?php elseif (($this->_foreach['service_type']['iteration'] - 1) == 1): ?>
                  <span class="iconfont icon-p-return n-user-huanbg"></span>
                  <?php elseif (($this->_foreach['service_type']['iteration'] - 1) == 2): ?>
                  <span class="iconfont icon-p-return n-user-huanbg"></span>
                  <?php elseif (($this->_foreach['service_type']['iteration'] - 1) == 3): ?>
                  <span class="iconfont icon-p-return n-user-huanbg"></span>
                  <?php endif; ?>
                <h5 class="title"><?php echo $this->_var['service']['service_name']; ?></h5>
                <div class="desc"><?php echo $this->_var['service']['service_desc']; ?></div>
                </a> </li>
							<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="mask"></div>
<div id="popup" class="bottom-side"></div>
<div id="load-layer">
	<div class="load-icon hidden">
		<div class="load-outer">
			<div class="load-inner"></div>
		</div>
		<div class="load-msg hidden">正在加载</div>
	</div>
</div>
<div id="error-layer">
	<div class="iconfont icon-wifi"></div>
	<p class="error-msg">资源加载失败，请稍候重试</p>
	<button class="retry-btn" onclick="javascript:location.reload();" type="button">重新加载</button>
</div>
<?php echo $this->fetch('library/new_search.lbi'); ?>
<?php echo $this->fetch('library/new_page_footer.lbi'); ?>
</body>