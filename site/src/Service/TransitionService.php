<?php
namespace Gust\Component\Crmstages\Site\Service;

use Gust\Component\Crmstages\Site\Stage\StageInterface;
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;

class TransitionService
{
    private $db;

    public function __construct()
    {
        $this->db = Factory::getContainer()->get(DatabaseInterface::class);
    }

    public function canTransition(StageInterface $fromStage, string $toStage, int $companyId): bool
    {
        return $fromStage->canTransitionTo($toStage, $companyId);
    }

    public function performTransition(int $companyId, string $fromStage, string $toStage, string $action, string $comment = ''): bool
    {
        $stage = StageFactory::create($fromStage);

        // 🔥 1. СНАЧАЛА логируем событие (чтобы оно появилось в БД до проверки)
        $this->logEvent($companyId, $action, $comment);

        // 🔥 2. ПОТОМ проверяем переход (теперь hasEvent() найдет запись)
        if (!$this->canTransition($stage, $toStage, $companyId)) {
            // Опционально: можно удалить событие, если переход невалиден,
            // но обычно лучше оставить лог для отладки
            return false;
        }

        // 3. Обновляем стадию
        $this->updateCompanyStage($companyId, $toStage);

        return true;
    }

    private function logEvent(int $companyId, string $eventCode, string $comment): void
    {
        $object = new \stdClass();
        $object->company_id = $companyId;
        $object->event_code = $eventCode;
        $object->comment = $comment;
        $object->created = Factory::getDate()->toSql();
        $this->db->insertObject('#__crm_events', $object);
    }

    private function updateCompanyStage(int $companyId, string $stage): void
    {
        $object = new \stdClass();
        $object->id = $companyId;
        $object->current_stage = $stage;
        $object->modified = Factory::getDate()->toSql();
        $this->db->updateObject('#__crm_companies', $object, 'id');
    }

    public function getCompanyMlsCode(int $companyId): string
    {
        $company = $this->getCompany($companyId);
        if (!$company) {
            return '';
        }
        return StageFactory::getMlsCode($company->current_stage);
    }

    private function getCompany(int $companyId)
    {
        $query = $this->db->getQuery(true)
            ->select('*')
            ->from($this->db->quoteName('#__crm_companies'))
            ->where($this->db->quoteName('id') . ' = ' . (int) $companyId);
        $this->db->setQuery($query);
        return $this->db->loadObject();
    }
    public function isStageTransition(string $action): bool
    {
        // make_call НЕ меняет стадию (остаёмся на Touched)
        // fill_discovery МЕНЯЕТ стадию (переход в Aware)
        $nonTransitionActions = ['make_call'];
        return !in_array($action, $nonTransitionActions);
    }
}