<?php

namespace Tests\Integration;

use App\Models\Movie;
use Tests\TestCase;

class SearchMovieTest extends TestCase
{
    public function test_search_movie_by_title_with_exact_match(): void
    {
        $results = Movie::searchByTitle('the matrix');
        $titles = $results->pluck('title')->toArray();

        self::assertCount(20, $titles);
        self::assertSame('The Matrix', $titles[0]);
        self::assertContains('The Matrix Reloaded', $titles);
        self::assertContains('Armitage: Dual Matrix', $titles);
    }

    public function test_search_movie_without_result(): void
    {
        $results = Movie::searchByTitle('AZERTY');

        self::assertCount(0, $results);
    }

    public function test_autocomplete_movie_by_title(): void
    {
        $results = Movie::autocompleteByTitle('matr');
        $titles = $results->pluck('title')->toArray();

        self::assertCount(5, $titles);
        self::assertContains('The Matrix Reloaded', $titles);
        self::assertContains('The Matrix Revolutions', $titles);
        self::assertArrayHasKey('highlights', $results[0]);
    }
}
