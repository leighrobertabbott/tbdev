#!/usr/bin/env php
<?php
/**
 * Fix PHP line lengths to comply with 80-character limit
 * This script handles common patterns that cause long lines
 */

class LineFormatter
{
    private const MAX_LENGTH = 80;
    private int $filesFixed = 0;
    private int $linesFixed = 0;

    public function fixFile(string $filepath): bool
    {
        if (!file_exists($filepath)) {
            return false;
        }

        $content = file_get_contents($filepath);
        $lines = explode("\n", $content);
        $newLines = [];
        $changed = false;

        foreach ($lines as $line) {
            $result = $this->fixLine($line);
            
            if (is_array($result)) {
                $newLines = array_merge($newLines, $result);
                $changed = true;
                $this->linesFixed++;
            } else {
                $newLines[] = $result;
                if ($result !== $line) {
                    $changed = true;
                    $this->linesFixed++;
                }
            }
        }

        if ($changed) {
            file_put_contents($filepath, implode("\n", $newLines));
            $this->filesFixed++;
            echo "Fixed: $filepath\n";
            return true;
        }

        return false;
    }

    private function fixLine(string $line): string|array
    {
        $trimmed = rtrim($line);
        
        // Skip if already short enough
        if (strlen($trimmed) <= self::MAX_LENGTH) {
            return $line;
        }

        // Get indentation
        preg_match('/^(\s*)/', $trimmed, $matches);
        $indent = $matches[1];
        $nextIndent = $indent . '    ';

        // Pattern 1: Router method calls
        if (preg_match('/^(\s*\$router->(get|post|put|patch|delete)\()(.+)$/', $trimmed, $matches)) {
            return $this->fixRouterCall($matches[1], $matches[3], $indent, $nextIndent);
        }

        // Pattern 2: Long array definitions in function calls
        if (preg_match('/^(\s*.+?\()(.+)(\)[,;]*)$/', $trimmed, $matches)) {
            $result = $this->fixFunctionCall($matches[1], $matches[2], $matches[3], $indent, $nextIndent);
            if ($result !== null) {
                return $result;
            }
        }

        // Pattern 3: Long string concatenations
        if (strpos($trimmed, ' .
            ') !== false && strlen($trimmed) > self::MAX_LENGTH) {
            return $this->fixStringConcat($trimmed, $indent, $nextIndent);
        }

        // Pattern 4: Long method chains
        if (substr_count($trimmed, '->') > 1 && strlen($trimmed) > self::MAX_LENGTH) {
            return $this->fixMethodChain($trimmed, $indent, $nextIndent);
        }

        // If can't fix, return original
        return $line;
    }

    private function fixRouterCall(string $prefix, string $params, string $indent, string $nextIndent): array
    {
        $parts = $this->splitParameters($params);
        
        if (count($parts) < 2) {
            return [$indent . $prefix . $params];
        }

        $result = [$indent . $prefix];
        
        foreach ($parts as $idx => $part) {
            $isLast = ($idx === count($parts) - 1);
            
            if ($isLast) {
                // Last parameter, close parenthesis
                $result[] = $nextIndent . $part;
            } else {
                $result[] = $nextIndent . $part . ',';
            }
        }

        return $result;
    }

    private function fixFunctionCall(string $before, string $params, string $after, string $indent, string $nextIndent): ?array
    {
        // Only fix if the line is actually too long
        $fullLine = $before . $params . $after;
        if (strlen(rtrim($fullLine)) <= self::MAX_LENGTH) {
            return null;
        }

        // Check if it has middleware array - common pattern
        if (strpos($params, '[Middleware::') !== false || strpos($params, '[\App\Controllers') !== false) {
            $parts = $this->splitParameters($params);
            
            if (count($parts) < 2) {
                return null;
            }

            $result = [$before];
            
            foreach ($parts as $idx => $part) {
                $isLast = ($idx === count($parts) - 1);
                
                if ($isLast) {
                    $result[] = $nextIndent . $part;
                } else {
                    $result[] = $nextIndent . $part . ',';
                }
            }
            
            $result[count($result) - 1] .= $after;
            
            return $result;
        }

        return null;
    }

    private function fixStringConcat(string $line, string $indent, string $nextIndent): array
    {
        $parts = explode(' . ', $line);
        
        if (count($parts) <= 1) {
            return [$line];
        }

        $result = [];
        foreach ($parts as $idx => $part) {
            if ($idx === 0) {
                $result[] = $part . ' .';
            } elseif ($idx === count($parts) - 1) {
                $result[] = $nextIndent . $part;
            } else {
                $result[] = $nextIndent . $part . ' .';
            }
        }

        return $result;
    }

    private function fixMethodChain(string $line, string $indent, string $nextIndent): array
    {
        $parts = explode('->', $line);
        
        if (count($parts) <= 2) {
            return [$line];
        }

        $result = [];
        foreach ($parts as $idx => $part) {
            if ($idx === 0) {
                $result[] = $part;
            } else {
                $result[] = $nextIndent . '->' . $part;
            }
        }

        return $result;
    }

    private function splitParameters(string $params): array
    {
        $result = [];
        $current = '';
        $depth = 0;
        $inString = false;
        $stringChar = null;

        for ($i = 0; $i < strlen($params); $i++) {
            $char = $params[$i];
            $prev = $i > 0 ? $params[$i - 1] : '';

            // Handle string boundaries
            if (!$inString && ($char === '"' || $char === "'")) {
                $inString = true;
                $stringChar = $char;
            } elseif ($inString && $char === $stringChar && $prev !== '\\') {
                $inString = false;
                $stringChar = null;
            }

            // Handle nesting
            if (!$inString) {
                if ($char === '[' || $char === '(') {
                    $depth++;
                } elseif ($char === ']' || $char === ')') {
                    $depth--;
                } elseif ($char === ',' && $depth === 0) {
                    $result[] = trim($current);
                    $current = '';
                    continue;
                }
            }

            $current .= $char;
        }

        if (!empty(trim($current))) {
            $result[] = trim($current);
        }

        return $result;
    }

    public function getStats(): array
    {
        return [
            'files' => $this->filesFixed,
            'lines' => $this->linesFixed
        ];
    }
}

// Main execution
if (php_sapi_name() !== 'cli') {
    die('This script must be run from command line');
}

$formatter = new LineFormatter();
$files = [];

// Find all PHP files
exec('find .
    -name "*.php" -not -path "./vendor/*" -not -path "./cache/*" -type f', $files);

echo "Processing " . count($files) . " PHP files...\n\n";

foreach ($files as $file) {
    $formatter->fixFile($file);
}

$stats = $formatter->getStats();

echo "\n=== Summary ===\n";
echo "Files fixed: " . $stats['files'] . "\n";
echo "Lines fixed: " . $stats['lines'] . "\n";
