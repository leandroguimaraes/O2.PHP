<?php
class StringUtil {
	public static function is_UTF8($string)
	{
		return (preg_match('!!u', $string));
	}

	public static function get_UTF8_DecodedString($string)
	{
		return (StringUtil::is_UTF8($string)) ? utf8_decode($string) : $string;
	}

	public static function get_UTF8_EncodedString($string)
	{
		return (StringUtil::is_UTF8($string)) ? $string : utf8_encode($string);
	}
}
