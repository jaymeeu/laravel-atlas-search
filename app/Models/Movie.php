<?php

namespace App\Models;

use Illuminate\Support\Collection;
use MongoDB\Laravel\Eloquent\Model;

class Movie extends Model
{
    protected $connection = 'mongodb';

    public static function searchByTitle(string $input): Collection
    {
        $searcher_aggregate = [
            [
                '$search' => [
                    'index' => 'movie_search',
                    'compound' => [
                        'must' => [
                            [
                                'text' => [
                                    'query' => $input,
                                    'path' => 'title',
                                    'fuzzy' => new \stdClass() // Adding fuzzy matching
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                '$limit' => 20
            ],
            [
                '$project' =>[
                    'title' => 1,
                    'genres' => 1,
                    'poster'=> 1,
                    'rated'=> 1,
                    'plot' => 1,
                ]
            ],
        ];

        // Execute the aggregation query on the collection
        return self::raw(function ($collection) use ($searcher_aggregate) {
            return $collection->aggregate($searcher_aggregate);
        });
    }

    public static function autocompleteByTitle(string $input): Collection
    {
        // Define the aggregation pipeline
        $searcher_aggregate = [
            [
                '$search' => [
                    'index' => 'movie_title_autocomplete',
                    'autocomplete' => [
                        'query' => $input,
                        'path' => 'title',
                    ],
                    'highlight' => [
                        'path' => ['title']
                    ]
                ]
            ],
            ['$limit' => 5], // Limit the result to 5
            [
                '$project' => [
                    'title' => 1,
                    'highlights' => ['$meta' => 'searchHighlights']
                ]
            ]
        ];

        // Execute the aggregation query
        return self::raw(function ($collection) use ($searcher_aggregate) {
            return $collection->aggregate($searcher_aggregate);
        });
    }
}
