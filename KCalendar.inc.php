<?php

class KCalendar {
	const NUM_MONTHS = 1; // Number of months to display by default

	/*
		params:
			when => date to start rendering (only month and year are important; day, hours, minutes and seconds are ignored); either a DateTime object, or a string recognizable by strtotime(); default=now
			now => current date; either a DateTime object, or a string recognizable by strtotime(); default=now
			num_months => 1..n (how many months to output)
			events => array of events: key=YYYYMMDD, value=string (or array of strings) describing that day's events (HTML OK), e.g.: array( '20161225' => 'Merry Christmas!', '20170101' => array( 'Happy New Year!', 'Public Domain Day' ))
			'stylesheet' => url-of-style-sheet.css (or TRUE for kcalendar.css) // Render CSS tag: <link type="text/css" rel="stylesheet" href="url-of-style-sheet.css" />
			header_format_current_year => Format to display header when rendering a month in the current year (default='F', i.e. full textual representation of a month, e.g. 'January')
			header_format_other_year => Format to display header when rendering a month in a year other than the current year (default='F, Y', e.g. 'January, 2017')

			For both header_format_???_year options: value should be either a format string which will get passed to date(), or FALSE (indicating no header should be displayed)
			
	*/
	function render( $params = array() ) {
		// Determine $dt_start, i.e. the date to start rendering (assumed to be the first day of that month):
		if (
			array_key_exists( 'when', $params )
				&&
			is_a( $params[ 'when' ], 'DateTime' )
		) {
			$dt_start = $params[ 'when' ];
		} else {
			try {
				$dt_start = new DateTime(
					array_key_exists( 'when', $params ) ? $params[ 'when' ] : 'now'
				);
			} catch ( Exception $e ) {
				$dt_start = new DateTime();
			}
		}

		// Determine $dt_now, i.e. today's date (so we know which cell to give a class name of "today", "past", or "future"):
		if (
			array_key_exists( 'now', $params )
				&&
			is_a( $params[ 'now' ], 'DateTime' )
		) {
			$dt_now = $params[ 'now' ];
		} else {
			try {
				$dt_now = new DateTime(
					array_key_exists( 'now', $params ) ? $params[ 'now' ] : 'now'
				);
			} catch ( Exception $e ) {
				$dt_now = new DateTime();
			}
		}

		$header_format_current_year = array_key_exists( 'header_format_current_year', $params ) ? $params[ 'header_format_current_year' ] : 'F';
		$header_format_other_year = array_key_exists( 'header_format_other_year', $params ) ? $params[ 'header_format_other_year' ] : 'F, Y';

		if ( $params[ 'stylesheet' ] ) {
?>
			<link rel="stylesheet" href="<?= ( TRUE === $params[ 'stylesheet' ] ? 'kcalendar.css' : $params[ 'stylesheet' ] ) ?>" type="text/css" media="screen" />
<?php
		}
?>
			<div class="kcalendar">
<?php
				$num_months_to_show = ( array_key_exists( 'num_months', $params ) ? $params[ 'num_months' ] : self::NUM_MONTHS );

				for ( $month_i = 0; $month_i <= $num_months_to_show; $month_i++ ) {
					$month = $month_i + $dt_start->format( 'n' );
					$year = $dt_start->format( 'Y' );
					if ( $month > 12 ) {
						$month -= 12;
						$year++;
					}

					$date_first_of_month = mktime( 0, 0, 0, $month, 1, $year );

					// Determine weekday of first day of month:
					$weekday_first = date( 'w', $date_first_of_month );

					if ( $num_months_to_show == $month_i ) {
						break;
					}

					$is_rendering_current_year = ( $year == $dt_now->format( 'Y' ) );

					if (
						FALSE
							!==
						(
							$is_rendering_current_year
								?
							$header_format_current_year
								:
							$header_format_other_year
						)
					) {
?>
				<h1>
						<?= date( ( ( $year == $dt_now->format( 'Y' ) ) ? $header_format_current_year : $header_format_other_year ), $date_first_of_month ) ?>
				</h1>
<?php
					}
?>

				<ul class="weekdays">
<?php
	foreach (
		array(
			'Sun',
			'Mon',
			'Tue',
			'Wed',
			'Thu',
			'Fri',
			'Sat',
		) as $weekday
	) {
?>
  					<li>
  						<?= $weekday ?>
  					</li>
<?php
	}
?>
				</ul>
<?php

					// As long as we're in the current month:
					$d = 0; // Day
					do {
?>
				<ul class="days">
<?php
						for ( $i=0; $i < 7; $i++ ) {
							if (
								0 == $d // Haven't hit the first day yet
							) {
								if (
									$i == $weekday_first // We're up to the weekday that is the first of the month
								) {
									$d = 1; // We're on day 1 of the month!
								}
							} else {
								$d++;
							}

							$date_current = mktime( 0, 0, 0, $month, $d, $year );
							$timestamp_current = date( 'Ymd', $date_current );

							$classes = array();
							
							if (
								$d // We've started to display this month
									&&
								( $timestamp_current == $dt_now->format( 'Ymd' ) ) // We're on today's date
							) {
								$classes[] = 'today';
							}

							if ( $timestamp_current < $dt_now->format( 'Ymd' ) ) {
								$classes[] = 'past';
							}

							if ( $timestamp_current > $dt_now->format( 'Ymd' ) ) {
								$classes[] = 'future';
							}

							$contents = array(); // assume
							$content = ''; // assume

							if (
								$d // We've started to display this month
									&&
								( date( 'n', $date_current ) == $month ) // We haven't gone to the next month
							) {
								$weekday = date( 'w', $date_current );

								if (
									is_array( $params[ 'events' ] )
										&&
									array_key_exists( $timestamp_current, $params[ 'events' ] )
								) {
									$values = $params[ 'events' ][ $timestamp_current ];

									if ( ! is_array( $values ) ) {
										$values = array( $values );
									}

									foreach ( $values as $value ) {
										$contents[] = $value; // E.g. "Happy Halloween!"
									}
								}

								if ( sizeof( $contents ) ) {
									$content = '<div class="event">' . join( '</div><div class="event">', $contents ) . '</div>';
								} else {
									$classes[] = 'free';
								}
							} else {
								$classes[] = 'blank';
							}

							$cell = NULL; // assume
							if ( $params[ 'cell_callback' ] ) {
								$cell = $params[ 'cell_callback' ](
									array(
										'date_current' => $date_current,
									)
								);
							}
							
							if ( NULL === $cell ) {
?>
				    <li class="<?= join( ' ', $classes ) ?>">
						<div class="date">
							<span class="day"><?= date( 'D', $date_current )?>,</span> <span class="month"><?= date( 'M', $date_current ) ?></span>
<?php
								if (
									$d // We've started to display this month
										&&
									( date( 'n', $date_current ) == $month ) // We haven't gone to the next month
								) {
?>
									<?= $d ?>
<?php
								}
?>

						</div>

						<div class="events">
							<?= $content ?>
						</div>
					</li>
<?php
							} else if ( FALSE === $cell) {
								break;
							} else {
								echo $cell;
							}
						}
?>
				</ul>
<?php

					} while ( $month == date( 'n', $date_current + 60*60*24 ) );

				}
?>
			</div><!--class="kcalendar"-->

<?php
	}
}

// EOF (KCalendar.inc.php)
