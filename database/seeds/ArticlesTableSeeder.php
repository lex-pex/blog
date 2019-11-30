<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArticlesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();
        $articles = [];
        for($i = 0; $i < 150; $i++) {
            $articles[] = [
                'title' => $faker->text(30),
                'text' => $faker->text(256),
                'category_id' => random_int(1, 5),
                'user_id' => random_int(1, 3),
                'created_at' => date('Y-m-d H:i:s', time())
            ];
        }
        DB::table('articles')->insert($articles);
    }
}
