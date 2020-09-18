<?php

declare(strict_types=1);

namespace AndrewSvirin\Ebics\Tests\Services\KeyRingManager;

use AndrewSvirin\Ebics\Models\KeyRing;
use AndrewSvirin\Ebics\Services\KeyRingManager;
use PHPUnit\Framework\TestCase;

class LoadKeyRingTest extends TestCase
{
    public function testNonExistingFile(): void
    {
        $sUT      = new KeyRingManager('nop', 'test');
        $expected = (new KeyRing());
        $expected->setPassword('test');

        self::assertEquals($expected, $sUT->loadKeyRing());
    }

    public function testEmptyFile(): void
    {
        $sUT = new KeyRingManager(__DIR__ . '/empty.json', 'test');

        $expected = (new KeyRing());
        $expected->setPassword('test');

        self::assertEquals($expected, $sUT->loadKeyRing());
    }

    public function testOk(): void
    {
        $sUT = new KeyRingManager(__DIR__ . '/ok.json', 'test');

        $expected = (new KeyRing());
        $expected->setPassword('test');

        self::assertEquals($expected, $sUT->loadKeyRing());
    }
}
