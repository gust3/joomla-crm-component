<?php
namespace Gust\Component\Crmstages\Site\Model;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;

class CompaniesModel extends ListModel
{
    public function __construct($config = [])
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = ['id', 'name', 'current_stage'];
        }

        parent::__construct($config);
    }

    protected function getListQuery()
    {
        // Создаём новый запрос
        $db = $this->getDatabase();
        $query = $db->getQuery(true);

        // Формируем запрос
        $query->select($db->quoteName(['id', 'name', 'current_stage', 'created', 'modified']))
            ->from($db->quoteName('#__crm_companies'))
            ->order($db->quoteName('id') . ' DESC');

        // Возвращаем объект запроса (это важно!)
        return $query;
    }
}