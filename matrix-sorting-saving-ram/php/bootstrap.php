<?php
// phpcs:ignoreFile

ini_set('memory_limit', '500M');
ini_set('display_errors', true);
error_reporting(E_ALL);

const ROWS_MIN = 50000;
const ROWS_MAX = 50000;
const COLS_MIN = 40;
const COLS_MAX = 40;
const NO_MIN   = -10;
const NO_MAX   = 10;

final class Printer {
	private static function decodeMem(string $char) : int {
		$sizes = [
			'K' => 1024,
			'M' => 1024 * 1024,
			'G' => 1024 * 1024 * 1024,
			'T' => 1024 * 1024 * 1024 * 1024,
			'P' => 1024 * 1024 * 1024 * 1024 * 1024
		];
		return $sizes[strtoupper($char)] ?? -1;
	}

	public static function ram($memory = null) : string {
		if (is_string($memory) && preg_match('/^(\d+)(k|m|g|t|p)$/i', $memory, $matches)) {
			$memory = intval($matches[1], 10) * static::decodeMem($matches[2]);
		}
		$size = $memory ?? memory_get_peak_usage(true);
		return '' . number_format($size / 1024, 0) . 'K';
	}

	public static function time(float $time) : string {
		return '' . number_format($time, 3) . 's';
	}

	public static function header(int $rows, int $cols, float $time) {
		printf(
			"\n" .
			"  %-20s %10s %10s %10s\n" .
			"  %-20s %10s %10s %10s\n" .
			"\n",

			'OS',
			'MEM',
			'ELEMENTS',
			'PHP',

			php_uname('s') . ' v' . php_uname('r'),
			static::ram(ini_get('memory_limit')),
			number_format($rows * $cols, 0),
			'v' . PHP_VERSION
		);
		printf(
			"  %-20s %10s %10s %10s\n" .
			"  %-20s %10s %10s %10s\n",

			'',
			'MEM',
			'TIME',
			'DONE %',

			'Started',
			static::ram(),
			static::time($time),
			''
		);
	}

	public static function progress(string $task, float $percentage, float $time) {
		printf(
			"  %-20s %10s %10s %10s %-20s\r",
			$task,
			static::ram(), 
			static::time($time),
			number_format($percentage, 3),
			''
		);
	}

	public static function br() {
		print("\n");
	}

	public static function footer(int $input, int $output, int $elements) {
		printf(
			"\n\n" .
			"  %-20s %10s %10s\n" .
			"  %-20s %10s %10s\n" .
			"  %-20s %10s %10s\n",

			'',
			'Total MEM',
			'Value MEM',

			'size of $input[][]',
			static::ram($input),
			round($input / $elements),

			'size of $output[]',
			static::ram($output),
			round($output / $elements)
		);
	}
}

