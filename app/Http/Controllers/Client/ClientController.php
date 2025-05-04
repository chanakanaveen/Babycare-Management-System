<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Baby;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Client;
use App\Models\Seller;
use App\Models\ServiceRequest;
use App\Models\VerificationToken;
use Illuminate\Support\Facades\DB;
use constGuards;
use constDefaults;
use Illuminate\Support\Facades\File;

class ClientController extends Controller
{
    //login
    public function login(Request $request){
        $data = [
            'pageTitle'=>'Client Login'
        ];
        return view('back.pages.client.auth.login',$data);
    }

    //register
    public function register(Request $request){
        $data = [
            'pageTitle'=>'Create Client Account'
        ];
        return view('back.pages.client.auth.register',$data);
    }

    //client home
    public function home(Request $request){
        $client = null;
        if( Auth::guard('client')->check() ){
            $client = Auth::guard('client')->user();
        }

        $babys = DB::table('baby as a')->selectRaw('a.*, b.name as parentname')
        ->leftJoin('sellers as b', 'b.id', '=', 'a.parent_id')
        ->where('a.parent_id',auth()->id())
        ->get();

        $midwife =  DB::table('sellers as a')->get();

        $parents = DB::table('clients as a')->get();

        $data = [
            'pageTitle'=>'Client Dashboard',
            'client'=>$client,
            'babys'=>$babys,
            'midwife'=>$midwife,
            'parents'=>$parents,
        ];
        return view('back.pages.client.baby',$data);
    }

    //create seller account
    public function createClient(Request $request){
        //Validate clients Registration Form
        $request->validate([
            'name'=>'required',
            'email'=>'required|email|unique:clients',
            'password'=>'min:5|required_with:confirm_password|same:confirm_password',
            'confirm_password'=>'min:5'
        ]);


        $clients = new Client();
        $clients->name = $request->name;
        $clients->email = $request->email;
        $clients->password = Hash::make($request->password);
        $saved = $clients->save();

        if( $saved ){
            // return redirect()->route('seller.register-success');
            return redirect()->route('client.login')->with('success','Good!, Registation successful. Login with your credentials and complete setup your Client account.');
        }else{
            return redirect()->route('client.register')->with('fail','Something went wrong.');
        }
    }

    //login function
    public function loginHandler(Request $request){
        $fieldType = filter_var($request->login_id, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if( $fieldType == 'email' ){
            $request->validate([
                'login_id'=>'required|email|exists:clients,email',
                'password'=>'required|min:5|max:45'
            ],[
                'login_id.required'=>'Email or Username is required.',
                'login_id.email'=>'Invalid email address.',
                'login_id.exists'=>'Email is not exists in system.',
                'password.required'=>'Password is required'
            ]);
        }else{
            $request->validate([
                'login_id'=>'required|exists:sellers,username',
                'password'=>'required|min:5|max:45'
            ],[
                'login_id.required'=>'Email or Username is required.',
                'login_id.exists'=>'Username is not exists in system.',
                'password.required'=>'Password is required'
            ]);
        }

        $creds = array(
            $fieldType => $request->login_id,
            'password' => $request->password
        );

        if( Auth::guard('client')->attempt($creds) ){


            if( !auth('client')->user()->verified ){
                return redirect()->route('client.client-details');
            }else{
                return redirect()->route('client.home');
            }
        }else{
            return redirect()->route('client.login')->withInput()->with('fail','Incorrect password.');
        }
    }

    //logout
    public function logoutHandler(Request $request){
        Auth::guard('client')->logout();
        return redirect()->route('client.login')->with('fail','You are logged out!');
    }

    //client details
    public function clientDetails(Request $request){
        $client = null;
        if( Auth::guard('client')->check() ){
            $client = Auth::guard('client')->user();
        }
        $provinces = DB::table('provinces')->get();
        $districts = DB::table('districts')->get();
        $cities = DB::table('cities')->get();
        //dd($client);
        $data = [
            'pageTitle'=>'Client Details',
            'client'=>$client,
            'provinces'=>$provinces,
            'districts'=>$districts,
            'cities'=>$cities
        ];
        return view('back.pages.client.client-details',$data);
    }

    //save client details
    public function saveClientDetails(Request $request){ //dd($request);
        $request->validate([
            'name'=>'required',
            'phone'=>'required',
            'address'=>'required',
            'provinces'=>'required',
            'districts'=>'required',
            'cities'=>'required',
        ]);

        $client = Client::find(auth()->id()); //dd($request,$client);
        $client->name = $request->name;
        $client->phone = $request->phone;
        $client->address = $request->address;
        $client->provinces = $request->provinces;
        $client->districts = $request->districts;
        $client->cities = $request->cities;
        $client->verified = "1";

        $saved = $client->save();
        if( $saved ){
            return redirect()->route('client.home')->with('success','Client details updated successfully.');
        }else{
            return redirect()->route('client.client-details')->with('fail','Something went wrong.');
        }
    }

    //change profile picture
    public function changeProfilePicture(Request $request){


        $client = Client::findOrFail(auth()->id());
        $path = 'images/users/clients/';

        if($request->hasFile('clientProfilePictureFile')){
            $file = $request->file('clientProfilePictureFile');
            $old_picture = $client->getAttributes()['picture'];
            $file_path = $path.$old_picture;
            $filename = 'CLIENT_IMG_'.rand(2,1000).$client->id.time().uniqid().'.jpg';

            $upload = $file->move(public_path($path),$filename);
            if($upload){
                if( $old_picture != null && File::exists(public_path($path.$old_picture)) ){
                    File::delete(public_path($path.$old_picture));
                }
                $client->update(['picture'=>$filename]);
                return response()->json(['status'=>1,'msg'=>'Your profile picture has been successfully updated.']);
            }else{
                return response()->json(['status'=>0,'msg'=>'Something went wrong.']);
            }
        }

        // return redirect()->route('client.client-details')->with('success','Profile picture updated successfully');
    }

     //profile view
     public function profileView(Request $request){
        $client = null;
        if( Auth::guard('client')->check() ){
            $client = client::findOrFail(auth()->id());
        }
        $provinces = DB::table('provinces')->get();
        $districts = DB::table('districts')->get();
        $cities = DB::table('cities')->get();

        $data = [
            'pageTitle'=>'Client Profile',
            'client'=>$client,
            'provinces'=>$provinces,
            'districts'=>$districts,
            'cities'=>$cities
        ];

        return view('back.pages.client.profile', $data);
    }

     //update profile
     public function updateProfile(Request $request){
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'username' => 'required',
            'address' => 'required',
            'phone'=> 'required',
            'provinces' => 'required',
            'districts' => 'required',
            'cities' => 'required',
        ]);

        $client = Client::findOrFail(auth()->id());
        $client->name = $request->name;
        $client->email = $request->email;
        $client->username = $request->username;
        $client->address = $request->address;
        $client->phone = $request->phone;
        $client->provinces = $request->provinces;
        $client->districts = $request->districts;
        $client->cities = $request->cities;
        $saved = $client->save();

        if($saved){
            return redirect()->route('client.profile')->with('success','Profile updated successfully');
        }else{
            return redirect()->route('client.profile')->with('fail','Something went wrong.');
        }

    }

    //find servise page
    public function findServise(Request $request){

        $client = DB::table('clients as a')->selectRaw('a.name, b.name_en as city, CONCAT(b.latitude, ",", b.longitude) AS location')
                    ->leftJoin('cities as b','b.id','=','a.cities')
                    ->where('a.id',auth()->id())
                    ->first();

        $services = DB::table('services')->get();

        $data = [
            'pageTitle'=>'Find Servise',
            'client'=>$client,
            'services'=>$services,
        ];
        return view('back.pages.client.find-servise',$data);
    }

    //find sellers
    public function findSellers(Request $request){
        $request->validate([
            'service'=>'required',
        ]);

        $client = null;
        if( Auth::guard('client')->check() ){
            $client = client::findOrFail(auth()->id());
        }
        $services = DB::table('services')->get();
        $servicesid = $request->service;

        $nearestseller = DB::table('sellers as a')->selectRaw('a.id, a.name, a.phone, a.email, a.address, a.picture,  e.title  as service1, f.title  as service2, g.title  as service3,  b.name_en as city, CONCAT(b.latitude, ",", b.longitude) AS location')
                    ->leftJoin('cities as b','b.id','=','a.cities')
                    ->leftJoin('services as e','e.id','=','a.service1')
                    ->leftJoin('services as f','f.id','=','a.service2')
                    ->leftJoin('services as g','g.id','=','a.service3')
                    ->where('a.status',1)
                    // ->where('a.cities',$client->cities)
                    ->get();
        // dd($nearestseller);

        $data = [
            'pageTitle'=>'Find Selelrs',
            'client'=>$client,
            'nearestseller'=>$nearestseller,
            'serviseid'=>$servicesid,
        ];

        return view('back.pages.client.find-sellers',$data);

    }

    //servise request
    public function serviseRequest(Request $request){
        $client = client::findOrFail(auth()->id());

        //save data on service_requests table
        $serviseRequest = new ServiceRequest();
        $serviseRequest->client_id = $client->id;
        $serviseRequest->seller_id = $request->slid;
        $serviseRequest->servise_id = $request->serviseid;
        $serviseRequest->status = "1";
        $saved = $serviseRequest->save();

        if($saved){

            $seller = DB::table('sellers')->where('id',$request->slid)->first();
            $service = DB::table('services')->where('id',$request->serviseid)->first();

            //send email
            $data = array(
                'client' => $client,
                'seller' => $seller,
                'service' => $service,
            );

            $mail_body = view('email-templates.service-request-template',$data)->render();

            $mailConfig = array(
                'mail_from_email' => env('EMAIL_FROM_ADDRESS'),
                'mail_from_name' => env('EMAIL_FROM_NAME'),
                'mail_recipient_email' => $seller->email,
                'mail_recipient_name' => $seller->name,
                'mail_subject' => 'New Service Request from TechConnect ',
                'mail_body' => $mail_body
            );

            sendEmail($mailConfig);


            return response()->json(['status'=>1,'msg'=>'Your request has been successfully sent to the seller.']);
        }else{
            return response()->json(['status'=>0,'msg'=>'Something went wrong']);

        }


    }
    //babys
    public function baby(Request $request){
        $babys = DB::table('baby as a')->selectRaw('a.*, b.name as parentname')
        ->leftJoin('sellers as b', 'b.id', '=', 'a.parent_id')
        ->where('a.parent_id',auth()->id())
        ->get();

        $parents = DB::table('clients as a')->get();

        $midwife =  DB::table('sellers as a')->get();

        $midwifedetails = null;
        if( Auth::guard('seller')->check() ){
            $midwifedetails = Seller::findOrFail(auth()->id());
        }

        $data = [
            'pageTitle'=>'Babys Details',
            'babys'=>$babys,
            'parents'=>$parents,
            'midwife'=>$midwife,
            'midwifedetails'=>$midwifedetails,

        ];

        return view('back.pages.client.baby',$data);
    }

    //report page
    public function report(Request $request){
        $sellers = DB::table('sellers as a')->selectRaw('a.*, 1 as baby_count, c.division_name, d.name_en AS cities')
        // ->leftJoin('baby as b', 'b.parent_id', '=', 'a.id') // Join with baby table
        ->leftJoin('divisions as c','c.division_id', '=' ,'a.division_id')
        ->leftJoin('cities as d','d.id', '=' ,'a.cities')
        ->get();

        $babies = Baby::all(); // Fetch all babies

        $data = [
            'pageTitle'=>'Report',
            'sellers'=>$sellers,
            'babies'=>$babies
        ];
        return view('back.pages.client.report',$data);
    }

    //report page
    public function heightReport(Request $request){
        $sellers = DB::table('sellers as a')->selectRaw('a.*, 1 as baby_count, c.division_name, d.name_en AS cities')
        // ->leftJoin('baby as b', 'b.parent_id', '=', 'a.id') // Join with baby table
        ->leftJoin('divisions as c','c.division_id', '=' ,'a.division_id')
        ->leftJoin('cities as d','d.id', '=' ,'a.cities')
        ->get();

        $babies = Baby::all(); // Fetch all babies

        $data = [
            'pageTitle'=>' Height Report',
            'sellers'=>$sellers,
            'babies'=>$babies
        ];
        return view('back.pages.client.height-report',$data);
    }

    //get babys helth record
    public function getBabyHealthRecord(Request $request){
        $babyId = $request->input('selectid');

        // Fetch the health record for the specified baby
        $healthRecords = DB::table('weight_record')
            ->where('baby_id', $babyId)
            ->get();

        return response()->json($healthRecords);
    }
}
