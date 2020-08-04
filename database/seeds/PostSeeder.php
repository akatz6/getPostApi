<?php

use Illuminate\Database\Seeder;
use App\Post;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $post_1 = new Post;
        $post_1->id = "abc";
        $post_1->title = "Alphabet";
        $post_1->content = "How much wood can a wood chuck";
        $post_1->views = 0;
        $post_1->timestamp = 1555832341;
        $post_1->save();
    }
}
