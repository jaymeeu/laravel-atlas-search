<?php

namespace App\Models;

use Illuminate\Support\Collection;
use MongoDB\Laravel\Eloquent\Model;


class Movie extends Model
{
    protected $connection = 'mongodb';

    public static function searchByTitle(string $input): Collection
    {
        return self::aggregate()
            ->search([
                'index' => 'default',
                'compound' => [
                    'must' => [
                        [
                            'text' => [
                                'query' => $input,
                                'path' => 'title',
                                'fuzzy' => ['maxEdits' => 2] // Adding fuzzy matching
                            ]
                        ]
                    ]
                ]
            ])
            ->limit(20)
            ->project(title: 1, genres: 1, poster: 1, rated: 1, plot: 1)
        ->get();
    }

    public static function autocompleteByTitle(string $input): Collection
    {
        return self::aggregate()
            ->search([
                'index' => 'movie_autocomplete',
                'autocomplete' => [
                    'query' => $input,
                    'path' => 'title'
                ],
                'highlight' => [
                    'path' => ['title']
                ]
            ])
            ->limit(5) // Limit the result to 5
            ->project(title: 1, highlights: ['$meta' => 'searchHighlights'])
            ->get();
    }
}
