<?php
/**
 * PHPUnit Test Suite for AI Image Detector
 * Final cleaned & CI-safe version
 */

use PHPUnit\Framework\TestCase;

class FileTypeTest extends TestCase
{
    private string $projectRoot;

    protected function setUp(): void
    {
        $this->projectRoot = dirname(__DIR__) . DIRECTORY_SEPARATOR;
    }

    /**
     * Test 1: Required PHP files must exist and be readable
     */
    public function testRequiredFilesExist(): void
    {
        $requiredFiles = [
            '',
            
        ];

        foreach ($requiredFiles as $file) {
            $path = $this->projectRoot . $file;

            $this->assertFileExists($path, "{$file} does not exist");
            $this->assertFileIsReadable($path, "{$file} is not readable");
        }
    }

    /**
     * Test 2: PHP files must not be empty
     */
    public function testPHPFilesAreNotEmpty(): void
    {
        $phpFiles = glob($this->projectRoot . '*.php');
        $this->assertNotEmpty($phpFiles, 'No PHP files found');

        foreach ($phpFiles as $file) {
            $this->assertGreaterThan(
                0,
                filesize($file),
                basename($file) . ' is empty'
            );
        }
    }

    /**
     * Test 3: Frontend PHP files must contain HTML structure
     */
    public function testHTMLStructureExists(): void
    {
        $htmlFiles = [
            '',
            
        ];

        foreach ($htmlFiles as $file) {
            $content = file_get_contents($this->projectRoot . $file);

            $this->assertMatchesRegularExpression('/<!DOCTYPE\s+html>/i', $content);
            $this->assertMatchesRegularExpression('/<html[^>]*lang=/i', $content);
            $this->assertMatchesRegularExpression('/<head>/i', $content);
            $this->assertMatchesRegularExpression('/<meta[^>]+charset=["\']UTF-8["\']/i', $content);
            $this->assertMatchesRegularExpression('/<title>.+<\/title>/i', $content);
            $this->assertMatchesRegularExpression('/<body>/i', $content);
            $this->assertMatchesRegularExpression('/<\/html>/i', $content);
        }
    }

    /**
     * Test 4: API configuration constants must be defined
     */
    public function testAPIConfigurationExists(): void
    {
        $detectFile = $this->projectRoot . 'detect.php';
        $content = file_get_contents($detectFile);

        $this->assertMatchesRegularExpression(
            '/define\s*\(\s*[\'"]API_USER[\'"]\s*,\s*[\'"].+[\'"]\s*\)/i',
            $content,
            'API_USER is not properly defined'
        );

        $this->assertMatchesRegularExpression(
            '/define\s*\(\s*[\'"]API_SECRET[\'"]\s*,\s*[\'"].+[\'"]\s*\)/i',
            $content,
            'API_SECRET is not properly defined'
        );
    }

    /**
     * Test 5: Basic XSS prevention on echoed user input
     */
    public function testXSSPrevention(): void
    {
        $phpFiles = glob($this->projectRoot . '*.php');

        foreach ($phpFiles as $file) {
            $content = file_get_contents($file);

            preg_match_all('/echo\s+.*\$_(GET|POST|REQUEST)/i', $content, $matches);

            foreach ($matches[0] as $echoStatement) {
                $this->assertMatchesRegularExpression(
                    '/htmlspecialchars|htmlentities/i',
                    $echoStatement,
                    'Potential XSS vulnerability in ' . basename($file)
                );
            }
        }

        $this->assertTrue(true);
    }
}
