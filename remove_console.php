<?php
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__));
$count = 0;

foreach ($iterator as $file) {
    if ($file->isDir()) continue;
    $path = $file->getPathname();
    if (strpos($path, '.git') !== false) continue;
    
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    
    if (in_array($ext, ['js', 'html', 'php'])) {
        $content = file_get_contents($path);
        
        // Remove console.log entirely
        $newContent = preg_replace('/^\s*console\.log\s*\(.*?\);\s*$/m', '', $content);
        $newContent = preg_replace('/console\.log\s*\(.*?\);?/', '', $newContent);
        
        if ($newContent !== $content) {
            file_put_contents($path, $newContent);
            echo "Cleaned console logs from $path\n";
            $count++;
        }
    }
}
echo "Cleaned $count files total.\n";
?>
