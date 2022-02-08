<?php

namespace App\Http\Controllers;

use App\Model\PublicPage;
use Illuminate\Http\Request;

class PublicPagesController extends Controller
{
    /**
     * Renders public ( admin-created ) pages.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getPage(Request $request)
    {
        $slug = $request->route('slug');
        $page = PublicPage::where('slug', $slug)->first();

        if (! $page) {
            abort(404);
        }

        return view('pages.public-page', ['page'=>$page]);
    }
}
