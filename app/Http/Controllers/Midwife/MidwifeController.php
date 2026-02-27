<?php

namespace App\Http\Controllers\Midwife;

use App\Http\Controllers\Controller;
use App\Models\Baby;
use App\Models\BabyVaccination;
use App\Models\VaccinationSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Midwife;
use App\Models\VerificationToken;
use App\Models\WeightRecord;
use Illuminate\Support\Facades\DB;
use constGuards;
use constDefaults;
use Illuminate\Support\Facades\File;

class MidwifeController extends Controller
{
    //login
    public function login(Request $request){
        $data = [
            'pageTitle'=>'Midwife Login'
        ];
        return view('back.pages.midwife.auth.login',$data);
    }

    //register
    public function register(Request $request){
        $data = [
            'pageTitle'=>'Create Midwife Account'
        ];
        return view('back.pages.midwife.auth.register',$data);
    }

    //midwife home
    public function home(Request $request){
        $midwife = null;
        if( Auth::guard('midwife')->check() ){
            $midwife = Midwife::findOrFail(auth()->id());
        }

        $parentCount  = DB::table('parents')->count();
        $midwifeCount = DB::table('midwives')->count();
        $babyCount    = DB::table('baby')->count();
        $vaccineCount = DB::table('vaccination_schedules')->count();

        $data = [
            'pageTitle'  => 'Midwife Home',
            'seller'     => $midwife,
            'clients'    => $parentCount,
            'sellers'    => $midwifeCount,
            'services'   => $babyCount,
            'serviceRequests' => $vaccineCount,
        ];

        return view('back.pages.midwife.home',$data);
    }

    public function registerSuccess(Request $request){
        return view('back.pages.midwife.register-success');
    }

    //create midwife account
    public function createMidwife(Request $request){
        $request->validate([
            'name'=>'required',
            'email'=>'required|email|unique:midwives',
            'password'=>'min:5|required_with:confirm_password|same:confirm_password',
            'confirm_password'=>'min:5'
        ]);

        $midwife = new Midwife();
        $midwife->name = $request->name;
        $midwife->email = $request->email;
        $midwife->password = Hash::make($request->password);
        $midwife->status = "0";
        $midwife->is_approved = false;
        $saved = $midwife->save();

        if( $saved ){
            return redirect()->route('midwife.login')->with('success','Registration successful. Your account needs MOH approval before you can access the system. Login with your credentials and complete setup.');
        }else{
            return redirect()->route('midwife.register')->with('fail','Something went wrong.');
        }
    }

    //login function
    public function loginHandler(Request $request){
        $fieldType = filter_var($request->login_id, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if( $fieldType == 'email' ){
            $request->validate([
                'login_id'=>'required|email|exists:midwives,email',
                'password'=>'required|min:5|max:45'
            ],[
                'login_id.required'=>'Email or Username is required.',
                'login_id.email'=>'Invalid email address.',
                'login_id.exists'=>'Email does not exist in system.',
                'password.required'=>'Password is required'
            ]);
        }else{
            $request->validate([
                'login_id'=>'required|exists:midwives,username',
                'password'=>'required|min:5|max:45'
            ],[
                'login_id.required'=>'Email or Username is required.',
                'login_id.exists'=>'Username does not exist in system.',
                'password.required'=>'Password is required'
            ]);
        }

        $creds = array(
            $fieldType => $request->login_id,
            'password' => $request->password
        );

        if( Auth::guard('midwife')->attempt($creds) ){
            if( !auth('midwife')->user()->verified ){
                return redirect()->route('midwife.midwife-details');
            }else{
                return redirect()->route('midwife.home');
            }
        }else{
            return redirect()->route('midwife.login')->withInput()->with('fail','Incorrect password.');
        }
    }

    //logout
    public function logoutHandler(Request $request){
        Auth::guard('midwife')->logout();
        return redirect()->route('midwife.login')->with('fail','You are logged out!');
    }

    //midwife details
    public function midwifeDetails(Request $request){
        $midwife = null;
        if( Auth::guard('midwife')->check() ){
            $midwife = Auth::guard('midwife')->user();
        }
        $provinces = DB::table('provinces')->get();
        $districts = DB::table('districts')->get();
        $cities = DB::table('cities')->get();
        $services = DB::table('services')->get();
        $divisions = DB::table('divisions')->get();

        $data = [
            'pageTitle'=>'Midwife Details',
            'seller'=>$midwife,
            'provinces'=>$provinces,
            'districts'=>$districts,
            'cities'=>$cities,
            'services'=>$services,
            'divisions'=>$divisions,
        ];
        return view('back.pages.midwife.seller-details',$data);
    }

    //change profile picture
    public function changeProfilePicture(Request $request){
        try {
            $midwife = Midwife::findOrFail(auth()->id());
            $path = 'images/users/sellers/';

            if($request->hasFile('sellerProfilePictureFile')){
                $file = $request->file('sellerProfilePictureFile');
                $old_picture = $midwife->getAttributes()['picture'];
                $file_path = $path.$old_picture;
                $filename = 'MIDWIFE_IMG_'.rand(2,1000).$midwife->id.time().uniqid().'.jpg';

                $upload = $file->move(public_path($path),$filename);
                if($upload){
                    if( $old_picture != null && File::exists(public_path($path.$old_picture)) ){
                        File::delete(public_path($path.$old_picture));
                    }
                    $midwife->update(['picture'=>$filename]);
                    return response()->json(['status'=>1,'msg'=>'Your profile picture has been successfully updated.']);
                }else{
                    return response()->json(['status'=>0,'msg'=>'Something went wrong.']);
                }
            }

            return response()->json(['status'=>0,'msg'=>'No file uploaded.']);
        } catch (\Exception $e) {
            return response()->json(['status'=>0,'msg'=>'Error: '.$e->getMessage()]);
        }
    }

    //save midwife details
    public function saveMidwifeDetails(Request $request){
        $request->validate([
            'name'=>'required',
            'phone'=>'required',
            'address'=>'required',
            'provinces'=>'required',
            'districts'=>'required',
            'cities'=>'required',
            'division'=>'required',
        ]);

        $midwife = Midwife::findOrFail(auth()->id());
        $midwife->name = $request->name;
        $midwife->phone = $request->phone;
        $midwife->address = $request->address;
        $midwife->provinces = $request->provinces;
        $midwife->districts = $request->districts;
        $midwife->cities = $request->cities;
        $midwife->service1 = $request->service1;
        $midwife->service2 = $request->service2;
        $midwife->service3 = $request->service3;
        $midwife->verified = "1";
        $midwife->division_id = $request->division;

        $saved = $midwife->save();
        if( $saved ){
            return redirect()->route('midwife.home')->with('success','Midwife details updated successfully.');
        }else{
            return redirect()->route('midwife.midwife-details')->with('fail','Something went wrong.');
        }
    }

    //profile view
    public function profileView(Request $request){
        $midwife = null;
        if( Auth::guard('midwife')->check() ){
            $midwife = Midwife::findOrFail(auth()->id());
        }
        $provinces = DB::table('provinces')->get();
        $districts = DB::table('districts')->get();
        $cities = DB::table('cities')->get();
        $services = DB::table('services')->get();
        $data = [
            'pageTitle'=>'Midwife Profile',
            'seller'=>$midwife,
            'provinces'=>$provinces,
            'districts'=>$districts,
            'cities'=>$cities,
            'services' =>$services,
        ];

        return view('back.pages.midwife.profile', $data);
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

        $midwife = Midwife::findOrFail(auth()->id());
        $midwife->name = $request->name;
        $midwife->email = $request->email;
        $midwife->username = $request->username;
        $midwife->address = $request->address;
        $midwife->phone = $request->phone;
        $midwife->provinces = $request->provinces;
        $midwife->districts = $request->districts;
        $midwife->cities = $request->cities;
        $midwife->service1 = $request->service1;
        $midwife->service2 = $request->service2;
        $midwife->service3 = $request->service3;
        $saved = $midwife->save();

        if($saved){
            return redirect()->route('midwife.profile')->with('success','Profile updated successfully');
        }else{
            return redirect()->route('midwife.profile')->with('fail','Something went wrong.');
        }
    }

    //job / certificate upload
    public function job(Request $request){
        $midwife = null;
        if( Auth::guard('midwife')->check() ){
            $midwife = Midwife::findOrFail(auth()->id());
        }
        $data = [
            'pageTitle'=>'Upload Certificate',
            'seller'=>$midwife,
        ];
        return view('back.pages.midwife.job',$data);
    }

    //document upload for verification
    public function midwifeDocumentUpload(Request $request){
        $request->validate([
            'comment'=>'required',
            'Certificate' => 'image|mimes:jpg,jpeg,png'
        ]);

        $midwife = Midwife::findOrFail(auth()->id());
        $midwife->About_Field = $request->comment;
        $midwife->status = "2";

        if($request->hasFile('Certificate')){
            $file = $request->file('Certificate');
            $extension = $file->getClientoriginalExtension();
            $path = 'images/users/sellers/certificate';
            $filename = 'MIDWIFE_CERTIFICATE_'.rand(2,1000).$midwife->id.time().uniqid().'.'.$extension;

            $upload = $file->move($path,$filename);
            if($upload){
                $midwife->Certificate = $filename;
                $midwife->save();

                return redirect()->route('midwife.job')->with('success','Your Certificate has been successfully uploaded.');
            }else{
                return redirect()->route('midwife.profile')->with('fail','Something went wrong.');
            }
        }
    }

    //view parents
    public function parentList(Request $request){
        $parents = DB::table('parents as a')->selectRaw('a.*, 1 as baby_count, c.division_name, d.name_en AS cities')
            ->leftJoin('divisions as c','c.division_id', '=' ,'a.division_id')
            ->leftJoin('cities as d','d.id', '=' ,'a.cities')
            ->get();

        $data = [
            'pageTitle'=>'Parent List',
            'clients'=>$parents,
        ];
        return view('back.pages.midwife.parent',$data);
    }

    //view babies
    public function baby(Request $request){
        $babys = DB::table('baby as a')->selectRaw('a.*, b.name as midwifename, c.name as parentname')
            ->leftJoin('midwives as b', 'b.id', '=', 'a.midwife_id')
            ->leftJoin('parents as c', 'c.id', '=', 'a.parent_id')
            ->get();

        $parents = DB::table('parents as a')->get();
        $midwives = DB::table('midwives as a')->get();

        $midwifedetails = null;
        if( Auth::guard('midwife')->check() ){
            $midwifedetails = Midwife::findOrFail(auth()->id());
        }

        $data = [
            'pageTitle'=>'Baby Profiles',
            'babys'=>$babys,
            'parents'=>$parents,
            'midwife'=>$midwives,
            'midwifedetails'=>$midwifedetails,
        ];

        return view('back.pages.midwife.baby',$data);
    }

    //create baby and assign to parent
    public function babyStore(Request $request){
        try {
            $request->validate([
                'full_name' => 'required',
                'date_of_birth' => 'required|date',
                'gender' => 'required',
                'blood_group' => 'required',
                'birth_hospital' => 'required',
                'birth_weight' => 'required|numeric',
                'parentname' => 'required|exists:parents,id',
                'midwife' => 'required|exists:midwives,id',
            ]);

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
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['status'=>1,'msg'=>'Baby profile created and assigned to parent successfully.','data'=>$baby]);
                }
                return redirect()->route('midwife.baby')->with('success', 'Baby profile saved and assigned to parent successfully.');
            } else {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['status'=>0,'msg'=>'Something went wrong while saving the baby profile.']);
                }
                return redirect()->route('midwife.baby')->with('fail', 'Something went wrong while saving the baby profile.');
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['status'=>0,'msg'=>'Validation failed.','errors'=>$e->errors()], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['status'=>0,'msg'=>'Error: '.$e->getMessage()]);
            }
            return redirect()->route('midwife.baby')->with('fail', 'Error: '.$e->getMessage());
        }
    }

    //weight record store
    public function weightRecordStore(Request $request){
        try {
            $request->validate([
                'baby_id' => 'required|exists:baby,baby_id',
                'weight' => 'required|numeric',
                'height' => 'required|numeric',
                'midwife_id' => 'required',
                'notes' => 'nullable|string|max:255',
            ]);

            $today = Carbon::now()->format('Y-m-d');

            $weightRecord = new WeightRecord();
            $weightRecord->baby_id = $request->baby_id;
            $weightRecord->weight = $request->weight;
            $weightRecord->height = $request->height;
            $weightRecord->midwife_id = $request->midwife_id;
            $weightRecord->record_date = $today;
            $weightRecord->notes = $request->notes;

            if ($weightRecord->save()) {
                // Calculate BMI
                $heightInMeters = $request->height / 100;
                $bmi = $request->weight / ($heightInMeters * $heightInMeters);

                $baby = Baby::find($request->baby_id);
                if ($baby) {
                    $baby->bmi = round($bmi, 2);
                    $baby->save();
                }

                return redirect()->route('midwife.baby')->with('success', 'Weight record saved successfully.');
            } else {
                return redirect()->route('midwife.baby')->with('fail', 'Something went wrong while saving the weight record.');
            }
        } catch (\Exception $e) {
            return redirect()->route('midwife.baby')->with('fail', 'Error: '.$e->getMessage());
        }
    }

    //report page
    public function report(Request $request){
        $midwives = DB::table('midwives as a')->selectRaw('a.*, 1 as baby_count, c.division_name, d.name_en AS cities')
            ->leftJoin('divisions as c','c.division_id', '=' ,'a.division_id')
            ->leftJoin('cities as d','d.id', '=' ,'a.cities')
            ->get();

        $babies = Baby::all();

        $data = [
            'pageTitle'=>'Report',
            'sellers'=>$midwives,
            'babies'=>$babies
        ];
        return view('back.pages.midwife.report',$data);
    }

    //height report page
    public function heightReport(Request $request){
        $midwives = DB::table('midwives as a')->selectRaw('a.*, 1 as baby_count, c.division_name, d.name_en AS cities')
            ->leftJoin('divisions as c','c.division_id', '=' ,'a.division_id')
            ->leftJoin('cities as d','d.id', '=' ,'a.cities')
            ->get();

        $babies = Baby::all();

        $data = [
            'pageTitle'=>'Height Report',
            'sellers'=>$midwives,
            'babies'=>$babies
        ];
        return view('back.pages.midwife.height-report',$data);
    }

    //get baby health record (AJAX)
    public function getBabyHealthRecord(Request $request){
        try {
            $babyId = $request->input('selectid');

            $healthRecords = DB::table('weight_record')
                ->where('baby_id', $babyId)
                ->get();

            return response()->json(['status'=>1,'data'=>$healthRecords]);
        } catch (\Exception $e) {
            return response()->json(['status'=>0,'msg'=>'Error: '.$e->getMessage()]);
        }
    }

    //notice
    public function notice(Request $request){
        $notices = DB::table('notice as a')->selectRaw('a.*')->get();

        $data = [
            'pageTitle'=>'Notice',
            'notices'=>$notices,
            'user' => Auth::guard('midwife')->user(),
        ];
        return view('back.pages.midwife.notice',$data);
    }

    //change password
    public function changePassword(Request $request){
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:5|max:45|required_with:new_password_confirmation|same:new_password_confirmation',
            'new_password_confirmation' => 'required'
        ]);

        $midwife = Midwife::findOrFail(auth()->id());

        if(Hash::check($request->current_password, $midwife->password)){
            $midwife->update(['password'=>Hash::make($request->new_password)]);
            return redirect()->route('midwife.profile')->with('success','Password updated successfully');
        }else{
            return redirect()->route('midwife.profile')->with('fail','Old password is incorrect');
        }
    }
}
