<?php

	/**
	 * @package core
	 */

	 /**
	  * The DateTimeObj provides static functions regarding dates in Symphony.
	  * Symphony will set the default timezone of the system using it's configuration
	  * values.
	  */
	Class DateTimeObj {

		/**
		 * Uses PHP's date_default_timezone_set function to set the system
		 * timezone. If the timezone provided is invalid, a `E_USER_WARNING` will be
		 * raised.
		 *
		 * @link http://php.net/manual/en/function.date-default-timezone-set.php
		 * @link http://www.php.net/manual/en/timezones.php
		 * @param string $timezone
		 *	A valid timezone identifier, such as UTC or Europe/Lisbon
		 */
		public static function setDefaultTimezone($timezone){
			if(!@date_default_timezone_set($timezone)) trigger_error(__("Invalid timezone '{$timezone}'"), E_USER_WARNING);
		}

		/**
		 * Given a `$format`, and a `$timestamp`,
		 * return the date in the format provided. This function is a basic
		 * wrapper for PHP's DateTime object. If the `$timestamp` is omitted,
		 * the current timestamp will be used. Optionally, you pass a
		 * timezone identifier with this function to localise the output
		 *
		 * If you like to display a date in the backend, please make use
		 * of `DateTimeObj::format()` which allows date and time localization
		 *
		 * @see class.datetimeobj.php#format()
		 * @link http://www.php.net/manual/en/book.datetime.php
		 * @param string $format
		 *	A valid PHP date format
		 * @param integer $timestamp (optional)
		 *	A unix timestamp to format. 'now' or omitting this parameter will
		 *	result in the current time being used
		 * @param string $timezone (optional)
		 *	The timezone associated with the timestamp
		 * @return string
		 *	The formatted date
		 */
		public static function get($format, $timestamp = 'now', $timezone = null) {

			// Format date
			return DateTimeObj::format($timestamp, $format, false, $timezone);
		}

		/**
		 * Formats the given date and time `$string` based on the given `$format`.
		 * Optionally the result will be localized and respect a timezone differing
		 * from the system default. The default output is ISO 8601.
		 *
		 * @since Symphony 2.2.1
		 * @param string $string (optional)
		 *	A string containing date and time, defaults to the current date and time
		 * @param string $format (optional)
		 *	A valid PHP date format, defaults to ISO 8601
		 * @param boolean $localize (optional)
		 *	Localizes the output, if true, defaults to false
		 * @param string $timezone (optional)
		 *	The timezone associated with the timestamp
		 * @return string
		 *	The formatted date
		 */
		public static function format($string = 'now', $format = 'c', $localize = false, $timezone = null) {

			// Current date and time
			if($string == 'now' || empty($string)) {
				$date = new DateTime();
			}

			// Timestamp
			elseif(ctype_digit($string)) {
				$date = new Datetime(date(DateTime::RFC822, $string));
			}

			// Custom date string
			else {

				// Standardize date
				$string = Lang::standardizeDate($string);

				// Apply Symphony date format using `createFromFormat`
				// if it exists, or fallbacking back to `strptime`
				if(!method_exists('DateTime', 'createFromFormat')) {
					$date = DateTime::createFromFormat(__SYM_DATETIME_FORMAT__, $string);
				}
				else {
					$date = strptime($string, DateTimeObj::dateFormatToStrftime(__SYM_DATETIME_FORMAT__));
					$date = new DateTime($string);
				}

				// Handle non-standard dates
				if($date === false) {
					$date = new DateTime($string);
				}
			}

			// Timezone
			if($timezone !== null) {
				$date->setTimezone(new DateTimeZone($timezone));
			}

			// Format date
			$date = $date->format($format);

			// Localize date
			if($localize === true) {
				$date = Lang::localizeDate($date);
			}

			// Return custom formatted date, use ISO 8601 date by default
			return $date;
		}

		/**
		 * Convert a date format to a strftime format
		 *
		 * Timezone conversion is done for unix. Windows users must exchange %z and %Z.
		 *
		 * Unsupported date formats : S, n, t, L, B, G, u, e, I, P, Z, c, r
		 * Unsupported strftime formats : %U, %W, %C, %g, %r, %R, %T, %X, %c, %D, %F, %x
		 *
		 * @since Symphony 2.2.1
		 * @link http://www.php.net/manual/en/function.strftime.php#96424
		 * @param string $dateFormat a date format
		 * @return string
		 */
		public static function dateFormatToStrftime($dateFormat) {

			$caracs = array(
				// Day - no strf eq : S
				'd' => '%d', 'D' => '%a', 'j' => '%e', 'l' => '%A', 'N' => '%u', 'w' => '%w', 'z' => '%j',
				// Week - no date eq : %U, %W
				'W' => '%V',
				// Month - no strf eq : n, t
				'F' => '%B', 'm' => '%m', 'M' => '%b',
				// Year - no strf eq : L; no date eq : %C, %g
				'o' => '%G', 'Y' => '%Y', 'y' => '%y',
				// Time - no strf eq : B, G, u; no date eq : %r, %R, %T, %X
				'a' => '%P', 'A' => '%p', 'g' => '%l', 'h' => '%I', 'H' => '%H', 'i' => '%M', 's' => '%S',
				// Timezone - no strf eq : e, I, P, Z
				'O' => '%z', 'T' => '%Z',
				// Full Date / Time - no strf eq : c, r; no date eq : %c, %D, %F, %x
				'U' => '%s'
			);

			return strtr((string)$dateFormat, $caracs);
		}

		/**
		 * A wrapper for get, this function will force the GMT timezone.
		 *
		 * @param string $format
		 *	A valid PHP date format
		 * @param integer $timestamp (optional)
		 *	A unix timestamp to format. Omitting this parameter will
		 *	result in the current time being used
		 * @return string
		 *	The formatted date in GMT
		 */
		public static function getGMT($format, $timestamp = 'now'){
			return self::get($format, $timestamp, 'GMT');
		}

		/**
		 * A wrapper for get, this function will return a HTML string representing
		 * an `<abbr>` element which contained the formatted date of now, and an
		 * RFC 2822 formatted date (Thu, 21 Dec 2000 16:01:07 +0200) as the title
		 * attribute. Symphony uses this in it's status messages so that it can
		 * dynamically update how long ago the action took place using Javascript.
		 *
		 * @param string $format
		 *	A valid PHP date format
		 * @return string
		 *	A HTML string of an `<abbr>` element with a class of 'timeago' and the current
		 * date (RFC 2822) as the title element. The value is the current time as
		 * specified by the `$format`.
		 */
		public static function getTimeAgo($format){
			return '<abbr class="timeago" title="' . self::get('r') . '">' . self::get($format) . '</abbr>';
		}

	}
