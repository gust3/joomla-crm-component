<?php
namespace Gust\Component\Crmstages\Site\Service\Handler;

use Gust\Component\Crmstages\Site\Service\BaseActionHandler;
use Gust\Component\Crmstages\Site\Service\Dto\ActionInput;
use Gust\Component\Crmstages\Site\Service\Dto\ActionResult;
use Joomla\CMS\Factory;

class ConfirmDemoHandler extends BaseActionHandler
{
    protected function getActionCode(): string
    {
        return 'confirm_demo';
    }

    public function handle(ActionInput $input): ActionResult
    {
        $confirmed = $input->getInt('confirmed', 0);
        $demoResult = $input->getString('demo_result', '');
        $feedback = $input->getString('feedback', '');

        $demoData = [
            'demo_confirmed' => $confirmed,
            'demo_result' => $demoResult,
            'feedback' => $feedback,
            'confirmed_at' => Factory::getDate()->toSql()
        ];

        if ($input->companyId) {
            $this->updateCompanyDiscovery($input->companyId, $demoData);
        }

        $comment = "✅ Демо подтверждено: {$demoResult}" . ($feedback ? " | Отзыв: {$feedback}" : "");

        return $this->success(
            message: 'Демо подтверждено!',
            eventCode: 'confirm_demo',
            shouldTransition: true,
            targetStage: 'Demo_done',
            comment: $comment
        );
    }
}