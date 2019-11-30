<?php

namespace App\Http\Controllers;

use App\Article;
use App\Category;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\File;
use Illuminate\Http\UploadedFile;

class ArticleController extends Controller
{
    /**
     * @var string Default path for storing image-files
     */
    private $folder = 'img/articles';

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(Gate::denies('create_article')) {
            return redirect('error_page')->with(['message' => 'There is no access to create article']);
        }
        $action = 'Create Article';
        $categories = Category::all();
        $users = User::all();
        $user = Auth::user();
        return view('articles.create', [
            'action' => $action,
            'categories' => $categories,
            'users' => $users,
            'user' => $user
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|min:3|max:128',
            'text' => 'required|min:50|max:512',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
        $data = $request->except('_token', 'image');
        $article = new Article();
        $article->fill( $data );
        if ($user_id = $request->user_id) {
            $article->user_id = $request->user()->id;
        }
        if ($file = $request->image) {
            $this->imageSave($file, $article);
        }
        $article->save();
        return redirect(route('article_show', $article->id));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function edit(Article $article)
    {
        if(Gate::denies('edit_article')) {
            return redirect('error_page')->with('message', 'There is no access to update article');
        }
        $action = 'Update Article';
        $categories = Category::all();
        $users = User::all();

        return view('articles.edit', [
            'action' => $action,
            'article' => $article,
            'categories' => $categories,
            'users' => $users
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Article $article)
    {
        $this->validate($request, [
            'title' => 'required|min:3|max:128',
            'text' => 'required|min:50|max:512',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
        $data = $request->except('_token', 'image', 'image_del');
        $article->fill( $data );
        if($request->has('image_del')) {
            $this->imageDelete($article->image);
            $article->image = '';
        } elseif ($file = $request->image) {
            $this->imageSave($file, $article);
        }
        $article->save();
        return redirect()->back()->with(['status' => 'Ad updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function destroy(Article $article)
    {
        if(Gate::denies('delete_article')) {
            return redirect('error_page')->with('message', 'There is no access to delete article');
        }
        $article->delete();
        return redirect('/');
    }

    // _________ Private File Helpers: _________

    private function imageSave(UploadedFile $file, Article $a) {
        if($path = $a->image)
            $this->imageDelete($path);
        $dateName = date('dmyHis');
        $name = $dateName . '.' . $file->getClientOriginalExtension();
        $file->move($this->folder, $name);
        $a->image = "/$this->folder/$name";
    }

    private function imageDelete(string $path) {
        File::delete(trim($path, '/'));
    }
}
