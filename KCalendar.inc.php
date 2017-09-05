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

		$dt_index = new DateTime();

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

				for ( $month_i = 0; $month_i < $num_months_to_show; $month_i++ ) {
					$dt_index->setDate( $dt_start->format( 'Y' ), $dt_start->format( 'n' ), 1 ); // 1st of the month
					$dt_index->add( new DateInterval( 'P' . $month_i . 'M' ) ); // Jump to the month we are up to

					$dt_month = clone $dt_index; // So we know what month we are up to

					// Determine weekday of first day of month:
					$weekday_first = $dt_index->format( 'w' );

					if ( $params[ 'header_callback' ] ) {
						echo $params[ 'header_callback' ](
							array(
								'dt_index' => $dt_index,
								'dt_now' => $dt_now,
							)
						);
					} else {

						if (
							FALSE
								!==
							(
								$header_format = ( // Yes, assign!
									( $dt_index->format( 'Y' ) == $dt_now->format( 'Y' ) ) // If we are rendering the current year
										?
									$header_format_current_year
										:
									$header_format_other_year
								)
							)
						) {
?>
				<h1>
						<?= $dt_index->format( $header_format ) ?>
				</h1>
<?php
						}
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

					$d = 0; // What day of the month we are generating (0=we are before the beginning of the month, or after the end of the month)
					$is_still_in_current_month = FALSE; // assume
					do {
?>
				<ul class="days">
<?php
						for ( $i=0; $i < 7; $i++ ) {
							if (
								0 == $d // Haven't hit the first day of the month yet...
							) {
								if (
									$i == $dt_index->format( 'w' ) // But we're up to the weekday that is the first of the month...
								) {
									$d = 1; // ...so that means we're on day 1 of the month!
									$is_still_in_current_month = TRUE;
								}
							} else {
								$d++; // We are in the month, so jump to the next day:
								$dt_index->setDate( $dt_index->format( 'Y' ), $dt_index->format( 'n' ), $d );

								$is_still_in_current_month = (
									$dt_index->format( 'm' ) == $dt_month->format( 'm' )
								);
							}

							// Determine what class(es) apply to this cell:
							$classes = array();
							if (
								$d // We've started to display this month
									&&
								( $dt_index->format( 'Ymd' ) == $dt_now->format( 'Ymd' ) ) // We're on today's date
							) {
								$classes[] = 'today';
							}

							if ( $dt_index < $dt_now ) {
								$classes[] = 'past';
							}

							if ( $dt_index > $dt_now ) {
								$classes[] = 'future';
							}

							$contents = array(); // assume
							$content = ''; // assume

							if (
								$d // We've started to display this month
									&&
								$is_still_in_current_month
							) {
								if (
									is_array( $params[ 'events' ] )
										&&
									array_key_exists( $dt_index->format( 'Ymd' ), $params[ 'events' ] )
								) {
									$values = $params[ 'events' ][ $dt_index->format( 'Ymd' ) ];

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
							<span class="day"><?= $dt_index->format( 'D' ) ?>,</span> <span class="month"><?= $dt_index->format( 'M' ) ?></span>
<?php
								if (
									$d // We've started to display this month
										&&
									$is_still_in_current_month
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
						$dt_index->setDate( $dt_index->format( 'Y' ), $dt_index->format( 'n' ), 1 + $d );

						$is_still_in_current_month = (
							$dt_index->format( 'm' ) == $dt_month->format( 'm' )
						);

					} while (
						$is_still_in_current_month
					);

				} // For each month
?>
			</div><!--class="kcalendar"-->

<?php
	}
}

// EOF (KCalendar.inc.php)
