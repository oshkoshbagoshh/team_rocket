<?php
/**
 * Base test case that wires Brain Monkey in/out around each test.
 *
 * @package KanjavaBase
 */

declare( strict_types=1 );

namespace KanjavaBase\Tests;

use Brain\Monkey;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

abstract class TestCase extends PHPUnitTestCase {

	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();
	}

	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}
}
