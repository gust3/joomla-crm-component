<?php
namespace Gust\Component\Crmstages\Site\Service\Handler;

use Gust\Component\Crmstages\Site\Service\BaseActionHandler;
use Gust\Component\Crmstages\Site\Service\Dto\ActionInput;
use Gust\Component\Crmstages\Site\Service\Dto\ActionResult;

class StartWorkHandler extends BaseActionHandler
{
    protected function getActionCode(): string
    {
        return 'start_work';
    }

    public function handle(ActionInput $input): ActionResult
    {
        $comment = $input->getString('comment', '');

        return $this->success(
            message: 'Работа начата!',
            eventCode: 'start_work',
            shouldTransition: true,
            targetStage: 'Touched',
            comment: "Работа начата. {$comment}"
        );
    }
}