<?php

namespace App\Helper;

use Symfony\Component\VarDumper\VarDumper;

/**
 * class:  App
 * -----------------------------------------------------
 * @author  Saqot (Mihail Shirnin) <saqott@gmail.com>
 * @package  App\Helper
 * -----------------------------------------------------
 */
class App
{
	/**
	 * @param $params
	 * @return mixed
	 */
	public static function dump(...$params)
	{
		if ($params) {
			$params = (count($params) <= 1) ? $params[0] : $params;
		} else {
			$params = '';
		}

		return VarDumper::dump($params);
	}

	/**
	 * @param $params
	 */
	public static function dumpExit(...$params)
	{
		if ($params) {
			$params = (count($params) <= 1) ? $params[0] : $params;
		} else {
			$params = '';
		}
		exit(VarDumper::dump($params));
	}

	/**
	 * Текущая неделя в году
	 * @return int
	 */
	public static function getCurWeek()
	{
		return date('W') - 1;
	}


}