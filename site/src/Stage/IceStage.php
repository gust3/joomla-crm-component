<?php
namespace Gust\Component\Crmstages\Site\Stage;

class IceStage extends AbstractStage
{
    public function getName(): string
    {
        return 'Ice';
    }

    public function getMlsCode(): string
    {
        return 'C0';
    }

    public function getNextStages(): array
    {
        return ['Touched'];
    }

    public function getRequiredEvents(): array
    {
        return [];
    }

    public function getDescription(): string
    {
        return 'Заявка создана. Работа по позиции начата.';
    }

    protected function createActionForStage(string $nextStage): array
    {
        $actions = [
            [
                'code' => 'start_work',
                'label' => '▶️ Начать работу',
                'target_stage' => $nextStage,
                'fields' => [
                    'comment' => ['type' => 'textarea', 'required' => false, 'label' => 'Комментарий']
                ]
            ]
        ];

        // Добавляем кнопку закрытия сделки для ВСЕХ стадий
        $actions[] = [
            'code' => 'close_lost',
            'label' => '❌ Закрыть как неудачу',
            'target_stage' => 'N0',
            'fields' => [
                'lose_reason' => ['type' => 'textarea', 'required' => true, 'label' => 'Причина отказа']
            ]
        ];

        return $actions;
    }
}