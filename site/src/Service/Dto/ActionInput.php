<?php
namespace Gust\Component\Crmstages\Site\Service\Dto;

/**
 * DTO для входных данных действий
 * Инкапсулирует парсинг $_POST/$_GET данных
 */
class ActionInput
{
    public function __construct(
        public readonly int $companyId,
        private readonly array $fields = []
    ) {}

    /**
     * Получает строковое значение поля
     */
    public function getString(string $key, string $default = ''): string
    {
        return $this->fields[$key] ?? $default;
    }

    /**
     * Получает целочисленное значение поля
     */
    public function getInt(string $key, int $default = 0): int
    {
        return (int) ($this->fields[$key] ?? $default);
    }

    /**
     * Проверяет наличие поля
     */
    public function has(string $key): bool
    {
        return isset($this->fields[$key]);
    }

    /**
     * Получает все поля (для отладки)
     */
    public function getAll(): array
    {
        return $this->fields;
    }
}