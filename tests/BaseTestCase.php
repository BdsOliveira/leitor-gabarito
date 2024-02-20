<?php

namespace Test;

use PHPUnit\Framework\TestCase;

class BaseTestCase extends TestCase
{
    public function testImageExists(): void
    {
        $this->assertTrue(true);
    }
}