<?php

namespace App\Http\Controllers;

use App\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $this->validate($request, [
            'text' => 'required'
        ]);

        $data = $request->all();

        $data['member_id'] = member()->id;

        $comment = Comment::create($data);
        
        flash('Комментарий успешно добавлен', 'success');

        return Redirect::to(URL::previous() . "#comments");
    }

    public function delete($id) {
        $coment = Comment::findOrFail($id);

        $coment->delete();

        flash('Комментарий успешно удален', 'success');

        return Redirect::to(URL::previous() . "#comments");
    }
}
