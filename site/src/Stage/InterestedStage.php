<?php
namespace Gust\Component\Crmstages\Site\Stage;

class InterestedStage extends AbstractStage
{
    public function getName(): string { return 'Interested'; }
    public function getMlsCode(): string { return 'W1'; }

    public function getNextStages(): array { return ['demo_planned']; }

    // Для перехода в demo_planned нужно событие plan_demo
    public function getRequiredEvents(): array { return ['plan_demo']; }

    public function getDescription(): string
    {
        return 'Клиент заинтересован. Запланируйте демо-сессию.';
    }

    protected function createActionForStage(string $nextStage): array
    {
        $actions = [];

        // 🔹 Действие 1: Запланировать Демо
        $actions[] = [
            'code' => 'plan_demo',
            'label' => '📅 Запланировать Демо',
            'target_stage' => $nextStage, // demo_planned
            'fields' => [
                'demo_date' => ['type' => 'date', 'required' => true, 'label' => '📆 Дата демо'],
                'demo_time' => ['type' => 'time', 'required' => true, 'label' => '⏰ Время'],
                'demo_link' => ['type' => 'text', 'required' => false, 'label' => '🔗 Ссылка на демо (Zoom/Meet)'],
                'demo_comment' => ['type' => 'textarea', 'required' => false, 'label' => '💬 Комментарий']
            ]
        ];

        // 🔹 Действие 2: Закрыть как неудачу
        $actions[] = $this->getCloseLostAction();

        return $actions; // 👈 Возвращаем массив действий
    }

    private function getCloseLostAction(): array
    {
        return [
            'code' => 'close_lost',
            'label' => '❌ Закрыть как неудачу',
            'target_stage' => 'N0',
            'fields' => [
                'lose_reason' => ['type' => 'textarea', 'required' => true, 'label' => 'Причина отказа']
            ]
        ];
    }
}