<?php
namespace Gust\Component\Crmstages\Site\Stage;

class N0Stage extends AbstractStage
{
    public function getName(): string
    {
        return 'N0';
    }

    public function getMlsCode(): string
    {
        return 'N0';
    }

    public function getNextStages(): array
    {
        return []; // Финальная стадия, нет переходов дальше
    }

    public function getRequiredEvents(): array
    {
        return []; // Можно перейти из любой стадии
    }

    public function getDescription(): string
    {
        return 'Сделка закрыта как неудачная. Работа прекращена.';
    }

    protected function createActionForStage(string $nextStage): array
    {
        return []; // Нет доступных действий
    }
}