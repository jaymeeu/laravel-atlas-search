<?php
namespace App\Http\Controllers;
use App\Models\Movie;
class SearchController extends Controller
{
   public function search($search)
   {
       // Define the aggregation based on the search conditions
       if (!empty($search)) {
           $searcher_aggregate = [
               [
                   '$search' => [
                       'index' => 'movie_search',
                       'compound' => [
                           'must' => [
                               [
                                   'text' => [
                                       'query' => $search,
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
           $items = Movie::raw(function ($collection) use ($searcher_aggregate) {
               return $collection->aggregate($searcher_aggregate);
           });
           return response()->json($items, 200);
       }
       return response()->json(['error' => 'conditions not met'], 400);
   }


   public function autocomplete($param)
   {
       try {
           // Define the aggregation pipeline
           $searcher_aggregate = [
               [
                   '$search' => [
                       'index' => 'movie_title_autocomplete',
                       'autocomplete' => [
                           'query' => $param,
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
           $results = Movie::raw(function ($collection) use ($searcher_aggregate) {
               return $collection->aggregate($searcher_aggregate);
           });


           return response()->json($results, 200);
       } catch (\Exception $e) {
           return response()->json(['error' => $e->getMessage()], 500);
       }
   }

}
