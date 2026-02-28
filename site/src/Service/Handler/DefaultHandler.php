<?php
namespace Gust\Component\Crmstages\Site\Service\Handler;

use Gust\Component\Crmstages\Site\Service\BaseActionHandler;
use Gust\Component\Crmstages\Site\Service\Dto\ActionInput;
use Gust\Component\Crmstages\Site\Service\Dto\ActionResult;

class DefaultHandler extends BaseActionHandler
{
    protected function getActionCode(): string
    {
        return 'default';
    }

    public function supports(string $actionCode): bool
    {
        // Default handler поддерживает всё, что не поддержали другие
        return true;
    }

    public function handle(ActionInput $input): ActionResult
    {
        $comment = $input->getString('comment', '');

        return new ActionResult(
            success: true,
            message: 'Действие выполнено',
            messageType: 'success',
            comment: $comment,
            eventCode: 'default_action',
            shouldTransition: true,
            targetStage: ''
        );
    }
}