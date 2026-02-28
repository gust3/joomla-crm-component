<?php
namespace Gust\Component\Crmstages\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Gust\Component\Crmstages\Site\Service\StageFactory;
use Gust\Component\Crmstages\Site\Stage\StageInterface;

/**
 * Unit-тест для StageFactory
 *
 * @covers \Gust\Component\Crmstages\Site\Service\StageFactory
 */
class StageFactoryTest extends TestCase
{
    /**
     * Полный список всех стадий
     */
    private array $allStages = [
        'Ice', 'Touched', 'Aware', 'Interested',
        'demo_planned', 'Demo_done', 'Committed',
        'Customer', 'Activated', 'Archived', 'N0'
    ];

    // ========================================================================
    // ТЕСТЫ МЕТОДА: create()
    // ========================================================================

    /**
     * Тест: Все стадии создаются через Factory
     */
    public function testCreateAllStages(): void
    {
        foreach ($this->allStages as $stageCode) {
            $stage = StageFactory::create($stageCode);

            $this->assertInstanceOf(
                StageInterface::class,
                $stage,
                "Стадия {$stageCode} должна реализовывать StageInterface"
            );
        }
    }

    /**
     * Тест: Неизвестная стадия выбрасывает исключение
     */
    public function testCreateUnknownStageThrowsException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unknown stage: InvalidStage');

        StageFactory::create('InvalidStage');
    }

    /**
     * Тест: Пустой код стадии выбрасывает исключение
     */
    public function testCreateEmptyStageCodeThrowsException(): void
    {
        $this->expectException(\Exception::class);

        StageFactory::create('');
    }

    /**
     * Тест: Созданная стадия имеет имя
     */
    public function testCreatedStageHasName(): void
    {
        foreach ($this->allStages as $stageCode) {
            $stage = StageFactory::create($stageCode);

            $this->assertIsString(
                $stage->getName(),
                "Стадия {$stageCode} должна иметь имя (string)"
            );
            $this->assertNotEmpty(
                $stage->getName(),
                "Стадия {$stageCode} не должна иметь пустое имя"
            );
        }
    }

    // ========================================================================
    // ТЕСТЫ МЕТОДА: getMlsCode()
    // ========================================================================

    /**
     * Тест: MLS коды возвращаются для всех стадий
     */
    public function testGetMlsCodeForAllStages(): void
    {
        foreach ($this->allStages as $stageCode) {
            $mlsCode = StageFactory::getMlsCode($stageCode);

            $this->assertIsString(
                $mlsCode,
                "MLS код для {$stageCode} должен быть строкой"
            );
            $this->assertNotEmpty(
                $mlsCode,
                "MLS код для {$stageCode} не должен быть пустым"
            );
        }
    }

    /**
     * Тест: MLS коды уникальны для всех стадий
     */
    public function testMlsCodesAreUnique(): void
    {
        $mlsCodes = [];

        foreach ($this->allStages as $stageCode) {
            $mlsCode = StageFactory::getMlsCode($stageCode);

            $this->assertNotContains(
                $mlsCode,
                $mlsCodes,
                "MLS код {$mlsCode} дублируется для стадии {$stageCode}"
            );
            $mlsCodes[] = $mlsCode;
        }
    }

    /**
     * Тест: MLS коды соответствуют ожидаемым значениям
     */
    public function testMlsCodesMatchExpectedValues(): void
    {
        $expectedMapping = [
            'Ice' => 'C0',
            'Touched' => 'C1',
            'Aware' => 'C2',
            'Interested' => 'W1',
            'demo_planned' => 'W2',
            'Demo_done' => 'W3',
            'Committed' => 'W4',
            'Customer' => 'H2',
            'Activated' => 'H3',
            'Archived' => 'H4',
            'N0' => 'N0',
        ];

        foreach ($expectedMapping as $stage => $expectedMls) {
            $mlsCode = StageFactory::getMlsCode($stage);
            $this->assertEquals(
                $expectedMls,
                $mlsCode,
                "MLS код для {$stage} должен быть {$expectedMls}"
            );
        }
    }

    /**
     * Тест: MLS код для неизвестной стадии выбрасывает исключение
     */
    public function testGetMlsCodeForUnknownStageThrowsException(): void
    {
        $this->expectException(\Exception::class);

        StageFactory::getMlsCode('UnknownStage');
    }

    // ========================================================================
    // ТЕСТЫ МЕТОДА: getAllStages()
    // ========================================================================

    /**
     * Тест: getAllStages возвращает массив
     */
    public function testGetAllStagesReturnsArray(): void
    {
        $stages = StageFactory::getAllStages();

        $this->assertIsArray($stages);
    }

    /**
     * Тест: getAllStages возвращает все стадии
     */
    public function testGetAllStagesReturnsAllStages(): void
    {
        $stages = StageFactory::getAllStages();

        $this->assertCount(count($this->allStages), $stages);

        foreach ($this->allStages as $stageCode) {
            $this->assertContains(
                $stageCode,
                $stages,
                "Стадия {$stageCode} должна быть в списке всех стадий"
            );
        }
    }

    /**
     * Тест: getAllStages не содержит дубликатов
     */
    public function testGetAllStagesHasNoDuplicates(): void
    {
        $stages = StageFactory::getAllStages();
        $uniqueStages = array_unique($stages);

        $this->assertCount(
            count($stages),
            $uniqueStages,
            'Список стадий не должен содержать дубликатов'
        );
    }

    // ========================================================================
    // ТЕСТЫ КОНТРАКТА StageInterface
    // ========================================================================

    /**
     * Тест: Все стадии реализуют StageInterface
     */
    public function testAllStagesImplementStageInterface(): void
    {
        foreach ($this->allStages as $stageCode) {
            $stage = StageFactory::create($stageCode);

            $this->assertInstanceOf(
                StageInterface::class,
                $stage,
                "Стадия {$stageCode} должна реализовывать StageInterface"
            );
        }
    }

    /**
     * Тест: Все стадии имеют метод getNextStages()
     */
    public function testAllStagesHaveGetNextStagesMethod(): void
    {
        foreach ($this->allStages as $stageCode) {
            $stage = StageFactory::create($stageCode);

            $this->assertTrue(
                method_exists($stage, 'getNextStages'),
                "Стадия {$stageCode} должна иметь метод getNextStages()"
            );

            $nextStages = $stage->getNextStages();
            $this->assertIsArray(
                $nextStages,
                "getNextStages() для {$stageCode} должен возвращать массив"
            );
        }
    }

    /**
     * Тест: Все стадии имеют метод getRequiredEvents()
     */
    public function testAllStagesHaveGetRequiredEventsMethod(): void
    {
        foreach ($this->allStages as $stageCode) {
            $stage = StageFactory::create($stageCode);

            $this->assertTrue(
                method_exists($stage, 'getRequiredEvents'),
                "Стадия {$stageCode} должна иметь метод getRequiredEvents()"
            );

            $events = $stage->getRequiredEvents();
            $this->assertIsArray(
                $events,
                "getRequiredEvents() для {$stageCode} должен возвращать массив"
            );
        }
    }

    /**
     * Тест: Все стадии имеют метод getDescription()
     */
    public function testAllStagesHaveGetDescriptionMethod(): void
    {
        foreach ($this->allStages as $stageCode) {
            $stage = StageFactory::create($stageCode);

            $this->assertTrue(
                method_exists($stage, 'getDescription'),
                "Стадия {$stageCode} должна иметь метод getDescription()"
            );

            $description = $stage->getDescription();
            $this->assertIsString(
                $description,
                "getDescription() для {$stageCode} должен возвращать строку"
            );
        }
    }

    /**
     * Тест: Все стадии имеют метод getAvailableActions()
     */
    public function testAllStagesHaveGetAvailableActionsMethod(): void
    {
        foreach ($this->allStages as $stageCode) {
            $stage = StageFactory::create($stageCode);

            $this->assertTrue(
                method_exists($stage, 'getAvailableActions'),
                "Стадия {$stageCode} должна иметь метод getAvailableActions()"
            );
        }
    }

    // ========================================================================
    // ТЕСТЫ ЦЕЛОСТНОСТИ ВОРОНКИ
    // ========================================================================

    /**
     * Тест: Полный цикл переходов (воронка)
     */
    public function testFullFunnelWorkflow(): void
    {
        $funnel = [
            'Ice' => 'Touched',
            'Touched' => 'Aware',
            'Aware' => 'Interested',
            'Interested' => 'demo_planned',
            'demo_planned' => 'Demo_done',
            'Demo_done' => 'Committed',
            'Committed' => 'Customer',
            'Customer' => 'Activated',
            'Activated' => 'Archived',
        ];

        foreach ($funnel as $from => $to) {
            $stage = StageFactory::create($from);
            $nextStages = $stage->getNextStages();

            $this->assertContains(
                $to,
                $nextStages,
                "Стадия {$from} должна иметь переход в {$to}"
            );
        }
    }

    /**
     * Тест: Финальные стадии не имеют переходов
     */
    public function testFinalStagesHaveNoNextStages(): void
    {
        $finalStages = ['Archived', 'N0'];

        foreach ($finalStages as $stageCode) {
            $stage = StageFactory::create($stageCode);
            $nextStages = $stage->getNextStages();

            $this->assertEmpty(
                $nextStages,
                "Финальная стадия {$stageCode} не должна иметь переходов"
            );
        }
    }
}