<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Crmstages
 * @author     gust33 <CWtorin@gmail.com>
 * @copyright  2026 gust33
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Gust\Component\Crmstages\Site\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Utilities\ArrayHelper;
// 🔹 Добавляем импорт сервиса демо-данных
use Gust\Component\Crmstages\Site\Service\DemoDataService;

/**
 * Companies class.
 *
 * @since  1.0.0
 */
class CompaniesController extends FormController
{
    /**
     * Proxy for getModel.
     *
     * @param   string  $name    The model name. Optional.
     * @param   string  $prefix  The class prefix. Optional
     * @param   array   $config  Configuration array for model. Optional
     *
     * @return  object	The model
     *
     * @since   1.0.0
     */
    public function getModel($name = 'Companies', $prefix = 'Site', $config = array())
    {
        return parent::getModel($name, $prefix, array('ignore_request' => true));
    }

    // 🔹 Метод: Создать одну демо-компанию
    public function addDemoCompany()
    {
        $this->checkToken();

        $demoService = new DemoDataService();
        $result = $demoService->createRandomCompany();

        if ($result['success']) {
            $this->setMessage($result['message'], 'success');
        } else {
            $this->setMessage($result['message'], 'error');
        }

        $this->setRedirect(Route::_('index.php?option=com_crmstages&view=companies', false));
    }

    // 🔹 Метод: Создать несколько демо-компаний
    public function addMultipleDemoCompanies()
    {
        $this->checkToken();

        $count = $this->input->getInt('count', 5);
        $demoService = new DemoDataService();
        $results = $demoService->createMultipleCompanies($count);

        $successCount = count(array_filter($results, fn($r) => $r['success']));
        $this->setMessage("Создано {$successCount} демо-компаний!", 'success');

        $this->setRedirect(Route::_('index.php?option=com_crmstages&view=companies', false));
    }

    // 🔹 Метод: Очистить демо-компании (опционально)
    public function clearDemoCompanies()
    {
        $this->checkToken();

        $db = Factory::getContainer()->get(\Joomla\Database\DatabaseInterface::class);
        $query = $db->getQuery(true)
            ->delete($db->quoteName('#__crm_companies'))
            ->where($db->quoteName('name') . ' LIKE ' . $db->quote('ООО%'))
            ->orWhere($db->quoteName('name') . ' LIKE ' . $db->quote('АО%'))
            ->orWhere($db->quoteName('name') . ' LIKE ' . $db->quote('ИП%'));

        $db->setQuery($query);
        $deleted = $db->execute();

        $this->setMessage("Удалено {$deleted} демо-компаний", 'warning');
        $this->setRedirect(Route::_('index.php?option=com_crmstages&view=companies', false));
    }
}