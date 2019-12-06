<?php

namespace App\Http\Controllers;

use App\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'moderator']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $articles = Article::orderBy('created_at', 'desc')->take(7)->get();
        $user = Auth::user();
        return view('home', [
            'user_name' => $user->name,
            'user_role' => $user->roles()->first()->role,
            'articles' => $articles
        ]);
    }
}
