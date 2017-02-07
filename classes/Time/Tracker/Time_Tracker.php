<?php /*

------------------------------------------
------ About the Time_Tracker Class ------
------------------------------------------

The Time_Tracker is designed to keep track of several units of time simultaneously and keep them synchronized when you change a value.

For example, lets say you set a Time_Tracker to the timestamp of 28800 using $tracker->setTimestamp(28800). The Time_Tracker would automatically synchronize all of its units to January 1st, 1970 at 12 AM.
	
	echo $tracker->year;		// prints "1970"
	echo $tracker->month;		// prints "1"
	
Now, if you set the month to "March" using $tracker->setMonth("March"), it would synchronize the timestamp to the value that corresponds with March 1st, 1970 at 12 AM.
	
	echo $tracker->timestamp;	// prints "5126400"
	
	
------------------------------------------
------ Example of using this class ------
------------------------------------------

	$tracker = new Time_Tracker($timestamp);
	
	// Assign the time units to the tracker 
	$tracker
		->setYear(2014)
		->setMonth("February")
		->setDay(15)
		->setHour("10 pm")
		->setMinute(22)
		->setSecond(30)
		;
	
	// Print the tracker data
	echo $tracker->timestamp;		// prints the tracker's timestamp
	echo $tracker->month;			// prints the tracker's month
	echo $tracker->dayOfWeek;		// prints the tracker's day of the week
	...
	
	
-------------------------------
------ Methods Available ------
-------------------------------

$tracker	= new Time_Tracker()						// Build tracker from current time
$tracker	= new Time_Tracker($timestamp)			// Build tracker from a desired timestamp
$tracker	= new Time_Tracker($year, [$month], [$day], [$hour], ...)		// Build tracker from time units

$tracker
	->setYear($year)			// Sets the "Year" time unit
	->setMonth($month)			// Sets the "Month" time unit (can use "March", "nov", etc)
	->setDay($day)				// Sets the "Day" time unit
	->setHour($hour)			// Sets the "Hour" time unit (can use "10 pm", "5 am" etc.)
	->setMinute($minute)		// Sets the "Minute" time unit
	->setSecond($second)		// Sets the "Second" time unit

$tracker->setMultiple($year, $month, $day, $hour, $minute, $second);		// Sets several units at once

$tracker->setTimestamp($timestamp);		// Updates the timestamp for the tracker (and syncs all time units to it)
 
$tracker->syncUnits();		// Syncs time units to the timestamp (runs automatically when you update the timestamp)
$tracker->syncTimestamp();	// Syncs timestmap (runs automatically when you change a time unit)

*/

class Time_Tracker {
	
	
/****** Class Variables ******/
	public $year = 0;			// <int>
	public $month = 0;			// <int>
	public $day = 0;			// <int>
	public $hour = 0;			// <int>
	public $minute = 0;			// <int>
	public $second = 0;			// <int>
	
	public $dayOfWeek = 0;		// <int>
	
	public $timestamp = 0;		// <int> The current timestamp
	
	
/****** Class Constructor ******/
	public function __construct(
	)				// RETURNS <void>
	
	// $tracker = new Time_Tracker();
	// $tracker = new Time_Tracker($timestamp);
	// $tracker = new Time_Tracker($year, [$month], [$day], [$hour], [$minute], [$second]);
	{
		// Prepare Values
		$args = func_get_args();
		$argLen = count($args);
		$timestamp = false;
		
		// Check what constructor type is being used
		if($argLen == 1)
		{
			// If the argument provided was a timestamp
			// e.g. new Time_Tracker($timestamp);
			if($args[0] >= date("Y") + 10000 or true)
			{
				$timestamp = $args[0];
			}
			
			// If the argument provided was a year
			// e.g. new Time_Tracker($year);
			else
			{
				var_dump($args[0]);
				$timestamp = mktime(0, 0, 0, 1, 1, $args[0]);
			}
		}
		
		// USED CONSTRUCTOR: new Time_Tracker($year, [$month], [$day], [$hour], [$minute], [$second]);
		else if($argLen > 1)
		{
			// Prepare Default Time Units
			if(!isset($args[2])) { $args[2] = 1; }		// day
			if(!isset($args[3])) { $args[3] = 0; }		// hour
			if(!isset($args[4])) { $args[4] = 0; }		// minute
			if(!isset($args[5])) { $args[5] = 0; }		// second
			
			// Convert the time units into a timestamp
			$timestamp = mktime(Time_Convert::hourToNumber($args[3]), $args[4], $args[5], Time_Convert::monthToNumber($args[1]), $args[2], $args[0]);
		}
		
		// USED CONSTRUCTOR: new Time_Tracker()
		if($timestamp === false)
		{
			$timestamp = time();
		}
		
		$this->timestamp = $timestamp;
		
		$this->syncUnits();
	}
	
	
/****** Set the year to track ******/
	public function setYear
	(
		$year		// <int> The year to track.
	)				// RETURNS <this>
	
	// $tracker->setYear($year);
	{
		$this->year = $year + 0;
		$this->syncTimestamp();
		return $this;
	}
	
	
/****** Set the month to track ******/
	public function setMonth
	(
		$month		// <mixed> The month to track (e.g. "April", 7, 10, etc).
	)				// RETURNS <this>
	
	// $tracker->setMonth($month);
	{
		$this->month = Time_Convert::monthToNumber($month);
		$this->syncTimestamp();
		return $this;
	}
	
	
/****** Set the day to track ******/
	public function setDay
	(
		$day		// <int> The day to track.
	)				// RETURNS <this>
	
	// $tracker->setDay($day);
	{
		$this->day = min(max($day, 1), 31);
		$this->syncTimestamp();
		return $this;
	}
	
	
/****** Set the hour to track ******/
	public function setHour
	(
		$hour		// <mixed> The hour to track (e.g. "10 pm", 8, etc).
	)				// RETURNS <this>
	
	// $tracker->setHour($hour);
	{
		$this->hour = Time_Convert::hourToNumber($hour);
		$this->syncTimestamp();
		return $this;
	}
	
	
/****** Set the minute to track ******/
	public function setMinute
	(
		$minute		// <int> The minute to track.
	)				// RETURNS <this>
	
	// $tracker->setMinute($minute);
	{
		$this->minute = $minute % 60;
		$this->syncTimestamp();
		return $this;
	}
	
	
/****** Set the second to track ******/
	public function setSecond
	(
		$second		// <int> The second to track.
	)				// RETURNS <this>
	
	// $tracker->setSecond($second);
	{
		$this->second = $second % 60;
		$this->syncTimestamp();
		return $this;
	}
	
	
/****** Sets multiple time units for the tracker simultaneously ******/
	public function setMultiple
	(
		$year			// <int> The year to track.
	,	$month = 1		// <mixed> The month to track (e.g. "April", 7, 10, etc).
	,	$day = 1		// <int> The day to track.
	,	$hour = 0		// <mixed> The hour to track (e.g. "10 pm", 8, etc).
	,	$minute = 0		// <int> The minute to track.
	,	$second = 0		// <int> The second to track.
	)					// RETURNS <this>
	
	// $tracker->setMultiple($year, $month, $day, $hour, $minute, $second);
	{
		$this->year = $year + 0;
		$this->month = ($this->month == 1 ? 1 : Time_Convert::monthToNumber($month));
		$this->day = min(max($day, 1), 31);
		$this->hour = ($this->hour === 0 ? 0 : Time_Convert::hourToNumber($hour));
		$this->minute = $minute % 60;
		$this->second = $second % 60;
		
		$this->syncTimestamp();
		
		return $this;
	}
	
	
/****** Set the timestamp ******/
	public function setTimestamp
	(
		$timestamp		// <int> The timestamp to track.
	)					// RETURNS <this>
	
	// $tracker->setTimestamp($timestamp);
	{
		$this->timestamp = $timestamp;
		$this->syncUnits();
		return $this;
	}
	
	
	
/****** Synchronize the time units to keep them consistent with timestamp updates ******/
	private function syncUnits (
	)			// RETURNS <this>
	
	// $tracker->syncUnits();
	{
		// Prepare Values
		$dateInfo = date("Y|n|j|N|G|i|s", $this->timestamp);
		
		// Set the base tracking time units to the current time
		list(
			$trackYear
		,	$trackMonth
		,	$trackDay
		,	$trackDayOfWeek
		,	$trackHour
		,	$trackMinute
		,	$trackSecond
								) = explode("|", $dateInfo);
		
		// Synchronize Time Units
		$this->year = $trackYear + 0;
		$this->month = $trackMonth + 0;
		$this->day = $trackDay + 0;
		$this->hour = $trackHour + 0;
		$this->minute = $trackMinute + 0;
		$this->second = $trackSecond + 0;
		
		$this->dayOfWeek = $trackDayOfWeek + 0;
		
		return $this;
	}
	
	
/****** Syncronize the timestamp to keep them consistent with unit updates ******/
	private function syncTimestamp (
	)			// RETURNS <this>
	
	// $tracker->syncTimestamp();
	{
		$this->timestamp = mktime($this->hour, $this->minute, $this->second, $this->month, $this->day, $this->year);
		
		return $this;
	}
}
