<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FrontendController extends Controller
{
    //home page
    public function homePage(){
        $clientCount =  DB::table('clients')->count();
        $sellersCount =  DB::table('sellers')->count();
        $servicesCount =  DB::table('services')->count();
        $servicseRequestCount =  DB::table('service_requests')->count();

        $data = [
            'title' => 'Home Page',
            'clients' =>$clientCount,
            'sellers' =>$sellersCount,
            'services' => $servicesCount,
            'serviceRequests' => $servicseRequestCount
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
