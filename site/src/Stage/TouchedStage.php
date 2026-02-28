<?php
namespace Gust\Component\Crmstages\Site\Stage;

class TouchedStage extends AbstractStage
{
    public function getName(): string { return 'Touched'; }
    public function getMlsCode(): string { return 'C1'; }

    public function getNextStages(): array { return ['Aware']; }
    public function getRequiredEvents(): array { return []; }

    public function getDescription(): string
    {
        return 'Был контакт с ЛПР. Необходимо подтвердить интерес для перехода к квалификации.';
    }

    protected function createActionForStage(string $nextStage): array
    {
        $actions = [
            [
                'code' => 'make_call',
                'label' => '📞 Позвонить ЛПР',
                'target_stage' => 'Aware', // <--- ИЗМЕНЕНО: было 'Touched'
                'fields' => [
                    'lp_reached' => ['type' => 'select', 'required' => true, 'label' => 'Дозвонились?'],
                    'call_result' => ['type' => 'textarea', 'required' => true, 'label' => 'Результат'],
                    'call_time' => ['type' => 'text', 'required' => false, 'label' => 'Время']
                ]
            ]
        ];

        // Кнопка закрытия сделки
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