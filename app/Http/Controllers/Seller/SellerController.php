<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Baby;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Seller;
use App\Models\VerificationToken;
use App\Models\WeightRecord;
use Illuminate\Support\Facades\DB;
use constGuards;
use constDefaults;
use Illuminate\Support\Facades\File;
// use Mberecall\Kropify\Kropify;
// use App\Models\Shop;

class SellerController extends Controller
{
    //login
    public function login(Request $request){
        $data = [
            'pageTitle'=>'Seller Login'
        ];
        return view('back.pages.seller.auth.login',$data);
    }

    //register
    public function register(Request $request){
        $data = [
            'pageTitle'=>'Create Seller Account'
        ];
        return view('back.pages.seller.auth.register',$data);
    }

    //seller home
    public function home(Request $request){
        $seller = null;
        if( Auth::guard('seller')->check() ){
            $seller = Seller::findOrFail(auth()->id());
        }

        $clientCount =  DB::table('clients')->count();
        $sellersCount =  DB::table('sellers')->count();
        $servicesCount =  DB::table('baby')->count();
        $servicseRequestCount =  DB::table('vaccine')->count();

        $data = [
            'pageTitle'=>'Midwife Home ',
            'seller'=>$seller,
            'clients' =>$clientCount,
            'sellers' =>$sellersCount,
            'services' => $servicesCount,
            'serviceRequests' => $servicseRequestCount
        ];


        return view('back.pages.seller.home',$data);
    }

    public function registerSuccess(Request $request){
        return view('back.pages.seller.register-success');
    }

    //create seller account
    public function createSeller(Request $request){
        //Validate Seller Registration Form
        $request->validate([
            'name'=>'required',
            'email'=>'required|email|unique:sellers',
            'password'=>'min:5|required_with:confirm_password|same:confirm_password',
            'confirm_password'=>'min:5'
        ]);

        $seller = new Seller();
        $seller->name = $request->name;
        $seller->email = $request->email;
        $seller->password = Hash::make($request->password);
        $seller->status = "0";
        $saved = $seller->save();

        if( $saved ){
        //    //Generate token
        //    $token = base64_encode(Str::random(64));

        //    VerificationToken::create([
        //       'user_type'=>'seller',
        //       'email'=>$request->email,
        //       'token'=>$token
        //    ]);

        //    $actionLink = route('seller.verify',['token'=>$token]);
        //    $data['action_link'] = $actionLink;
        //    $data['seller_name'] = $request->name;
        //    $data['seller_email'] = $request->email;

        //    //Send Activation link to this seller email
        //    $mail_body = view('email-templates.seller-verify-template',$data)->render();

        //    $mailConfig = array(
        //       'mail_from_email'=>env('EMAIL_FROM_ADDRESS'),
        //       'mail_from_name'=>env('EMAIL_FROM_NAME'),
        //       'mail_recipient_email'=>$request->email,
        //       'mail_recipient_name'=>$request->name,
        //       'mail_subject'=>'Verify Seller Account',
        //       'mail_body'=>$mail_body
        //    );

        //    if( sendEmail($mailConfig) ){
        //       return redirect()->route('seller.register-success');
        //    }else{
        //      return redirect()->route('seller.register')->with('fail','Something went wrong while sending verification link.');
        //    }
            // return redirect()->route('seller.register-success');
            return redirect()->route('seller.login')->with('success','Good!, Registation successful. Login with your credentials and complete setup your Technicians account.');
        }else{
            return redirect()->route('seller.register')->with('fail','Something went wrong.');
        }
    }

    //login function
    public function loginHandler(Request $request){
        $fieldType = filter_var($request->login_id, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if( $fieldType == 'email' ){
            $request->validate([
                'login_id'=>'required|email|exists:sellers,email',
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

        if( Auth::guard('seller')->attempt($creds) ){

            if( !auth('seller')->user()->verified ){
                return redirect()->route('seller.seller-details');
            }else{
                return redirect()->route('seller.home');
            }
        }else{
            return redirect()->route('seller.login')->withInput()->with('fail','Incorrect password.');
        }
    }

    //logout
    public function logoutHandler(Request $request){
        Auth::guard('seller')->logout();
        return redirect()->route('seller.login')->with('fail','You are logged out!');
    }

    //seller details
    public function sellerDetails(Request $request){
        $seller = null;
        if( Auth::guard('seller')->check() ){
            $seller = Auth::guard('seller')->user();
        }
        $provinces = DB::table('provinces')->get();
        $districts = DB::table('districts')->get();
        $cities = DB::table('cities')->get();
        $services = DB::table('services')->get();
        $divisions = DB::table('divisions')->get();

        $data = [
            'pageTitle'=>'Seller Details',
            'seller'=>$seller,
            'provinces'=>$provinces,
            'districts'=>$districts,
            'cities'=>$cities,
            'services'=>$services,
            'divisions'=>$divisions,
        ];
        return view('back.pages.seller.seller-details',$data);
    }

    //change profile picture
    public function changeProfilePicture(Request $request){


        $seller = Seller::findOrFail(auth()->id());
        $path = 'images/users/sellers/';

        if($request->hasFile('sellerProfilePictureFile')){
            $file = $request->file('sellerProfilePictureFile');
            $old_picture = $seller->getAttributes()['picture'];
            $file_path = $path.$old_picture;
            $filename = 'SELLER_IMG_'.rand(2,1000).$seller->id.time().uniqid().'.jpg';

            $upload = $file->move(public_path($path),$filename);
            if($upload){
                if( $old_picture != null && File::exists(public_path($path.$old_picture)) ){
                    File::delete(public_path($path.$old_picture));
                }
                $seller->update(['picture'=>$filename]);
                return response()->json(['status'=>1,'msg'=>'Your profile picture has been successfully updated.']);
            }else{
                return response()->json(['status'=>0,'msg'=>'Something went wrong.']);
            }
        }

        // return redirect()->route('seller.seller-details')->with('success','Profile picture updated successfully');
    }

    //save seller details
    public function saveSellerDetails(Request $request){
        $request->validate([
            'name'=>'required',
            'phone'=>'required',
            'address'=>'required',
            'provinces'=>'required',
            'districts'=>'required',
            'cities'=>'required',
            'division'=>'required',
        ]);

        $seller = Seller::findOrFail(auth()->id());
        $seller->name = $request->name;
        $seller->phone = $request->phone;
        $seller->address = $request->address;
        $seller->provinces = $request->provinces;
        $seller->districts = $request->districts;
        $seller->cities = $request->cities;
        $seller->service1 = $request->service1;
        $seller->service2 = $request->service2;
        $seller->service3 = $request->service3;
        $seller->verified = "1";
        $seller->division_id = $request->division;

        $saved = $seller->save();
        if( $saved ){
            return redirect()->route('seller.home')->with('success','Seller details updated successfully.');
        }else{
            return redirect()->route('seller.seller-details')->with('fail','Something went wrong.');
        }

    }

    //profile view
    public function profileView(Request $request){
        $seller = null;
        if( Auth::guard('seller')->check() ){
            $seller = Seller::findOrFail(auth()->id());
        }
        $provinces = DB::table('provinces')->get();
        $districts = DB::table('districts')->get();
        $cities = DB::table('cities')->get();
        $services = DB::table('services')->get();
        $data = [
            'pageTitle'=>'Seller Profile',
            'seller'=>$seller,
            'provinces'=>$provinces,
            'districts'=>$districts,
            'cities'=>$cities,
            'services' =>$services,
        ];

        return view('back.pages.seller.profile', $data);
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
            'service1' => 'required',
            'service2' => 'required',
            'service3' => 'required',
        ]);

        $seller = Seller::findOrFail(auth()->id());
        $seller->name = $request->name;
        $seller->email = $request->email;
        $seller->username = $request->username;
        $seller->address = $request->address;
        $seller->phone = $request->phone;
        $seller->provinces = $request->provinces;
        $seller->districts = $request->districts;
        $seller->cities = $request->cities;
        $seller->service1 = $request->service1;
        $seller->service2 = $request->service2;
        $seller->service3 = $request->service3;
        $saved = $seller->save();

        if($saved){
            return redirect()->route('seller.profile')->with('success','Profile updated successfully');
        }else{
            return redirect()->route('seller.profile')->with('fail','Something went wrong.');
        }

        // $this->showToastr('success','Your personal details have been successfully updated.');

        // return redirect()->route('admin.profile')->with('success','Profile updated successfully');
    }

    //job
    public function job(Request $request){
        $seller = null;
        if( Auth::guard('seller')->check() ){
            $seller = Seller::findOrFail(auth()->id());
        }
        $data = [
            'pageTitle'=>'Post Job',
            'seller'=>$seller,
        ];
        return view('back.pages.seller.job',$data);
    }

    //job verify
    public function sellerDocumentUpload(Request $request){
        $request->validate([
            'comment'=>'required',
            'Certificate' => 'image|mimes:jpg,jpeg,png'
        ]);

        $seller = Seller::findOrFail(auth()->id());
        $seller->About_Field  = $request->comment;
        $seller->status  = "2";

        if($request->hasFile('Certificate')){
            $file = $request->file('Certificate');
            $extension = $file->getClientoriginalExtension();
            $path = 'images/users/sellers/certificate';
            $filename = 'SELLER_CERTIFICATE_'.rand(2,1000).$seller->id.time().uniqid().'.'.$extension;

            $upload = $file->move($path,$filename);
            if($upload){
                $seller->Certificate = $filename;
                $seller->save();

                return redirect()->route('seller.job')->with('success','Your Certificate has been successfully uploaded.');
            }else{
                return redirect()->route('seller.profile')->with('fail','Something went wrong.');
            }
        }

    }

    //parent
    public function parent(Request $request){
        $clients = DB::table('clients as a')->selectRaw('a.*, 1 as baby_count, c.division_name, d.name_en AS cities')
        // ->leftJoin('baby as b', 'b.parent_id', '=', 'a.id') // Join with baby table
        ->leftJoin('divisions as c','c.division_id', '=' ,'a.division_id')
        ->leftJoin('cities as d','d.id', '=' ,'a.cities')
        ->get();

        $data = [
            'pageTitle'=>'Parent',
            'clients'=>$clients,
        ];
        return view('back.pages.seller.parent',$data);
    }

    //babys
    public function baby(Request $request){
        $babys = DB::table('baby as a')->selectRaw('a.*, b.name as parentname')
        ->leftJoin('sellers as b', 'b.id', '=', 'a.parent_id')
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

        return view('back.pages.seller.baby',$data);
    }

    //baby store
    public function babyStore(Request $request){
        //dd($request);
        // Validate the required fields
        $request->validate([
            'full_name' => 'required',
            'date_of_birth' => 'required|date',
            'gender' => 'required',
            'blood_group' => 'required',
            'birth_hospital' => 'required',
            'birth_weight' => 'required|numeric',
            'parentname' => 'required|exists:clients,id',
            'midwife' => 'required|exists:sellers,id',
        ]);

        // Save the baby details
        $baby = new Baby();
        $baby->full_name = $request->full_name;
        $baby->date_of_birth = $request->date_of_birth;
        $baby->gender = $request->gender;
        $baby->blood_group = $request->blood_group;
        $baby->birth_hospital = $request->birth_hospital;
        $baby->birth_weight = $request->birth_weight;
        $baby->parent_id = $request->parentname;
        $baby->midwife_id = $request->midwife;

        if ($baby->save()) {
            return redirect()->route('seller.baby')->with('success', 'Baby profile saved successfully.');
        } else {
            return redirect()->route('seller.baby')->with('fail', 'Something went wrong while saving the baby profile.');
        }

    }

    //weight record store
    public function weightRecordStore(Request $request){
        // Validate the required fields
        $request->validate([
            'baby_id' => 'required|exists:baby,baby_id',
            'weight' => 'required|numeric',
            'height' => 'required|numeric',
            'midwife_id' => 'required',
            'notes' => 'nullable|string|max:255',
        ]);

        $today = Carbon::now()->format('Y-m-d');

        // Save the weight record
        $weightRecord = new WeightRecord();
        $weightRecord->baby_id = $request->baby_id;
        $weightRecord->weight = $request->weight;
        $weightRecord->height = $request->height;
        $weightRecord->midwife_id = $request->midwife_id;
        $weightRecord->record_date =  $today;
        $weightRecord->notes = $request->notes;

        if ($weightRecord->save()) {
            // Calculate BMI
            $heightInMeters = $request->height / 100; // Convert height from cm to meters
            $bmi = $request->weight / ($heightInMeters * $heightInMeters);

            // Update the baby's BMI in the baby table
            $baby = Baby::find($request->baby_id);
            if ($baby) {
                $baby->bmi = round($bmi, 2); // Round BMI to 2 decimal places
                $baby->save();
            }

            return redirect()->route('seller.baby')->with('success', 'Weight record saved successfully.');
        } else {
            return redirect()->route('seller.baby')->with('fail', 'Something went wrong while saving the weight record.');
        }
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
        return view('back.pages.seller.report',$data);
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
        return view('back.pages.seller.height-report',$data);
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

    //notice
    public function notice(Request $request){
        $notices = DB::table('notice as a')->selectRaw('a.*')
        ->get();

        $data = [
            'pageTitle'=>'Notice',
            'notices'=>$notices,
            'user' => Auth::guard('seller')->user(),
        ];
        return view('back.pages.seller.notice',$data);
    }

}
