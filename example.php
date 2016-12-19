<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>KCalendar Example</title>
	<meta name="description" content="Example of KCalendar usage" />
	<meta name="author" content="Kim Moser" />
	<link type="text/css" rel="stylesheet" href="stylesheet.css" />
</head>
<body>

<style>
code {
	display: block;
	background-color: #000000;
	padding: 0px 10px 0px 10px;
	color: #00ff00;
	white-space: pre-wrap;
}
</style>

<h2>Simple Example</h2>

<?php
	ob_start();
?>
require_once 'KCalendar.inc.php';

$c = new KCalendar();

$c->render();
<?php
	$code = ob_get_clean();

?>
	<code>
<?= $code ?>
	</code>

<?php

	eval( $code );
?>

<h2>Complex Example</h2>

<?php
	ob_start();
?>
require_once 'KCalendar.inc.php';
$c = new KCalendar();

$dt = new DateTime( 'today' ); // Change to whatever you want

// Keys are in YYYYMMDD format; values are either strings (HTML OK) or arrays of strings
$events = array(
	sprintf( '%04d%02d%02d', $dt->format( 'Y' ), $dt->format( 'n' ), $dt->format( 't' )-3 ) => 'Event 1 is <em>excellent</em>', // A day with a single event
	sprintf( '%04d%02d%02d', $dt->format( 'Y' ), $dt->format( 'n' ), $dt->format( 't' )-2 ) => array( 'Event 2 is <em>radical</em>', 'Event 3 is <em>wild</em>' ), // A day with two events
	sprintf( '%04d%02d%02d', $dt->format( 'Y' ), $dt->format( 'n' ), $dt->format( 't' )-1 ) => array( 'Event 4 is <em>awesome</em>', 'Event 5 is <em>off the hook</em>', 'Event 6 is <em>da bomb</em>' ), // A day with three events

	/* Usually you will just do something like this:
		'20161218' => 'This is an event',
		'20161219' => array( 'First event of the day', 'Second event of the day' ),
		'20161220' => array( 'First event of the day', 'Second event of the day', 'Third event of the day' ),
	*/
);

$c->render(
	array(
		'when' => $dt,
		'events' => $events,
		'num_months' => 4,
	)
);

<?php
	$code = ob_get_clean();

?>
	<code>
<?= $code ?>
	</code>

<?php

	eval( $code );
?>

</body>
</html>
