<?php
declare(strict_types=1);

namespace PhpStyler;

use PHPUnit\Framework\TestCase;

class CacheTest extends TestCase
{
    private string $cacheFile;

    private string $tempDir;

    protected function setUp() : void
    {
        $this->tempDir = sys_get_temp_dir() . '/php-styler-cache-test-' . getmypid();
        mkdir($this->tempDir, 0777, true);
        $this->cacheFile = $this->tempDir . '/.php-styler.cache';
    }

    protected function tearDown() : void
    {
        $files = glob($this->tempDir . '/*') ?: [];

        foreach ($files as $f) {
            unlink($f);
        }

        // also remove hidden files
        $hidden = glob($this->tempDir . '/.*') ?: [];

        foreach ($hidden as $f) {
            if (basename($f) !== '.' && basename($f) !== '..') {
                unlink($f);
            }
        }

        rmdir($this->tempDir);
    }

    private function createTempFile(string $name, string $content) : string
    {
        $path = $this->tempDir . '/' . $name;
        file_put_contents($path, $content);

        return $path;
    }

    public function testLoadWithNoCacheFile() : void
    {
        $cache = new Cache($this->cacheFile, 'config-hash');
        $cache->load();

        // no file exists, so nothing is current
        $file = $this->createTempFile('a.php', '<?php echo 1;');
        $this->assertFalse($cache->isCurrent($file));
    }

    public function testUpdateAndIsCurrent() : void
    {
        $cache = new Cache($this->cacheFile, 'config-hash');
        $file = $this->createTempFile('a.php', '<?php echo 1;');

        $this->assertFalse($cache->isCurrent($file));

        $cache->update($file);
        $this->assertTrue($cache->isCurrent($file));
    }

    public function testIsCurrentReturnsFalseAfterFileChange() : void
    {
        $cache = new Cache($this->cacheFile, 'config-hash');
        $file = $this->createTempFile('a.php', '<?php echo 1;');

        $cache->update($file);
        $this->assertTrue($cache->isCurrent($file));

        // modify the file
        file_put_contents($file, '<?php echo 2;');
        $this->assertFalse($cache->isCurrent($file));
    }

    public function testSaveAndLoad() : void
    {
        $file = $this->createTempFile('a.php', '<?php echo 1;');

        // save
        $cache = new Cache($this->cacheFile, 'config-hash');
        $cache->update($file);
        $cache->save();

        $this->assertFileExists($this->cacheFile);

        // load in a fresh instance
        $cache2 = new Cache($this->cacheFile, 'config-hash');
        $cache2->load();

        $this->assertTrue($cache2->isCurrent($file));
    }

    public function testLoadInvalidatesOnConfigHashChange() : void
    {
        $file = $this->createTempFile('a.php', '<?php echo 1;');

        // save with one config hash
        $cache = new Cache($this->cacheFile, 'hash-v1');
        $cache->update($file);
        $cache->save();

        // load with a different config hash
        $cache2 = new Cache($this->cacheFile, 'hash-v2');
        $cache2->load();

        $this->assertFalse($cache2->isCurrent($file));
    }

    public function testLoadIgnoresCorruptJson() : void
    {
        file_put_contents($this->cacheFile, 'not-valid-json{{{');

        $cache = new Cache($this->cacheFile, 'config-hash');
        $cache->load();

        $file = $this->createTempFile('a.php', '<?php echo 1;');
        $this->assertFalse($cache->isCurrent($file));
    }

    public function testDelete() : void
    {
        $cache = new Cache($this->cacheFile, 'config-hash');
        $file = $this->createTempFile('a.php', '<?php echo 1;');

        $cache->update($file);
        $this->assertTrue($cache->isCurrent($file));

        $cache->delete($file);
        $this->assertFalse($cache->isCurrent($file));
    }

    public function testClear() : void
    {
        $file = $this->createTempFile('a.php', '<?php echo 1;');

        $cache = new Cache($this->cacheFile, 'config-hash');
        $cache->update($file);
        $cache->save();

        $this->assertFileExists($this->cacheFile);

        $cache->clear();

        $this->assertFileDoesNotExist($this->cacheFile);
        $this->assertFalse($cache->isCurrent($file));
    }

    public function testClearWithNoCacheFile() : void
    {
        $cache = new Cache($this->cacheFile, 'config-hash');

        // should not throw
        $cache->clear();
        $this->assertFileDoesNotExist($this->cacheFile);
    }

    public function testMultipleFiles() : void
    {
        $cache = new Cache($this->cacheFile, 'config-hash');
        $fileA = $this->createTempFile('a.php', '<?php echo 1;');
        $fileB = $this->createTempFile('b.php', '<?php echo 2;');

        $cache->update($fileA);
        $cache->update($fileB);
        $cache->save();

        // reload
        $cache2 = new Cache($this->cacheFile, 'config-hash');
        $cache2->load();

        $this->assertTrue($cache2->isCurrent($fileA));
        $this->assertTrue($cache2->isCurrent($fileB));

        // modify one
        file_put_contents($fileB, '<?php echo 3;');
        $this->assertTrue($cache2->isCurrent($fileA));
        $this->assertFalse($cache2->isCurrent($fileB));
    }

    public function testSavePersistedIncrementally() : void
    {
        $cache = new Cache($this->cacheFile, 'config-hash');
        $fileA = $this->createTempFile('a.php', '<?php echo 1;');
        $fileB = $this->createTempFile('b.php', '<?php echo 2;');

        // save after first file
        $cache->update($fileA);
        $cache->save();

        // save after second file
        $cache->update($fileB);
        $cache->save();

        // reload — both should be present
        $cache2 = new Cache($this->cacheFile, 'config-hash');
        $cache2->load();

        $this->assertTrue($cache2->isCurrent($fileA));
        $this->assertTrue($cache2->isCurrent($fileB));
    }
}
