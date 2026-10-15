<?php

declare(strict_types=1);

namespace App\Tests\Architecture;

/**
 * Reads the two documents that fix the vocabulary: contexts.md and glossary.md.
 */
final class ProjectDocs
{
    /**
     * Contexts declared in the table of the "Contexts" section of contexts.md.
     *
     * @return list<string>
     */
    public static function declaredContexts(string $contextsMarkdown): array
    {
        if (1 !== preg_match('/^## Contexts\R(.*?)(?=^## |\z)/ms', $contextsMarkdown, $section)) {
            return [];
        }

        preg_match_all('/^\| `([A-Za-z]+)` \|/m', $section[1], $matches);

        return $matches[1];
    }

    /**
     * Words to avoid in code, from the "Avoid" column of the glossary.
     *
     * @return array<string, string> forbidden word => term to use instead
     */
    public static function forbiddenWords(string $glossaryMarkdown): array
    {
        $avoidColumn = null;
        $forbidden = [];

        $lines = preg_split('/\R/', $glossaryMarkdown);

        foreach (false === $lines ? [] : $lines as $line) {
            if (!str_starts_with($line, '|')) {
                continue;
            }

            $cells = array_map(trim(...), explode('|', trim($line, " |\t")));

            if (null === $avoidColumn) {
                foreach ($cells as $index => $cell) {
                    if (str_starts_with($cell, 'Avoid')) {
                        $avoidColumn = $index;
                    }
                }

                continue;
            }

            if (!isset($cells[$avoidColumn]) || 1 !== preg_match('/`([A-Za-z]+)`/', $cells[0], $term)) {
                continue;
            }

            preg_match_all('/`([A-Za-z]+)`/', $cells[$avoidColumn], $avoided);
            foreach ($avoided[1] as $word) {
                $forbidden[$word] = $term[1];
            }
        }

        return $forbidden;
    }

    /**
     * Splits a code name ("PickedUp", "picked_up") into lowercase words.
     *
     * @return list<string>
     */
    public static function words(string $name): array
    {
        $parts = preg_split('/(?<=[a-z0-9])(?=[A-Z])|[_\W]+/', $name, -1, PREG_SPLIT_NO_EMPTY);

        return array_map(strtolower(...), false === $parts ? [] : $parts);
    }
}
