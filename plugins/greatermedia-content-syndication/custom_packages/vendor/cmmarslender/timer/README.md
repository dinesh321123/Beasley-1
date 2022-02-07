# Timer [![Build Status](https://travis-ci.org/cmmarslender/timer.svg?branch=master)](https://travis-ci.org/cmmarslender/timer)
PHP Timer w/ Average Time per Item

## Installation
Install with composer `composer require cmmarslender/timer`

## Usage

#### Simple - Tracking elapsed time

```php
<?php

use Cmmarslender\Timer as Timer;

// Get a new Timer object
$timer = new Timer();

// Start the timer
$timer->start();

// Get elapsed time
$timer->elapsed_time();

// Stop timer
$timer->stop();

// Reset timer
$timer->reset();

```

#### Advanced - Tracking time, averages, and percent complete

This example demonstrates how to use the timer to not only track time, but also average time per item and estimated remaining time, when you know how many items you have to process. In this example, we have 100 items to process. The `->tick()` method is called each time an item is processed, to let the timer know you are on to the next item.

```php
<?php

use Cmmarslender\Timer as Timer;

// Get a new Timer object
$timer = new Timer();

// Tell the timer we have 100 total items
$timer->set_total_items( 100 );

// Start the timer
$timer->start();

// Imaginary loop that processes you items
foreach ( $items as $item ) {
	// Do something to the item
	$timer->tick();
	
	// Get average time per item
	$average = $timer->average();
	
	// Get estimated time remaining (based on average)
	$remaining = $timer->remaining_time();
	
	// Get percent complete
	$percent = $timer->percent_complete();
}
```
