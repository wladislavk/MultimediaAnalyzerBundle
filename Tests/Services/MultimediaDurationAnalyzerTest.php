<?php
namespace VKR\MultimediaAnalyzerBundle\Tests\Services;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\File;
use VKR\MultimediaAnalyzerBundle\Exception\MultimediaAnalyzerException;
use VKR\SettingsBundle\Exception\SettingNotFoundException;
use VKR\SettingsBundle\Services\SettingsRetriever;
use VKR\MultimediaAnalyzerBundle\Services\MultimediaDurationAnalyzer;

class MultimediaDurationAnalyzerTest extends TestCase
{
    const PATH_TO_VIDEO = __DIR__ . '/../../TestHelpers/static/my_video.webm';
    const VIDEO_LENGTH = 61;

    /**
     * @var array
     */
    private $settings = [
        'video_maximum_length' => '1000',
        'video_short_length' => '2',
    ];

    private $settingsRetriever;

    /**
     * @var MultimediaDurationAnalyzer
     */
    private $analyzer;

    public function setUp()
    {
        $this->mockSettingsRetriever();
        $this->analyzer = new MultimediaDurationAnalyzer($this->settingsRetriever);
    }

    public function testGetFileDuration()
    {
        $file = new File(self::PATH_TO_VIDEO);
        $duration = $this->analyzer->getFileDuration($file, 'video_maximum_length');
        $this->assertEquals(self::VIDEO_LENGTH, $duration);
    }

    public function testNonMultimediaFile()
    {
        $file = new File(__DIR__ . '/../../TestHelpers/static/test.txt');
        $duration = $this->analyzer->getFileDuration($file, 'video_maximum_length');
        $this->assertEquals(0, $duration);
    }

    public function testLengthExceedsMaximum()
    {
        $file = new File(self::PATH_TO_VIDEO);
        $this->expectException(MultimediaAnalyzerException::class);
        $this->expectExceptionMessage('Multimedia duration cannot be longer than 2 seconds');
        $this->analyzer->getFileDuration($file, 'video_short_length');
    }

    public function testWithoutMaximumLength()
    {
        $file = new File(self::PATH_TO_VIDEO);
        $duration = $this->analyzer->getFileDuration($file);
        $this->assertEquals(self::VIDEO_LENGTH, $duration);
    }

    private function mockSettingsRetriever()
    {
        $this->settingsRetriever = $this->createMock(SettingsRetriever::class);
        $this->settingsRetriever->method('get')
            ->will($this->returnCallback([$this, 'getMockedSettingValueCallback']));
    }

    public function getMockedSettingValueCallback($settingName)
    {
        if (isset($this->settings[$settingName])) {
            return $this->settings[$settingName];
        }
        throw new SettingNotFoundException($settingName);
    }

}
