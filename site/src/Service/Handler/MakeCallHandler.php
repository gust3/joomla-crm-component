<?php
namespace Gust\Component\Crmstages\Site\Service\Handler;

use Gust\Component\Crmstages\Site\Service\BaseActionHandler;
use Gust\Component\Crmstages\Site\Service\Dto\ActionInput;
use Gust\Component\Crmstages\Site\Service\Dto\ActionResult;

class MakeCallHandler extends BaseActionHandler
{
    protected function getActionCode(): string
    {
        return 'make_call';
    }

    public function handle(ActionInput $input): ActionResult
    {
        $lpReached = $input->getInt('lp_reached');
        $callResult = $input->getString('call_result', '');
        $callTime = $input->getString('call_time', '');

        if (!$lpReached) {
            $this->logEvent($input->companyId, 'make_call_failed', "Не дозвонился. {$callResult}");

            return new ActionResult(
                success: true,
                message: 'Звонок зафиксирован. Попробуйте ещё раз.',
                messageType: 'info',
                eventCode: 'make_call_failed',
                shouldTransition: false,
                targetStage: 'Touched'
            );
        }

        $this->logEvent($input->companyId, 'call_successful', "✅ Дозвонился: {$callResult}. Время: {$callTime}");

        return $this->success(
            message: 'Разговор с ЛПР зафиксирован!',
            eventCode: 'call_successful',
            shouldTransition: true,
            targetStage: 'Aware'
        );
    }
}