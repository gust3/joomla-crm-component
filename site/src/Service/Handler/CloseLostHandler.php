<?php
namespace Gust\Component\Crmstages\Site\Service\Handler;

use Gust\Component\Crmstages\Site\Service\BaseActionHandler;
use Gust\Component\Crmstages\Site\Service\Dto\ActionInput;
use Gust\Component\Crmstages\Site\Service\Dto\ActionResult;

class CloseLostHandler extends BaseActionHandler
{
    protected function getActionCode(): string
    {
        return 'close_lost';
    }

    public function handle(ActionInput $input): ActionResult
    {
        $loseReason = $input->getString('lose_reason', '');

        return new ActionResult(
            success: true,
            message: 'Сделка закрыта как неудачная',
            messageType: 'warning',
            comment: "Сделка закрыта как неудачная. Причина: {$loseReason}",
            eventCode: 'close_lost',
            shouldTransition: true,
            targetStage: 'N0'
        );
    }
}