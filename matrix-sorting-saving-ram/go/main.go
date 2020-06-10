package main

import (
	"runtime"
	"time"

	"golang.org/x/text/language"
	"golang.org/x/text/message"
	"golang.org/x/text/number"
)

const ROWS_MIN = 50000
const ROWS_MAX = 50000
const COLS_MIN = 40
const COLS_MAX = 40
const NO_MIN = -10
const NO_MAX = 10

type Printer struct {
}

func makeTimestamp() int64 {
	return time.Now().UnixNano() / int64(time.Millisecond)
}

func (p Printer) ram() string {
	printer := message.NewPrinter(language.English)
	m := runtime.MemStats{}
	runtime.ReadMemStats(&m)

	return printer.Sprintf("%vK", number.Decimal(m.Alloc))
}

func (p Printer) time(time float32) string {
	printer := message.NewPrinter(language.English)
	return printer.Sprintf("%vs", number.Decimal(time, number.MinFractionDigits(3)))
}

func (p Printer) header(elements int, time float32) {
	printer := message.NewPrinter(language.English)
	printer.Printf(
		"\n"+
			"  %-20s %10s %10s %10s\n"+
			"  %-20s %10s %10v %10s\n"+
			"\n",

		"OS",
		"MEM",
		"ELEMENTS",
		"GO",

		runtime.GOOS,
		p.ram(),
		number.Decimal(elements),
		runtime.Version(),
	)
	printer.Printf(
		"  %-20s %10s %10s %10s\n"+
			"  %-20s %10s %10s %10s\n",

		"",
		"MEM",
		"TIME",
		"DONE %",

		"Started",
		p.ram(),
		p.time(time),
		"",
	)
	// MinFractionDigits
}

func main() {
	printer := Printer{}
	printer.header(2000000, float32(makeTimestamp()))
}
