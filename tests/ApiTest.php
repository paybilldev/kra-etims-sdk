<?php

use PHPUnit\Framework\TestCase;
use KraEtimsSdk\Services\ApiService;
use KraEtimsSdk\Exceptions\AuthenticationException;

class ApiTest extends TestCase
{
    /** @var ApiService&\PHPUnit\Framework\MockObject\MockObject */
    private $apiService;

    protected function setUp(): void
    {
        $this->apiService = $this->getMockBuilder(ApiService::class)
            ->setConstructorArgs(['https://example.com', 'user', 'pass'])
            ->onlyMethods(['curlRequest'])
            ->getMock();
    }

    public function testIsTokenValidReturnsFalseWhenNoToken()
    {
        $this->apiService->token = null;
        $this->apiService->tokenExpiry = null;
        $this->assertFalse($this->apiService->isTokenValid());
    }

    public function testIsTokenValidReturnsFalseWhenTokenExpired()
    {
        $this->apiService->token = 'token';
        $this->apiService->tokenExpiry = time() - 10; // expired
        $this->assertFalse($this->apiService->isTokenValid());
    }

    public function testIsTokenValidReturnsTrueWhenTokenValid()
    {
        $this->apiService->token = 'token';
        $this->apiService->tokenExpiry = time() + 600; // valid for 10 more minutes
        $this->assertTrue($this->apiService->isTokenValid());
    }

    // TODO: add more tests
}
