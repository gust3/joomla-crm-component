<?php
namespace Gust\Component\Crmstages\Site\Service\Handler;

use Gust\Component\Crmstages\Site\Service\BaseActionHandler;
use Gust\Component\Crmstages\Site\Service\Dto\ActionInput;
use Gust\Component\Crmstages\Site\Service\Dto\ActionResult;
use Joomla\CMS\Factory;

class SendInvoiceHandler extends BaseActionHandler
{
    protected function getActionCode(): string
    {
        return 'send_invoice';
    }

    public function handle(ActionInput $input): ActionResult
    {
        $invoiceNumber = $input->getString('invoice_number', '');
        $amount = $input->getString('amount', '');
        $invoiceComment = $input->getString('invoice_comment', '');

        $invoiceData = [
            'invoice_number' => $invoiceNumber,
            'amount' => $amount,
            'invoice_comment' => $invoiceComment,
            'invoice_sent_at' => Factory::getDate()->toSql()
        ];

        if ($input->companyId) {
            $this->updateCompanyDiscovery($input->companyId, $invoiceData);
        }

        $comment = "📄 Счёт выставлен: №{$invoiceNumber} на сумму {$amount}" . ($invoiceComment ? " | {$invoiceComment}" : "");

        return $this->success(
            message: 'Счёт выставлен!',
            eventCode: 'send_invoice',
            shouldTransition: true,
            targetStage: 'Committed',
            comment: $comment
        );
    }
}