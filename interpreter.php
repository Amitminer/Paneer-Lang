<?php

class PaneerLangInterpreter {
    /**
     * @var array Holds the variable assignments
     */
    private $variables = [];

    /**
     * Runs the PaneerLang interpreter on the given file
     *
     * @param string $filename The name of the PaneerLang file to interpret
     */
    public function run($filename) {
        $code = file_get_contents($filename);
        $lines = explode("\n", $code);

        foreach ($lines as $line) {
            if ($this->startsWith(trim($line), '$')) {
                $this->handleVariableAssignment($line);
            } elseif ($this->startsWith(trim($line), 'paneer.bol(') && $this->endsWith(trim($line), ');')) {
                $output = $this->extractStringArgument($line);
                $output = trim($output, '"');
                $output = $this->replaceVariableReferences($output); //Handle variable
                echo $output . PHP_EOL;
            }
        }
    }

    /**
     * Checks if a string starts with a specific prefix
     *
     * @param string $string The string to check
     * @param string $prefix The prefix to check against
     * @return bool Returns true if the string starts with the prefix, false otherwise
     */
    public function startsWith($string, $prefix) {
        return strncmp($string, $prefix, strlen($prefix)) === 0;
    }

    /**
     * Checks if a string ends with a specific suffix
     *
     * @param string $string The string to check
     * @param string $suffix The suffix to check against
     * @return bool Returns true if the string ends with the suffix, false otherwise
     */
    public function endsWith($string, $suffix) {
        return substr($string, -strlen($suffix)) === $suffix;
    }

    /**
     * Extracts the string argument from a line of code
     *
     * @param string $line The line of code to extract the argument from
     * @return string The extracted string argument
     */
    public function extractStringArgument($line) {
        $startPos = strpos($line, '(') + 1;
        $endPos = strrpos($line, ')');

        return substr($line, $startPos, $endPos - $startPos);
    }

    /**
     * Handles variable assignment in the code
     *
     * @param string $line The line of code containing the variable assignment
     */
    public function handleVariableAssignment($line) {
        $line = trim($line, ';');
        $parts = explode('=', $line);
        $variable = trim($parts[0]);
        $value = trim($parts[1]);

        // Remove $ sign from variable name
        $variable = ltrim($variable, '$');

        // Remove leading/trailing spaces from the value
        $value = trim($value, '"');

        $this->assignValueToVariable($variable, $value);
    }

    /**
     * Assigns a value to a variable
     *
     * @param string $variable The variable name
     * @param string $value The value to assign
     */
    public function assignValueToVariable($variable, $value) {
        $this->variables[$variable] = $value;
    }

    /**
     * Retrieves the value of a variable
     *
     * @param string $variable The variable name
     * @return string|null The value of the variable, or null if it is not defined
     */
    public function getVariableValue($variable) {
        if (isset($this->variables[$variable])) {
            return $this->variables[$variable];
        } else {
            return null;
        }
    }

    /**
     * Replaces variable references with their corresponding values in a string
     *
     * @param string $string The string to replace variable references in
     * @return string The string with variable references replaced
     */
    private function replaceVariableReferences($string) {
        preg_match_all('/\$(\w+)/', $string, $matches);
        foreach ($matches[1] as $match) {
            if (isset($this->variables[$match])) {
                $string = str_replace('$' . $match, $this->variables[$match], $string);
            } else {
                $string = str_replace('$' . $match, '', $string);
            }
        }
        return $string;
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
