<?php
namespace Gust\Component\Crmstages\Site\Stage;

class DemoDoneStage extends AbstractStage
{
    public function getName(): string { return 'Demo_done'; }
    public function getMlsCode(): string { return 'W3'; }

    public function getNextStages(): array { return ['Committed']; }

    // Нет обязательных событий для перехода в Committed (счёт можно выставить сразу)
    public function getRequiredEvents(): array { return []; }

    public function getDescription(): string
    {
        return 'Демо проведено. Выставление счёта для перехода к сделке.';
    }

    protected function createActionForStage(string $nextStage): array
    {
        $actions = [];

        // 🔹 Действие 1: Выставить счёт
        $actions[] = [
            'code' => 'send_invoice',
            'label' => '📄 Выставить счёт',
            'target_stage' => $nextStage, // Committed
            'fields' => [
                'invoice_number' => ['type' => 'text', 'required' => true, 'label' => 'Номер счёта'],
                'amount' => ['type' => 'number', 'required' => true, 'label' => 'Сумма счёта'],
                'invoice_comment' => ['type' => 'textarea', 'required' => false, 'label' => 'Комментарий к счёту']
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