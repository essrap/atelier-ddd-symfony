<?php

declare(strict_types=1);

namespace App\Tests\Architecture;

use PHPUnit\Framework\TestCase;

/**
 * Workshop guard: the folders of src/ are declared contexts, and the code
 * uses the words of the glossary, not the ones it asks to avoid.
 */
final class ContextNamingTest extends TestCase
{
    private const string SHARED = 'Shared';

    public function testEveryFolderOfSrcIsADeclaredContext(): void
    {
        $declared = [...ProjectDocs::declaredContexts($this->read('docs/contexts.md')), self::SHARED];

        $paths = glob($this->root() . '/src/*', GLOB_ONLYDIR);
        $folders = array_map(basename(...), false === $paths ? [] : $paths);

        foreach ($folders as $folder) {
            self::assertContains(
                $folder,
                $declared,
                \sprintf('src/%s is not a declared context: add it to docs/contexts.md.', $folder),
            );
        }
    }

    public function testNamesDoNotUseTheWordsTheGlossaryAsksToAvoid(): void
    {
        $forbidden = ProjectDocs::forbiddenWords($this->read('docs/glossary.md'));
        $lowerCased = array_change_key_case($forbidden);

        foreach ($this->namesInSrc() as $path => $names) {
            foreach ($names as $name) {
                foreach (ProjectDocs::words($name) as $word) {
                    if (isset($lowerCased[$word])) {
                        self::fail(\sprintf(
                            '%s is forbidden, use %s (glossary), found in %s',
                            ucfirst($word),
                            $lowerCased[$word],
                            $path,
                        ));
                    }
                }
            }
        }

        self::assertNotSame([], $forbidden, 'The glossary must list at least one word to avoid.');
    }

    /**
     * Names of folders, files and enum cases, by path relative to the project.
     *
     * @return array<string, list<string>>
     */
    private function namesInSrc(): array
    {
        $root = $this->root();
        $found = [];

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($root . '/src', \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST,
        );

        foreach ($iterator as $entry) {
            if (!$entry instanceof \SplFileInfo) {
                continue;
            }

            $relative = substr($entry->getPathname(), \strlen($root) + 1);
            $names = [$entry->isDir() ? $entry->getFilename() : $entry->getBasename('.php')];

            if ($entry->isFile() && 'php' === $entry->getExtension()) {
                $code = (string) file_get_contents($entry->getPathname());
                preg_match_all('/^\s*case\s+([A-Za-z0-9_]+)/m', $code, $cases);
                $names = [...$names, ...$cases[1]];
            }

            $found[$relative] = $names;
        }

        return $found;
    }

    private function read(string $relativePath): string
    {
        $content = file_get_contents($this->root() . '/' . $relativePath);

        return false === $content ? self::fail('Cannot read ' . $relativePath) : $content;
    }

    private function root(): string
    {
        return \dirname(__DIR__, 2);
    }
}
