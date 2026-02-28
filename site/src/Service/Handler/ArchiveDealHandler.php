<?php
namespace Gust\Component\Crmstages\Site\Service\Handler;

use Gust\Component\Crmstages\Site\Service\BaseActionHandler;
use Gust\Component\Crmstages\Site\Service\Dto\ActionInput;
use Gust\Component\Crmstages\Site\Service\Dto\ActionResult;
use Joomla\CMS\Factory;

class ArchiveDealHandler extends BaseActionHandler
{
    protected function getActionCode(): string
    {
        return 'archive_deal';
    }

    public function handle(ActionInput $input): ActionResult
    {
        $archiveComment = $input->getString('archive_comment', '');

        $archiveData = [
            'archived_at' => Factory::getDate()->toSql(),
            'archive_comment' => $archiveComment
        ];

        if ($input->companyId) {
            $this->updateCompanyDiscovery($input->companyId, $archiveData);
        }

        $comment = "📁 Сделка архивирована" . ($archiveComment ? " | {$archiveComment}" : "");

        return $this->success(
            message: 'Сделка успешно архивирована!',
            eventCode: 'archive_deal',
            shouldTransition: true,
            targetStage: 'Archived',
            comment: $comment
        );
    }
}