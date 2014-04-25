<?php
	require_once('include/ICal.php');
	
	$calendrier = new ICal('https://www.google.com/calendar/ical/m68voqdd74bdenhl20fq766a8s%40group.calendar.google.com/public/basic.ics');
	if ($calendrier->event_count > 0)
	{
		$evenements = array_filter
		(
			$calendrier->events(),
			function($e)
			{
				return ($e['DTEND'] > date('Ymd'));
			}
		);
		usort
		(
			$evenements,
			function($e1, $e2)
			{
				if ($e1['DTSTART'] == $e2['DTSTART'])
					return 0;
				if ($e1['DTSTART'] > $e2['DTSTART'])
					return 1;
				return -1;
			}
		);
		foreach($evenements as $evenement)
		{
			echo '<section class="evenement">';
			$date = $calendrier->iCalDateToUnixTimestamp($evenement['DTSTART']);
			echo '<div class="date"><span class="day">'.date('d', $date).'</span><br>'.date('M', $date).'</div>';
			echo '<div class="lieu">'.htmlentities($evenement['LOCATION'], ENT_COMPAT, 'UTF-8').'</div>';
			echo '<div class="titre">'.htmlentities($evenement['SUMMARY'], ENT_COMPAT, 'UTF-8').'</div>';
			echo '<div class="description">'.htmlentities($evenement['DESCRIPTION'], ENT_COMPAT, 'UTF-8').'</div>';
			echo '</section>';
		}
	}
?>