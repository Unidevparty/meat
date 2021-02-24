<?php

namespace App\Http\Controllers;

use App\Photogallery;
use App\PhotogalleryTag;
use Illuminate\Http\Request;

use View;

class PhotogalleryController extends Controller
{
    protected $first_page = 18;
	protected $perpage = 4;

    public function index(Request $request) {
        $galleries = Photogallery::published();

        if ($tag_alias = $request->get('tag')) {
            $current_tag = PhotogalleryTag::where('alias', $tag_alias)->first();

            if (!$current_tag || !$current_tag->count()) {
    			abort(404);
    		}

            $galleries = $current_tag->photogalleries()->published();
        }

        if ($search = $request->get('search')) {
            $galleries->where('name', 'like', '%' . $search . '%');
        }

        $galleries = $galleries->take($this->first_page)->get();

        $more_link = $this->more_link();

        $tags = PhotogalleryTag::all();


        return view('photogallery.list', [
            'tags' => $tags,
            'galleries' => $galleries,
            'more_link' => $more_link,
        ]);
    }

    public function more(Request $request) {
        $page = (int) request()->get('page');
        $page = $page > 1 ? $page - 2 : 0;
        $skip = $this->first_page + $page * $this->perpage;

        $galleries = Photogallery::published();

        if ($tag_alias = $request->get('tag')) {
            $current_tag = PhotogalleryTag::where('alias', $tag_alias)->first();

            if (!$current_tag || !$current_tag->count()) {
    			abort(404);
    		}

            $galleries = $current_tag->photogalleries()->published();
        }

        if ($search = $request->get('search')) {
            $galleries->where('name', 'like', '%' . $search . '%');
        }
        $galleries = $galleries->skip($skip)->take($this->perpage)->get();

        $more_link = $this->more_link();

        return view('photogallery.page', [
            'galleries' => $galleries,
            'more_link' => $more_link,
        ]);
    }

    public function show_by_id($id)
    {
        $alias = Photogallery::where('id', $id)
            ->value('alias');
        return redirect()->route('photogallery.show', $alias)->setStatusCode(301);

//        return $this->show('', $id);
    }

    public function show($alias, $id = null)
    {
        $photogallery = null;
        if ($id) {
            $photogallery = Photogallery::where('id', $id);
        } else {
            $photogallery = Photogallery::where('alias', $alias);
        }

        if (!member() || !member()->is_admin()) {
            $photogallery = $photogallery->published();
        }
        $photogallery = $photogallery->first();

        if (!$photogallery) abort(404);

        // Увеличиваем просмотры
        $photogallery->views += 1;
        $photogallery->save();

        View::share('title', $photogallery->title);
		View::share('keywords', $photogallery->keywords);
		View::share('description', $photogallery->description);
		View::share('source_image', $photogallery->main_image);
		View::share('canonical', url(route('photogallery.show', $photogallery->alias)));

        return view('photogallery.show', [
            'photogallery' => $photogallery
        ]);
    }

    protected function more_link() {
        $page   = (int) request()->get('page');
        $search = request()->get('search');
        $tag_alias = request()->get('tag');

        $galleries = Photogallery::published();

        if ($tag_alias) {
            $current_tag = PhotogalleryTag::where('alias', $tag_alias)->first();

            if (!$current_tag || !$current_tag->count()) {
    			abort(404);
    		}

            $galleries = $current_tag->photogalleries()->published();
        }
        if ($search) {
            $galleries->where('name', 'like', '%' . $search . '%');
        }

        $total = $galleries->count();

        $page = $page > 0 ? $page - 1 : 0;

        $current_count = $this->first_page + $page * $this->perpage;

        if ($total <= $current_count) {
            return null;
        }

        $next_page = $page + 2;

        return route('photogallery.more', [
            'page' => $next_page,
            'search' => $search
        ]);
    }
}
