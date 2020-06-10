<?php
// phpcs:ignoreFile

function IsNumberLower(int $no, $target) : bool {
  return null === $target || $no <= $target;
}

function AssignNumber(int $no, &$target, int &$count) : bool {
  if ($target !== $no && null !== $target) {
    $count = 0;
  }
  $target = $no;
  $count++;
  return true;
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

  $pointerNo = null;
  $pointerCount = 0;

	foreach ($input as $i => $row) {
		foreach ($row as $j => $no) {
			if (null !== $bottom && $no <= $bottom) {
				continue;
			}
      if (IsNumberLower($no, $pointerNo)) {
        AssignNumber($no, $pointerNo, $pointerCount);
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
		$output = array_merge($output, array_fill(0, $pointerCount, $pointerNo));
		$bottom = $pointerNo;
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