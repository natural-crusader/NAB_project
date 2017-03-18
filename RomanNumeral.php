<?php


if($argc > 1) {
	$args = array_slice($argv, 2);
	$parsed_file = RomanNumeral::convert($argv[1], $args);

	// we can add furthor commands to indicate what to do with this 
	// or we can redirect standard output
	echo $parsed_file;
	echo "\r\n\r\n";
} else {
	echo "Example usage: php RomanNumeral.php 87234965 one=p five=d ten=i\r\n\r\n";
	exit();
}


class RomanNumeral {
	private $one = 'I';
	private $five = 'V';
	private $ten = 'X';
	private $fifty = 'L';
	private $hundred = 'C';
	private $fivehundred = 'D';
	private $thousand = 'M';

	function __construct($custom_figures) {
		foreach($custom_figures as $custom) {
			List($key, $value) = explode('=', $custom);
			$this->$key = $value;
		}
	}

	public function toRoman($value) {
		$str = '';
		echo "value: $value\r\n";

		$thousands = floor($value / 1000);
		$str.= str_repeat($this->thousand, $thousands);
		$value = $value % 1000;
		
		$fivehundreds = floor($value / 500);
		$str.= str_repeat($this->fivehundred, $fivehundreds);
		$value = $value % 500;
		
		$hundreds = floor($value / 100);
		$str.= str_repeat($this->hundred, $hundreds);
		$value = $value % 100;
		
		$fifties = floor($value / 50);
		$str.= str_repeat($this->fifty, $fifties);
		$value = $value % 50;
		
		$tens = floor($value / 10) ;
		$str.= str_repeat($this->ten, $tens);
		$value = $value % 10;
		
		$fives = floor($value / 5);
		$str.= str_repeat($this->five, $fives);
		$value = $value % 5;
		
		$ones = $value;
		$str.= str_repeat($this->one, $ones);
		
		return $str;
	}

	public static function convert($value, $custom_figures = []){
		$instance = new RomanNumeral($custom_figures);
		return $instance->toRoman($value);
	}
}