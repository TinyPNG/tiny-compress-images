<?php

require_once(dirname(__FILE__) . "/TinyTestCase.php");

class Tiny_Compress_Client_Test extends TinyTestCase {
    protected $php_mock;

    public function setUp() {
        parent::setUp();
        $this->php_mock = \Mockery::mock('alias:Tiny_PHP');
        $this->php_mock->shouldReceive('client_library_supported')->andReturn(true);
    }

    public function testShouldReturnCompressor() {
        $compressor = Tiny_Compress::create('api1234');
        $this->assertInstanceOf('Tiny_Compress', $compressor);
    }

    public function testShouldReturnClientCompressorByDefault() {
        $compressor = Tiny_Compress::create('api1234');
        $this->assertInstanceOf('Tiny_Compress_Client', $compressor);
    }
}
