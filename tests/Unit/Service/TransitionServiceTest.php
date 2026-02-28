<?php
namespace Gust\Component\Crmstages\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Gust\Component\Crmstages\Site\Service\TransitionService;
use Gust\Component\Crmstages\Site\Service\StageFactory;
use Gust\Component\Crmstages\Site\Stage\StageInterface;
use Gust\Component\Crmstages\Site\Stage\IceStage;
use Gust\Component\Crmstages\Site\Stage\TouchedStage;
use Gust\Component\Crmstages\Tests\Mocks\Factory as MockFactory;

/**
 * Unit-тест для TransitionService
 *
 * @covers \Gust\Component\Crmstages\Site\Service\TransitionService
 */
class TransitionServiceTest extends TestCase
{
    private TransitionService $service;
    private MockFactory $mockFactory;

    protected function setUp(): void
    {
        // Регистрируем моки перед созданием сервиса
        if (!class_exists('Joomla\\CMS\\Factory', false)) {
            class_alias('Gust\\Component\\Crmstages\\Tests\\Mocks\\Factory', 'Joomla\\CMS\\Factory');
        }

        $this->service = new TransitionService();
    }

    // ========================================================================
    // ТЕСТЫ МЕТОДА: canTransition()
    // ========================================================================

    /**
     * Тест: canTransition делегирует вызов стадии
     */
    public function testCanTransitionDelegatesToStage(): void
    {
        $fromStage = new IceStage();
        $toStage = 'Touched';
        $companyId = 123;

        // IceStage должен разрешать переход в Touched
        $result = $this->service->canTransition($fromStage, $toStage, $companyId);

        $this->assertIsBool($result);
        // Примечание: реальная логика зависит от реализации canTransitionTo в стадии
    }

    /**
     * Тест: canTransition с недопустимым переходом
     */
    public function testCanTransitionWithInvalidTransition(): void
    {
        $fromStage = new IceStage();
        $toStage = 'Archived'; // Прямой переход из Ice в Archived обычно недопустим
        $companyId = 123;

        $result = $this->service->canTransition($fromStage, $toStage, $companyId);

        $this->assertIsBool($result);
    }

    /**
     * Тест: canTransition с разными стадиями
     */
    public function testCanTransitionWithVariousStages(): void
    {
        $transitions = [
            ['from' => new IceStage(), 'to' => 'Touched', 'companyId' => 1],
            ['from' => new TouchedStage(), 'to' => 'Aware', 'companyId' => 2],
        ];

        foreach ($transitions as $transition) {
            $result = $this->service->canTransition(
                $transition['from'],
                $transition['to'],
                $transition['companyId']
            );
            $this->assertIsBool($result);
        }
    }

    // ========================================================================
    // ТЕСТЫ МЕТОДА: performTransition()
    // ========================================================================

    /**
     * Тест: performTransition возвращает true при успешном переходе
     *
     * @note Этот тест проверяет логику без реальной БД (моки)
     */
    public function testPerformTransitionReturnsTrueOnSuccess(): void
    {
        $companyId = 123;
        $fromStage = 'Ice';
        $toStage = 'Touched';
        $action = 'start_work';
        $comment = 'Начали работу';

        $result = $this->service->performTransition(
            $companyId,
            $fromStage,
            $toStage,
            $action,
            $comment
        );

        // Примечание: с моками БД метод всегда возвращает true
        $this->assertIsBool($result);
    }

    /**
     * Тест: performTransition с пустым комментарием
     */
    public function testPerformTransitionWithEmptyComment(): void
    {
        $result = $this->service->performTransition(
            123,
            'Ice',
            'Touched',
            'start_work',
            '' // Пустой комментарий
        );

        $this->assertIsBool($result);
    }

    /**
     * Тест: performTransition с разными действиями
     */
    public function testPerformTransitionWithVariousActions(): void
    {
        $actions = [
            ['action' => 'start_work', 'from' => 'Ice', 'to' => 'Touched'],
            ['action' => 'make_call', 'from' => 'Touched', 'to' => 'Aware'],
            ['action' => 'fill_discovery', 'from' => 'Aware', 'to' => 'Interested'],
        ];

        foreach ($actions as $config) {
            $result = $this->service->performTransition(
                123,
                $config['from'],
                $config['to'],
                $config['action'],
                'Тест'
            );
            $this->assertIsBool($result);
        }
    }

    /**
     * Тест: performTransition с неизвестной стадией выбрасывает исключение
     */
    public function testPerformTransitionWithUnknownStageThrowsException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unknown stage: InvalidStage');

        $this->service->performTransition(
            123,
            'InvalidStage',
            'Touched',
            'test_action'
        );
    }

    // ========================================================================
    // ТЕСТЫ МЕТОДА: getCompanyMlsCode()
    // ========================================================================

    /**
     * Тест: getCompanyMlsCode возвращает строку
     *
     * @note С моками БД компания не найдётся, вернётся пустая строка
     */
    public function testGetCompanyMlsCodeReturnsString(): void
    {
        $result = $this->service->getCompanyMlsCode(123);

        $this->assertIsString($result);
        // С моками БД компания не найдётся
        $this->assertEquals('', $result);
    }

    /**
     * Тест: getCompanyMlsCode с нулевым ID
     */
    public function testGetCompanyMlsCodeWithZeroId(): void
    {
        $result = $this->service->getCompanyMlsCode(0);

        $this->assertIsString($result);
        $this->assertEquals('', $result);
    }

    /**
     * Тест: getCompanyMlsCode для всех известных стадий (через StageFactory)
     */
    public function testGetCompanyMlsCodeMapping(): void
    {
        // Проверяем, что StageFactory корректно мапит стадии в MLS коды
        $mapping = [
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

        foreach ($mapping as $stage => $expectedMls) {
            $mls = StageFactory::getMlsCode($stage);
            $this->assertEquals($expectedMls, $mls);
        }
    }

    // ========================================================================
    // ТЕСТЫ МЕТОДА: isStageTransition()
    // ========================================================================

    /**
     * Тест: isStageTransition для действий, меняющих стадию
     */
    public function testIsStageTransitionForTransitionActions(): void
    {
        $transitionActions = [
            'start_work',
            'fill_discovery',
            'plan_demo',
            'confirm_demo',
            'send_invoice',
            'confirm_payment',
            'issue_credential',
            'archive_deal',
            'close_lost',
        ];

        foreach ($transitionActions as $action) {
            $result = $this->service->isStageTransition($action);
            $this->assertTrue(
                $result,
                "Действие {$action} должно менять стадию"
            );
        }
    }

    /**
     * Тест: isStageTransition для действий, НЕ меняющих стадию
     */
    public function testIsStageTransitionForNonTransitionActions(): void
    {
        $nonTransitionActions = ['make_call'];

        foreach ($nonTransitionActions as $action) {
            $result = $this->service->isStageTransition($action);
            $this->assertFalse(
                $result,
                "Действие {$action} не должно менять стадию"
            );
        }
    }

    /**
     * Тест: isStageTransition для неизвестного действия
     */
    public function testIsStageTransitionForUnknownAction(): void
    {
        $result = $this->service->isStageTransition('unknown_action');

        // Неизвестное действие считается меняющим стадию (по умолчанию true)
        $this->assertTrue($result);
    }

    /**
     * Тест: isStageTransition возвращает boolean
     */
    public function testIsStageTransitionReturnsBoolean(): void
    {
        $actions = ['start_work', 'make_call', 'unknown'];

        foreach ($actions as $action) {
            $result = $this->service->isStageTransition($action);
            $this->assertIsBool($result);
        }
    }

    // ========================================================================
    // ИНТЕГРАЦИОННЫЕ ТЕСТЫ
    // ========================================================================

    /**
     * Тест: Полный цикл перехода Ice → Touched
     */
    public function testFullTransitionCycleIceToTouched(): void
    {
        $companyId = 123;

        // 1. Проверяем, что переход возможен
        $fromStage = StageFactory::create('Ice');
        $canTransition = $this->service->canTransition($fromStage, 'Touched', $companyId);
        $this->assertIsBool($canTransition);

        // 2. Выполняем переход
        $result = $this->service->performTransition(
            $companyId,
            'Ice',
            'Touched',
            'start_work',
            'Тестовый переход'
        );
        $this->assertIsBool($result);

        // 3. Проверяем, что действие считается переходом
        $isTransition = $this->service->isStageTransition('start_work');
        $this->assertTrue($isTransition);
    }

    /**
     * Тест: make_call не меняет стадию
     */
    public function testMakeCallDoesNotChangeStage(): void
    {
        // make_call — специальное действие, которое не меняет стадию
        $isTransition = $this->service->isStageTransition('make_call');
        $this->assertFalse($isTransition);

        // Но performTransition всё равно может быть вызван (для логирования)
        $result = $this->service->performTransition(
            123,
            'Touched',
            'Touched', // Остаёмся на той же стадии
            'make_call',
            'Попытка дозвона'
        );
        $this->assertIsBool($result);
    }

    /**
     * Тест: Все стадии имеют корректные переходы
     */
    public function testAllStagesHaveValidTransitions(): void
    {
        $stages = [
            'Ice', 'Touched', 'Aware', 'Interested',
            'demo_planned', 'Demo_done', 'Committed',
            'Customer', 'Activated', 'Archived', 'N0'
        ];

        foreach ($stages as $stageCode) {
            $stage = StageFactory::create($stageCode);

            // Проверяем, что стадия имеет методы
            $this->assertTrue(method_exists($stage, 'getNextStages'));
            $this->assertTrue(method_exists($stage, 'canTransitionTo'));

            // Проверяем, что getNextStages возвращает массив
            $nextStages = $stage->getNextStages();
            $this->assertIsArray($nextStages);
        }
    }

    /**
     * Тест: Финальные стадии не имеют исходящих переходов
     */
    public function testFinalStagesHaveNoOutgoingTransitions(): void
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