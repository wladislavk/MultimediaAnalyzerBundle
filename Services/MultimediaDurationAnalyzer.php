<?php
namespace VKR\MultimediaAnalyzerBundle\Services;

use GetId3\GetId3Core;
use VKR\MultimediaAnalyzerBundle\Exception\MultimediaAnalyzerException;
use VKR\SettingsBundle\Services\SettingsRetriever;
use Symfony\Component\HttpFoundation\File\File;

class MultimediaDurationAnalyzer
{
    const PLAYTIME_SECONDS_KEY = 'playtime_seconds';

    /**
     * @var SettingsRetriever
     */
    private $settingsRetriever;

    public function __construct(SettingsRetriever $settingsRetriever)
    {
        $this->settingsRetriever = $settingsRetriever;
    }

    /**
     * Utilizes GetID3 library to get the uploaded video length
     *
     * @param File $file
     * @param string|null $maximumLengthSettingName
     * @return int
     * @throws MultimediaAnalyzerException
     */
    public function getFileDuration(File $file, $maximumLengthSettingName = null)
    {
        if (!strstr($file->getMimeType(), 'video') && !strstr($file->getMimeType(), 'audio')) {
            return 0;
        }
        $getID3 = new GetId3Core();
        $videoData = $getID3->analyze($file->getRealPath());
        if (!isset($videoData[self::PLAYTIME_SECONDS_KEY])) {
            throw new MultimediaAnalyzerException('Could not analyze file data');
        }
        $duration = intval($videoData[self::PLAYTIME_SECONDS_KEY]);
        if ($maximumLengthSettingName === null) {
            return $duration;
        }
        $maximumLength = $this->settingsRetriever->get($maximumLengthSettingName);
        if ($duration > $maximumLength) {
            throw new MultimediaAnalyzerException("Multimedia duration cannot be longer than $maximumLength seconds");
        }
        return $duration;
    }

}
