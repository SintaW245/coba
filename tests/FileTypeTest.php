<?php
/**
 * PHPUnit Test Suite for AI Image Detector
 * Tests file validation, API keys, JSON response, security, and more
 */

use PHPUnit\Framework\TestCase;

class FileTypeTest extends TestCase
{
    private $projectRoot;
    
    protected function setUp(): void
    {
        $this->projectRoot = __DIR__ . '/../';
    }
    
    /**
     * Test Case 1: Test that all PHP files have valid syntax
     */
    public function testPHPFilesAreValid(): void
    {
        $phpFiles = glob($this->projectRoot . '*.php');
        
        $this->assertNotEmpty($phpFiles, "No PHP files found in project root");
        
        foreach ($phpFiles as $file) {
            $output = [];
            $return = 0;
            
            // Use php -l to check syntax
            exec("php -l " . escapeshellarg($file), $output, $return);
            
            $this->assertEquals(
                0,
                $return,
                "PHP file {$file} has syntax errors: " . implode("\n", $output)
            );
        }
    }
    
    /**
     * Test Case 2: Test that all PHP files contain HTML tags
     */
    public function testPHPFilesContainHTML(): void
    {
        $phpFiles = glob($this->projectRoot . '*.php');
        // Exclude files that don't need HTML
        $excludeFiles = ['detect.php', 'clear_history.php'];
        
        foreach ($phpFiles as $file) {
            $basename = basename($file);
            
            if (in_array($basename, $excludeFiles)) {
                continue;
            }
            
            $content = file_get_contents($file);
            
            // Check for basic HTML structure
            $this->assertMatchesRegularExpression(
                '/<html|<!DOCTYPE/i',
                $content,
                "File {$basename} does not contain valid HTML structure"
            );
            
            // Check for closing html tag
            $this->assertMatchesRegularExpression(
                '/<\/html>/i',
                $content,
                "File {$basename} does not have closing HTML tag"
            );
        }
    }
    
    /**
     * Test Case 3: Test that required files exist
     */
    public function testRequiredFilesExist(): void
    {
        $requiredFiles = [
            'index.php',
            'detect.php',
            'result.php',
            'history.php',
            'about.php',
            'clear_history.php'
        ];
        
        foreach ($requiredFiles as $file) {
            $filePath = $this->projectRoot . $file;
            $this->assertFileExists(
                $filePath,
                "Required file {$file} does not exist"
            );
            
            // Also check if file is readable
            $this->assertFileIsReadable(
                $filePath,
                "Required file {$file} is not readable"
            );
        }
    }
    
    /**
     * Test Case 4: Test that API keys are configured and not empty
     */
    public function testAPIKeysAreConfigured(): void
    {
        $detectFile = $this->projectRoot . 'detect.php';
        $this->assertFileExists($detectFile, "detect.php does not exist");
        
        $content = file_get_contents($detectFile);
        
        // Check API_USER is defined
        $this->assertMatchesRegularExpression(
            '/define\s*\(\s*[\'"]API_USER[\'"]\s*,/i',
            $content,
            "API_USER constant is not defined in detect.php"
        );
        
        // Check API_SECRET is defined
        $this->assertMatchesRegularExpression(
            '/define\s*\(\s*[\'"]API_SECRET[\'"]\s*,/i',
            $content,
            "API_SECRET constant is not defined in detect.php"
        );
        
        // Check they are not using default placeholder values
        $this->assertDoesNotMatchRegularExpression(
            '/define\s*\(\s*[\'"]API_USER[\'"]\s*,\s*[\'"]YOUR_API_USER[\'"]/i',
            $content,
            "API_USER is still using default placeholder value. Please configure your Sightengine API credentials."
        );
        
        $this->assertDoesNotMatchRegularExpression(
            '/define\s*\(\s*[\'"]API_SECRET[\'"]\s*,\s*[\'"]YOUR_API_SECRET[\'"]/i',
            $content,
            "API_SECRET is still using default placeholder value. Please configure your Sightengine API credentials."
        );
    }
    
    /**
     * Test Case 5: Test HTML structure completeness
     */
    public function testHTMLStructureIsComplete(): void
    {
        $htmlFiles = [
            'index.php',
            'result.php',
            'history.php',
            'about.php'
        ];
        
        foreach ($htmlFiles as $file) {
            $filePath = $this->projectRoot . $file;
            $content = file_get_contents($filePath);
            
            // Check DOCTYPE
            $this->assertMatchesRegularExpression(
                '/<!DOCTYPE\s+html>/i',
                $content,
                "File {$file} is missing DOCTYPE declaration"
            );
            
            // Check html tag with lang attribute
            $this->assertMatchesRegularExpression(
                '/<html[^>]+lang=/i',
                $content,
                "File {$file} is missing lang attribute in html tag"
            );
            
            // Check head section
            $this->assertMatchesRegularExpression(
                '/<head>/i',
                $content,
                "File {$file} is missing <head> tag"
            );
            
            // Check meta charset
            $this->assertMatchesRegularExpression(
                '/<meta[^>]+charset=["\']UTF-8["\']/i',
                $content,
                "File {$file} is missing UTF-8 charset meta tag"
            );
            
            // Check viewport meta
            $this->assertMatchesRegularExpression(
                '/<meta[^>]+name=["\']viewport["\']/i',
                $content,
                "File {$file} is missing viewport meta tag"
            );
            
            // Check title
            $this->assertMatchesRegularExpression(
                '/<title>(.+)<\/title>/i',
                $content,
                "File {$file} is missing or has empty <title> tag"
            );
            
            // Check body
            $this->assertMatchesRegularExpression(
                '/<body>/i',
                $content,
                "File {$file} is missing <body> tag"
            );
        }
    }
    
    /**
     * Test Case 6: Test security - XSS prevention
     */
    public function testXSSPreventionInPHPFiles(): void
    {
        $phpFiles = glob($this->projectRoot . '*.php');
        
        foreach ($phpFiles as $file) {
            $content = file_get_contents($file);
            
            // Find all echo statements with variables
            preg_match_all('/echo\s+\$[^;]+;/i', $content, $echoMatches);
            
            if (!empty($echoMatches[0])) {
                foreach ($echoMatches[0] as $echoStatement) {
                    // Check if htmlspecialchars is used
                    if (strpos($echoStatement, '$_') !== false) {
                        $this->assertMatchesRegularExpression(
                            '/htmlspecialchars|htmlentities/i',
                            $echoStatement,
                            "Potential XSS vulnerability in " . basename($file) . ": {$echoStatement}\n" .
                            "User input should be escaped with htmlspecialchars()"
                        );
                    }
                }
            }
        }
        
        // This test passes if no assertions fail
        $this->assertTrue(true, "XSS prevention check completed");
    }
}
?>