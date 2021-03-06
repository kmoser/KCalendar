KCalendar
=========

9/5/2017 - v1.2

---------------------

**KCalendar** is a PHP class which generates a responsive HTML/CSS calendar. It comes with a basic style sheet which you can change, or you can add an additional style sheet to override the defaults.

### What does it look like? ### 

See http://www.kmoser.com/kcalendar/ for an example of a functional KCalendar script.

### How do I use KCalendar? ### 

1. Include the KCalendar.inc.php file:

	```php
	require_once 'KCalendar.inc.php';
	```

2. Instantiate a KCalendar object:

	```php
	$c = new KCalendar();
	```

3. Render your calendar:

	```php
	$c->render();
	```

You will also need to include the style sheet:

```html
<link type="text/css" rel="stylesheet" href="kcalendar.css" />
```

### Advanced Options ### 

When rendering a calendar, you can specify options in an array of key/value pairs, e.g.:

```php
$c->render(
	array(
		'num_months' => 3,
		'header_format_current_year' => 'F, Y',
	)
);
```

Valid options are:

 - `when`=> *Date*

	Date to start rendering (only month and year are important; day, hours, minutes and seconds are ignored); must be either a PHP `DateTime` object, or a string recognizable by `strtotime()`; default=now
 - `now`=> *Date*

	Today's date; must be either a PHP `DateTime` object or a string recognizable by `strtotime()`; default=now
 - `num_months` => 1..n

	How many months to output; default=1
 - `header_format_current_year` => *string*

	Format string which will be passed to `date()` for rendering header of months in the current year; if FALSE, header will not render at all; default='F', meaning the full textual description of the current month, e.g. 'January'
 - `header_format_other_year` => *string*

	Format string which will be passed to `date()` for rendering header of months in a year **other than** the current year; if FALSE, header will not render at all; default='F, Y', e.g. 'January, 2017'
 - `stylesheet` => TRUE or FALSE or *URL*

	TRUE: generate default style sheet link: `<link rel="stylesheet" href="kcalendar.css" type="text/css" media="screen" />`

	FALSE: no style sheet (default)

	URL:  generate style sheet with the given URL, e.g.:  `<link rel="stylesheet" href="*URL*" type="text/css" media="screen" />`
 - `events` => *array*

	Keys must be in *YYYYMMDD* format and values must be strings (or an array of strings) describing that day's events (HTML OK), e.g.:

	```php
	array(
		'20160921' => 'International Day of Peace',
		'20161031' => '<em>Happy Halloween!</em>',
		'20170101' => array(
			'New Years Day',
			'Converse with a <a href="http://chatbot.kmoser.com">chatbot</a>'
		),
	)
	```
 - `cell_callback` => *function*

	Function must accept an array of parameters. Parameters passed in:
		`date_current` => Unix timestamp containing the date of the current cell being rendered.
	Function must return a chunk of HTML (presumably `<li>...</li>`) which will be rendered for that cell, or NULL indicating the default cell should be rendered normally

 - `header_callback` => *function*

	Function must accept an array of parameters. Parameters passed in:
		`dt_index` => PHP DateTime object set to the first day of the current month being rendered.
		`dt_now` => PHP DateTime object set the date to begin rendering.
	By comparing `dt_index` with `dt_now`, the callback function can determine whether it is rendering the current month, a previous month, or the next month.
	Whatever the callback function returns (presumably a string of text/HTML) will be rendered just before the beginning of the month.

### Styling ### 

The calendar cells are generated as list items (`<li>`), with one or more class names describing what is in the cell:

- **```today```**: this day
- **```past```**: a day in the past
- **```future```**: a day in the future
- **```free```**: there are no events today
- **```blank```**: this cell is not a valid day

For example, ```<li class="future free">``` indicates a cell that lies in the future, and contains no events.

You can either edit the default style sheet (```kcalendar.css```), add your own style sheet to override the defaults, or use only your own style sheet to define all the styles.

### To Do ### 

Features I plan to add:

- Ability to specify a callback function to render the weekday column headers (Sun-Sat)
- Ability to have the week start on any day (not just Sunday)

### Done ### 

- 9/5/2017: Ability to specify a callback function to render the calendar header

### Comments? Feedback? ### 

- Submit your pull requests on this project's [GitHub page](https://github.com/kmoser/KCalendar/)
- Contact me on GitHub or by email; see my [website](http://www.kmoser.com/) for contact info
