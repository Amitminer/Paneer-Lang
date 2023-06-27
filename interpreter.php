<?php

class PaneerLangInterpreter {
    
    /**
     * Run the Paneer Lang interpreter on the given file.
     *
     * @param string $filename The name of the Paneer Lang file to execute.
     */
    public function run($filename) {
        $code = file_get_contents($filename);
        $lines = explode("\n", $code);

        foreach ($lines as $line) {
            if ($this->startsWith(trim($line), 'paneer.bol(') && $this->endsWith(trim($line), ');')) {
                $output = $this->extractStringArgument($line);
                $output = trim($output, '"');
                echo $output . PHP_EOL;
            }
        }
    }

    /**
     * Check if a string starts with a specified prefix.
     *
     * @param string $string The string to check.
     * @param string $prefix The prefix to compare.
     * @return bool Returns true if the string starts with the prefix, false otherwise.
     */
    public function startsWith($string, $prefix) {
        return strncmp($string, $prefix, strlen($prefix)) === 0;
    }

    /**
     * Check if a string ends with a specified suffix.
     *
     * @param string $string The string to check.
     * @param string $suffix The suffix to compare.
     * @return bool Returns true if the string ends with the suffix, false otherwise.
     */
    public function endsWith($string, $suffix) {
        return substr($string, -strlen($suffix)) === $suffix;
    }

    /**
     * Extract the string argument from a "paneer.bol()" line.
     *
     * @param string $line The line containing the "paneer.bol()" statement.
     * @return string The extracted string argument.
     */
    public function extractStringArgument($line) {
        $startPos = strpos($line, '(') + 1;
        $endPos = strrpos($line, ')');

        return substr($line, $startPos, $endPos - $startPos);
    }
}

$interpreter = new PaneerLangInterpreter();
$command = isset($argv[1]) ? $argv[1] : '';
$filename = isset($argv[2]) ? $argv[2] : '';

if ($command === 'paneer' && !empty($filename)) {
    $interpreter->run($filename);
} else {
    echo "Invalid command or filename. Please provide 'paneer language file' as the command and a valid filename." . PHP_EOL;
}
