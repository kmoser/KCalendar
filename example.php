<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>KCalendar Example</title>
	<meta name="description" content="Example of KCalendar usage" />
	<meta name="author" content="Kim Moser, http://www.kmoser.com/" />

	<style>
	code {
		display: block;
		background-color: #000000;
		padding: 0px 10px 0px 10px;
		color: #00ff00;
		white-space: pre-wrap;
	}
	</style>

</head>
<body>

<p>KCalendar is a PHP class which generates a responsive HTML/CSS calendar. It comes with a basic style sheet which you can change, or you can add an additional style sheet to override the defaults.</p>

<a href="https://github.com/kmoser/KCalendar">View this project on GitHub</a></p>

<hr />

<p style="text-align: center;">
	Examples: <a href="#simple">Simple</a>
		|
	<a href="#complex">Complex</a>
</p>

<hr />

<p>Resize your browser to see how the page looks with different screen sizes.</p>

<a name="simple"></a>

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
<?= htmlentities( $code ) ?>
	</code>

<?php

	eval( $code );
?>

<a name="complex"></a>

<h2>Complex Example</h2>

<?php
	ob_start();
?>
require_once 'KCalendar.inc.php';

$c = new KCalendar();

// Date to start rendering (only month and year are relevant, but DateTime() requires a complete date with month, day and year):
$dt_start = new DateTime( 'December 1, 2016' ); // Change to whatever you want

// Current date to show on calendar, i.e. "today":
$dt_now = new DateTime( 'December 25, 2016' ); // Change to whatever you want

$events = array(
	sprintf( '%04d%02d%02d', $dt_now->format( 'Y' ), $dt_now->format( 'n' ), $dt_now->format( 'd' ) ) => 'Merry Christmas!',
	sprintf( '%04d%02d%02d', $dt_start->format( 'Y' ), $dt_start->format( 'n' ), $dt_start->format( 't' )-2 ) => 'Today is a great day to download <a href="http://www.github.com/kmoser/KCalendar/">KCalendar</a>!', // A day with a single event
	sprintf( '%04d%02d%02d', $dt_start->format( 'Y' ), $dt_start->format( 'n' ), $dt_start->format( 't' )-1 ) => array(
		'Event 2 is <em>radical</em>',
		'Event 3 is <em>wild</em>',
	), // A day with two events
	sprintf( '%04d%02d%02d', $dt_start->format( 'Y' ), $dt_start->format( 'n' ), $dt_start->format( 't' )-0 ) => array(
		'Event 4 is <em>awesome</em>',
		'Event 5 is <em>off the hook</em>',
		'Event 6 is <em>da bomb</em>',
	), // A day with three events
	'20170101' => 'Happy New Year!',

	/* Usually you will just do something like this:
		'20161218' => 'This is an event',
		'20161219' => array( 'First event of the day', 'Second event of the day' ),
		'20161220' => array( 'First event of the day', 'Second event of the day', 'Third event of the day' ),
	*/
);

$c->render(
	array(
		'when' => $dt_start,
		'now' => $dt_now,
		'events' => $events,
		'num_months' => 3,
		'stylesheet' => TRUE,
		'header_format_other_year' => 'M, \'y',
		'cell_callback' => function( $params ) {
			if ( '20170102' == date( 'Ymd', $params[ 'date_current' ] ) ) {
				return '<li class="foo">Custom cell for January 2nd, 2017</li>'; // Generate a custom cell for 1/2/2017
			} else {
				return NULL; // So the standard cell will be generated
			}
		},
	)
);

<?php
	$code = ob_get_clean();

?>
	<code>
<?= htmlentities( $code ) ?>
	</code>

<?php

	eval( $code );
?>

</body>
</html>
