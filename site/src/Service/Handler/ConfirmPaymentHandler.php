<?php
namespace Gust\Component\Crmstages\Site\Service\Handler;

use Gust\Component\Crmstages\Site\Service\BaseActionHandler;
use Gust\Component\Crmstages\Site\Service\Dto\ActionInput;
use Gust\Component\Crmstages\Site\Service\Dto\ActionResult;
use Joomla\CMS\Factory;

class ConfirmPaymentHandler extends BaseActionHandler
{
    protected function getActionCode(): string
    {
        return 'confirm_payment';
    }

    public function handle(ActionInput $input): ActionResult
    {
        $paymentComment = $input->getString('payment_comment', '');

        $paymentData = [
            'payment_confirmed_at' => Factory::getDate()->toSql(),
            'payment_comment' => $paymentComment
        ];

        if ($input->companyId) {
            $this->updateCompanyDiscovery($input->companyId, $paymentData);
        }

        $comment = "💰 Оплата подтверждена" . ($paymentComment ? " | {$paymentComment}" : "");

        return $this->success(
            message: 'Оплата подтверждена! Переход на стадию Customer.',
            eventCode: 'confirm_payment',
            shouldTransition: true,
            targetStage: 'Customer',
            comment: $comment
        );
    }
}