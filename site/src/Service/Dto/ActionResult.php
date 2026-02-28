<?php
namespace Gust\Component\Crmstages\Site\Service\Dto;

/**
 * DTO для результата выполнения действия
 * Инкапсулирует все возможные возвращаемые значения
 */
class ActionResult
{
    public function __construct(
        public readonly bool $success,
        public readonly string $message,
        public readonly string $messageType = 'info',
        public readonly ?string $comment = null,
        public readonly ?string $eventCode = null,
        public readonly bool $shouldTransition = false,
        public readonly ?string $targetStage = null,
    ) {}

    /**
     * Конвертация в массив для совместимости с Joomla API
     * (setMessage, редиректы и т.д.)
     */
    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'messageType' => $this->messageType,
            'comment' => $this->comment,
            'eventCode' => $this->eventCode,
            'shouldTransition' => $this->shouldTransition,
            'targetStage' => $this->targetStage,
        ];
    }

    /**
     * Быстрый метод для создания успешного результата
     */
    public static function success(
        string $message,
        string $eventCode,
        bool $shouldTransition = false,
        ?string $targetStage = null,
        string $comment = '',
        string $messageType = 'success'
    ): self {
        return new self(
            success: true,
            message: $message,
            messageType: $messageType,
            comment: $comment,
            eventCode: $eventCode,
            shouldTransition: $shouldTransition,
            targetStage: $targetStage
        );
    }

    /**
     * Быстрый метод для создания результата с ошибкой
     */
    public static function error(
        string $message,
        string $messageType = 'error'
    ): self {
        return new self(
            success: false,
            message: $message,
            messageType: $messageType
        );
    }
}