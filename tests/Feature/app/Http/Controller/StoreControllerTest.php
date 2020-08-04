<?php

namespace Tests\Feature\app\Http\Controller;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Post;

class StoreControllerTest extends TestCase
{
    use DatabaseMigrations;
    use WithoutMiddleware;


    // test
    public function test_post_not_created_no_title()
    {
        $response = $this->post('/store', ['id' => "abc", 'title' => "", 
        "content" => 'A, B, C', "views" => 0, 'timestamp' => 155583241]);
        $response->assertStatus(302);
    }

    public function test_post_not_created_no_id()
    {
        $response = $this->post('/store', ['id' => "", 'title' => "abc", 
        "content" => 'A, B, C', "views" => 0, 'timestamp' => 155583241]);
        $response->assertStatus(302);
    }

    public function test_post_not_created_no_content()
    {
        $response = $this->post('/store', ['id' => "A, B, C", 'title' => "abc", 
        "content" => '', "views" => 0, 'timestamp' => 155583241]);
        $response->assertStatus(302);
    }

    // test
    public function test_post_created()
    {
        $response = $this->post('/store', ['id' => "abc", 'title' => "Alphabet", 
        "content" => 'A, B, C', "views" => 0, 'timestamp' => 155583241]);
        $post = Post::where('id', "abc")->first();

        $this->assertEquals($post->title, "Alphabet");
        $response->assertStatus(200);
    }

    // // test
    public function test_post_id_found()
    {
        $response = $this->post('/store', ['id' => "abc", 'title' => "Alphabet", 
        "content" => 'A, B, C']);
        $response->assertStatus(200);

        $post = Post::where('id', "abc")->first();
        $this->assertEquals($post->title, "Alphabet");

        $response = $this->post('/store', ['id' => "abc", 'title' => "New Alphabet", 
        "content" => 'New A, B, C']);

        $post = Post::where('id', "abc")->first();
        $this->assertEquals($post->title, "New Alphabet");
        $response->assertStatus(200);
    }

    public function test_add_multiple_fields()
    {
        $response = $this->post('/store', ['id' => "abc", 'title' => "Alphabet", 
        "content" => 'A, B, C']);
        $response->assertStatus(200);

        $post = Post::where('id', "abc")->first();
        $this->assertEquals($post->title, "Alphabet");

        $response = $this->post('/store', ['id' => "abc3", 'title' => "New Alphabet", 
        "content" => 'New A, B, C']);

        $post = Post::where('id', "abc3")->first();
        $this->assertEquals($post->title, "New Alphabet");
        $response->assertStatus(200);

        $post = Post::count();
        $this->assertEquals($post, 2);
    }

    public function test_get_posts_with_under_10_views()
    {
        $response = $this->post('/store', ['id' => "abc", 'title' => "Alphabet", 
        "content" => 'A, B, C',  "views" => 15]);
        $response = $this->post('/store', ['id' => "abc3", 'title' => "Alphabet", 
        "content" => 'A, B, C',  "views" => 8]);
        $response = $this->post('/store', ['id' => "ab33", 'title' => "Alphabet", 
        "content" => 'A, B, C',  "views" => 18]);
        $response = $this->post('/store', ['id' => "axb33", 'title' => "Alphabet", 
        "content" => 'A, B, C',  "views" => 5]);
        $response = $this->post('/store', ['id' => "a3", 'title' => "Alphabet", 
        "content" => 'A, B, C',  "views" => 0]);

        $post = Post::count();
        $this->assertEquals($post, 5);

        $responses = $this->get('/store?query=LESS_THAN(views,10)');
        $array = json_decode($responses->content());
        $this->assertEquals(count($array), 3);
    }

    public function test_get_posts_with_equal_0_views()
    {
        $response = $this->post('/store', ['id' => "abc", 'title' => "Alphabet123", 
        "content" => 'A, B, C',  "views" => 0]);
        $response = $this->post('/store', ['id' => "abc3", 'title' => "Alphabet321", 
        "content" => 'A, B, C',  "views" => 8]);
        $response = $this->post('/store', ['id' => "ab33", 'title' => "Alphabet221", 
        "content" => 'A, B, C',  "views" => 18]);
        $response = $this->post('/store', ['id' => "axb33", 'title' => "Alphabet2112", 
        "content" => 'A, B, C',  "views" => 5]);
        $response = $this->post('/store', ['id' => "a3", 'title' => "Alphabet333", 
        "content" => 'A, B, C',  "views" => 0]);

        $post = Post::count();
        $this->assertEquals($post, 5);
        $temp = Post::all();

        $responses = $this->get('/store?query=EQUAL(views,0)');
        $array = json_decode($responses->content());
        $this->assertEquals(count($array), 2);
    }

    public function test_get_posts_greater_then_10_views()
    {
        $response = $this->post('/store', ['id' => "abc", 'title' => "Alphabet", 
        "content" => 'A, B, C',  "views" => 30]);
        $response = $this->post('/store', ['id' => "abc3", 'title' => "Alphabet", 
        "content" => 'A, B, C',  "views" => 28]);
        $response = $this->post('/store', ['id' => "ab33", 'title' => "Alphabet", 
        "content" => 'A, B, C',  "views" => 18]);
        $response = $this->post('/store', ['id' => "axb33", 'title' => "Alphabet", 
        "content" => 'A, B, C',  "views" => 15]);
        $response = $this->post('/store', ['id' => "a3", 'title' => "Alphabet", 
        "content" => 'A, B, C',  "views" => 0]);

        $post = Post::count();
        $this->assertEquals($post, 5);

        $responses = $this->get('/store?query=GREATER_THAN(views,10)');
        $array = json_decode($responses->content());
        $this->assertEquals(count($array), 4);
    }

    public function test_get_posts_title_not_equal_to_alphabet()
    {
        $response = $this->post('/store', ['id' => "abc", 'title' => "Alphabet", 
        "content" => 'A, B, C',  "views" => 30]);
        $response = $this->post('/store', ['id' => "abc3", 'title' => "Alphabet", 
        "content" => 'A, B, C',  "views" => 28]);
        $response = $this->post('/store', ['id' => "ab33", 'title' => "Alphabet", 
        "content" => 'A, B, C',  "views" => 18]);
        $response = $this->post('/store', ['id' => "ab3323", 'title' => "This One", 
        "content" => 'A, B, C',  "views" => 18]);
        $response = $this->post('/store', ['id' => "axb33", 'title' => "Alphabet", 
        "content" => 'A, B, C',  "views" => 15]);
        $response = $this->post('/store', ['id' => "a3", 'title' => "Alphabet", 
        "content" => 'A, B, C',  "views" => 0]);
        $response = $this->post('/store', ['id' => "one", 'title' => "This Two", 
        "content" => 'A, B, C',  "views" => 18]);
        $response = $this->post('/store', ['id' => "abc", 'title' => "No longer alphabet", 
        "content" => 'A, B, C',  "views" => 30]);

        $post = Post::count();
        $this->assertEquals($post, 7);

        $responses = $this->get('/store?query=NOT(EQUAL(title, "Alphabet"))');
        $array = json_decode($responses->content());
        
        $this->assertEquals(count($array), 3);
    }

    //   //test
    public function test_get_posts_title_equal_to_alphabet_and_conter_equal_to_a_b()
    {
        $response = $this->post('/store', ['id' => "abc", 'title' => "Alphabet", 
        "content" => 'A,B',  "views" => 30]);
        $response = $this->post('/store', ['id' => "abc3", 'title' => "Alphabet", 
        "content" => 'A,B,C',  "views" => 28]);
        $response = $this->post('/store', ['id' => "ab33", 'title' => "Alphabet", 
        "content" => 'A,B,C',  "views" => 18]);
        $response = $this->post('/store', ['id' => "ab3323", 'title' => "Alphabet", 
        "content" => 'A,B',  "views" => 18]);
        $response = $this->post('/store', ['id' => "axb33", 'title' => "Alphabet", 
        "content" => 'A,B',  "views" => 15]);
        $response = $this->post('/store', ['id' => "a3", 'title' => "Alphabet", 
        "content" => 'A,B',  "views" => 0]);
        $response = $this->post('/store', ['id' => "one", 'title' => "This Two", 
        "content" => 'A,B,C',  "views" => 18]);
        $response = $this->post('/store', ['id' => "abc", 'title' => "No longer alphabet", 
        "content" => 'A,B,C',  "views" => 30]);

        $post = Post::count();
        $this->assertEquals($post, 7);

        $responses = $this->get('/store?query=AND(EQUAL(title, "Alphabet"),EQUAL(content, "A,B") )');
        $array = json_decode($responses->content());
        
        $this->assertEquals(count($array), 3);
    }
}
