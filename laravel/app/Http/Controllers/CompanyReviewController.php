<?php

namespace App\Http\Controllers;

use App\CompanyReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class CompanyReviewController extends Controller
{
    public function store(Request $request)
    {
        $this->validate($request, [
            'text' => 'required',
            'company_id' => 'required'
        ]);

        $data = $request->all();

        $data['member_id'] = member()->id;

        $review = CompanyReview::create($data);

        $review->company->updateRating();

        flash('Отзыв успешно добавлен', 'success');

        return Redirect::to(URL::previous() . "#reviews");
    }

    public function delete($id) {
        $review = CompanyReview::findOrFail($id);

        $company = $review->company;

        $review->delete();

        $company->updateRating();

        flash('Отзыв успешно удален', 'success');

        return Redirect::to(URL::previous() . "#reviews");
    }

    public function like($id)
    {
        $review = CompanyReview::findOrFail($id);

        $review->likes++;

        $review->save();

        return Redirect::to(URL::previous() . "#reviews");
    }

    public function dislike($id)
    {
        $review = CompanyReview::findOrFail($id);

        $review->dislikes++;

        $review->save();

        return Redirect::to(URL::previous() . "#reviews");
    }
}
