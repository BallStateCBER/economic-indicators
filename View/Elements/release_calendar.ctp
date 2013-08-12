<?php
	$this->Html->css('/jquery_ui/css/no-theme/jquery-ui-1.10.3.custom.min.css', null, array('inline' => false));
	$this->Html->script('/jquery_ui/js/jquery-ui-1.10.3.custom.js', array('inline' => false));
	$this->Js->buffer("
		var release_calendar = ".$this->Js->object($calendar).";
		setupReleaseCalendar(release_calendar);
	");
?>

<div id="release_calendar"></div>
<div class="release_calendar_footnote">
	<?php if (count($calendar) == 1): // Meaning only 'max_date' => null and no releases ?>
		No upcoming data releases scheduled. Please check back later for updates. 
	<?php else: ?> 
		Roll over highlighted date to see scheduled data releases.
	<?php endif; ?>
</div>