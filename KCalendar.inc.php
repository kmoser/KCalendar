<?php

class KCalendar {
	const NUM_MONTHS = 1; // Number of months to display by default

	/*
		params:
			date => date to start rendering (only month and year are important; day, hours, minutes and seconds are ignored); either a DateTime object, or a string recognizable by strtotime(); default=now
			num_months => 1..n (how many months to output)
	*/
	function render( $params = array() ) {
		if (
			array_key_exists( 'when', $params )
				&&
			is_a( $params[ 'when' ], 'DateTime' )
		) {
			$dt = $params[ 'when' ];
		} else {
			try {
				$dt = new DateTime(
					array_key_exists( 'when', $params ) ? $params[ 'when' ] : 'now'
				);
			} catch ( Exception $e ) {
				$dt = new DateTime();
			}
		}
?>

			<link rel="stylesheet" href="calendar.css" type="text/css" media="screen" />

			<div class="calendar">
<?php
				$num_months_to_show = ( array_key_exists( 'num_months', $params ) ? $params[ 'num_months' ] : self::NUM_MONTHS );

				for ( $month_i = 0; $month_i <= $num_months_to_show; $month_i++ ) {
					$month = $month_i + $dt->format( 'n' );
					$year = $dt->format( 'Y' );
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

?>
				<h1>
						<?= date( ( ( $year == (int) date( 'Y' ) ) ? 'F' : 'F, Y' ), $date_first_of_month ) ?>
				</h1>

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

							$classes = array(
								'calendar-day'
							);
							
							if (
								$d // We've started to display this month
									&&
								( $timestamp_current == date( 'Ymd' ) ) // We're on today's date
							) {
								$classes[] = 'today';
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

										if ( is_numeric( $value ) || is_bool( $value ) ) {
											if ( $value ) {
												$contents[] = $texts_yes[ $weekday ];
											} else {
												$contents[] = '<span class="no">' . $texts_no[ $weekday ] . '</span>';
											}
										} else {
											$contents[] = "<strong>" . ( $value ) . "</strong>"; // E.g. "Happy Hallween!"
										}

									}
								} else {
									$contents[] = $texts_yes[ $weekday ];
								}

								if ( sizeof( $contents ) ) {
									$content = '<p>' . join( '</p><p>', $contents ) . '</p>';
								}
							} else {
								$classes[] = 'out_of_range';
							}

							if ( $timestamp_current < date( 'Ymd' ) ) {
								$classes[] = 'past';
							}

?>
				    <li class="<?= join( ' ', $classes ) ?>">
						<div class="date day_cell">
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
								if ( $timestamp_current < date( 'Ymd' ) ) {
?>
	<span class="x<?= ( strlen( $d ) > 1 ) ? ' x2' : '' ?>"></span>
<?php
								}
?>




<?php
							}
?>

						</div>

						<div class="description">
							<?= $content ?>
						</div>
					</li>
<?php
						}
?>
				</ul>
<?php

					} while( $month == date( 'n', $date_current + 60*60*24 ) );

				}
?>
			</div>

<?php
	}
}

// EOF (KCalendar.inc.php)
