<?php
declare(strict_types=1);

namespace PhpStyler\Command;

class ClearCacheTest extends CommandTestCase
{
    public function testClearsExistingCacheFile() : void
    {
        $configFile = $this->writeConfig($this->tmpDir);
        $cacheFile = $this->tmpDir . DIRECTORY_SEPARATOR . '.php-styler.cache';
        file_put_contents($cacheFile, serialize(['hash' => 'abc', 'files' => []]));

        $cmd = new ClearCache();
        $options = new ClearCacheOptions(configFile: $configFile);

        ob_start();
        $exit = $cmd($options);
        $out = (string) ob_get_clean();

        $this->assertSame(0, $exit);
        $this->assertStringContainsString('Cache cleared', $out);
        $this->assertFileDoesNotExist($cacheFile);
    }

    public function testReturnsOneWhenConfigNotFound() : void
    {
        $cmd = new ClearCache();
        $options = new ClearCacheOptions(configFile: null);

        ob_start();
        $exit = $cmd($options);
        $out = (string) ob_get_clean();

        $this->assertSame(1, $exit);
        $this->assertStringContainsString('Could not find', $out);
    }

    public function testUsesConfigFromCwdWhenNotProvided() : void
    {
        $this->writeConfig($this->tmpDir);

        $cmd = new ClearCache();
        $options = new ClearCacheOptions(configFile: null);

        ob_start();
        $exit = $cmd($options);
        ob_end_clean();

        $this->assertSame(0, $exit);
    }
}
