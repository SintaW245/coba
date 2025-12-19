<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../config.php';

class FileTypeTest extends TestCase
{
    public function testFileTypePdf()
    {
        $this->assertEquals('pdf', getFileType('file.pdf'));
    }

    public function testFileTypeJpg()
    {
        $this->assertEquals('jpg', getFileType('foto.jpg'));
    }
}
