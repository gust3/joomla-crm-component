<?php
namespace Gust\Component\Crmstages\Tests\Mocks;

/**
 * Mock для Joomla\CMS\Factory в Unit-тестах
 * ⚠️ НЕ использовать use Joomla\... внутри этого файла!
 */
class Factory
{
    private static $instance = null;

    public static function getApplication($name = null)
    {
        if (self::$instance === null) {
            self::$instance = new ApplicationMock();
        }
        return self::$instance;
    }

    public static function getDate($time = 'now', $tz = null)
    {
        return new DateMock($time);
    }

    public static function getContainer()
    {
        return new ContainerMock();
    }

    public static function getDocument()
    {
        return new DocumentMock();
    }

    public static function getLanguage()
    {
        return new LanguageMock();
    }

    public static function getSession()
    {
        return new SessionMock();
    }

    public static function getURI()
    {
        return new UriMock();
    }

    public static function getUser()
    {
        return new UserMock();
    }
}

class ApplicationMock
{
    private $input;

    public function __construct()
    {
        $this->input = new InputMock();
    }

    public function input()
    {
        return $this->input;
    }

    public function getInput()
    {
        return $this->input;
    }

    public function enqueueMessage($message, $type = 'message')
    {
        // Mock
    }

    public function redirect($url, $msg = '', $msgType = 'message')
    {
        // Mock
    }
}

class InputMock
{
    private array $data = [];

    public function setArray(array $data): void
    {
        $this->data = $data;
    }

    public function getArray(): array
    {
        return $this->data;
    }

    public function getString(string $name, string $default = ''): string
    {
        return $this->data[$name] ?? $default;
    }

    public function getInt(string $name, int $default = 0): int
    {
        return (int)($this->data[$name] ?? $default);
    }

    public function getCmd(string $name, string $default = ''): string
    {
        return preg_replace('#[^A-Z0-9_-]#i', '', $this->data[$name] ?? $default);
    }

    public function get(string $name, $default = null)
    {
        return $this->data[$name] ?? $default;
    }

    public function set(string $name, $value): void
    {
        $this->data[$name] = $value;
    }
}

class ContainerMock
{
    private $db;

    public function __construct()
    {
        $this->db = new DatabaseMock();
    }

    public function get(string $service)
    {
        if ($service === 'Joomla\\Database\\DatabaseInterface' ||
            $service === \Joomla\Database\DatabaseInterface::class ||
            $service === 'DatabaseInterface') {
            return $this->db;
        }
        return $this->db;
    }

    public function has(string $id): bool
    {
        return true;
    }
}

class DatabaseMock
{
    private array $events = [];
    private array $companies = [];

    public function insertObject(string $table, &$object, $pk = null): bool
    {
        if ($table === '#__crm_events') {
            $this->events[] = $object;
            return true;
        }
        return true;
    }

    public function updateObject(string $table, &$object, $pk, $nulls = false): bool
    {
        if ($table === '#__crm_companies') {
            $this->companies[$object->id] = $object;
            return true;
        }
        return true;
    }

    public function getQuery(bool $new = false): QueryMock
    {
        return new QueryMock($this);
    }

    public function setQuery($query): void
    {
        // Mock
    }

    public function loadResult(): ?string
    {
        return null;
    }

    public function loadObject(): ?object
    {
        return null;
    }

    public function loadObjectList(): array
    {
        return [];
    }

    public function loadAssoc(): ?array
    {
        return null;
    }

    public function loadAssocList(): array
    {
        return [];
    }

    public function loadColumn(): array
    {
        return [];
    }

    public function loadRow(): array
    {
        return [];
    }

    public function loadRowList(): array
    {
        return [];
    }

    public function execute(): bool
    {
        return true;
    }

    public function insertid(): int
    {
        return 1;
    }

    public function getAffectedRows(): int
    {
        return 1;
    }

    public function quoteName($name): string
    {
        if (is_array($name)) {
            return array_map([$this, 'quoteName'], $name);
        }
        return '`' . trim($name, '`') . '`';
    }

    public function quote($text, $escape = true): string
    {
        return "'" . $text . "'";
    }

    public function escape($text, $extra = false): string
    {
        return addslashes($text);
    }

    public function getEvents(): array
    {
        return $this->events;
    }

    public function getCompanies(): array
    {
        return $this->companies;
    }

    public function clearEvents(): void
    {
        $this->events = [];
    }

    public function clearCompanies(): void
    {
        $this->companies = [];
    }
}

class QueryMock
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function select($columns): self { return $this; }
    public function from($table): self { return $this; }
    public function update($table): self { return $this; }
    public function set($set): self { return $this; }
    public function where($where): self { return $this; }
    public function orWhere($where): self { return $this; }
    public function insert($table): self { return $this; }
    public function delete($table): self { return $this; }
    public function quoteName($name): string { return $name; }
    public function quote($value): string { return "'" . $value . "'"; }
    public function order($order): self { return $this; }
    public function limit($limit, $offset = 0): self { return $this; }
    public function join($type, $conditions): self { return $this; }
    public function clear($clause = 'all'): self { return $this; }
    public function castAsChar($column): string { return $column; }
    public function concat($values, $separator = ''): string { return implode($separator, $values); }
    public function length($column): string { return $column; }
    public function getQueryType(): string { return 'SELECT'; }
}

class DateMock extends \DateTime
{
    public function toSql(): string
    {
        return $this->format('Y-m-d H:i:s');
    }

    public function formatRFC822($asLocal = false): string
    {
        return $this->format(\DateTime::RFC822);
    }

    public function toUnix(): int
    {
        return $this->getTimestamp();
    }
}

// 🔹 Заглушки для остальных классов (чтобы не было ошибок)
class DocumentMock {}
class LanguageMock {}
class SessionMock {}
class UriMock {}
class UserMock {}