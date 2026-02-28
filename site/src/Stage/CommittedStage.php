<?php
namespace Gust\Component\Crmstages\Site\Stage;

class CommittedStage extends AbstractStage
{
    public function getName(): string { return 'Committed'; }
    public function getMlsCode(): string { return 'W4'; }

    public function getNextStages(): array { return ['Customer']; }

    public function getRequiredEvents(): array { return ['confirm_payment']; }

    public function getDescription(): string
    {
        return 'Клиент принял решение. Подтвердите получение оплаты.';
    }

    protected function createActionForStage(string $nextStage): array
    {
        $actions = [];

        // 🔹 Действие 1: Подтвердить оплату (БЕЗ ПОЛЕЙ)
        $actions[] = [
            'code' => 'confirm_payment',
            'label' => '✅ Оплата получена',
            'target_stage' => $nextStage, // Customer
            'fields' => [] // 👈 Пустой массив — форма не показывается
        ];

        // 🔹 Действие 2: Закрыть как неудачу
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