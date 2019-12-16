<?php

declare(strict_types=1);

namespace Ramsey\Skeleton\Test;

use Composer\IO\IOInterface;
use Composer\Script\Event;
use Mockery;
use Ramsey\Skeleton\Setup;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class CreateProjectTest extends TestCase
{
    private $tmpDir;
    private $oldDir;

    protected function mockeryTestSetUp(): void
    {
        $this->tmpDir = __DIR__ . '/../../build/test';
        $this->oldDir = getcwd();

        $filesystem = new Filesystem();
        $filesystem->mkdir($this->tmpDir);
        $filesystem->symlink(__DIR__ . '/../../skeleton', $this->tmpDir . '/skeleton');

        chdir($this->tmpDir);
    }

    protected function mockeryTestTearDown(): void
    {
        chdir($this->oldDir);
        (new Filesystem())->remove($this->tmpDir);
    }

    public function testProjectCreation(): void
    {
        $questions = static function () {
            static $questions = [
                'authorName' => 'Some Name',
                'authorEmail' => 'some.email@example.com',
                'authorUrl' => 'https://example.com/some/page',
                'copyrightHolder' => 'Some Copyright',
                'copyrightEmail' => 'copyright.email@example.com',
                'copyrightUrl' => 'https://example.com/copyright/url',
                'copyrightYear' => '2020',
                'conductEmail' => 'conduct.email@example.com',
                'githubUsername' => 'github',
                'githubProject' => 'project',
                'packageName' => 'package/name',
                'packageDescription' => 'Package description.',
                'keywords' => 'some,test,words',
                'namespace' => 'SomeApp',
                'baseClass' => 'App',
            ];

            return $questions ? array_shift($questions) : end(func_get_args());
        };

        $io = Mockery::mock(IOInterface::class);
        $io->shouldReceive('ask')->andReturnUsing($questions);
        $io->shouldReceive('askAndValidate')->andReturnUsing($questions);

        $event = Mockery::mock(Event::class, [
            'getIO' => $io,
        ]);

        Setup::wizard($event);

        $fixtures = (new Finder())
            ->ignoreDotFiles(false)
            ->files()
            ->in(realpath(__DIR__ . '/../fixture'));

        /** @var SplFileInfo $sample */
        foreach ($fixtures as $sample) {
            $actual = realpath($this->tmpDir . '/' . $sample->getRelativePathname());

            $this->assertFileEquals($sample->getRealPath(), $actual);
        }
    }
}
