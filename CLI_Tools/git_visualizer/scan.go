package main

import (
	"bufio"
	"fmt"
	"io"
	"log"
	"os"
	"os/user"
	"strings"
)

// getDotFilePath returns the dot file for the repos lsit.
// Creates it and the enclosing folder if it does not exist.
func getDotFilePath() string {
	usr, err := user.Current()
	if err != nil {
		log.Fatal(err)
	}

	dotFile := usr.HomeDir + "/.gogitlocalstats"

	return dotFile

}

// openFile opens the file located at `filePath`. Creates the file if nothing exists.

func openFile(filePath string) *os.File {
	f, err := os.OpenFile(filePath, os.O_APPEND|os.O_RDWR, 0755)
	if err != nil {
		if os.IsNotExist(err) {
			// file does not exist, create it and assign to `f`
			f, err = os.Create(filePath)
			if err != nil {
				panic(err)
			}
		} else {
			panic(err)
		}
	}
	return f
}

//parseFileLinnesToSlice given a file path string, gets the content
// of each line and parses it to a slice of strings

func parseFileLinnesToSlice(filePath string) []string {
	f := openFile(filePath)
	defer f.Close()

	var lines []string
	scanner := bufio.NewScanner(f)
	for scanner.Scan() {
		lines = append(lines, scanner.Text())
	}
	if err := scanner.Err(); err != nil {
		if err != io.EOF {
			panic(err)
		}
	}
	return lines

}

// sliceContains returns true if `slice` contains `value`

func sliceContaions(slice []string, value string) bool {
	for _, v := range slice {
		if v == value {
			return true
		}
	}
	return false
}

//joinSlices adds the element of the `new` slice
// into the `existing` slice, only if not already there.

func joinSlices(new []string, existing []string) []string {
	for _, i := range new {
		if !sliceContaions(existing, i) {
			existing = append(existing, i)
		}
	}
	return existing
}

// dumpStringSliceToFile writes content to the file in path`filePath` (overwriting existing content)
func dumpStringsSliceToFile(repos []string, filePath string) {
	content := strings.Join(repos, "\n")
	os.WriteFile(filePath, []byte(content), 0755)
}

// addnewSliceElementsToFile given a slice of strings representing paths, stores them
// to the filesystem.

func addnewSliceElementsToFile(filePath string, newRepos []string) {

	existingRepos := parseFileLinnesToSlice(filePath)
	repos := joinSlices(newRepos, existingRepos)
	dumpStringsSliceToFile(repos, filePath)
}

// recursiveScanFolder starts the recursive search of git repositories
// living in the `folder` subtree.

func recursiveScanFolder(folder string) []string {
	return scanGitFolders(make([]string, 0), folder)
}

// scan scnas a new folder for git repositories
func scan(folder string) {
	fmt.Printf("Found folders:\n\n")
	repositories := recursiveScanFolder(folder)
	filePath := getDotFilePath()
	addnewSliceElementsToFile(filePath, repositories)
	fmt.Printf("\n\nSuccessfully added\n\n")
}

// scanGitFolders returns a list of subfolders of `folder` ending with `.git`.
// Returns the base folder of the repo, the .git folder parent.
// Recursively searches in the subfolders by passing an existing `folders` slice.

func scanGitFolders(folders []string, folder string) []string {
	// trim the last `/`
	folder = strings.TrimSuffix(folder, "/")

	f, err := os.Open(folder)
	if err != nil {
		log.Fatal(err)
	}
	files, err := f.Readdir(-1)
	f.Close()
	if err != nil {
		log.Fatal(err)
	}

	var path string

	for _, file := range files {
		if file.IsDir() {
			path = folder + "/" + file.Name()
			if file.Name() == ".git" {
				path = strings.TrimSuffix(path, "/.git")
				fmt.Print(path)
				folders = append(folders, path)
				continue
			}
			if file.Name() == "vendor" || file.Name() == " node_moduels" {
				continue
			}
			folders = scanGitFolders(folders, path)

		}

	}
	return folders
}
