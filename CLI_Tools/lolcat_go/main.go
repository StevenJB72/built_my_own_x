package main

import (
	"bufio"
	"fmt"
	"io"
	"math"
	"strings"
	"time"

	"github.com/brianvoe/gofakeit/v6"
)

func rgb(i int) (int, int, int) {

	f := 0.1
	return int(math.Sin(f*float64(i)+0)*127 + 128),
		int(math.Sin(f*float64(i)+2*math.Pi/3)*127 + 128),
		int(math.Sin(f*float64(i)+4*math.Pi/3)*127 + 128)
}

func main() {
	gofakeit.Seed(0) // optional, for consistent results

	var phrases []string
	for i := 0; i < 3; i++ {
		phrases = append(phrases, gofakeit.HackerPhrase())
	}

	input := strings.Join(phrases, ";") + "\n"

	//reader := bufio.NewReader(strings.NewReader(input))
	reader := bufio.NewReader(strings.NewReader(input))

	i := 0

	for {
		char, _, err := reader.ReadRune()
		if err == io.EOF {
			break
		}
		r, g, b := rgb(i)
		fmt.Printf("\033[38;2;%d;%d;%dm%c\033[0m", r, g, b, char)
		time.Sleep(30 * time.Millisecond) // ← Add delay here
		i++
	}
}
