<?php
namespace Gust\Component\Crmstages\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Gust\Component\Crmstages\Site\Service\ActionHandler;
use Gust\Component\Crmstages\Site\Service\Dto\ActionInput;
use Gust\Component\Crmstages\Site\Service\Dto\ActionResult;

/**
 * Unit-тест для ActionHandler (диспетчер команд)
 *
 * @covers \Gust\Component\Crmstages\Site\Service\ActionHandler
 */
class ActionHandlerTest extends TestCase
{
    private ActionHandler $handler;

    protected function setUp(): void
    {
        $this->handler = new ActionHandler();
    }

    /**
     * Тест: StartWork переводит на Touched
     */
    public function testStartWorkTransitionsToTouched(): void
    {
        $input = new ActionInput(
            companyId: 123,
            fields: ['comment' => 'Начали работу']
        );

        $result = $this->handler->handle('start_work', $input);

        $this->assertInstanceOf(ActionResult::class, $result);
        $this->assertTrue($result->success);
        $this->assertTrue($result->shouldTransition);
        $this->assertEquals('Touched', $result->targetStage);
        $this->assertEquals('start_work', $result->eventCode);
    }

    /**
     * Тест: MakeCall с успехом переводит на Aware
     */
    public function testMakeCallSuccessTransitionsToAware(): void
    {
        $input = new ActionInput(
            companyId: 123,
            fields: [
                'lp_reached' => 1,
                'call_result' => 'Договорились',
                'call_time' => '14:30',
            ]
        );

        $result = $this->handler->handle('make_call', $input);

        $this->assertTrue($result->success);
        $this->assertTrue($result->shouldTransition);
        $this->assertEquals('Aware', $result->targetStage);
        $this->assertEquals('call_successful', $result->eventCode);
    }

    /**
     * Тест: MakeCall без дозвона остаётся на Touched
     */
    public function testMakeCallFailedStaysOnTouched(): void
    {
        $input = new ActionInput(
            companyId: 123,
            fields: [
                'lp_reached' => 0,
                'call_result' => 'Не дозвонился',
            ]
        );

        $result = $this->handler->handle('make_call', $input);

        $this->assertTrue($result->success);
        $this->assertFalse($result->shouldTransition);
        $this->assertEquals('Touched', $result->targetStage);
        $this->assertEquals('make_call_failed', $result->eventCode);
    }

    /**
     * Тест: FillDiscovery переводит на Interested
     */
    public function testFillDiscoveryTransitionsToInterested(): void
    {
        $input = new ActionInput(
            companyId: 123,
            fields: [
                'pains' => 'Нужна автоматизация',
                'budget' => '100000',
                'timeline' => '3 месяца',
                'decision_maker' => 'Иванов И.И.',
                'next_steps' => 'Отправить КП',
            ]
        );

        $result = $this->handler->handle('fill_discovery', $input);

        $this->assertTrue($result->success);
        $this->assertTrue($result->shouldTransition);
        $this->assertEquals('Interested', $result->targetStage);
        $this->assertEquals('fill_discovery', $result->eventCode);
        $this->assertStringContainsString('Нужна автоматизация', $result->comment);
    }

    /**
     * Тест: PlanDemo переводит на demo_planned
     */
    public function testPlanDemoTransitionsToDemoPlanned(): void
    {
        $input = new ActionInput(
            companyId: 123,
            fields: [
                'demo_date' => '2026-02-01',
                'demo_time' => '15:00',
                'demo_link' => 'https://zoom.us/j/123456',
                'demo_comment' => 'Показать функционал',
            ]
        );

        $result = $this->handler->handle('plan_demo', $input);

        $this->assertTrue($result->success);
        $this->assertTrue($result->shouldTransition);
        $this->assertEquals('demo_planned', $result->targetStage);
        $this->assertEquals('plan_demo', $result->eventCode);
    }

    /**
     * Тест: ConfirmDemo переводит на Demo_done
     */
    public function testConfirmDemoTransitionsToDemoDone(): void
    {
        $input = new ActionInput(
            companyId: 123,
            fields: [
                'confirmed' => 1,
                'demo_result' => 'Клиент доволен',
                'feedback' => 'Хотят доработки',
            ]
        );

        $result = $this->handler->handle('confirm_demo', $input);

        $this->assertTrue($result->success);
        $this->assertTrue($result->shouldTransition);
        $this->assertEquals('Demo_done', $result->targetStage);
        $this->assertEquals('confirm_demo', $result->eventCode);
    }

    /**
     * Тест: SendInvoice переводит на Committed
     */
    public function testSendInvoiceTransitionsToCommitted(): void
    {
        $input = new ActionInput(
            companyId: 123,
            fields: [
                'invoice_number' => 'INV-2026-001',
                'amount' => '150000',
                'invoice_comment' => 'Оплата в течение 5 дней',
            ]
        );

        $result = $this->handler->handle('send_invoice', $input);

        $this->assertTrue($result->success);
        $this->assertTrue($result->shouldTransition);
        $this->assertEquals('Committed', $result->targetStage);
        $this->assertEquals('send_invoice', $result->eventCode);
    }

    /**
     * Тест: ConfirmPayment переводит на Customer
     */
    public function testConfirmPaymentTransitionsToCustomer(): void
    {
        $input = new ActionInput(
            companyId: 123,
            fields: [
                'payment_comment' => 'Оплата получена на расчётный счёт',
            ]
        );

        $result = $this->handler->handle('confirm_payment', $input);

        $this->assertTrue($result->success);
        $this->assertTrue($result->shouldTransition);
        $this->assertEquals('Customer', $result->targetStage);
        $this->assertEquals('confirm_payment', $result->eventCode);
    }

    /**
     * Тест: IssueCredential переводит на Activated
     */
    public function testIssueCredentialTransitionsToActivated(): void
    {
        $input = new ActionInput(
            companyId: 123,
            fields: [
                'credential_number' => 'CERT-001',
                'issue_date' => '2026-02-15',
                'credential_comment' => 'Выдано лично',
            ]
        );

        $result = $this->handler->handle('issue_credential', $input);

        $this->assertTrue($result->success);
        $this->assertTrue($result->shouldTransition);
        $this->assertEquals('Activated', $result->targetStage);
        $this->assertEquals('issue_credential', $result->eventCode);
    }

    /**
     * Тест: ArchiveDeal переводит на Archived
     */
    public function testArchiveDealTransitionsToArchived(): void
    {
        $input = new ActionInput(
            companyId: 123,
            fields: [
                'archive_comment' => 'Сделка успешно завершена',
            ]
        );

        $result = $this->handler->handle('archive_deal', $input);

        $this->assertTrue($result->success);
        $this->assertTrue($result->shouldTransition);
        $this->assertEquals('Archived', $result->targetStage);
        $this->assertEquals('archive_deal', $result->eventCode);
    }

    /**
     * Тест: CloseLost переводит на N0
     */
    public function testCloseLostTransitionsToN0(): void
    {
        $input = new ActionInput(
            companyId: 123,
            fields: [
                'lose_reason' => 'Клиент выбрал конкурента',
            ]
        );

        $result = $this->handler->handle('close_lost', $input);

        $this->assertTrue($result->success);
        $this->assertTrue($result->shouldTransition);
        $this->assertEquals('N0', $result->targetStage);
        $this->assertEquals('close_lost', $result->eventCode);
        $this->assertEquals('warning', $result->messageType);
    }

    /**
     * Тест: Неизвестное действие использует DefaultHandler
     */
    public function testUnknownActionUsesDefaultHandler(): void
    {
        $input = new ActionInput(
            companyId: 123,
            fields: [
                'comment' => 'Тестовое действие',
            ]
        );

        $result = $this->handler->handle('unknown_action', $input);

        $this->assertTrue($result->success);
        $this->assertEquals('default_action', $result->eventCode);
        $this->assertStringContainsString('Действие выполнено', $result->message);
    }

    /**
     * Тест: Все действия возвращают ActionResult
     */
    public function testAllActionsReturnActionResult(): void
    {
        $actions = [
            'start_work',
            'make_call',
            'fill_discovery',
            'plan_demo',
            'confirm_demo',
            'send_invoice',
            'confirm_payment',
            'issue_credential',
            'archive_deal',
            'close_lost',
        ];

        foreach ($actions as $action) {
            $input = new ActionInput(123, []);
            $result = $this->handler->handle($action, $input);

            $this->assertInstanceOf(
                ActionResult::class,
                $result,
                "Действие {$action} должно возвращать ActionResult"
            );
        }
    }

    /**
     * Тест: Сообщение имеет правильный тип
     */
    /**
     * Тест: Сообщение имеет правильный тип
     */
    public function testMessageTypesAreCorrect(): void
    {
        // Успешные действия (с правильными данными для каждого)
        $result = $this->handler->handle('start_work', new ActionInput(123, []));
        $this->assertEquals('success', $result->messageType);

        // make_call с УСПЕШНЫМ звонком (lp_reached = 1)
        $result = $this->handler->handle('make_call', new ActionInput(123, ['lp_reached' => 1]));
        $this->assertEquals('success', $result->messageType);

        $result = $this->handler->handle('fill_discovery', new ActionInput(123, []));
        $this->assertEquals('success', $result->messageType);

        // Закрытие сделки (warning)
        $result = $this->handler->handle('close_lost', new ActionInput(123, ['lose_reason' => 'Тест']));
        $this->assertEquals('warning', $result->messageType);

        // Неудачный звонок (info)
        $result = $this->handler->handle('make_call', new ActionInput(123, ['lp_reached' => 0]));
        $this->assertEquals('info', $result->messageType);
    }
}