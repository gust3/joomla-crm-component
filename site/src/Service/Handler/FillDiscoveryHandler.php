<?php
namespace Gust\Component\Crmstages\Site\Service\Handler;

use Gust\Component\Crmstages\Site\Service\BaseActionHandler;
use Gust\Component\Crmstages\Site\Service\Dto\ActionInput;
use Gust\Component\Crmstages\Site\Service\Dto\ActionResult;
use Joomla\CMS\Factory;

class FillDiscoveryHandler extends BaseActionHandler
{
    protected function getActionCode(): string
    {
        return 'fill_discovery';
    }

    public function handle(ActionInput $input): ActionResult
    {
        $pains = $input->getString('pains', '');
        $budget = $input->getString('budget', '');
        $timeline = $input->getString('timeline', '');
        $decisionMaker = $input->getString('decision_maker', '');
        $nextSteps = $input->getString('next_steps', '');

        $discoveryData = [
            'pains' => $pains,
            'budget' => $budget,
            'timeline' => $timeline,
            'decision_maker' => $decisionMaker,
            'next_steps' => $nextSteps,
            'filled_at' => Factory::getDate()->toSql()
        ];

        if ($input->companyId) {
            $this->updateCompanyDiscovery($input->companyId, $discoveryData);
        }

        $comment = "📝 Discovery: Боли={$pains} | Бюджет={$budget} | Сроки={$timeline} | ЛПР={$decisionMaker}";

        return $this->success(
            message: 'Discovery заполнен и сохранён!',
            eventCode: 'fill_discovery',
            shouldTransition: true,
            targetStage: 'Interested',
            comment: $comment
        );
    }
}