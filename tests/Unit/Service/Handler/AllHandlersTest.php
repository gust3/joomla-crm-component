<?php
namespace Gust\Component\Crmstages\Tests\Unit\Service\Handler;

use PHPUnit\Framework\TestCase;
use Gust\Component\Crmstages\Site\Service\Dto\ActionInput;
use Gust\Component\Crmstages\Site\Service\Dto\ActionResult;
use Gust\Component\Crmstages\Site\Service\Handler\StartWorkHandler;
use Gust\Component\Crmstages\Site\Service\Handler\MakeCallHandler;
use Gust\Component\Crmstages\Site\Service\Handler\FillDiscoveryHandler;
use Gust\Component\Crmstages\Site\Service\Handler\PlanDemoHandler;
use Gust\Component\Crmstages\Site\Service\Handler\ConfirmDemoHandler;
use Gust\Component\Crmstages\Site\Service\Handler\SendInvoiceHandler;
use Gust\Component\Crmstages\Site\Service\Handler\ConfirmPaymentHandler;
use Gust\Component\Crmstages\Site\Service\Handler\IssueCredentialHandler;
use Gust\Component\Crmstages\Site\Service\Handler\ArchiveDealHandler;
use Gust\Component\Crmstages\Site\Service\Handler\CloseLostHandler;
use Gust\Component\Crmstages\Site\Service\Handler\DefaultHandler;

/**
 * Unit-тест для всех обработчиков действий (Command Pattern)
 *
 * @covers \Gust\Component\Crmstages\Site\Service\Handler\StartWorkHandler
 * @covers \Gust\Component\Crmstages\Site\Service\Handler\MakeCallHandler
 * @covers \Gust\Component\Crmstages\Site\Service\Handler\FillDiscoveryHandler
 * @covers \Gust\Component\Crmstages\Site\Service\Handler\PlanDemoHandler
 * @covers \Gust\Component\Crmstages\Site\Service\Handler\ConfirmDemoHandler
 * @covers \Gust\Component\Crmstages\Site\Service\Handler\SendInvoiceHandler
 * @covers \Gust\Component\Crmstages\Site\Service\Handler\ConfirmPaymentHandler
 * @covers \Gust\Component\Crmstages\Site\Service\Handler\IssueCredentialHandler
 * @covers \Gust\Component\Crmstages\Site\Service\Handler\ArchiveDealHandler
 * @covers \Gust\Component\Crmstages\Site\Service\Handler\CloseLostHandler
 * @covers \Gust\Component\Crmstages\Site\Service\Handler\DefaultHandler
 */
class AllHandlersTest extends TestCase
{
    // ========================================================================
    // START WORK HANDLER
    // ========================================================================

    public function testStartWorkHandlerSupportsStartWork(): void
    {
        $handler = new StartWorkHandler();
        $this->assertTrue($handler->supports('start_work'));
        $this->assertFalse($handler->supports('other_action'));
    }

    public function testStartWorkHandlerTransitionsToTouched(): void
    {
        $handler = new StartWorkHandler();
        $input = new ActionInput(123, ['comment' => 'Начали работу']);
        $result = $handler->handle($input);

        $this->assertInstanceOf(ActionResult::class, $result);
        $this->assertTrue($result->success);
        $this->assertTrue($result->shouldTransition);
        $this->assertEquals('Touched', $result->targetStage);
        $this->assertEquals('start_work', $result->eventCode);
    }

    public function testStartWorkHandlerWithEmptyComment(): void
    {
        $handler = new StartWorkHandler();
        $input = new ActionInput(123, []);
        $result = $handler->handle($input);

        $this->assertTrue($result->success);
        $this->assertEquals('success', $result->messageType);
    }

    // ========================================================================
    // MAKE CALL HANDLER
    // ========================================================================

    public function testMakeCallHandlerSupportsMakeCall(): void
    {
        $handler = new MakeCallHandler();
        $this->assertTrue($handler->supports('make_call'));
        $this->assertFalse($handler->supports('other_action'));
    }

    public function testMakeCallHandlerSuccessfulCall(): void
    {
        $handler = new MakeCallHandler();
        $input = new ActionInput(123, [
            'lp_reached' => 1,
            'call_result' => 'Договорились',
            'call_time' => '14:30',
        ]);
        $result = $handler->handle($input);

        $this->assertTrue($result->success);
        $this->assertTrue($result->shouldTransition);
        $this->assertEquals('Aware', $result->targetStage);
        $this->assertEquals('call_successful', $result->eventCode);
    }

    public function testMakeCallHandlerFailedCall(): void
    {
        $handler = new MakeCallHandler();
        $input = new ActionInput(123, [
            'lp_reached' => 0,
            'call_result' => 'Не дозвонился',
        ]);
        $result = $handler->handle($input);

        $this->assertTrue($result->success);
        $this->assertFalse($result->shouldTransition);
        $this->assertEquals('Touched', $result->targetStage);
        $this->assertEquals('make_call_failed', $result->eventCode);
        $this->assertEquals('info', $result->messageType);
    }

    public function testMakeCallHandlerWithEmptyData(): void
    {
        $handler = new MakeCallHandler();
        $input = new ActionInput(123, []);
        $result = $handler->handle($input);

        $this->assertFalse($result->shouldTransition);
        $this->assertEquals('Touched', $result->targetStage);
    }

    // ========================================================================
    // FILL DISCOVERY HANDLER
    // ========================================================================

    public function testFillDiscoveryHandlerSupportsFillDiscovery(): void
    {
        $handler = new FillDiscoveryHandler();
        $this->assertTrue($handler->supports('fill_discovery'));
        $this->assertFalse($handler->supports('other_action'));
    }

    public function testFillDiscoveryHandlerTransitionsToInterested(): void
    {
        $handler = new FillDiscoveryHandler();
        $input = new ActionInput(123, [
            'pains' => 'Нужна автоматизация',
            'budget' => '100000',
            'timeline' => '3 месяца',
            'decision_maker' => 'Иванов И.И.',
            'next_steps' => 'Отправить КП',
        ]);
        $result = $handler->handle($input);

        $this->assertTrue($result->success);
        $this->assertTrue($result->shouldTransition);
        $this->assertEquals('Interested', $result->targetStage);
        $this->assertEquals('fill_discovery', $result->eventCode);
        $this->assertStringContainsString('Нужна автоматизация', $result->comment);
    }

    public function testFillDiscoveryHandlerWithEmptyFields(): void
    {
        $handler = new FillDiscoveryHandler();
        $input = new ActionInput(123, []);
        $result = $handler->handle($input);

        $this->assertTrue($result->success);
        $this->assertTrue($result->shouldTransition);
        $this->assertEquals('Interested', $result->targetStage);
    }

    // ========================================================================
    // PLAN DEMO HANDLER
    // ========================================================================

    public function testPlanDemoHandlerSupportsPlanDemo(): void
    {
        $handler = new PlanDemoHandler();
        $this->assertTrue($handler->supports('plan_demo'));
        $this->assertFalse($handler->supports('other_action'));
    }

    public function testPlanDemoHandlerTransitionsToDemoPlanned(): void
    {
        $handler = new PlanDemoHandler();
        $input = new ActionInput(123, [
            'demo_date' => '2026-02-01',
            'demo_time' => '15:00',
            'demo_link' => 'https://zoom.us/j/123456',
            'demo_comment' => 'Показать функционал',
        ]);
        $result = $handler->handle($input);

        $this->assertTrue($result->success);
        $this->assertTrue($result->shouldTransition);
        $this->assertEquals('demo_planned', $result->targetStage);
        $this->assertEquals('plan_demo', $result->eventCode);
        $this->assertStringContainsString('2026-02-01', $result->comment);
    }

    public function testPlanDemoHandlerWithoutLink(): void
    {
        $handler = new PlanDemoHandler();
        $input = new ActionInput(123, [
            'demo_date' => '2026-02-01',
            'demo_time' => '15:00',
        ]);
        $result = $handler->handle($input);

        $this->assertTrue($result->success);
        $this->assertStringNotContainsString('Ссылка', $result->comment);
    }

    // ========================================================================
    // CONFIRM DEMO HANDLER
    // ========================================================================

    public function testConfirmDemoHandlerSupportsConfirmDemo(): void
    {
        $handler = new ConfirmDemoHandler();
        $this->assertTrue($handler->supports('confirm_demo'));
        $this->assertFalse($handler->supports('other_action'));
    }

    public function testConfirmDemoHandlerTransitionsToDemoDone(): void
    {
        $handler = new ConfirmDemoHandler();
        $input = new ActionInput(123, [
            'confirmed' => 1,
            'demo_result' => 'Клиент доволен',
            'feedback' => 'Хотят доработки',
        ]);
        $result = $handler->handle($input);

        $this->assertTrue($result->success);
        $this->assertTrue($result->shouldTransition);
        $this->assertEquals('Demo_done', $result->targetStage);
        $this->assertEquals('confirm_demo', $result->eventCode);
    }

    public function testConfirmDemoHandlerWithoutFeedback(): void
    {
        $handler = new ConfirmDemoHandler();
        $input = new ActionInput(123, [
            'confirmed' => 1,
            'demo_result' => 'Успешно',
        ]);
        $result = $handler->handle($input);

        $this->assertTrue($result->success);
        $this->assertStringNotContainsString('Отзыв', $result->comment);
    }

    // ========================================================================
    // SEND INVOICE HANDLER
    // ========================================================================

    public function testSendInvoiceHandlerSupportsSendInvoice(): void
    {
        $handler = new SendInvoiceHandler();
        $this->assertTrue($handler->supports('send_invoice'));
        $this->assertFalse($handler->supports('other_action'));
    }

    public function testSendInvoiceHandlerTransitionsToCommitted(): void
    {
        $handler = new SendInvoiceHandler();
        $input = new ActionInput(123, [
            'invoice_number' => 'INV-2026-001',
            'amount' => '150000',
            'invoice_comment' => 'Оплата в течение 5 дней',
        ]);
        $result = $handler->handle($input);

        $this->assertTrue($result->success);
        $this->assertTrue($result->shouldTransition);
        $this->assertEquals('Committed', $result->targetStage);
        $this->assertEquals('send_invoice', $result->eventCode);
        $this->assertStringContainsString('INV-2026-001', $result->comment);
    }

    public function testSendInvoiceHandlerWithoutComment(): void
    {
        $handler = new SendInvoiceHandler();
        $input = new ActionInput(123, [
            'invoice_number' => 'INV-001',
            'amount' => '100000',
        ]);
        $result = $handler->handle($input);

        $this->assertTrue($result->success);
    }

    // ========================================================================
    // CONFIRM PAYMENT HANDLER
    // ========================================================================

    public function testConfirmPaymentHandlerSupportsConfirmPayment(): void
    {
        $handler = new ConfirmPaymentHandler();
        $this->assertTrue($handler->supports('confirm_payment'));
        $this->assertFalse($handler->supports('other_action'));
    }

    public function testConfirmPaymentHandlerTransitionsToCustomer(): void
    {
        $handler = new ConfirmPaymentHandler();
        $input = new ActionInput(123, [
            'payment_comment' => 'Оплата получена на расчётный счёт',
        ]);
        $result = $handler->handle($input);

        $this->assertTrue($result->success);
        $this->assertTrue($result->shouldTransition);
        $this->assertEquals('Customer', $result->targetStage);
        $this->assertEquals('confirm_payment', $result->eventCode);
    }

    public function testConfirmPaymentHandlerWithoutComment(): void
    {
        $handler = new ConfirmPaymentHandler();
        $input = new ActionInput(123, []);
        $result = $handler->handle($input);

        $this->assertTrue($result->success);
        $this->assertEquals('Customer', $result->targetStage);
    }

    // ========================================================================
    // ISSUE CREDENTIAL HANDLER
    // ========================================================================

    public function testIssueCredentialHandlerSupportsIssueCredential(): void
    {
        $handler = new IssueCredentialHandler();
        $this->assertTrue($handler->supports('issue_credential'));
        $this->assertFalse($handler->supports('other_action'));
    }

    public function testIssueCredentialHandlerTransitionsToActivated(): void
    {
        $handler = new IssueCredentialHandler();
        $input = new ActionInput(123, [
            'credential_number' => 'CERT-001',
            'issue_date' => '2026-02-15',
            'credential_comment' => 'Выдано лично',
        ]);
        $result = $handler->handle($input);

        $this->assertTrue($result->success);
        $this->assertTrue($result->shouldTransition);
        $this->assertEquals('Activated', $result->targetStage);
        $this->assertEquals('issue_credential', $result->eventCode);
        $this->assertStringContainsString('CERT-001', $result->comment);
    }

    // ========================================================================
    // ARCHIVE DEAL HANDLER
    // ========================================================================

    public function testArchiveDealHandlerSupportsArchiveDeal(): void
    {
        $handler = new ArchiveDealHandler();
        $this->assertTrue($handler->supports('archive_deal'));
        $this->assertFalse($handler->supports('other_action'));
    }

    public function testArchiveDealHandlerTransitionsToArchived(): void
    {
        $handler = new ArchiveDealHandler();
        $input = new ActionInput(123, [
            'archive_comment' => 'Сделка успешно завершена',
        ]);
        $result = $handler->handle($input);

        $this->assertTrue($result->success);
        $this->assertTrue($result->shouldTransition);
        $this->assertEquals('Archived', $result->targetStage);
        $this->assertEquals('archive_deal', $result->eventCode);
    }

    public function testArchiveDealHandlerWithoutComment(): void
    {
        $handler = new ArchiveDealHandler();
        $input = new ActionInput(123, []);
        $result = $handler->handle($input);

        $this->assertTrue($result->success);
        $this->assertEquals('Archived', $result->targetStage);
    }

    // ========================================================================
    // CLOSE LOST HANDLER
    // ========================================================================

    public function testCloseLostHandlerSupportsCloseLost(): void
    {
        $handler = new CloseLostHandler();
        $this->assertTrue($handler->supports('close_lost'));
        $this->assertFalse($handler->supports('other_action'));
    }

    public function testCloseLostHandlerTransitionsToN0(): void
    {
        $handler = new CloseLostHandler();
        $input = new ActionInput(123, [
            'lose_reason' => 'Клиент выбрал конкурента',
        ]);
        $result = $handler->handle($input);

        $this->assertTrue($result->success);
        $this->assertTrue($result->shouldTransition);
        $this->assertEquals('N0', $result->targetStage);
        $this->assertEquals('close_lost', $result->eventCode);
        $this->assertEquals('warning', $result->messageType);
        $this->assertStringContainsString('Клиент выбрал конкурента', $result->comment);
    }

    public function testCloseLostHandlerWithoutReason(): void
    {
        $handler = new CloseLostHandler();
        $input = new ActionInput(123, []);
        $result = $handler->handle($input);

        $this->assertTrue($result->success);
        $this->assertEquals('N0', $result->targetStage);
        $this->assertEquals('warning', $result->messageType);
    }

    // ========================================================================
    // DEFAULT HANDLER
    // ========================================================================

    public function testDefaultHandlerSupportsAnyAction(): void
    {
        $handler = new DefaultHandler();
        $this->assertTrue($handler->supports('any_action'));
        $this->assertTrue($handler->supports('unknown_action'));
        $this->assertTrue($handler->supports('custom_action'));
    }

    public function testDefaultHandlerReturnsSuccess(): void
    {
        $handler = new DefaultHandler();
        $input = new ActionInput(123, [
            'comment' => 'Тестовое действие',
        ]);
        $result = $handler->handle($input);

        $this->assertTrue($result->success);
        $this->assertEquals('default_action', $result->eventCode);
        $this->assertStringContainsString('Действие выполнено', $result->message);
    }

    public function testDefaultHandlerWithoutComment(): void
    {
        $handler = new DefaultHandler();
        $input = new ActionInput(123, []);
        $result = $handler->handle($input);

        $this->assertTrue($result->success);
        $this->assertEquals('success', $result->messageType);
    }

    // ========================================================================
    // ОБЩИЕ ТЕСТЫ ДЛЯ ВСЕХ ХЕНДЛЕРОВ
    // ========================================================================

    public function testAllHandlersReturnActionResult(): void
    {
        $handlers = [
            new StartWorkHandler(),
            new MakeCallHandler(),
            new FillDiscoveryHandler(),
            new PlanDemoHandler(),
            new ConfirmDemoHandler(),
            new SendInvoiceHandler(),
            new ConfirmPaymentHandler(),
            new IssueCredentialHandler(),
            new ArchiveDealHandler(),
            new CloseLostHandler(),
            new DefaultHandler(),
        ];

        foreach ($handlers as $handler) {
            $result = $handler->handle(new ActionInput(123, []));
            $this->assertInstanceOf(
                ActionResult::class,
                $result,
                get_class($handler) . ' должен возвращать ActionResult'
            );
        }
    }

    public function testAllHandlersHaveSupportsMethod(): void
    {
        $handlers = [
            new StartWorkHandler(),
            new MakeCallHandler(),
            new FillDiscoveryHandler(),
            new PlanDemoHandler(),
            new ConfirmDemoHandler(),
            new SendInvoiceHandler(),
            new ConfirmPaymentHandler(),
            new IssueCredentialHandler(),
            new ArchiveDealHandler(),
            new CloseLostHandler(),
            new DefaultHandler(),
        ];

        foreach ($handlers as $handler) {
            $this->assertTrue(
                method_exists($handler, 'supports'),
                get_class($handler) . ' должен иметь метод supports()'
            );
        }
    }

    public function testAllHandlersHaveHandleMethod(): void
    {
        $handlers = [
            new StartWorkHandler(),
            new MakeCallHandler(),
            new FillDiscoveryHandler(),
            new PlanDemoHandler(),
            new ConfirmDemoHandler(),
            new SendInvoiceHandler(),
            new ConfirmPaymentHandler(),
            new IssueCredentialHandler(),
            new ArchiveDealHandler(),
            new CloseLostHandler(),
            new DefaultHandler(),
        ];

        foreach ($handlers as $handler) {
            $this->assertTrue(
                method_exists($handler, 'handle'),
                get_class($handler) . ' должен иметь метод handle()'
            );
        }
    }

    public function testAllHandlersReturnSuccessTrue(): void
    {
        $handlers = [
            ['handler' => new StartWorkHandler(), 'input' => new ActionInput(123, [])],
            ['handler' => new MakeCallHandler(), 'input' => new ActionInput(123, ['lp_reached' => 1])],
            ['handler' => new FillDiscoveryHandler(), 'input' => new ActionInput(123, [])],
            ['handler' => new PlanDemoHandler(), 'input' => new ActionInput(123, [])],
            ['handler' => new ConfirmDemoHandler(), 'input' => new ActionInput(123, ['confirmed' => 1])],
            ['handler' => new SendInvoiceHandler(), 'input' => new ActionInput(123, [])],
            ['handler' => new ConfirmPaymentHandler(), 'input' => new ActionInput(123, [])],
            ['handler' => new IssueCredentialHandler(), 'input' => new ActionInput(123, [])],
            ['handler' => new ArchiveDealHandler(), 'input' => new ActionInput(123, [])],
            ['handler' => new CloseLostHandler(), 'input' => new ActionInput(123, [])],
            ['handler' => new DefaultHandler(), 'input' => new ActionInput(123, [])],
        ];

        foreach ($handlers as $config) {
            $result = $config['handler']->handle($config['input']);
            $this->assertTrue(
                $result->success,
                get_class($config['handler']) . ' должен возвращать success=true'
            );
        }
    }

    /**
     * Тест: Все хендлеры имеют корректный eventCode
     * Исправлено: передаём корректные входные данные для каждого хендлера
     */
    public function testAllHandlersHaveValidEventCode(): void
    {
        $handlers = [
            [
                'handler' => new StartWorkHandler(),
                'input' => new ActionInput(123, []),
                'expectedCode' => 'start_work'
            ],
            [
                'handler' => new MakeCallHandler(),
                'input' => new ActionInput(123, ['lp_reached' => 1, 'call_result' => 'OK']),
                'expectedCode' => 'call_successful'
            ],
            [
                'handler' => new FillDiscoveryHandler(),
                'input' => new ActionInput(123, []),
                'expectedCode' => 'fill_discovery'
            ],
            [
                'handler' => new PlanDemoHandler(),
                'input' => new ActionInput(123, []),
                'expectedCode' => 'plan_demo'
            ],
            [
                'handler' => new ConfirmDemoHandler(),
                'input' => new ActionInput(123, ['confirmed' => 1]),
                'expectedCode' => 'confirm_demo'
            ],
            [
                'handler' => new SendInvoiceHandler(),
                'input' => new ActionInput(123, []),
                'expectedCode' => 'send_invoice'
            ],
            [
                'handler' => new ConfirmPaymentHandler(),
                'input' => new ActionInput(123, []),
                'expectedCode' => 'confirm_payment'
            ],
            [
                'handler' => new IssueCredentialHandler(),
                'input' => new ActionInput(123, []),
                'expectedCode' => 'issue_credential'
            ],
            [
                'handler' => new ArchiveDealHandler(),
                'input' => new ActionInput(123, []),
                'expectedCode' => 'archive_deal'
            ],
            [
                'handler' => new CloseLostHandler(),
                'input' => new ActionInput(123, []),
                'expectedCode' => 'close_lost'
            ],
            [
                'handler' => new DefaultHandler(),
                'input' => new ActionInput(123, []),
                'expectedCode' => 'default_action'
            ],
        ];

        foreach ($handlers as $config) {
            $result = $config['handler']->handle($config['input']);
            $this->assertEquals(
                $config['expectedCode'],
                $result->eventCode,
                get_class($config['handler']) . ' должен иметь eventCode=' . $config['expectedCode']
            );
        }
    }

    public function testAllHandlersHaveNonEmptyMessage(): void
    {
        $handlers = [
            new StartWorkHandler(),
            new MakeCallHandler(),
            new FillDiscoveryHandler(),
            new PlanDemoHandler(),
            new ConfirmDemoHandler(),
            new SendInvoiceHandler(),
            new ConfirmPaymentHandler(),
            new IssueCredentialHandler(),
            new ArchiveDealHandler(),
            new CloseLostHandler(),
            new DefaultHandler(),
        ];

        foreach ($handlers as $handler) {
            $result = $handler->handle(new ActionInput(123, []));
            $this->assertNotEmpty(
                $result->message,
                get_class($handler) . ' должен иметь не пустое сообщение'
            );
        }
    }
}