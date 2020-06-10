<?php
// phpcs:ignoreFile

class Pointer {
	public $no;
	public $count = 0;

	public function isLower(int $no) : bool {
		return null === $this->no || $no <= $this->no;
	}

	public function assign(int $no) : bool {
		if ($this->no !== $no && null !== $this->no) {
			$this->count = 0;
		}
		$this->no = $no;
		$this->count++;
		return true;
	}

	public function compare(array $arr) : bool {
		$p = new self();
		foreach ($arr as $i => $row) {
			foreach ($row as $j => $no) {
				if ($no === $this->no) {
					$p->assign($no);
				}
			}
		}
		return $p->no === $this->no && $p->count === $this->count;
	}

	public static function IsNumberLower(int $no, $target) : bool {
		return null === $target || $no <= $target;
	}

	public static function AssignNumber(int $no, &$target, int &$count) : bool {
		if ($target !== $no && null !== $target) {
			$count = 0;
		}
		$target = $no;
		$count++;
		return true;
	}

	public static function CompareNumber(int $no, int $target, int $count) : bool {
		$p = new self();
		foreach ($arr as $i => $row) {
			foreach ($row as $j => $no) {
				if ($no === $target) {
					$p->assign($no);
				}
			}
		}
		return $p->no === $target && $p->count === $count;
	}
}

require_once(__DIR__ . '/bootstrap.php');

$PHP_WASTING_RAM = in_array('--waste', $argv) ?? false;

$rowsCount = abs(rand(ROWS_MIN, ROWS_MAX));
$colsCount = abs(rand(COLS_MIN, COLS_MAX));

$time = microtime(true);
Printer::header($rowsCount, $colsCount, microtime(true) - $time);

$_ram = [];
$_ram[] = memory_get_peak_usage(true);

$input = [];
for ($x = 0; $x < $rowsCount; $x++ ) {
	$input[$x] = [];
	for ($y = 0; $y < $colsCount; $y++) {
		$input[$x][$y] = rand(NO_MIN, NO_MAX);
	}
}

$_ram[] = memory_get_peak_usage(true);

Printer::progress('$input[][] added', 0, microtime(true) - $time);
Printer::br();

$_ram[] = memory_get_peak_usage(true);

$output = [];

$_index = 0;
$count = 1;
// the current number from min to top to see the current pointer
$bottom = null;
while (count($output) < $count) {
	$c = 0;

	if ($PHP_WASTING_RAM) {
		$p = new Pointer();
	} else {
		$pointerNo = null;
		$pointerCount = 0;
	}
	// $p->no = $bottom;

	foreach ($input as $i => $row) {
		foreach ($row as $j => $no) {
			if (null !== $bottom && $no <= $bottom) {
				continue;
			}
			if ($PHP_WASTING_RAM) {
				if ($p->isLower($no)) {
					$p->assign($no);
				}
			} else {
				if (Pointer::IsNumberLower($no, $pointerNo)) {
					Pointer::AssignNumber($no, $pointerNo, $pointerCount);
				}
			}

			// measure total items in array
			if (1 === $count) {
				$c++;
			}
		}
	}
	if (1 === $count && $c > $count) {
		$count = $c;
	}
	if ($PHP_WASTING_RAM) {
		// array_fill() & array_merge() uses 105Mb / 1M elements more
		$output = array_merge($output, array_fill(0, $p->count, $p->no));
		$bottom = $p->no;
	} else {
		// array_fill() uses 4Mb / 1M elements more
		// foreach (array_fill(0, $pointerCount, $pointerNo) as $value) {
		// 	$output[] = $value;
		for ($z = 0; $z < $pointerCount; $z++) {
			$output[] = $pointerNo;
		}
		$bottom = $pointerNo;
	}

	$_index++;
	Printer::progress('Sorting > $output', 100 * $_index / $count, microtime(true) - $time);
}

$_ram[] = memory_get_peak_usage(true);
Printer::progress('Sorted > $output[]', 100, microtime(true) - $time);
Printer::footer($_ram[1] - $_ram[0], $_ram[3] - $_ram[2], $rowsCount * $colsCount);

// printf("\$output = [ %s ]\n", implode(', ', $output));