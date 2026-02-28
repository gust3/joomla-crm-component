<?php
namespace Gust\Component\Crmstages\Tests\Unit\Stage;

use PHPUnit\Framework\TestCase;
use Gust\Component\Crmstages\Site\Service\StageFactory;
use Gust\Component\Crmstages\Site\Stage\StageInterface;
use Gust\Component\Crmstages\Site\Stage\IceStage;
use Gust\Component\Crmstages\Site\Stage\TouchedStage;
use Gust\Component\Crmstages\Site\Stage\AwareStage;
use Gust\Component\Crmstages\Site\Stage\InterestedStage;
use Gust\Component\Crmstages\Site\Stage\DemoPlannedStage;
use Gust\Component\Crmstages\Site\Stage\DemoDoneStage;
use Gust\Component\Crmstages\Site\Stage\CommittedStage;
use Gust\Component\Crmstages\Site\Stage\CustomerStage;
use Gust\Component\Crmstages\Site\Stage\ActivatedStage;
use Gust\Component\Crmstages\Site\Stage\ArchivedStage;
use Gust\Component\Crmstages\Site\Stage\N0Stage;

/**
 * Unit-тест для всех стадий CRM
 *
 * @covers \Gust\Component\Crmstages\Site\Stage\IceStage
 * @covers \Gust\Component\Crmstages\Site\Stage\TouchedStage
 * @covers \Gust\Component\Crmstages\Site\Stage\AwareStage
 * @covers \Gust\Component\Crmstages\Site\Stage\InterestedStage
 * @covers \Gust\Component\Crmstages\Site\Stage\DemoPlannedStage
 * @covers \Gust\Component\Crmstages\Site\Stage\DemoDoneStage
 * @covers \Gust\Component\Crmstages\Site\Stage\CommittedStage
 * @covers \Gust\Component\Crmstages\Site\Stage\CustomerStage
 * @covers \Gust\Component\Crmstages\Site\Stage\ActivatedStage
 * @covers \Gust\Component\Crmstages\Site\Stage\ArchivedStage
 * @covers \Gust\Component\Crmstages\Site\Stage\N0Stage
 */
class AllStagesTest extends TestCase
{
    /**
     * Полный список всех стадий с классами
     */
    private array $stages = [
        'Ice' => IceStage::class,
        'Touched' => TouchedStage::class,
        'Aware' => AwareStage::class,
        'Interested' => InterestedStage::class,
        'demo_planned' => DemoPlannedStage::class,
        'Demo_done' => DemoDoneStage::class,
        'Committed' => CommittedStage::class,
        'Customer' => CustomerStage::class,
        'Activated' => ActivatedStage::class,
        'Archived' => ArchivedStage::class,
        'N0' => N0Stage::class,
    ];

    // ========================================================================
    // ОБЩИЕ ТЕСТЫ ДЛЯ ВСЕХ СТАДИЙ
    // ========================================================================

    /**
     * Тест: Все стадии создаются через конструктор
     */
    public function testAllStagesCanBeInstantiated(): void
    {
        foreach ($this->stages as $code => $class) {
            $stage = new $class();
            $this->assertInstanceOf(StageInterface::class, $stage);
        }
    }

    /**
     * Тест: Все стадии реализуют StageInterface
     */
    public function testAllStagesImplementStageInterface(): void
    {
        foreach ($this->stages as $code => $class) {
            $stage = new $class();
            $this->assertInstanceOf(StageInterface::class, $stage);
        }
    }

    /**
     * Тест: Все стадии имеют getName()
     */
    public function testAllStagesHaveGetName(): void
    {
        foreach ($this->stages as $code => $class) {
            $stage = new $class();
            $name = $stage->getName();

            $this->assertIsString($name);
            $this->assertNotEmpty($name);
            $this->assertEquals($code, $name);
        }
    }

    /**
     * Тест: Все стадии имеют getMlsCode()
     */
    public function testAllStagesHaveGetMlsCode(): void
    {
        foreach ($this->stages as $code => $class) {
            $stage = new $class();
            $mlsCode = $stage->getMlsCode();

            $this->assertIsString($mlsCode);
            $this->assertNotEmpty($mlsCode);
            $this->assertMatchesRegularExpression('/^[A-Z0-9]+$/', $mlsCode);
        }
    }

    /**
     * Тест: Все стадии имеют getNextStages()
     */
    public function testAllStagesHaveGetNextStages(): void
    {
        foreach ($this->stages as $code => $class) {
            $stage = new $class();
            $nextStages = $stage->getNextStages();

            $this->assertIsArray($nextStages);
        }
    }

    /**
     * Тест: Все стадии имеют getRequiredEvents()
     */
    public function testAllStagesHaveGetRequiredEvents(): void
    {
        foreach ($this->stages as $code => $class) {
            $stage = new $class();
            $events = $stage->getRequiredEvents();

            $this->assertIsArray($events);
        }
    }

    /**
     * Тест: Все стадии имеют getDescription()
     */
    public function testAllStagesHaveGetDescription(): void
    {
        foreach ($this->stages as $code => $class) {
            $stage = new $class();
            $description = $stage->getDescription();

            $this->assertIsString($description);
            $this->assertNotEmpty($description);
        }
    }

    /**
     * Тест: Все стадии имеют getAvailableActions()
     */
    public function testAllStagesHaveGetAvailableActions(): void
    {
        foreach ($this->stages as $code => $class) {
            $stage = new $class();
            $actions = $stage->getAvailableActions(1, 'NextStage');

            $this->assertIsArray($actions);
        }
    }

    /**
     * Тест: Все стадии имеют canTransitionTo()
     */
    public function testAllStagesHaveCanTransitionTo(): void
    {
        foreach ($this->stages as $code => $class) {
            $stage = new $class();
            $result = $stage->canTransitionTo('NextStage', 1);

            $this->assertIsBool($result);
        }
    }

    // ========================================================================
    // ТЕСТЫ MLS КОДОВ
    // ========================================================================

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
            $class = $this->stages[$stage];
            $stageObj = new $class();
            $this->assertEquals($expectedMls, $stageObj->getMlsCode());
        }
    }

    /**
     * Тест: Все MLS коды уникальны
     */
    public function testAllMlsCodesAreUnique(): void
    {
        $mlsCodes = [];

        foreach ($this->stages as $code => $class) {
            $stage = new $class();
            $mlsCode = $stage->getMlsCode();

            $this->assertNotContains(
                $mlsCode,
                $mlsCodes,
                "MLS код {$mlsCode} дублируется для стадии {$code}"
            );
            $mlsCodes[] = $mlsCode;
        }
    }

    // ========================================================================
    // ТЕСТЫ ПЕРЕХОДОВ (WORKFLOW)
    // ========================================================================

    /**
     * Тест: IceStage имеет переход в Touched
     */
    public function testIceStageTransitionsToTouched(): void
    {
        $stage = new IceStage();
        $nextStages = $stage->getNextStages();

        $this->assertContains('Touched', $nextStages);
    }

    /**
     * Тест: TouchedStage имеет переход в Aware
     */
    public function testTouchedStageTransitionsToAware(): void
    {
        $stage = new TouchedStage();
        $nextStages = $stage->getNextStages();

        $this->assertContains('Aware', $nextStages);
    }

    /**
     * Тест: AwareStage имеет переход в Interested
     */
    public function testAwareStageTransitionsToInterested(): void
    {
        $stage = new AwareStage();
        $nextStages = $stage->getNextStages();

        $this->assertContains('Interested', $nextStages);
    }

    /**
     * Тест: InterestedStage имеет переход в demo_planned
     */
    public function testInterestedStageTransitionsToDemoPlanned(): void
    {
        $stage = new InterestedStage();
        $nextStages = $stage->getNextStages();

        $this->assertContains('demo_planned', $nextStages);
    }

    /**
     * Тест: DemoPlannedStage имеет переход в Demo_done
     */
    public function testDemoPlannedStageTransitionsToDemoDone(): void
    {
        $stage = new DemoPlannedStage();
        $nextStages = $stage->getNextStages();

        $this->assertContains('Demo_done', $nextStages);
    }

    /**
     * Тест: DemoDoneStage имеет переход в Committed
     */
    public function testDemoDoneStageTransitionsToCommitted(): void
    {
        $stage = new DemoDoneStage();
        $nextStages = $stage->getNextStages();

        $this->assertContains('Committed', $nextStages);
    }

    /**
     * Тест: CommittedStage имеет переход в Customer
     */
    public function testCommittedStageTransitionsToCustomer(): void
    {
        $stage = new CommittedStage();
        $nextStages = $stage->getNextStages();

        $this->assertContains('Customer', $nextStages);
    }

    /**
     * Тест: CustomerStage имеет переход в Activated
     */
    public function testCustomerStageTransitionsToActivated(): void
    {
        $stage = new CustomerStage();
        $nextStages = $stage->getNextStages();

        $this->assertContains('Activated', $nextStages);
    }

    /**
     * Тест: ActivatedStage имеет переход в Archived
     */
    public function testActivatedStageTransitionsToArchived(): void
    {
        $stage = new ActivatedStage();
        $nextStages = $stage->getNextStages();

        $this->assertContains('Archived', $nextStages);
    }

    /**
     * Тест: Полный цикл воронки продаж
     */
    public function testFullSalesFunnelWorkflow(): void
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
            $class = $this->stages[$from];
            $stage = new $class();
            $nextStages = $stage->getNextStages();

            $this->assertContains(
                $to,
                $nextStages,
                "Стадия {$from} должна иметь переход в {$to}"
            );
        }
    }

    // ========================================================================
    // ТЕСТЫ ФИНАЛЬНЫХ СТАДИЙ
    // ========================================================================

    /**
     * Тест: ArchivedStage не имеет исходящих переходов
     */
    public function testArchivedStageHasNoOutgoingTransitions(): void
    {
        $stage = new ArchivedStage();
        $nextStages = $stage->getNextStages();

        $this->assertEmpty($nextStages);
    }

    /**
     * Тест: N0Stage не имеет исходящих переходов
     */
    public function testN0StageHasNoOutgoingTransitions(): void
    {
        $stage = new N0Stage();
        $nextStages = $stage->getNextStages();

        $this->assertEmpty($nextStages);
    }

    /**
     * Тест: Финальные стадии имеют правильные MLS коды
     */
    public function testFinalStagesHaveCorrectMlsCodes(): void
    {
        $finalStages = [
            'Archived' => 'H4',
            'N0' => 'N0',
        ];

        foreach ($finalStages as $stage => $expectedMls) {
            $class = $this->stages[$stage];
            $stageObj = new $class();
            $this->assertEquals($expectedMls, $stageObj->getMlsCode());
        }
    }

    // ========================================================================
    // ТЕСТЫ ДЕЙСТВИЙ (ACTIONS)
    // ========================================================================

    /**
     * Тест: IceStage имеет действие start_work
     */
    public function testIceStageHasStartWorkAction(): void
    {
        $stage = new IceStage();
        $actions = $stage->getAvailableActions(1, 'Touched');

        $actionCodes = array_column($actions, 'code');
        $this->assertContains('start_work', $actionCodes);
    }

    /**
     * Тест: TouchedStage имеет действие make_call
     */
    public function testTouchedStageHasMakeCallAction(): void
    {
        $stage = new TouchedStage();
        $actions = $stage->getAvailableActions(1, 'Aware');

        $actionCodes = array_column($actions, 'code');
        $this->assertContains('make_call', $actionCodes);
    }

    /**
     * Тест: AwareStage имеет действие fill_discovery
     */
    public function testAwareStageHasFillDiscoveryAction(): void
    {
        $stage = new AwareStage();
        $actions = $stage->getAvailableActions(1, 'Interested');

        $actionCodes = array_column($actions, 'code');
        $this->assertContains('fill_discovery', $actionCodes);
    }

    /**
     * Тест: InterestedStage имеет действие plan_demo
     */
    public function testInterestedStageHasPlanDemoAction(): void
    {
        $stage = new InterestedStage();
        $actions = $stage->getAvailableActions(1, 'demo_planned');

        $actionCodes = array_column($actions, 'code');
        $this->assertContains('plan_demo', $actionCodes);
    }

    /**
     * Тест: DemoPlannedStage имеет действие confirm_demo
     */
    public function testDemoPlannedStageHasConfirmDemoAction(): void
    {
        $stage = new DemoPlannedStage();
        $actions = $stage->getAvailableActions(1, 'Demo_done');

        $actionCodes = array_column($actions, 'code');
        $this->assertContains('confirm_demo', $actionCodes);
    }

    /**
     * Тест: DemoDoneStage имеет действие send_invoice
     */
    public function testDemoDoneStageHasSendInvoiceAction(): void
    {
        $stage = new DemoDoneStage();
        $actions = $stage->getAvailableActions(1, 'Committed');

        $actionCodes = array_column($actions, 'code');
        $this->assertContains('send_invoice', $actionCodes);
    }

    /**
     * Тест: CommittedStage имеет действие confirm_payment
     */
    public function testCommittedStageHasConfirmPaymentAction(): void
    {
        $stage = new CommittedStage();
        $actions = $stage->getAvailableActions(1, 'Customer');

        $actionCodes = array_column($actions, 'code');
        $this->assertContains('confirm_payment', $actionCodes);
    }

    /**
     * Тест: CustomerStage имеет действие issue_credential
     */
    public function testCustomerStageHasIssueCredentialAction(): void
    {
        $stage = new CustomerStage();
        $actions = $stage->getAvailableActions(1, 'Activated');

        $actionCodes = array_column($actions, 'code');
        $this->assertContains('issue_credential', $actionCodes);
    }

    /**
     * Тест: ActivatedStage имеет действие archive_deal
     */
    public function testActivatedStageHasArchiveDealAction(): void
    {
        $stage = new ActivatedStage();
        $actions = $stage->getAvailableActions(1, 'Archived');

        $actionCodes = array_column($actions, 'code');
        $this->assertContains('archive_deal', $actionCodes);
    }

    /**
     * Тест: Все стадии (кроме финальных и Activated) имеют действие close_lost
     */
    public function testAllStagesHaveCloseLostAction(): void
    {
        $nonFinalStages = [
            'Ice', 'Touched', 'Aware', 'Interested',
            'demo_planned', 'Demo_done', 'Committed',
            'Customer',
            // ❌ Activated исключён — сделка уже активирована
        ];

        foreach ($nonFinalStages as $stageCode) {
            $class = $this->stages[$stageCode];
            $stage = new $class();
            $actions = $stage->getAvailableActions(1, 'N0');

            $actionCodes = array_column($actions, 'code');
            $this->assertContains(
                'close_lost',
                $actionCodes,
                "Стадия {$stageCode} должна иметь действие close_lost"
            );
        }
    }

    /**
     * Тест: Финальные стадии не имеют действий
     */
    public function testFinalStagesHaveNoActions(): void
    {
        $finalStages = ['Archived', 'N0'];

        foreach ($finalStages as $stageCode) {
            $class = $this->stages[$stageCode];
            $stage = new $class();
            $actions = $stage->getAvailableActions(1, '');

            $this->assertEmpty($actions);
        }
    }

    // ========================================================================
    // ТЕСТЫ ОПИСАНИЙ
    // ========================================================================

    /**
     * Тест: Все стадии имеют осмысленные описания
     */
    public function testAllStagesHaveMeaningfulDescriptions(): void
    {
        foreach ($this->stages as $code => $class) {
            $stage = new $class();
            $description = $stage->getDescription();

            // Описание должно быть не пустым
            $this->assertNotEmpty($description);

            // Описание должно быть на русском или английском
            $this->assertMatchesRegularExpression('/[\p{Cyrillic}A-Za-z]/u', $description);
        }
    }

    /**
     * Тест: Описания стадий не дублируются
     */
    public function testStageDescriptionsAreUnique(): void
    {
        $descriptions = [];

        foreach ($this->stages as $code => $class) {
            $stage = new $class();
            $description = $stage->getDescription();

            $this->assertNotContains(
                $description,
                $descriptions,
                "Описание стадии {$code} дублируется"
            );
            $descriptions[] = $description;
        }
    }

    // ========================================================================
    // ТЕСТЫ CAN TRANSITION TO
    // ========================================================================

    /**
     * Тест: canTransitionTo возвращает boolean
     */
    public function testCanTransitionToReturnsBoolean(): void
    {
        foreach ($this->stages as $code => $class) {
            $stage = new $class();
            $result = $stage->canTransitionTo('NextStage', 1);

            $this->assertIsBool($result);
        }
    }

    /**
     * Тест: canTransitionTo для валидного перехода
     */
    public function testCanTransitionToValidTransition(): void
    {
        $stage = new IceStage();
        $result = $stage->canTransitionTo('Touched', 1);

        $this->assertIsBool($result);
    }

    /**
     * Тест: canTransitionTo для невалидного перехода
     */
    public function testCanTransitionToInvalidTransition(): void
    {
        $stage = new IceStage();
        $result = $stage->canTransitionTo('Archived', 1);

        $this->assertIsBool($result);
    }

    // ========================================================================
    // ТЕСТЫ ЧЕРЕЗ STAGE FACTORY
    // ========================================================================

    /**
     * Тест: StageFactory создаёт все стадии
     */
    public function testStageFactoryCreatesAllStages(): void
    {
        foreach ($this->stages as $code => $class) {
            $stage = StageFactory::create($code);
            $this->assertInstanceOf($class, $stage);
        }
    }

    /**
     * Тест: StageFactory::getMlsCode работает для всех стадий
     */
    public function testStageFactoryGetMlsCodeForAllStages(): void
    {
        foreach ($this->stages as $code => $expectedClass) {
            $mlsCode = StageFactory::getMlsCode($code);
            $this->assertIsString($mlsCode);
            $this->assertNotEmpty($mlsCode);
        }
    }

    /**
     * Тест: StageFactory::getAllStages возвращает все коды
     */
    public function testStageFactoryGetAllStagesReturnsAllCodes(): void
    {
        $allStages = StageFactory::getAllStages();

        foreach (array_keys($this->stages) as $code) {
            $this->assertContains($code, $allStages);
        }
    }
}