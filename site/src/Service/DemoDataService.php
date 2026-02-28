<?php
namespace Gust\Component\Crmstages\Site\Service;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;

class DemoDataService
{
    private $db;

    private static $companyNames = [
        'ООО "Вектор Развития"', 'АО "ТехноПром"', 'ИП Смирнов А.В.',
        'ООО "Альфа Групп"', 'ЗАО "Бета Системс"', 'ООО "Гамма Консалт"',
        'АО "Дельта Инвест"', 'ООО "Эпсилон Тех"', 'ИП Иванов П.С.',
        'ООО "Жетон Сервис"', 'АО "Зенит Трейд"', 'ООО "Империал ЛТД"',
        'ИП Петрова М.И.', 'ООО "Капитал Плюс"', 'АО "Лидер Рынка"',
        'ООО "Мегаполис"', 'ИП Сидоров К.А.', 'ООО "Новатор"',
        'АО "Омега Корп"', 'ООО "Прайм Групп"', 'ИП Кузнецов Д.В.',
        'ООО "Квант"', 'АО "РосТех"', 'ООО "Спектр"',
        'ИП Морозов Е.Н.', 'ООО "Титан"', 'АО "УралСиб"',
        'ООО "Феникс"', 'ИП Волков А.А.', 'ООО "Холдинг Партнер"'
    ];

    private static $stages = ['Ice', 'Touched', 'Aware', 'Interested', 'demo_planned', 'Demo_done', 'Committed'];

    public function __construct()
    {
        $this->db = Factory::getContainer()->get(DatabaseInterface::class);
    }

    public function createRandomCompany(): array
    {
        $name = $this->getRandomValue(self::$companyNames);
        $stage = $this->getRandomValue(self::$stages);

        // 🔹 Создаём ОБЪЕКТ, а не массив
        $companyData = new \stdClass();
        $companyData->name = $name;
        $companyData->current_stage = $stage;
        $companyData->discovery_data = null;
        $companyData->created = Factory::getDate()->toSql();
        $companyData->modified = Factory::getDate()->toSql();

        try {
            $this->db->insertObject('#__crm_companies', $companyData);
            $companyId = $this->db->insertid();

            return [
                'success' => true,
                'id' => $companyId,
                'name' => $name,
                'stage' => $stage,
                'message' => "Демо-компания «{$name}» создана!"
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()];
        }
    }

    public function createMultipleCompanies(int $count = 5): array
    {
        $results = [];
        for ($i = 0; $i < $count; $i++) {
            $results[] = $this->createRandomCompany();
        }
        return $results;
    }

    private function getRandomValue(array $array): string
    {
        return $array[array_rand($array)];
    }
}