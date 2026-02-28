<?php
namespace Gust\Component\Crmstages\Tests\Unit\Service;

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
 * Интеграционный тест для стадий и переходов
 *
 * @covers \Gust\Component\Crmstages\Site\Service\StageFactory
 * @covers \Gust\Component\Crmstages\Site\Stage\*
 */
class StageTransitionTest extends TestCase
{
    /**
     * Тест: Все стадии создаются через Factory
     */
    public function testAllStagesAreCreatable(): void
    {
        $stages = [
            'Ice', 'Touched', 'Aware', 'Interested',
            'demo_planned', 'Demo_done', 'Committed',
            'Customer', 'Activated', 'Archived', 'N0'
        ];

        foreach ($stages as $stageCode) {
            $stage = StageFactory::create($stageCode);
            $this->assertInstanceOf(StageInterface::class, $stage);
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
     * Тест: MLS коды уникальны для всех стадий
     */
    public function testMlsCodesAreUnique(): void
    {
        $stages = [
            'Ice', 'Touched', 'Aware', 'Interested',
            'demo_planned', 'Demo_done', 'Committed',
            'Customer', 'Activated', 'Archived', 'N0'
        ];
        $mlsCodes = [];

        foreach ($stages as $stageCode) {
            $stage = StageFactory::create($stageCode);
            $mlsCode = $stage->getMlsCode();

            $this->assertNotContains(
                $mlsCode,
                $mlsCodes,
                "MLS код {$mlsCode} дублируется для стадии {$stageCode}"
            );
            $mlsCodes[] = $mlsCode;
        }
    }

    /**
     * Тест: IceStage имеет правильную конфигурацию
     */
    public function testIceStageConfiguration(): void
    {
        $stage = new IceStage();

        $this->assertEquals('Ice', $stage->getName());
        $this->assertIsString($stage->getMlsCode());
        $this->assertIsArray($stage->getNextStages());
        $this->assertNotEmpty($stage->getDescription());
    }

    /**
     * Тест: AwareStage переводит на Interested
     */
    public function testAwareStageNextStages(): void
    {
        $stage = new AwareStage();
        $nextStages = $stage->getNextStages();

        $this->assertIsArray($nextStages);
        $this->assertContains('Interested', $nextStages);
    }

    /**
     * Тест: InterestedStage имеет действие plan_demo
     */
    public function testInterestedStageHasPlanDemoAction(): void
    {
        $stage = new InterestedStage();

        // Используем рефлекссию для доступа к protected методу
        $reflection = new \ReflectionClass($stage);
        $method = $reflection->getMethod('createActionForStage');
        $method->setAccessible(true);

        $actions = $method->invoke($stage, 'demo_planned');

        $this->assertIsArray($actions);

        $actionCodes = array_column($actions, 'code');
        $this->assertContains('plan_demo', $actionCodes);
        $this->assertContains('close_lost', $actionCodes);
    }

    /**
     * Тест: DemoPlannedStage имеет действие confirm_demo
     */
    public function testDemoPlannedStageHasConfirmDemoAction(): void
    {
        $stage = new DemoPlannedStage();

        $reflection = new \ReflectionClass($stage);
        $method = $reflection->getMethod('createActionForStage');
        $method->setAccessible(true);

        $actions = $method->invoke($stage, 'Demo_done');

        $this->assertIsArray($actions);

        $actionCodes = array_column($actions, 'code');
        $this->assertContains('confirm_demo', $actionCodes);
    }

    /**
     * Тест: DemoDoneStage имеет действие send_invoice
     */
    public function testDemoDoneStageHasSendInvoiceAction(): void
    {
        $stage = new DemoDoneStage();

        $reflection = new \ReflectionClass($stage);
        $method = $reflection->getMethod('createActionForStage');
        $method->setAccessible(true);

        $actions = $method->invoke($stage, 'Committed');

        $this->assertIsArray($actions);

        $actionCodes = array_column($actions, 'code');
        $this->assertContains('send_invoice', $actionCodes);
    }

    /**
     * Тест: CommittedStage имеет действие confirm_payment
     */
    public function testCommittedStageHasConfirmPaymentAction(): void
    {
        $stage = new CommittedStage();

        $reflection = new \ReflectionClass($stage);
        $method = $reflection->getMethod('createActionForStage');
        $method->setAccessible(true);

        $actions = $method->invoke($stage, 'Customer');

        $this->assertIsArray($actions);

        $actionCodes = array_column($actions, 'code');
        $this->assertContains('confirm_payment', $actionCodes);
    }

    /**
     * Тест: CustomerStage имеет действие issue_credential
     */
    public function testCustomerStageHasIssueCredentialAction(): void
    {
        $stage = new CustomerStage();

        $reflection = new \ReflectionClass($stage);
        $method = $reflection->getMethod('createActionForStage');
        $method->setAccessible(true);

        $actions = $method->invoke($stage, 'Activated');

        $this->assertIsArray($actions);

        $actionCodes = array_column($actions, 'code');
        $this->assertContains('issue_credential', $actionCodes);
    }

    /**
     * Тест: ActivatedStage имеет действие archive_deal
     */
    public function testActivatedStageHasArchiveAction(): void
    {
        $stage = new ActivatedStage();

        $reflection = new \ReflectionClass($stage);
        $method = $reflection->getMethod('createActionForStage');
        $method->setAccessible(true);

        $actions = $method->invoke($stage, 'Archived');

        $this->assertIsArray($actions);

        $actionCodes = array_column($actions, 'code');
        $this->assertContains('archive_deal', $actionCodes);
    }

    /**
     * Тест: N0Stage не имеет переходов (финальная)
     */
    public function testN0StageIsFinal(): void
    {
        $stage = new N0Stage();
        $nextStages = $stage->getNextStages();

        $this->assertIsArray($nextStages);
        $this->assertEmpty($nextStages);
    }

    /**
     * Тест: ArchivedStage не имеет переходов (финальная)
     */
    public function testArchivedStageIsFinal(): void
    {
        $stage = new ArchivedStage();
        $nextStages = $stage->getNextStages();

        $this->assertIsArray($nextStages);
        $this->assertEmpty($nextStages);
    }

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
     * Тест: Все стадии имеют описание
     */
    public function testAllStagesHaveDescription(): void
    {
        $stages = [
            'Ice', 'Touched', 'Aware', 'Interested',
            'demo_planned', 'Demo_done', 'Committed',
            'Customer', 'Activated', 'Archived', 'N0'
        ];

        foreach ($stages as $stageCode) {
            $stage = StageFactory::create($stageCode);
            $description = $stage->getDescription();

            $this->assertIsString($description);
            $this->assertNotEmpty($description);
        }
    }

    /**
     * Тест: StageFactory::getMlsCode работает корректно
     */
    public function testStageFactoryGetMlsCode(): void
    {
        $mlsMapping = [
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
            'N0' => 'N0',  // ✅ Исправлено: N0Stage возвращает 'N0'
        ];

        foreach ($mlsMapping as $stage => $expectedMls) {
            $mlsCode = StageFactory::getMlsCode($stage);
            $this->assertEquals($expectedMls, $mlsCode);
        }
    }
}