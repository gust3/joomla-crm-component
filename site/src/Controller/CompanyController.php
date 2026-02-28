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
// 🔹 Импорт сервисов
use Gust\Component\Crmstages\Site\Service\TransitionService;
use Gust\Component\Crmstages\Site\Service\ActionHandler;
use Gust\Component\Crmstages\Site\Service\Dto\ActionInput;
use Gust\Component\Crmstages\Site\Service\Dto\ActionResult;

/**
 * Company controller class.
 *
 * @since  1.0.0
 */
class CompanyController extends FormController
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
    public function getModel($name = 'Company', $prefix = 'Site', $config = array())
    {
        return parent::getModel($name, $prefix, array('ignore_request' => true));
    }

    /**
     * Обрабатывает выполнение действия над компанией
     *
     * @return  void
     */
    public function performAction()
    {
        $this->checkToken();

        $companyId = $this->input->getInt('id');
        $action = $this->input->getCmd('action');

        $company = $this->getCompany($companyId);
        if (!$company) {
            $this->setMessage('Компания не найдена', 'error');
            $this->setRedirect($this->getCompaniesListUrl());
            return;
        }

        // 🔹 1. Парсим входные данные в DTO (выносим логику из ActionHandler)
        $input = Factory::getApplication()->input;
        $actionInput = new ActionInput(
            companyId: $companyId,
            fields: $input->getArray() // Получаем все POST-поля
        );

        // 🔹 2. Делегируем бизнес-логику сервису (передаём DTO, получаем DTO)
        $actionHandler = new ActionHandler();
        $result = $actionHandler->handle($action, $actionInput);

        // 🔹 3. Проверяем тип результата (для безопасности)
        if (!$result instanceof ActionResult) {
            $this->setMessage('Ошибка: некорректный результат действия', 'error');
            $this->setRedirect($this->getCompanyUrl($companyId));
            return;
        }

        // 🔹 4. Конвертируем DTO в массив для совместимости с Joomla API
        $resultArray = $result->toArray();

        // 🔹 5. Устанавливаем сообщение
        $this->setMessage($resultArray['message'], $resultArray['messageType']);

        // 🔹 6. Если нужно перейти на другую стадию
        if ($result->shouldTransition && $result->success) {
            $transitionService = new TransitionService();
            $targetStage = $result->targetStage;

            if ($transitionService->performTransition(
                $companyId,
                $company->current_stage,
                $targetStage,
                $resultArray['eventCode'],
                $resultArray['comment']
            )) {
                $this->setMessage($resultArray['message'] . ' Стадия: ' . $targetStage, $resultArray['messageType']);
            }
        }

        // 🔹 7. Редирект обратно на карточку
        $this->setRedirect($this->getCompanyUrl($companyId));
    }

    /**
     * Получает URL карточки компании
     *
     * @param   int  $id  ID компании
     *
     * @return  string
     */
    private function getCompanyUrl(int $id): string
    {
        return Route::_('index.php?option=com_crmstages&view=company&id=' . $id, false);
    }

    /**
     * Получает URL списка компаний
     *
     * @return  string
     */
    private function getCompaniesListUrl(): string
    {
        return Route::_('index.php?option=com_crmstages&view=companies', false);
    }

    /**
     * Загружает компанию из БД
     *
     * @param   int  $id  ID компании
     *
     * @return  object|null
     */
    private function getCompany(int $id)
    {
        $db = Factory::getContainer()->get(\Joomla\Database\DatabaseInterface::class);
        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__crm_companies'))
            ->where($db->quoteName('id') . ' = ' . (int) $id);
        $db->setQuery($query);
        return $db->loadObject();
    }
}