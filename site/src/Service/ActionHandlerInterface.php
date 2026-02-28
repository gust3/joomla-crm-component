<?php
namespace Gust\Component\Crmstages\Site\Service;

use Gust\Component\Crmstages\Site\Service\Dto\ActionInput;
use Gust\Component\Crmstages\Site\Service\Dto\ActionResult;

/**
 * Интерфейс для обработчиков действий
 */
interface ActionHandlerInterface
{
    /**
     * Обрабатывает действие
     *
     * @param ActionInput $input Входные данные
     * @return ActionResult Результат выполнения
     */
    public function handle(ActionInput $input): ActionResult;

    /**
     * Проверяет, поддерживает ли хендлер данный код действия
     *
     * @param string $actionCode Код действия
     * @return bool
     */
    public function supports(string $actionCode): bool;
}