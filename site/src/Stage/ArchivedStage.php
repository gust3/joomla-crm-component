<?php
namespace Gust\Component\Crmstages\Site\Stage;

class ArchivedStage extends AbstractStage
{
    public function getName(): string { return 'Archived'; }
    public function getMlsCode(): string { return 'H4'; }

    public function getNextStages(): array { return []; }
    public function getRequiredEvents(): array { return []; }

    public function getDescription(): string
    {
        return 'Сделка успешно завершена и архивирована.';
    }

    protected function createActionForStage(string $nextStage): array
    {
        // Нет действий — стадия финальная
        return [];
    }
}