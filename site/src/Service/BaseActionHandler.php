<?php
namespace Gust\Component\Crmstages\Site\Service;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Gust\Component\Crmstages\Site\Service\Dto\ActionInput;
use Gust\Component\Crmstages\Site\Service\Dto\ActionResult;

/**
 * Базовый класс для всех обработчиков действий
 * Предоставляет общую функциональность (логирование, обновление компании)
 */
abstract class BaseActionHandler implements ActionHandlerInterface
{
    protected $db;

    public function __construct()
    {
        $this->db = Factory::getContainer()->get(DatabaseInterface::class);
    }

    /**
     * Код действия, который поддерживает этот хендлер
     * Переопределяется в дочерних классах
     */
    abstract protected function getActionCode(): string;

    /**
     * Проверяет поддержку действия
     */
    public function supports(string $actionCode): bool
    {
        return $actionCode === $this->getActionCode();
    }

    /**
     * Логирование события
     */
    protected function logEvent(int $companyId, string $eventCode, string $comment): void
    {
        $object = new \stdClass();
        $object->company_id = $companyId;
        $object->event_code = $eventCode;
        $object->comment = $comment;
        $object->created = Factory::getDate()->toSql();
        $this->db->insertObject('#__crm_events', $object);
    }

    /**
     * Обновление discovery_data компании
     */
    protected function updateCompanyDiscovery(int $companyId, array $newData): bool
    {
        try {
            $query = $this->db->getQuery(true)
                ->select($this->db->quoteName('discovery_data'))
                ->from($this->db->quoteName('#__crm_companies'))
                ->where($this->db->quoteName('id') . ' = ' . (int) $companyId);
            $this->db->setQuery($query);
            $existingJson = $this->db->loadResult();

            $existingData = !empty($existingJson) ? json_decode($existingJson, true) : [];
            $mergedData = array_merge($existingData, $newData);

            $query = $this->db->getQuery(true)
                ->update($this->db->quoteName('#__crm_companies'))
                ->set($this->db->quoteName('discovery_data') . ' = ' . $this->db->quote(json_encode($mergedData, JSON_UNESCAPED_UNICODE)))
                ->set($this->db->quoteName('modified') . ' = ' . $this->db->quote(Factory::getDate()->toSql()))
                ->where($this->db->quoteName('id') . ' = ' . (int) $companyId);

            $this->db->setQuery($query);
            return $this->db->execute();
        } catch (\Exception $e) {
            Factory::getLogger()->error('Failed to update discovery  ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Хелпер для создания успешного результата
     */
    protected function success(
        string $message,
        string $eventCode,
        bool $shouldTransition = false,
        ?string $targetStage = null,
        string $comment = ''
    ): ActionResult {
        return ActionResult::success(
            message: $message,
            eventCode: $eventCode,
            shouldTransition: $shouldTransition,
            targetStage: $targetStage,
            comment: $comment
        );
    }
}