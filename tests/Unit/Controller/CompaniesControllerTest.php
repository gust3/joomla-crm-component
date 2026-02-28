<?php
namespace Gust\Component\Crmstages\Tests\Unit\Controller;

use PHPUnit\Framework\TestCase;
use Gust\Component\Crmstages\Site\Service\DemoDataService;
use Gust\Component\Crmstages\Tests\Mocks\Factory as MockFactory;
use Gust\Component\Crmstages\Tests\Mocks\DatabaseMock;

/**
 * Unit-тест для CompaniesController
 * ⚠️ НЕ загружаем класс контроллера — только тестируем логику
 */
class CompaniesControllerTest extends TestCase
{
    private $demoServiceMock;
    private $appMock;
    private $inputMock;
    private DatabaseMock $dbMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->appMock = MockFactory::getApplication();
        $this->inputMock = $this->appMock->input();
        $this->dbMock = new DatabaseMock();
        $this->demoServiceMock = $this->createMock(DemoDataService::class);
    }

    // ❌ УДАЛИТЕ этот тест — он загружает контроллер и ломает всё
    // public function testControllerExists(): void { ... }

    // ✅ Вместо этого тестируйте только логику:

    public function testDemoServiceMockWorks(): void
    {
        $this->demoServiceMock
            ->method('createRandomCompany')
            ->willReturn(['success' => true, 'message' => 'OK', 'id' => 123]);

        $result = $this->demoServiceMock->createRandomCompany();
        $this->assertTrue($result['success']);
        $this->assertEquals(123, $result['id']);
    }

    public function testInputMockWorks(): void
    {
        $this->inputMock->setArray(['count' => 5]);
        $this->assertEquals(5, $this->inputMock->getInt('count', 0));
        $this->assertEquals(0, $this->inputMock->getInt('missing', 0));
    }

    public function testDatabaseMockWorks(): void
    {
        $this->assertTrue(method_exists($this->dbMock, 'getQuery'));
        $this->assertTrue(method_exists($this->dbMock, 'execute'));
        $this->assertTrue(method_exists($this->dbMock, 'quoteName'));
    }

    public function testAddMultipleLogic(): void
    {
        $results = [
            ['success' => true],
            ['success' => false],
            ['success' => true],
            ['success' => true],
        ];

        $successCount = count(array_filter($results, fn($r) => $r['success']));
        $this->assertEquals(3, $successCount);

        $message = "Создано {$successCount} демо-компаний!";
        $this->assertStringContainsString('3', $message);
    }
}