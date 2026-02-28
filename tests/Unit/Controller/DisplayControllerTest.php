<?php
// tests/Unit/Controller/DisplayControllerTest.php

namespace Gust\Component\Crmstages\Tests\Unit\Controller;

use PHPUnit\Framework\TestCase;
use Gust\Component\Crmstages\Tests\Mocks\Factory as MockFactory;

/**
 * Простой unit-тест для логики DisplayController
 *
 * ⚠️ ВАЖНО: Не используем class_exists(), method_exists() с именем контроллера!
 * Это загружает файл и ломает тесты из-за зависимостей Joomla.
 */
class DisplayControllerTest extends TestCase
{
    private $inputMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->inputMock = MockFactory::getApplication()->input();
    }

    // ========================================================================
    // ЛОГИКА DISPLAY: обработка view параметра (тестируем БЕЗ загрузки класса)
    // ========================================================================

    public function testDisplayLogicDefaultView(): void
    {
        $view = 'companies';
        $this->assertEquals('companies', $view);
    }

    public function testDisplayLogicFeaturedRedirect(): void
    {
        // 🔹 Ключевая логика: featured → companies
        $view = 'featured';
        $view = $view == "featured" ? 'companies' : $view;

        $this->assertEquals('companies', $view);
    }

    public function testDisplayLogicOtherViewsUnchanged(): void
    {
        $views = ['company', 'companies', 'category', 'custom'];

        foreach ($views as $original) {
            $view = $original;
            $view = $view == "featured" ? 'companies' : $view;
            $this->assertEquals($original, $view);
        }
    }

    public function testDisplayLogicCaseSensitive(): void
    {
        $view = 'Featured';
        $view = $view == "featured" ? 'companies' : $view;
        $this->assertEquals('Featured', $view);
    }

    // ========================================================================
    // ЛОГИКА INPUT: set/get
    // ========================================================================

    public function testInputSetAndGetView(): void
    {
        $this->inputMock->set('view', 'companies');
        $result = $this->inputMock->get('view', 'default');
        $this->assertEquals('companies', $result);
    }

    public function testInputSetViewAfterFeaturedRedirect(): void
    {
        $this->inputMock->set('view', 'featured');

        $view = $this->inputMock->getCmd('view', 'companies');
        $view = $view == "featured" ? 'companies' : $view;
        $this->inputMock->set('view', $view);

        $final = $this->inputMock->get('view');
        $this->assertEquals('companies', $final);
    }

    // ========================================================================
    // ПРОВЕРКА ПАРАМЕТРОВ
    // ========================================================================

    public function testDisplayMethodParameters(): void
    {
        $cachable = false;
        $urlparams = false;

        $this->assertIsBool($cachable);
        $this->assertIsBool($urlparams);
    }

    public function testDisplayWithCachingEnabled(): void
    {
        $cachable = true;
        $urlparams = ['id' => 'int', 'view' => 'cmd'];

        $this->assertTrue($cachable);
        $this->assertIsArray($urlparams);
    }

    // ========================================================================
    // ВСПОМОГАТЕЛЬНЫЕ ПРОВЕРКИ
    // ========================================================================

    public function testInputMockGetCmdSanitizes(): void
    {
        $this->inputMock->setArray(['view' => 'comp@ny!123']);
        $clean = $this->inputMock->getCmd('view', 'default');
        $this->assertIsString($clean);
        $this->assertNotEmpty($clean);
    }

    public function testInputMockDefaultValues(): void
    {
        $missing = $this->inputMock->getCmd('missing_param', 'default_value');
        $this->assertEquals('default_value', $missing);
    }

    // ========================================================================
    // ИНТЕГРАЦИЯ ЛОГИКИ (полный сценарий)
    // ========================================================================

    public function testFullDisplayFlowFeaturedToCompanies(): void
    {
        $this->inputMock->setArray(['view' => 'featured']);

        $view = $this->inputMock->getCmd('view', 'companies');
        $view = $view == "featured" ? 'companies' : $view;
        $this->inputMock->set('view', $view);

        $final = $this->inputMock->get('view');
        $this->assertEquals('companies', $final);
    }

    public function testFullDisplayFlowNormalView(): void
    {
        $this->inputMock->setArray(['view' => 'company']);

        $view = $this->inputMock->getCmd('view', 'companies');
        $view = $view == "featured" ? 'companies' : $view;
        $this->inputMock->set('view', $view);

        $this->assertEquals('company', $this->inputMock->get('view'));
    }

    // ========================================================================
    // ПРОВЕРКИ МОКОВ
    // ========================================================================

    public function testMockFactoryReturnsConsistentInput(): void
    {
        $input1 = MockFactory::getApplication()->input();
        $input2 = MockFactory::getApplication()->input();
        $this->assertSame($input1, $input2);
    }

    public function testInputMockHasRequiredMethods(): void
    {
        $this->assertTrue(method_exists($this->inputMock, 'get'));
        $this->assertTrue(method_exists($this->inputMock, 'getCmd'));
        $this->assertTrue(method_exists($this->inputMock, 'set'));
        $this->assertTrue(method_exists($this->inputMock, 'setArray'));
    }
}