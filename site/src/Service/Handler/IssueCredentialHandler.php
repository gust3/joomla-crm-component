<?php
namespace Gust\Component\Crmstages\Site\Service\Handler;

use Gust\Component\Crmstages\Site\Service\BaseActionHandler;
use Gust\Component\Crmstages\Site\Service\Dto\ActionInput;
use Gust\Component\Crmstages\Site\Service\Dto\ActionResult;
use Joomla\CMS\Factory;

class IssueCredentialHandler extends BaseActionHandler
{
    protected function getActionCode(): string
    {
        return 'issue_credential';
    }

    public function handle(ActionInput $input): ActionResult
    {
        $credentialNumber = $input->getString('credential_number', '');
        $issueDate = $input->getString('issue_date', '');
        $credentialComment = $input->getString('credential_comment', '');

        $credentialData = [
            'credential_number' => $credentialNumber,
            'issue_date' => $issueDate,
            'credential_comment' => $credentialComment,
            'credential_issued_at' => Factory::getDate()->toSql()
        ];

        if ($input->companyId) {
            $this->updateCompanyDiscovery($input->companyId, $credentialData);
        }

        $comment = "🎫 Удостоверение выдано: №{$credentialNumber} от {$issueDate}" . ($credentialComment ? " | {$credentialComment}" : "");

        return $this->success(
            message: 'Удостоверение выдано!',
            eventCode: 'issue_credential',
            shouldTransition: true,
            targetStage: 'Activated',
            comment: $comment
        );
    }
}