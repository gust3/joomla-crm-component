<?php
namespace Gust\Component\Crmstages\Site\Stage;

use Gust\Component\Crmstages\Site\Action\ActionInterface;

interface StageInterface
{
    public function getName(): string;
    public function getNextStages(): array;
    public function getRequiredEvents(): array;
    public function getAvailableActions(int $companyId): array;
    public function canTransitionTo(string $targetStage, int $companyId): bool;
}