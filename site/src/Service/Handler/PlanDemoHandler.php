<?php
namespace Gust\Component\Crmstages\Site\Service\Handler;

use Gust\Component\Crmstages\Site\Service\BaseActionHandler;
use Gust\Component\Crmstages\Site\Service\Dto\ActionInput;
use Gust\Component\Crmstages\Site\Service\Dto\ActionResult;
use Joomla\CMS\Factory;

class PlanDemoHandler extends BaseActionHandler
{
    protected function getActionCode(): string
    {
        return 'plan_demo';
    }

    public function handle(ActionInput $input): ActionResult
    {
        $demoDate = $input->getString('demo_date', '');
        $demoTime = $input->getString('demo_time', '');
        $demoLink = $input->getString('demo_link', '');
        $demoComment = $input->getString('demo_comment', '');

        $demoData = [
            'demo_date' => $demoDate,
            'demo_time' => $demoTime,
            'demo_link' => $demoLink,
            'demo_comment' => $demoComment,
            'planned_at' => Factory::getDate()->toSql()
        ];

        if ($input->companyId) {
            $this->updateCompanyDiscovery($input->companyId, $demoData);
        }

        $comment = "📅 Демо запланировано: {$demoDate} {$demoTime}" . ($demoLink ? " | Ссылка: {$demoLink}" : "");

        return $this->success(
            message: 'Демо запланировано!',
            eventCode: 'plan_demo',
            shouldTransition: true,
            targetStage: 'demo_planned',
            comment: $comment
        );
    }
}