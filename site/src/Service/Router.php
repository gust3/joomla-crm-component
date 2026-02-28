<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Crmstages
 * @author     gust33 <CWtorin@gmail.com>
 * @copyright  2026 gust33
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Gust\Component\Crmstages\Site\Service;

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Factory;
use Joomla\CMS\Categories\Categories;
use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Categories\CategoryFactoryInterface;
use Joomla\CMS\Categories\CategoryInterface;
use Joomla\Database\DatabaseInterface;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\CMS\Component\ComponentHelper;

/**
 * Class CrmstagesRouter
 *
 */
class Router extends RouterView
{
	private $noIDs;
	/**
	 * The category factory
	 *
	 * @var    CategoryFactoryInterface
	 *
	 * @since  1.0.0
	 */
	private $categoryFactory;

	/**
	 * The category cache
	 *
	 * @var    array
	 *
	 * @since  1.0.0
	 */
	private $categoryCache = [];

	public function __construct(SiteApplication $app, AbstractMenu $menu, CategoryFactoryInterface $categoryFactory, DatabaseInterface $db)
	{
		$params = ComponentHelper::getParams('com_crmstages');
		$this->noIDs = (bool) $params->get('sef_ids');
		$this->categoryFactory = $categoryFactory;
		
		
			$companies = new RouterViewConfiguration('companies');
			$this->registerView($companies);
			$ccCompany = new RouterViewConfiguration('company');
			$ccCompany->setKey('id')->setParent($companies);
			$this->registerView($ccCompany);
			$companyform = new RouterViewConfiguration('companyform');
			$companyform->setKey('id');
			$this->registerView($companyform);
		$companies = new RouterViewConfiguration('companies');
		$this->registerView($companies);

		parent::__construct($app, $menu);

		$this->attachRule(new MenuRules($this));
		$this->attachRule(new StandardRules($this));
		$this->attachRule(new NomenuRules($this));
	}


	
		/**
		 * Method to get the segment(s) for an company
		 *
		 * @param   string  $id     ID of the company to retrieve the segments for
		 * @param   array   $query  The request that is built right now
		 *
		 * @return  array|string  The segments of this item
		 */
		public function getCompanySegment($id, $query)
		{
			return array((int) $id => $id);
		}
			/**
			 * Method to get the segment(s) for an companyform
			 *
			 * @param   string  $id     ID of the companyform to retrieve the segments for
			 * @param   array   $query  The request that is built right now
			 *
			 * @return  array|string  The segments of this item
			 */
			public function getCompanyformSegment($id, $query)
			{
				return $this->getCompanySegment($id, $query);
			}

	
		/**
		 * Method to get the segment(s) for an company
		 *
		 * @param   string  $segment  Segment of the company to retrieve the ID for
		 * @param   array   $query    The request that is parsed right now
		 *
		 * @return  mixed   The id of this item or false
		 */
		public function getCompanyId($segment, $query)
		{
			return (int) $segment;
		}
			/**
			 * Method to get the segment(s) for an companyform
			 *
			 * @param   string  $segment  Segment of the companyform to retrieve the ID for
			 * @param   array   $query    The request that is parsed right now
			 *
			 * @return  mixed   The id of this item or false
			 */
			public function getCompanyformId($segment, $query)
			{
				return $this->getCompanyId($segment, $query);
			}

	/**
	 * Method to get categories from cache
	 *
	 * @param   array  $options   The options for retrieving categories
	 *
	 * @return  CategoryInterface  The object containing categories
	 *
	 * @since   1.0.0
	 */
	private function getCategories(array $options = []): CategoryInterface
	{
		$key = serialize($options);

		if (!isset($this->categoryCache[$key]))
		{
			$this->categoryCache[$key] = $this->categoryFactory->createCategory($options);
		}

		return $this->categoryCache[$key];
	}
}
