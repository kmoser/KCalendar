KCalendar
=========

KCalendar is a PHP class which can be used to generate a responsive HTML calendar. It comes with a basic style sheet which you can change, or you can add an additional style sheet to override these defaults.

###What does the letter "K" mean in the name "KCalendar"?###

It stands for my name: Kim

###How do I use KCalendar?###

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

Of course, you will need to output HTML to pull in the style sheet as well:

```html
<link type="text/css" rel="stylesheet" href="stylesheet.css"/>
```

###Advanced Options###
