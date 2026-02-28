<?php
namespace Gust\Component\Crmstages\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Gust\Component\Crmstages\Site\Service\DemoDataService;

/**
 * Unit-тест для DemoDataService
 *
 * @covers \Gust\Component\Crmstages\Site\Service\DemoDataService
 */
class DemoDataServiceTest extends TestCase
{
    private DemoDataService $service;

    protected function setUp(): void
    {
        $this->service = new DemoDataService();
    }

    /**
     * Тест: Создаётся компания с валидным названием
     */
    public function testCreateRandomCompanyReturnsValidName(): void
    {
        $result = $this->service->createRandomCompany();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('stage', $result);
        $this->assertArrayHasKey('message', $result);

        // Название должно содержать ООО, АО или ИП
        $this->assertMatchesRegularExpression('/(ООО|АО|ИП)/u', $result['name']);
    }

    /**
     * Тест: Стадия из допустимого списка
     */
    public function testCreateRandomCompanyReturnsValidStage(): void
    {
        $validStages = [
            'Ice', 'Touched', 'Aware', 'Interested',
            'demo_planned', 'Demo_done', 'Committed'
        ];

        $result = $this->service->createRandomCompany();

        $this->assertContains($result['stage'], $validStages);
    }

    /**
     * Тест: Успешный результат создания
     */
    public function testCreateRandomCompanyReturnsSuccess(): void
    {
        $result = $this->service->createRandomCompany();

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('id', $result);
        $this->assertIsInt($result['id']);
        $this->assertGreaterThan(0, $result['id']);
    }

    /**
     * Тест: Создание нескольких компаний
     */
    public function testCreateMultipleCompaniesReturnsCorrectCount(): void
    {
        $count = 5;
        $results = $this->service->createMultipleCompanies($count);

        $this->assertIsArray($results);
        $this->assertCount($count, $results);

        $successCount = count(array_filter($results, fn($r) => $r['success']));
        $this->assertEquals($count, $successCount);
    }

    /**
     * Тест: Создание 10 компаний
     */
    public function testCreateTenCompanies(): void
    {
        $results = $this->service->createMultipleCompanies(10);

        $this->assertCount(10, $results);

        foreach ($results as $result) {
            $this->assertTrue($result['success']);
            $this->assertMatchesRegularExpression('/(ООО|АО|ИП)/u', $result['name']);
        }
    }

    /**
     * Тест: Названия компаний уникальны (в пределах одной сессии)
     */
    public function testCompanyNamesAreVaried(): void
    {
        $names = [];
        for ($i = 0; $i < 20; $i++) {
            $result = $this->service->createRandomCompany();
            $names[] = $result['name'];
        }

        // Хотя бы 5 уникальных названий из 20
        $uniqueNames = array_unique($names);
        $this->assertGreaterThanOrEqual(5, count($uniqueNames));
    }

    /**
     * Тест: Стадии распределяются случайно
     */
    public function testStagesAreVaried(): void
    {
        $stages = [];
        for ($i = 0; $i < 30; $i++) {
            $result = $this->service->createRandomCompany();
            $stages[] = $result['stage'];
        }

        // Хотя бы 3 разные стадии из 30 компаний
        $uniqueStages = array_unique($stages);
        $this->assertGreaterThanOrEqual(3, count($uniqueStages));
    }

    /**
     * Тест: Сообщение содержит название компании
     */
    public function testMessageContainsCompanyName(): void
    {
        $result = $this->service->createRandomCompany();

        $this->assertStringContainsString($result['name'], $result['message']);
        $this->assertStringContainsString('создана', $result['message']);
    }
}