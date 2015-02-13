<?php
	echo $this->element('DataCenter.jquery_ui');
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