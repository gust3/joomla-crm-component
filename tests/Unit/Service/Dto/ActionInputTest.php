<?php
namespace Gust\Component\Crmstages\Tests\Unit\Dto;

use PHPUnit\Framework\TestCase;
use Gust\Component\Crmstages\Site\Service\Dto\ActionInput;

/**
 * Unit-тест для ActionInput DTO
 *
 * @covers \Gust\Component\Crmstages\Site\Service\Dto\ActionInput
 */
class ActionInputTest extends TestCase
{
    // ========================================================================
    // ТЕСТЫ КОНСТРУКТОРА
    // ========================================================================

    /**
     * Тест: Конструктор создаёт объект с обязательным companyId
     */
    public function testConstructorWithRequiredCompanyId(): void
    {
        $input = new ActionInput(
            companyId: 123
        );

        $this->assertEquals(123, $input->companyId);
    }

    /**
     * Тест: Конструктор с полями
     */
    public function testConstructorWithFields(): void
    {
        $input = new ActionInput(
            companyId: 456,
            fields: [
                'name' => 'Test',
                'value' => '100',
            ]
        );

        $this->assertEquals(456, $input->companyId);
        $this->assertEquals('Test', $input->getString('name'));
        $this->assertEquals('100', $input->getString('value'));
    }

    /**
     * Тест: Конструктор с пустыми полями (по умолчанию)
     */
    public function testConstructorWithEmptyFields(): void
    {
        $input = new ActionInput(
            companyId: 789
        );

        $this->assertEquals(789, $input->companyId);
        $this->assertEmpty($input->getAll());
    }

    /**
     * Тест: companyId всегда integer
     */
    public function testCompanyIdIsAlwaysInteger(): void
    {
        $input = new ActionInput(123);
        $this->assertIsInt($input->companyId);
    }

    /**
     * Тест: companyId не может быть нулевым (валидация)
     */
    public function testCompanyIdCanBeZero(): void
    {
        $input = new ActionInput(0);
        $this->assertEquals(0, $input->companyId);
    }

    /**
     * Тест: Свойства readonly (не могут быть изменены после создания)
     */
    public function testPropertiesAreReadonly(): void
    {
        $input = new ActionInput(
            companyId: 123,
            fields: ['key' => 'value']
        );

        // Проверяем, что значения остались неизменными
        $this->assertEquals(123, $input->companyId);
        $this->assertEquals('value', $input->getString('key'));

        // Примечание: в runtime PHP выбросит Error при попытке записи в readonly
        // $input->companyId = 456; // Fatal error
    }

    // ========================================================================
    // ТЕСТЫ МЕТОДА: getString()
    // ========================================================================

    /**
     * Тест: getString() возвращает значение поля
     */
    public function testGetStringReturnsFieldValue(): void
    {
        $input = new ActionInput(
            companyId: 123,
            fields: ['name' => 'John', 'city' => 'Moscow']
        );

        $this->assertEquals('John', $input->getString('name'));
        $this->assertEquals('Moscow', $input->getString('city'));
    }

    /**
     * Тест: getString() возвращает default для несуществующего поля
     */
    public function testGetStringReturnsDefaultForMissingField(): void
    {
        $input = new ActionInput(
            companyId: 123,
            fields: ['name' => 'John']
        );

        $this->assertEquals('', $input->getString('missing'));
        $this->assertEquals('N/A', $input->getString('missing', 'N/A'));
    }

    /**
     * Тест: getString() с пустым значением
     */
    public function testGetStringWithEmptyValue(): void
    {
        $input = new ActionInput(
            companyId: 123,
            fields: ['name' => '']
        );

        $this->assertEquals('', $input->getString('name'));
        $this->assertEquals('', $input->getString('name', 'Default'));
    }

    /**
     * Тест: getString() с null значением в массиве
     */
    public function testGetStringWithNullValue(): void
    {
        $input = new ActionInput(
            companyId: 123,
            fields: ['name' => null]
        );

        // null преобразуется в пустую строку через ??
        $this->assertEquals('', $input->getString('name'));
    }

    /**
     * Тест: getString() с числовым значением
     */
    public function testGetStringWithNumericValue(): void
    {
        $input = new ActionInput(
            companyId: 123,
            fields: ['count' => 100]
        );

        $this->assertEquals('100', $input->getString('count'));
    }

    /**
     * Тест: getString() с булевым значением
     */
    public function testGetStringWithBooleanValue(): void
    {
        $input = new ActionInput(
            companyId: 123,
            fields: ['active' => true, 'inactive' => false]
        );

        $this->assertEquals('1', $input->getString('active'));
        $this->assertEquals('', $input->getString('inactive'));
    }

    /**
     * Тест: getString() с UTF-8 символами
     */
    public function testGetStringWithUtf8Characters(): void
    {
        $input = new ActionInput(
            companyId: 123,
            fields: [
                'russian' => 'Привет мир',
                'emoji' => '🎉🚀',
                'chinese' => '你好',
            ]
        );

        $this->assertEquals('Привет мир', $input->getString('russian'));
        $this->assertEquals('🎉🚀', $input->getString('emoji'));
        $this->assertEquals('你好', $input->getString('chinese'));
    }

    // ========================================================================
    // ТЕСТЫ МЕТОДА: getInt()
    // ========================================================================

    /**
     * Тест: getInt() возвращает целочисленное значение
     */
    public function testGetIntReturnsIntegerValue(): void
    {
        $input = new ActionInput(
            companyId: 123,
            fields: ['age' => '25', 'count' => '100']
        );

        $this->assertEquals(25, $input->getInt('age'));
        $this->assertEquals(100, $input->getInt('count'));
    }

    /**
     * Тест: getInt() возвращает default для несуществующего поля
     */
    public function testGetIntReturnsDefaultForMissingField(): void
    {
        $input = new ActionInput(
            companyId: 123,
            fields: ['age' => 25]
        );

        $this->assertEquals(0, $input->getInt('missing'));
        $this->assertEquals(-1, $input->getInt('missing', -1));
    }

    /**
     * Тест: getInt() с пустым значением
     */
    public function testGetIntWithEmptyValue(): void
    {
        $input = new ActionInput(
            companyId: 123,
            fields: ['count' => '']
        );

        $this->assertEquals(0, $input->getInt('count'));
    }

    /**
     * Тест: getInt() с float значением (обрезаются дробные)
     */
    public function testGetIntWithFloatValue(): void
    {
        $input = new ActionInput(
            companyId: 123,
            fields: ['price' => '19.99', 'quantity' => 5.7]
        );

        $this->assertEquals(19, $input->getInt('price'));
        $this->assertEquals(5, $input->getInt('quantity'));
    }

    /**
     * Тест: getInt() с отрицательными числами
     */
    public function testGetIntWithNegativeNumbers(): void
    {
        $input = new ActionInput(
            companyId: 123,
            fields: ['delta' => '-50', 'change' => -100]
        );

        $this->assertEquals(-50, $input->getInt('delta'));
        $this->assertEquals(-100, $input->getInt('change'));
    }

    /**
     * Тест: getInt() с булевым значением
     */
    public function testGetIntWithBooleanValue(): void
    {
        $input = new ActionInput(
            companyId: 123,
            fields: ['enabled' => true, 'disabled' => false]
        );

        $this->assertEquals(1, $input->getInt('enabled'));
        $this->assertEquals(0, $input->getInt('disabled'));
    }

    /**
     * Тест: getInt() с нечисловым значением
     */
    public function testGetIntWithNonNumericValue(): void
    {
        $input = new ActionInput(
            companyId: 123,
            fields: ['value' => 'abc']
        );

        $this->assertEquals(0, $input->getInt('value'));
    }

    // ========================================================================
    // ТЕСТЫ МЕТОДА: has()
    // ========================================================================

    /**
     * Тест: has() возвращает true для существующего поля
     */
    public function testHasReturnsTrueForExistingField(): void
    {
        $input = new ActionInput(
            companyId: 123,
            fields: ['name' => 'John', 'age' => 25]
        );

        $this->assertTrue($input->has('name'));
        $this->assertTrue($input->has('age'));
    }

    /**
     * Тест: has() возвращает false для несуществующего поля
     */
    public function testHasReturnsFalseForMissingField(): void
    {
        $input = new ActionInput(
            companyId: 123,
            fields: ['name' => 'John']
        );

        $this->assertFalse($input->has('email'));
        $this->assertFalse($input->has('phone'));
    }

    /**
     * Тест: has() возвращает true для поля с пустым значением
     */
    public function testHasReturnsTrueForEmptyValue(): void
    {
        $input = new ActionInput(
            companyId: 123,
            fields: ['name' => '', 'count' => 0]
        );

        $this->assertTrue($input->has('name'));
        $this->assertTrue($input->has('count'));
    }

    /**
     * Тест: has() возвращает false для null значения
     */
    public function testHasReturnsFalseForNullValue(): void
    {
        $input = new ActionInput(
            companyId: 123,
            fields: ['name' => null]
        );

        $this->assertFalse($input->has('name'));
    }

    /**
     * Тест: has() с пустыми полями
     */
    public function testHasWithEmptyFields(): void
    {
        $input = new ActionInput(123);

        $this->assertFalse($input->has('anything'));
    }

    // ========================================================================
    // ТЕСТЫ МЕТОДА: getAll()
    // ========================================================================

    /**
     * Тест: getAll() возвращает все поля
     */
    public function testGetAllReturnsAllFields(): void
    {
        $input = new ActionInput(
            companyId: 123,
            fields: [
                'name' => 'John',
                'age' => 25,
                'city' => 'Moscow',
            ]
        );

        $fields = $input->getAll();

        $this->assertIsArray($fields);
        $this->assertCount(3, $fields);
        $this->assertEquals('John', $fields['name']);
        $this->assertEquals(25, $fields['age']);
        $this->assertEquals('Moscow', $fields['city']);
    }

    /**
     * Тест: getAll() возвращает пустой массив если полей нет
     */
    public function testGetAllReturnsEmptyArrayWhenNoFields(): void
    {
        $input = new ActionInput(123);

        $fields = $input->getAll();

        $this->assertIsArray($fields);
        $this->assertEmpty($fields);
    }

    /**
     * Тест: getAll() возвращает независимую копию массива
     */
    public function testGetAllReturnsIndependentCopy(): void
    {
        $input = new ActionInput(
            companyId: 123,
            fields: ['name' => 'Original']
        );

        $fields = $input->getAll();
        $fields['name'] = 'Modified';

        // Оригинальные данные не должны измениться
        $this->assertEquals('Original', $input->getString('name'));
    }

    // ========================================================================
    // ТЕСТЫ ТИПОВ ДАННЫХ
    // ========================================================================

    /**
     * Тест: companyId всегда integer
     */
    public function testCompanyIdPropertyIsAlwaysInteger(): void
    {
        $inputs = [
            new ActionInput(1),
            new ActionInput(123),
            new ActionInput(999999),
        ];

        foreach ($inputs as $input) {
            $this->assertIsInt($input->companyId);
        }
    }

    /**
     * Тест: getString() всегда возвращает string
     */
    public function testGetStringAlwaysReturnsString(): void
    {
        $input = new ActionInput(123, [
            'text' => 'hello',
            'number' => 123,
            'boolean' => true,
            'null' => null,
        ]);

        $this->assertIsString($input->getString('text'));
        $this->assertIsString($input->getString('number'));
        $this->assertIsString($input->getString('boolean'));
        $this->assertIsString($input->getString('null'));
        $this->assertIsString($input->getString('missing'));
    }

    /**
     * Тест: getInt() всегда возвращает int
     */
    public function testGetIntAlwaysReturnsInteger(): void
    {
        $input = new ActionInput(123, [
            'number' => 123,
            'string' => '456',
            'float' => 7.89,
            'boolean' => true,
        ]);

        $this->assertIsInt($input->getInt('number'));
        $this->assertIsInt($input->getInt('string'));
        $this->assertIsInt($input->getInt('float'));
        $this->assertIsInt($input->getInt('boolean'));
        $this->assertIsInt($input->getInt('missing'));
    }

    /**
     * Тест: has() всегда возвращает bool
     */
    public function testHasAlwaysReturnsBoolean(): void
    {
        $input = new ActionInput(123, ['key' => 'value']);

        $this->assertIsBool($input->has('key'));
        $this->assertIsBool($input->has('missing'));
    }

    /**
     * Тест: getAll() всегда возвращает array
     */
    public function testGetAllAlwaysReturnsArray(): void
    {
        $inputs = [
            new ActionInput(1),
            new ActionInput(123, ['a' => 'b']),
            new ActionInput(999, ['x' => 1, 'y' => 2]),
        ];

        foreach ($inputs as $input) {
            $this->assertIsArray($input->getAll());
        }
    }

    // ========================================================================
    // ТЕСТЫ ГРАНИЧНЫХ СЛУЧАЕВ
    // ========================================================================

    /**
     * Тест: Очень большой companyId
     */
    public function testVeryLargeCompanyId(): void
    {
        $input = new ActionInput(PHP_INT_MAX);
        $this->assertEquals(PHP_INT_MAX, $input->companyId);
    }

    /**
     * Тест: Очень длинные строки
     */
    public function testVeryLongStrings(): void
    {
        $longString = str_repeat('a', 10000);
        $input = new ActionInput(123, ['data' => $longString]);

        $this->assertEquals($longString, $input->getString('data'));
        $this->assertEquals(10000, strlen($input->getString('data')));
    }

    /**
     * Тест: Специальные символы
     */
    public function testSpecialCharacters(): void
    {
        $input = new ActionInput(123, [
            'html' => '<script>alert("xss")</script>',
            'sql' => "'; DROP TABLE users; --",
            'path' => '../../etc/passwd',
        ]);

        $this->assertIsString($input->getString('html'));
        $this->assertIsString($input->getString('sql'));
        $this->assertIsString($input->getString('path'));
    }

    /**
     * Тест: Поля с числовыми ключами
     */
    public function testNumericFieldKeys(): void
    {
        $input = new ActionInput(123, [
            0 => 'first',
            1 => 'second',
            2 => 'third',
        ]);

        $this->assertEquals('first', $input->getString('0'));
        $this->assertEquals('second', $input->getString('1'));
    }

    /**
     * Тест: Поля с special keys
     */
    public function testSpecialFieldKeys(): void
    {
        $input = new ActionInput(123, [
            'key-with-dash' => 'value1',
            'key_with_underscore' => 'value2',
            'key.with.dot' => 'value3',
            'key with space' => 'value4',
        ]);

        $this->assertEquals('value1', $input->getString('key-with-dash'));
        $this->assertEquals('value2', $input->getString('key_with_underscore'));
        $this->assertEquals('value3', $input->getString('key.with.dot'));
        $this->assertEquals('value4', $input->getString('key with space'));
    }

    // ========================================================================
    // ИНТЕГРАЦИОННЫЕ ТЕСТЫ
    // ========================================================================

    /**
     * Тест: Типичный сценарий использования (звонок)
     */
    public function testTypicalUsageScenarioMakeCall(): void
    {
        $input = new ActionInput(
            companyId: 123,
            fields: [
                'lp_reached' => '1',
                'call_result' => 'Договорились о встрече',
                'call_time' => '14:30',
            ]
        );

        $this->assertEquals(123, $input->companyId);
        $this->assertEquals(1, $input->getInt('lp_reached'));
        $this->assertEquals('Договорились о встрече', $input->getString('call_result'));
        $this->assertEquals('14:30', $input->getString('call_time'));
        $this->assertTrue($input->has('lp_reached'));
    }

    /**
     * Тест: Типичный сценарий использования (Discovery)
     */
    public function testTypicalUsageScenarioDiscovery(): void
    {
        $input = new ActionInput(
            companyId: 456,
            fields: [
                'pains' => 'Нужна автоматизация',
                'budget' => '100000',
                'timeline' => '3 месяца',
                'decision_maker' => 'Иванов И.И.',
            ]
        );

        $this->assertEquals(456, $input->companyId);
        $this->assertEquals('Нужна автоматизация', $input->getString('pains'));
        $this->assertEquals(100000, $input->getInt('budget'));
        $this->assertEquals('3 месяца', $input->getString('timeline'));
        $this->assertTrue($input->has('decision_maker'));
    }

    /**
     * Тест: Типичный сценарий использования (Демо)
     */
    public function testTypicalUsageScenarioDemo(): void
    {
        $input = new ActionInput(
            companyId: 789,
            fields: [
                'demo_date' => '2026-02-01',
                'demo_time' => '15:00',
                'demo_link' => 'https://zoom.us/j/123456',
                'confirmed' => '1',
            ]
        );

        $this->assertEquals(789, $input->companyId);
        $this->assertEquals('2026-02-01', $input->getString('demo_date'));
        $this->assertEquals('15:00', $input->getString('demo_time'));
        $this->assertEquals('https://zoom.us/j/123456', $input->getString('demo_link'));
        $this->assertEquals(1, $input->getInt('confirmed'));
    }

    /**
     * Тест: Сценарий с отсутствующими полями (fallback на default)
     */
    public function testScenarioWithMissingFields(): void
    {
        $input = new ActionInput(
            companyId: 123,
            fields: [
                'name' => 'Test',
                // остальные поля отсутствуют
            ]
        );

        $this->assertEquals('Test', $input->getString('name'));
        $this->assertEquals('', $input->getString('missing', ''));
        $this->assertEquals('N/A', $input->getString('missing', 'N/A'));
        $this->assertEquals(0, $input->getInt('missing', 0));
        $this->assertFalse($input->has('missing'));
    }

    /**
     * Тест: Цепочка методов
     */
    public function testMethodChaining(): void
    {
        $input = new ActionInput(
            companyId: 123,
            fields: ['key' => 'value']
        );

        // Последовательное использование методов
        $this->assertTrue($input->has('key'));
        $this->assertEquals('value', $input->getString('key'));
        $this->assertIsArray($input->getAll());
        $this->assertCount(1, $input->getAll());
    }

    /**
     * Тест: Совместимость с ActionHandler
     */
    public function testCompatibilityWithActionHandler(): void
    {
        // Эмуляция данных от формы
        $formData = [
            'lp_reached' => '1',
            'call_result' => 'Успешно',
            'call_time' => '10:00',
        ];

        $input = new ActionInput(
            companyId: 999,
            fields: $formData
        );

        // ActionHandler будет использовать эти методы
        $lpReached = $input->getInt('lp_reached');
        $callResult = $input->getString('call_result');
        $callTime = $input->getString('call_time');

        $this->assertEquals(1, $lpReached);
        $this->assertEquals('Успешно', $callResult);
        $this->assertEquals('10:00', $callTime);
    }
}