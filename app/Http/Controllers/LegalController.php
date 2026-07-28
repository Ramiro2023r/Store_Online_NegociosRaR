<?php

namespace App\Http\Controllers;

use App\Models\Faq;

class LegalController extends Controller
{
    public function privacyPolicy()
    {
        return view('legal.privacy-policy');
    }

    public function termsConditions()
    {
        return view('legal.terms-conditions');
    }

    public function shippingReturns()
    {
        $faqs = Faq::active()->ordered()->get();

        return view('legal.shipping-returns', compact('faqs'));
    }
}
