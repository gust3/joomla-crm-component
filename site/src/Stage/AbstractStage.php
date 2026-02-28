<?php
namespace Gust\Component\Crmstages\Site\Stage;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;

abstract class AbstractStage implements StageInterface
{
    protected $db;

    public function __construct()
    {
        $this->db = Factory::getContainer()->get(DatabaseInterface::class);
    }

    abstract public function getName(): string;
    abstract public function getMlsCode(): string;
    abstract public function getNextStages(): array;
    abstract public function getRequiredEvents(): array;
    abstract public function getDescription(): string;

    public function getAvailableActions(int $companyId): array
    {
        $actions = [];

        // 1. Получаем следующую стадию и передаем её в createActionForStage
        $nextStages = $this->getNextStages();
        $nextStage = $nextStages[0] ?? '';

        $rawActions = $this->createActionForStage($nextStage);

        if (empty($rawActions)) {
            return [];
        }

        $requiredEvents = $this->getRequiredEvents();
        $currentStageName = $this->getName();

        foreach ($rawActions as $action) {
            $actionCode = $action['code'] ?? '';
            $targetStage = $action['target_stage'] ?? '';

            // Если действие не меняет стадию (звонок, заметка) — показываем всегда
            if (empty($targetStage) || $targetStage === $currentStageName) {
                $actions[] = $action;
                continue;
            }

            // 🔥 КЛЮЧЕВОЕ ИСПРАВЛЕНИЕ:
            // Если действие САМО является требуемым событием — показываем его всегда!
            // Иначе: нельзя показать кнопку, пока событие не выполнено,
            // а событие нельзя выполнить без кнопки.
            if (in_array($actionCode, $requiredEvents, true)) {
                $actions[] = $action;
                continue;
            }

            // Для остальных переходов (например, повторные действия) проверяем требования
            if ($this->canTransitionTo($targetStage, $companyId)) {
                $actions[] = $action;
            }
        }

        return $actions;
    }

    public function canTransitionTo(string $targetStage, int $companyId): bool
    {
        // В N0 (Null) можно перейти из любой стадии без требований
        if ($targetStage === 'N0') {
            return true;
        }

        $requiredEvents = $this->getRequiredEvents();
        foreach ($requiredEvents as $event) {
            if (!$this->hasEvent($companyId, $event)) {
                return false;
            }
        }
        return true;
    }

    protected function hasEvent(int $companyId, string $eventCode): bool
    {
        $query = $this->db->getQuery(true)
            ->select('COUNT(*)')
            ->from($this->db->quoteName('#__crm_events'))
            ->where($this->db->quoteName('company_id') . ' = ' . (int) $companyId)
            ->where($this->db->quoteName('event_code') . ' = ' . $this->db->quote($eventCode));

        $this->db->setQuery($query);
        return (int) $this->db->loadResult() > 0;
    }

    // Возвращает массив действий (один или несколько)
    abstract protected function createActionForStage(string $nextStage): array;
}