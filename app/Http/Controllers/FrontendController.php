<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FrontendController extends Controller
{
    //home page
    public function homePage(){
        $parentCount =  DB::table('parents')->count();
        $midwivesCount =  DB::table('midwives')->count();
        $babiesCount =  DB::table('baby')->count();
        $vaccinationCount =  DB::table('baby_vaccinations')->count();

        $data = [
            'title' => 'Home Page',
            'clients' => $parentCount,
            'sellers' => $midwivesCount,
            'services' => $babiesCount,
            'serviceRequests' => $vaccinationCount
        ];
        return view('front.layout.pages-layout',$data);
        // return view('example-frontend',$data);
    }

    //about page
    public function aboutPage(){
        $data = [
            'title' => 'About Page'
        ];
        return view('front.pages.about',$data);
    }

    //contact page
    public function contactPage(){
        $data = [
            'title' => 'Contact Page'
        ];
        return view('front.pages.contact',$data);
    }
}
