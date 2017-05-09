Overview
========

This is a very simple bundle that currently does just one thing - gets the length of a video 
or audio file in seconds. It has a dependency on VKRSettingsBundle and it also depends on an 
object-oriented version of GetID3 library. The dependency on GetID3 is my fork of 
*phansys/getid3* that is compatible with PHP 7.

Installation
============

Nothing to install except for standard Symfony bundle installation procedure.

Usage
=====

There is just a single public method that needs to be called as follows:

```
$analyzer = $this->get('vkr_multimedia_analyzer.multimedia_duration_analyzer');
$file = new Symfony\Component\HttpFoundation\File('/path/to/file');
try {
    $length = $analyzer->getFileDuration($file);
} catch (MultimediaAnalyzerException $e) {
    // handle exception
}
```

If the file is not a multimedia file, 0 will be returned.

You may also use VKRSettingsBundle in this manner:

```
$maximumLengthSetting = 'maximum_length';
$length = $analyzer->getFileDuration($file, $maximumLengthSetting);
```

Here, if the file duration is longer than maximum, ```MultimediaAnalyzerException``` 
will be thrown.
