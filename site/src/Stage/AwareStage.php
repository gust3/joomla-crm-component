<?php
namespace Gust\Component\Crmstages\Site\Stage;

class AwareStage extends AbstractStage
{
    public function getName(): string { return 'Aware'; }
    public function getMlsCode(): string { return 'C2'; }

    public function getNextStages(): array { return ['Interested']; }

    // Для перехода в Interested нужно заполнить Discovery
    public function getRequiredEvents(): array { return ['fill_discovery']; }

    public function getDescription(): string
    {
        return 'ЛПР ознакомлен с предложением. Необходимо заполнить Discovery форму для квалификации.';
    }

    protected function createActionForStage(string $nextStage): array
    {
        $actions = [
            [
                'code' => 'fill_discovery',
                'label' => '📝 Заполнить Discovery',
                'target_stage' => $nextStage,
                'fields' => [
                    'pains' => ['type' => 'textarea', 'required' => true, 'label' => '🎯 Основные проблемы / задачи'],
                    'budget' => ['type' => 'text', 'required' => false, 'label' => '💰 Бюджет'],
                    'timeline' => ['type' => 'text', 'required' => false, 'label' => '📅 Сроки'],
                    'decision_maker' => ['type' => 'text', 'required' => false, 'label' => '👥 ЛПР (ФИО, должность)'],
                    'next_steps' => ['type' => 'textarea', 'required' => false, 'label' => '🔄 Следующие шаги']
                ]
            ]
        ];

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