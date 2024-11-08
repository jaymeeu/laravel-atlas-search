<?php
namespace App\Http\Controllers;
use App\Models\Movie;
use Illuminate\Http\JsonResponse;

class SearchController extends Controller
{
   public function search($search): JsonResponse
   {
       // Define the aggregation based on the search conditions
       if (!empty($search)) {

           $items = Movie::searchByTitle($search);

           return response()->json($items, 200);
       }

       return response()->json(['error' => 'conditions not met'], 400);
   }

   public function autocomplete($param): JsonResponse
   {
       try {
          $results = Movie::autocompleteByTitle($param);

           return response()->json($results, 200);
       } catch (\Exception $e) {
           return response()->json(['error' => $e->getMessage()], 500);
       }
   }
}
