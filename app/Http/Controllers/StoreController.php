<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'id' => 'required|max:255',
            'title' => 'required|max:255',
            'content' => 'required',
        ]); 
       
        $info = $request->all();
        $postFound = Post::where('id', $info['id'])->first();
        if(count($postFound ) > 0){
            $postFound->title = $info['title'];
            $postFound->content = $info['content'];
            $postFound->views =  (isset($info['views'])) ? $info['views'] : 0;
            $postFound->timestamp = Carbon::now()->timestamp;
            $postFound->save();
        }else{
            $post = new Post;
            $post->id = $info['id'];
            $post->title = $info['title'];
            $post->content = $info['content'];
            $post->views =  (isset($info['views'])) ? $info['views'] : 0;
            $post->timestamp = Carbon::now()->timestamp;
            $post->save();
        }
        return response('Ok', 200);
    }

    public function retrieveStore(Request $request)
    {
        $regexEnding = '\s*\([^,)]+(?:,\s*[^,)]+)+\)';
        $params = $request->query("query");
        $params = $this->refineParams("LESS", "/\bLESS_THAN".$regexEnding."/m", "<", $params);
        $params = $this->refineParams("GREATER", "/\bGREATER_THAN".$regexEnding."/m", ">", $params);
        $params = $this->refineParams("EQUAL", "/\bEQUAL".$regexEnding."/m", "=", $params);
        
        $params = $this->refineParams("NOT", "/\bNOT".$regexEnding."/m", "!=", $params);
        
        $params = $this->refineParams("AND",  "/\bAND".$regexEnding."/m", " and ", $params);
        $params = $this->refineParams("OR",  "/\bOR".$regexEnding."/m", " or ", $params);
        $json = Post::whereRaw($params)->get();
        return response()->json($json, 200);
    }

    private function refineParams($queryParam, $regex, $symbol, $params)
    {
       
        $fields = ["id" => 0, "title" => 0, "content" => 0, "views" => 0, "timestamp" => 0];
        preg_match_all($regex, $params, $out, PREG_PATTERN_ORDER);
    
        if(!empty($out[0])){
            foreach($out[0] as $key){
                
                $str= $key;
                
                $begining = strpos($str, '(') + 1;
                
                $end = strpos($str, ',') -  $begining;
                
                $firstValue = substr($str,  $begining , $end);
                
                $firstValue  = str_replace(' ', '', $firstValue);
              
                $begining = strpos($str, ',') + 1;
                
                $end = strpos($str, ')') -  $begining;
                
                $endValue = substr($str,  $begining , $end);
                
                $endValue = str_replace(' ', '', $endValue);
                
                if(array_search($firstValue, $fields) > -1){
                    
                    $params = str_replace($str, $firstValue . $symbol . $endValue, $params);
                    
                }
                
            }
        }
        // dd($params);
        return $params;
    }
}
