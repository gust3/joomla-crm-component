<?php
namespace Gust\Component\Crmstages\Site\Stage;

class CustomerStage extends AbstractStage
{
    public function getName(): string { return 'Customer'; }
    public function getMlsCode(): string { return 'H2'; }

    public function getNextStages(): array { return ['Activated']; }

    public function getRequiredEvents(): array { return ['issue_credential']; }

    public function getDescription(): string
    {
        return 'Оплата получена. Выдача удостоверения.';
    }

    protected function createActionForStage(string $nextStage): array
    {
        $actions = [];

        // 🔹 Действие 1: Выдать удостоверение
        $actions[] = [
            'code' => 'issue_credential',
            'label' => '🎫 Выдать удостоверение',
            'target_stage' => $nextStage, // Activated
            'fields' => [
                'credential_number' => ['type' => 'text', 'required' => true, 'label' => 'Номер удостоверения'],
                'issue_date' => ['type' => 'date', 'required' => true, 'label' => 'Дата выдачи'],
                'credential_comment' => ['type' => 'textarea', 'required' => false, 'label' => 'Комментарий']
            ]
        ];

        // 🔹 Действие 2: Закрыть как неудачу (на всякий случай)
        $actions[] = $this->getCloseLostAction();

        return $actions;
    }

    private function getCloseLostAction(): array
    {
        return [
            'code' => 'close_lost',
            'label' => '❌ Закрыть как неудачу',
            'target_stage' => 'N0',
            'fields' => [
                'lose_reason' => ['type' => 'textarea', 'required' => true, 'label' => 'Причина']
            ]
        ];
    }
}