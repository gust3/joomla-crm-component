<?php
namespace Gust\Component\Crmstages\Site\View\Company;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Factory;
use Gust\Component\Crmstages\Site\Service\StageFactory;
use Joomla\CMS\Router\Route;

class HtmlView extends BaseHtmlView
{
    protected $item;
    protected $state;
    protected $availableActions;
    protected $history;
    protected $mlsCode;

    public function display($tpl = null)
    {
        $this->item = $this->get('Item');
        $this->state = $this->get('State');

        if ($this->item) {
            $stage = StageFactory::create($this->item->current_stage);

            $nextStages = $stage->getNextStages();
            $nextStage = $nextStages[0] ?? '';
            $this->availableActions = $stage->getAvailableActions($this->item->id, $nextStage);
            $this->mlsCode = $stage->getMlsCode();

            // 🔥 ДЕКОДИРУЕМ discovery_data
            $this->discoveryData = !empty($this->item->discovery_data)
                ? json_decode($this->item->discovery_data, true)
                : [];
        } else {
            $this->availableActions = [];
            $this->mlsCode = '';
            $this->discoveryData = [];
        }

        $this->history = $this->getHistory($this->item->id ?? 0);

        // 🔹 ДОБАВИТЬ: Настройка хлебных крошек
        $this->setBreadcrumbs($this->item);

        if (count($errors = $this->get('Errors'))) {
            throw new \Exception(implode("\n", $errors), 500);
        }

        return parent::display($tpl);
    }

    /**
     * Устанавливает хлебные крошки для карточки компании
     */
    private function setBreadcrumbs($item): void
    {
        $app = Factory::getApplication();
        $breadcrumbs = $app->getPathway();

        // 2. Добавляем текущую компанию (без ссылки - активный пункт)
        if ($item && !empty($item->name)) {
            $breadcrumbs->addItem($this->escape($item->name));
        }
    }

    private function getHistory(int $companyId): array
    {
        if (!$companyId) {
            return [];
        }

        $db = Factory::getContainer()->get(\Joomla\Database\DatabaseInterface::class);
        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__crm_events'))
            ->where($db->quoteName('company_id') . ' = ' . (int) $companyId)
            ->order($db->quoteName('created') . ' DESC');
        $db->setQuery($query);
        return $db->loadObjectList();
    }
}