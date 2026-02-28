<?php
namespace Gust\Component\Crmstages\Tests\Unit\Dto;

use PHPUnit\Framework\TestCase;
use Gust\Component\Crmstages\Site\Service\Dto\ActionResult;

/**
 * @covers \Gust\Component\Crmstages\Site\Service\Dto\ActionResult
 */
class ActionResultTest extends TestCase
{
    public function testSuccessResult(): void
    {
        $result = new ActionResult(
            success: true,
            message: 'Тест успешно',
            messageType: 'success',
            comment: 'Тестовый комментарий',
            eventCode: 'test_event',
            shouldTransition: true,
            targetStage: 'TestStage'
        );

        $this->assertTrue($result->success);
        $this->assertEquals('Тест успешно', $result->message);
        $this->assertEquals('success', $result->messageType);
        $this->assertEquals('Тестовый комментарий', $result->comment);
        $this->assertEquals('test_event', $result->eventCode);
        $this->assertTrue($result->shouldTransition);
        $this->assertEquals('TestStage', $result->targetStage);
    }

    public function testSuccessStaticMethod(): void
    {
        $result = ActionResult::success(
            message: 'Статический метод',
            eventCode: 'static_test',
            shouldTransition: true,
            targetStage: 'NextStage',
            comment: 'Комментарий'
        );

        $this->assertTrue($result->success);
        $this->assertEquals('Статический метод', $result->message);
        $this->assertEquals('static_test', $result->eventCode);
        $this->assertEquals('Комментарий', $result->comment);
        $this->assertTrue($result->shouldTransition);
        $this->assertEquals('NextStage', $result->targetStage);
    }

    public function testToArrayConversion(): void
    {
        $result = new ActionResult(
            success: true,
            message: 'Конвертация',
            messageType: 'info',
            comment: 'Тест',
            eventCode: 'convert_test',
            shouldTransition: false,
            targetStage: null
        );

        $array = $result->toArray();

        $this->assertIsArray($array);
        $this->assertEquals('Конвертация', $array['message']);
        $this->assertEquals('info', $array['messageType']);
        $this->assertEquals('Тест', $array['comment']);
        $this->assertFalse($array['shouldTransition']);
    }
}