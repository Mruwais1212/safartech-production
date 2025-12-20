<?php

namespace App\Http\Controllers\Website;

use App\Models\PageContent;
use App\Models\Panel\FixedPage;
use Illuminate\Http\Request;

class FixedPageController extends Controller
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function aboutUs()
    {
        $aboutUs = PageContent::where('url', 'about-us')->where('slug', 'about-us')->first();
        $ourCompanyOverview = PageContent::where('url', 'about-us')->where('slug', 'our-company-overview')->first();

        $contents = PageContent::where('url', 'about-us')->whereNotIn('slug', ['about-us', 'our-company-overview'])->get();

        return view('website.about-us', compact('aboutUs', 'contents', 'ourCompanyOverview'));
    }

    public function termsAndConditions()
    {
        $fixedPage = FixedPage::where('slug', 'terms-and-conditions')->first();

        return view('website.page', [
            'page' => app()->getLocale() == 'ar' ? $fixedPage->name_ar : $fixedPage->name_en,
            'content' => app()->getLocale() == 'ar' ? $fixedPage->content_ar : $fixedPage->content_en,
        ]);
    }

    public function privacyPolicy()
    {
        $fixedPage = FixedPage::where('slug', 'privacy-policy')->first();

        return view('website.page', [
            'page' => app()->getLocale() == 'ar' ? $fixedPage->name_ar : $fixedPage->name_en,
            'content' => app()->getLocale() == 'ar' ? $fixedPage->content_ar : $fixedPage->content_en,
        ]);
    }
}
