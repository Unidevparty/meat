<?php

namespace App\Http\Controllers\Admin;

use App\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class CommentsController extends Controller
{	public function __construct() {
		View::share('section_name', 'Комментарии');
		View::share('page_name', '');
	}


    public function index()
    {
        View::share('page_name', 'Список');

		

		return view('admin.сomments.list', compact(
		'text',
		'member_id',
		'page_id',
		'type',));
    }


    public function delete($id) {
        $coment = Comment::findOrFail($id);

        $coment->delete();

        flash('Комментарий успешно удален', 'success');

        return Redirect::to(URL::previous() . "#comments");
    }
}

