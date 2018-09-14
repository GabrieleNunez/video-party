<div class="show-for-medium" id="header">
	<div class="row maximum navigation-menu" >
		<div class="small-12 medium-12 large-3 columns">
			<div class="logo">
				<a href="/" title="" target="_self"><img src="/images/logo.png" alt="" /></a>
			</div>
		</div>
		<div class="small-12 medium-12 large-9 columns">
			<ul class="nav-menu">
				<?php echo \Library\View::make('/templates/navigation.php')->with(array('maintab' => $maintab)); ?>
			</ul>
			<div class="clear"></div>
		</div>
	</div>
</div>