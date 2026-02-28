<?php
namespace Gust\Component\Crmstages\Site\Service;

use Gust\Component\Crmstages\Site\Stage\StageInterface;

class StageFactory
{
    private static $stages = [
        'Ice' => \Gust\Component\Crmstages\Site\Stage\IceStage::class,
        'Touched' => \Gust\Component\Crmstages\Site\Stage\TouchedStage::class,
        'Aware' => \Gust\Component\Crmstages\Site\Stage\AwareStage::class,
        'Interested' => \Gust\Component\Crmstages\Site\Stage\InterestedStage::class,
        'demo_planned' => \Gust\Component\Crmstages\Site\Stage\DemoPlannedStage::class,
        'Demo_done' => \Gust\Component\Crmstages\Site\Stage\DemoDoneStage::class,
        'Committed' => \Gust\Component\Crmstages\Site\Stage\CommittedStage::class,
        'Customer' => \Gust\Component\Crmstages\Site\Stage\CustomerStage::class,
        'Activated' => \Gust\Component\Crmstages\Site\Stage\ActivatedStage::class,
        'Archived' => \Gust\Component\Crmstages\Site\Stage\ArchivedStage::class,
        'N0' => \Gust\Component\Crmstages\Site\Stage\N0Stage::class,
    ];

    public static function create(string $stageCode): StageInterface
    {
        if (!isset(self::$stages[$stageCode])) {
            throw new \Exception("Unknown stage: {$stageCode}");
        }

        $class = self::$stages[$stageCode];
        return new $class();
    }

    public static function getMlsCode(string $stageCode): string
    {
        $stage = self::create($stageCode);
        return $stage->getMlsCode();
    }

    public static function getAllStages(): array
    {
        return array_keys(self::$stages);
    }
}