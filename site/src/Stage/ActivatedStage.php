<?php
namespace Gust\Component\Crmstages\Site\Stage;

class ActivatedStage extends AbstractStage
{
    public function getName(): string { return 'Activated'; }
    public function getMlsCode(): string { return 'H3'; }

    public function getNextStages(): array { return ['Archived']; }
    public function getRequiredEvents(): array { return []; }

    public function getDescription(): string
    {
        return 'Клиент активирован. Удостоверение выдано. Можно архивировать.';
    }

    protected function createActionForStage(string $nextStage): array
    {
        $actions = [];

        // 🔹 Действие: Архивировать
        $actions[] = [
            'code' => 'archive_deal',
            'label' => '📁 В архив',
            'target_stage' => $nextStage, // Archived
            'fields' => [
                'archive_comment' => ['type' => 'textarea', 'required' => false, 'label' => 'Комментарий к архиву']
            ]
        ];

        return $actions;
    }
}