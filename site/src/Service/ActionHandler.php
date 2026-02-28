<?php
namespace Gust\Component\Crmstages\Site\Service;

use Gust\Component\Crmstages\Site\Service\Dto\ActionInput;
use Gust\Component\Crmstages\Site\Service\Dto\ActionResult;
use Gust\Component\Crmstages\Site\Service\Handler\MakeCallHandler;
use Gust\Component\Crmstages\Site\Service\Handler\FillDiscoveryHandler;
use Gust\Component\Crmstages\Site\Service\Handler\PlanDemoHandler;
use Gust\Component\Crmstages\Site\Service\Handler\ConfirmDemoHandler;
use Gust\Component\Crmstages\Site\Service\Handler\SendInvoiceHandler;
use Gust\Component\Crmstages\Site\Service\Handler\ConfirmPaymentHandler;
use Gust\Component\Crmstages\Site\Service\Handler\IssueCredentialHandler;
use Gust\Component\Crmstages\Site\Service\Handler\ArchiveDealHandler;
use Gust\Component\Crmstages\Site\Service\Handler\StartWorkHandler;
use Gust\Component\Crmstages\Site\Service\Handler\CloseLostHandler;
use Gust\Component\Crmstages\Site\Service\Handler\DefaultHandler;

/**
 * Диспетчер обработчиков действий (Command Pattern)
 * Делегирует выполнение конкретным хендлерам
 */
class ActionHandler
{
    /**
     * @var ActionHandlerInterface[]
     */
    private array $handlers = [];

    public function __construct()
    {
        $this->registerHandlers();
    }

    /**
     * Регистрирует все обработчики
     * Порядок важен: DefaultHandler должен быть последним
     */
    private function registerHandlers(): void
    {
        $this->handlers = [
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
            new DefaultHandler(), // Всегда последний!
        ];
    }

    /**
     * Обрабатывает действие, делегируя соответствующему хендлеру
     *
     * @param string $action Код действия
     * @param ActionInput $input DTO с входными данными
     */
    public function handle(string $action, ActionInput $input): ActionResult
    {
        foreach ($this->handlers as $handler) {
            if ($handler->supports($action)) {
                return $handler->handle($input);
            }
        }

        // Не должно произойти, т.к. DefaultHandler всегда последний
        return new ActionResult(
            success: false,
            message: 'Обработчик действия не найден',
            messageType: 'error'
        );
    }
}