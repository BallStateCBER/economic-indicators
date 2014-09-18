<?php
	$this->extend('DataCenter.default');
	$this->assign('sidebar', $this->element('sidebar'));
	$this->Html->script('script', array('inline' => false));
	echo $this->element('flash_messages', array(), array('plugin' => 'DataCenter'));
?>

<?php $this->start('subsite_title'); ?>
	<h1 id="subsite_title" class="max_width">
		<a href="/">
			<img src="/img/Indicators.jpg" alt="Economic Indicators" />
		</a>
	</h1>
<?php $this->end(); ?>

<?php $this->start('footer_about'); ?>
	<h3>
		About This Site
	</h3>
	<p>
		Economic Indicators is powered by <a href="http://www.datazoa.com/about/about.asp?uid=dzadmin">dataZoa</a>,
		a data delivery tool offering more than 200 million data series.  DataZoa is a product of
		<a href="http://lmtech.com">Leading Market Technologies</a>.
	</p>
	<p>
		The <a href="http://www.cberdata.org/">CBER Data Center</a> is a product of the Center for Business
		and Economic Research at Ball State University.  CBER's mission is to conduct relevant and timely
		public policy research on a wide range of economic issues affecting the state and nation.
		<a href="http://www.bsu.edu/cber">Learn more</a>.
	</p>
<?php $this->end(); ?>

<div id="content">
	<?php echo $this->fetch('content'); ?>
</div>