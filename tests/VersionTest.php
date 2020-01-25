<?php
declare(strict_types=1);

namespace geldek\tests;

use PHPUnit\Framework\TestCase;
use geldek\Version;

class VersionTest extends TestCase
{
    /**
     * @dataProvider parseValidProvider
     */
    public function testParseFromValidVersion($version): void
    {
        $this->assertInstanceOf(Version::class, Version::parse($version));
    }

    /**
     * @dataProvider parseValidProvider
     */
    public function testTryParseFromValidVersion($version): void
    {
        $v = null;
        $result = Version::tryParse($version, $v);
        $this->assertTrue($result);
        $this->assertInstanceOf(Version::class, $v);
    }

    public function parseValidProvider()
    {
        return [
            [new Version('5.4.2')],
            ['1.2'],
            ['3.4.5'],
            ['6.7.8.9'],
            ['864654115.215.5483484.35484'],
        ];
    }
    
    /**
     * @dataProvider parseInvalidProvider
     */
    public function testParseFromInvalidStringVersion($version): void
    {
        $this->expectException(\Exception::class);
        Version::parse($version);
    }
    
    /**
     * @dataProvider parseInvalidProvider
     */
    public function testTryParseFromInvalidStringVersion($version): void
    {
        $result = null;
        $this->assertFalse(Version::tryParse($version, $result));
    }

    public function parseInvalidProvider()
    {
        return [
            ['1'],
            ['6.7.8.9.10'],
            ['1.3.4.a'],
            ['howno'],
            ['484844488484343435121484843434343844384'],
        ];
    }
    
    public function equalsProvider()
    {
        return [
            [1, 1],
            ["1.0", "1.0.0"],
            ["1.2", "1.2.0.0"]
        ];
    }

    /**
     * @dataProvider equalsProvider
     */
    public function testEqualsTrue($v1, $v2): void
    {
        $version1 = new Version($v1);
        $version2 = new Version($v2);
        $this->assertTrue($version1->equals($version2));
    }
    
    /**
     * @dataProvider notEqualsProvider
     */
    public function testEqualsFalse($v1, $v2): void
    {
        $version1 = new Version($v1);
        $version2 = new Version($v2);
        $this->assertFalse($version1->equals($version2));
    }
    
    public function notEqualsProvider()
    {
        return [
            [1, 2],
            ["1.0", "1.0.1"],
            ["1.2", "1.2.0.1"],
            ["1.4", "1.2.35.15"],
            [new Version(1, 2, 3, 4), new Version(4, 3, 2, 1)],
        ];
    }
    
    /**
     * @dataProvider parseValidProvider
     */
    public function testVersionGetters($version): void
    {
        $result = new Version($version);
        $this->assertTrue($result->getMajor() >= 0);
        $this->assertTrue($result->getMinor() >= 0);
        $this->assertTrue($result->getBuild() >= 0);
        $this->assertTrue($result->getRevision() >= 0);
    }
    
    /**
     * @dataProvider notEqualsProvider
     */
    public function testCompareTo($v1, $v2): void
    {
        $version1 = new Version($v1);
        $version2 = new Version($v2);
        $compare = $version1->compareTo($version2);
        $this->assertContains($compare, [-1, 0, 1]);
    }
    
    public function testCompareToVersion() {
        $v1 = new Version(1);
        $v2 = new Version('2.0');
        $v3 = new Version('1.0.0.0');
        $this->assertEquals($v1->compareTo($v2), -1);
        $this->assertEquals($v2->compareTo($v1), 1);
        $this->assertEquals($v3->compareTo($v1), 0);
    }

    public function testToString() {
        $version = new Version(1);
        $this->assertTrue($version->toString(-1) == '1.0.0.0');
        $this->assertTrue($version->toString(1) == '1');
        $this->assertTrue($version->toString(2) == '1.0');
        $this->assertTrue($version->toString(3) == '1.0.0');
        $this->assertTrue($version->toString(4) == '1.0.0.0');
        $this->assertTrue($version->toString(5) == '1.0.0.0');
    }
}