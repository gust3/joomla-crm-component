<?php
namespace Gust\Component\Crmstages\Site\Stage;

class DemoPlannedStage extends AbstractStage
{
    public function getName(): string { return 'demo_planned'; }
    public function getMlsCode(): string { return 'W2'; }

    public function getNextStages(): array { return ['Demo_done']; }

    // Требуемое событие должно совпадать с кодом действия!
    public function getRequiredEvents(): array { return ['confirm_demo']; }

    public function getDescription(): string
    {
        return 'Демо запланировано. Подтвердите проведение и результат.';
    }

    protected function createActionForStage(string $nextStage): array
    {
        // 👈 Возвращаем массив действий (обернули в [])
        return [
            [
                'code' => 'confirm_demo',
                'label' => '✅ Подтвердить Демо',
                'target_stage' => $nextStage, // Demo_done
                'fields' => [
                    'confirmed' => ['type' => 'select', 'required' => true, 'label' => 'Демо состоялось?'],
                    'demo_result' => ['type' => 'textarea', 'required' => true, 'label' => 'Результат демо'],
                    'feedback' => ['type' => 'textarea', 'required' => false, 'label' => 'Обратная связь клиента']
                ]
            ],
            // 🔹 Также добавим кнопку закрытия сделки на всякий случай
            $this->getCloseLostAction()
        ];
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