<?php
/**
 * generate_full_report.php
 * 
 * Generates FULL_REPORT.md containing:
 * - Directory tree (like `tree /f` on Windows)
 * - Content of every source file (PHP, SQL, CSS, JS, HTML, MD, etc.)
 * 
 * Usage: php generate_full_report.php
 */

class FullReportGenerator
{
    private const ROOT_DIR = __DIR__;
    private const OUTPUT_FILE = __DIR__ . '/FULL_REPORT.md';
    
    // Directories and files to exclude from the report
    private const EXCLUDE_DIRS = [
        'vendor', '.git', 'node_modules', 'cache', 'logs', 'temp', 'tmp',
        'uploads' // uploaded files (usually large and not part of source)
    ];
    
    private const EXCLUDE_FILES = [
        '.env', // sensitive credentials
        'FULL_REPORT.md', // avoid including itself
        'generate_full_report.php' // exclude the generator script
    ];
    
    private const ALLOWED_EXTENSIONS = [
        'php', 'sql', 'css', 'js', 'html', 'htm', 'md', 'json', 'xml',
        'ini', 'txt', 'yml', 'yaml', 'htaccess', 'env.example'
    ];
    
    private array $fileList = [];
    
    public function generate(): void
    {
        $this->collectFiles(self::ROOT_DIR);
        $tree = $this->buildTree();
        $content = $this->buildMarkdown($tree);
        file_put_contents(self::OUTPUT_FILE, $content);
        echo "[OK] Full report generated: " . self::OUTPUT_FILE . PHP_EOL;
        echo "Total files included: " . count($this->fileList) . PHP_EOL;
    }
    
    /**
     * Recursively collect all allowed files, excluding unwanted dirs/files.
     */
    private function collectFiles(string $dir): void
    {
        $items = scandir($dir);
        if ($items === false) return;
        
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            $relativePath = str_replace(self::ROOT_DIR . DIRECTORY_SEPARATOR, '', $path);
            
            // Skip excluded directories
            if (is_dir($path)) {
                if (in_array($item, self::EXCLUDE_DIRS)) continue;
                $this->collectFiles($path);
                continue;
            }
            
            // Skip excluded files
            if (in_array($item, self::EXCLUDE_FILES)) continue;
            
            // Only include files with allowed extensions
            $ext = pathinfo($item, PATHINFO_EXTENSION);
            if (!in_array(strtolower($ext), self::ALLOWED_EXTENSIONS)) continue;
            
            $this->fileList[] = $relativePath;
        }
    }
    
    /**
     * Build an ASCII tree representation (like `tree /f`).
     */
    private function buildTree(): string
    {
        $tree = "```\n" . basename(self::ROOT_DIR) . "\n";
        $this->buildTreeRecursive(self::ROOT_DIR, '', $tree);
        $tree .= "```\n";
        return $tree;
    }
    
    private function buildTreeRecursive(string $dir, string $prefix, string &$tree): void
    {
        $items = scandir($dir);
        if ($items === false) return;
        
        $files = [];
        $subdirs = [];
        
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            $relative = str_replace(self::ROOT_DIR . DIRECTORY_SEPARATOR, '', $path);
            
            // Skip excluded dirs/files that were filtered in collection
            if (is_dir($path)) {
                if (in_array($item, self::EXCLUDE_DIRS)) continue;
                $subdirs[] = $item;
            } else {
                if (in_array($item, self::EXCLUDE_FILES)) continue;
                $ext = pathinfo($item, PATHINFO_EXTENSION);
                if (in_array(strtolower($ext), self::ALLOWED_EXTENSIONS)) {
                    $files[] = $item;
                }
            }
        }
        
        sort($subdirs);
        sort($files);
        
        $entries = array_merge($subdirs, $files);
        $count = count($entries);
        
        foreach ($entries as $idx => $entry) {
            $isLast = ($idx === $count - 1);
            $branch = $isLast ? '└── ' : '├── ';
            $tree .= $prefix . $branch . $entry . "\n";
            
            $entryPath = $dir . DIRECTORY_SEPARATOR . $entry;
            if (is_dir($entryPath)) {
                $newPrefix = $prefix . ($isLast ? '    ' : '│   ');
                $this->buildTreeRecursive($entryPath, $newPrefix, $tree);
            }
        }
    }
    
    /**
     * Build the final markdown report.
     */
    private function buildMarkdown(string $tree): string
    {
        $timestamp = date('Y-m-d H:i:s');
        $rootDir = self::ROOT_DIR;
        
        $markdown = "# Full Project Report\n\n";
        $markdown .= "**Generated:** {$timestamp}\n\n";
        $markdown .= "**Root Directory:** `{$rootDir}`\n\n";
        $markdown .= "## Directory Tree\n\n";
        $markdown .= $tree . "\n";
        $markdown .= "## File Contents\n\n";
        
        // Sort files alphabetically for consistent output
        sort($this->fileList);
        
        foreach ($this->fileList as $file) {
            $fullPath = self::ROOT_DIR . DIRECTORY_SEPARATOR . $file;
            $content = file_get_contents($fullPath);
            if ($content === false) {
                $content = "[ERROR: Unable to read file]";
            }
            
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            $language = $this->getLanguageForHighlight($extension);
            
            $markdown .= "### `{$file}`\n\n";
            $markdown .= "```{$language}\n";
            $markdown .= rtrim($content) . "\n";
            $markdown .= "```\n\n";
        }
        
        return $markdown;
    }
    
    /**
     * Map file extension to markdown code block language.
     */
    private function getLanguageForHighlight(string $ext): string
    {
        return match(strtolower($ext)) {
            'php' => 'php',
            'sql' => 'sql',
            'css' => 'css',
            'js' => 'javascript',
            'html', 'htm' => 'html',
            'md' => 'markdown',
            'json' => 'json',
            'xml' => 'xml',
            'ini' => 'ini',
            'yml', 'yaml' => 'yaml',
            'htaccess' => 'apache',
            'txt' => 'text',
            default => 'text'
        };
    }
}

// Run the generator
$generator = new FullReportGenerator();
$generator->generate();