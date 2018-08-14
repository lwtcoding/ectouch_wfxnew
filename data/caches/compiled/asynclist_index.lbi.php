
<?php if ($this->_var['goods']): ?>
<?php if ($this->_var['goods']['iteration'] % 2 == 0): ?>
<li class="fr">
	
<?php else: ?>
<li class="fl">
<?php endif; ?>
<div class="product-div">
  <a href="<?php echo $this->_var['goods']['url']; ?>">
  		<img class="lazy" src="<?php echo $this->_var['goods']['goods_img']; ?>" alt="<?php echo $this->_var['goods']['name']; ?>">
  </a>
  <a href="<?php echo $this->_var['goods']['url']; ?>"><h4><?php echo $this->_var['goods']['name']; ?></h4></a>
  <p><span><?php if ($this->_var['goods']['promote_price']): ?><?php echo $this->_var['goods']['promote_price']; ?><?php else: ?><?php echo $this->_var['goods']['shop_price']; ?><?php endif; ?></span></p>
  </div>
</li>
<?php endif; ?> 
 
<script type="text/javascript">
      $(function() {
      			/*商品详情相册切换*/
				var swiper = new Swiper('.goods-photo', {
			paginationClickable: true,
			pagination : '.swiper-pagination',
			paginationType : 'fraction'
		});
      	 
      });
</script>