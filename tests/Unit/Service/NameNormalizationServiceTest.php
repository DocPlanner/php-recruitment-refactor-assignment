<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Exception\InvalidNameException;
use App\Service\NameNormalizationService;
use PHPUnit\Framework\TestCase;

class NameNormalizationServiceTest extends TestCase
{
    private NameNormalizationService $service;

    protected function setUp(): void
    {
        $this->service = new NameNormalizationService();
    }

    /**
     * @dataProvider nameProvider
     */
    public function testNormalizeName(string $input, string $expected): void
    {
        $result = $this->service->normalize($input);
        
        $this->assertEquals($expected, $result);
    }

    public static function nameProvider(): array
    {
        return [
            'lowercase name' => ['john doe', 'John Doe'],
            'uppercase name' => ['JANE SMITH', 'Jane Smith'],
            'mixed case name' => ['mArY jOnEs', 'Mary Jones'],
            'irish surname lowercase' => ["mary o'connor", "Mary O'Connor"],
            'irish surname mixed case' => ["patrick O'Brien", "Patrick O'Brien"],
            'irish surname with apostrophe' => ["sean o'malley", "Sean O'Malley"],
            'already normalized' => ['John Doe', 'John Doe'],
            'three names' => ['mary jane watson', 'Mary Jane Watson'],
        ];
    }

    public function testThrowsExceptionForEmptyName(): void
    {
        $this->expectException(InvalidNameException::class);
        $this->expectExceptionMessage('Doctor name cannot be empty');
        
        $this->service->normalize('');
    }

    public function testThrowsExceptionForWhitespaceOnlyName(): void
    {
        $this->expectException(InvalidNameException::class);
        $this->expectExceptionMessage('Doctor name cannot be empty');
        
        $this->service->normalize('   ');
    }

    public function testThrowsExceptionForSingleName(): void
    {
        $this->expectException(InvalidNameException::class);
        $this->expectExceptionMessage('Full name must contain both first and last name');
        
        $this->service->normalize('John');
    }
}
