<?php

namespace App\Http\Controllers\ParentUser;

use App\Http\Controllers\Controller;
use App\Models\Baby;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\ParentUser;
use App\Models\Midwife;
use App\Models\ServiceRequest;
use App\Models\VerificationToken;
use Illuminate\Support\Facades\DB;
use constGuards;
use constDefaults;
use Illuminate\Support\Facades\File;

class ParentController extends Controller
{
    //login
    public function login(Request $request){
        $data = [
            'pageTitle'=>'Parent Login'
        ];
        return view('back.pages.parent.auth.login',$data);
    }

    //register
    public function register(Request $request){
        $data = [
            'pageTitle'=>'Create Parent Account'
        ];
        return view('back.pages.parent.auth.register',$data);
    }

    //parent home
    public function home(Request $request){
        $parent = null;
        if( Auth::guard('parent')->check() ){
            $parent = Auth::guard('parent')->user();
        }

        $babys = DB::table('baby as a')->selectRaw('a.*, b.name as midwifename, c.name as parentname')
            ->leftJoin('midwives as b', 'b.id', '=', 'a.midwife_id')
            ->leftJoin('parents as c', 'c.id', '=', 'a.parent_id')
            ->where('a.parent_id', auth()->id())
            ->get();

        $midwife = DB::table('midwives as a')->get();
        $parents = DB::table('parents as a')->get();

        $data = [
            'pageTitle'=>'Parent Dashboard',
            'client'=>$parent,
            'babys'=>$babys,
            'midwife'=>$midwife,
            'parents'=>$parents,
        ];
        return view('back.pages.parent.baby',$data);
    }

    //create parent account
    public function createParent(Request $request){
        $request->validate([
            'name'=>'required',
            'email'=>'required|email|unique:parents',
            'password'=>'min:5|required_with:confirm_password|same:confirm_password',
            'confirm_password'=>'min:5'
        ]);

        $parent = new ParentUser();
        $parent->name = $request->name;
        $parent->email = $request->email;
        $parent->password = Hash::make($request->password);
        $saved = $parent->save();

        if( $saved ){
            return redirect()->route('parent.login')->with('success','Registration successful. Login with your credentials and complete setup your account.');
        }else{
            return redirect()->route('parent.register')->with('fail','Something went wrong.');
        }
    }

    //login function
    public function loginHandler(Request $request){
        $fieldType = filter_var($request->login_id, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if( $fieldType == 'email' ){
            $request->validate([
                'login_id'=>'required|email|exists:parents,email',
                'password'=>'required|min:5|max:45'
            ],[
                'login_id.required'=>'Email or Username is required.',
                'login_id.email'=>'Invalid email address.',
                'login_id.exists'=>'Email does not exist in system.',
                'password.required'=>'Password is required'
            ]);
        }else{
            $request->validate([
                'login_id'=>'required|exists:parents,username',
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

        if( Auth::guard('parent')->attempt($creds) ){
            if( !auth('parent')->user()->verified ){
                return redirect()->route('parent.parent-details');
            }else{
                return redirect()->route('parent.home');
            }
        }else{
            return redirect()->route('parent.login')->withInput()->with('fail','Incorrect password.');
        }
    }

    //logout
    public function logoutHandler(Request $request){
        Auth::guard('parent')->logout();
        return redirect()->route('parent.login')->with('fail','You are logged out!');
    }

    //parent details
    public function parentDetails(Request $request){
        $parent = null;
        if( Auth::guard('parent')->check() ){
            $parent = Auth::guard('parent')->user();
        }
        $provinces = DB::table('provinces')->get();
        $districts = DB::table('districts')->get();
        $cities = DB::table('cities')->get();

        $data = [
            'pageTitle'=>'Parent Details',
            'client'=>$parent,
            'provinces'=>$provinces,
            'districts'=>$districts,
            'cities'=>$cities
        ];
        return view('back.pages.parent.client-details',$data);
    }

    //save parent details
    public function saveParentDetails(Request $request){
        $request->validate([
            'name'=>'required',
            'phone'=>'required',
            'address'=>'required',
            'provinces'=>'required',
            'districts'=>'required',
            'cities'=>'required',
        ]);

        $parent = ParentUser::find(auth()->id());
        $parent->name = $request->name;
        $parent->phone = $request->phone;
        $parent->address = $request->address;
        $parent->provinces = $request->provinces;
        $parent->districts = $request->districts;
        $parent->cities = $request->cities;
        $parent->verified = "1";

        $saved = $parent->save();
        if( $saved ){
            return redirect()->route('parent.home')->with('success','Parent details updated successfully.');
        }else{
            return redirect()->route('parent.parent-details')->with('fail','Something went wrong.');
        }
    }

    //change profile picture
    public function changeProfilePicture(Request $request){
        try {
            $parent = ParentUser::findOrFail(auth()->id());
            $path = 'images/users/clients/';

            if($request->hasFile('clientProfilePictureFile')){
                $file = $request->file('clientProfilePictureFile');
                $old_picture = $parent->getAttributes()['picture'];
                $file_path = $path.$old_picture;
                $filename = 'PARENT_IMG_'.rand(2,1000).$parent->id.time().uniqid().'.jpg';

                $upload = $file->move(public_path($path),$filename);
                if($upload){
                    if( $old_picture != null && File::exists(public_path($path.$old_picture)) ){
                        File::delete(public_path($path.$old_picture));
                    }
                    $parent->update(['picture'=>$filename]);
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

    //profile view
    public function profileView(Request $request){
        $parent = null;
        if( Auth::guard('parent')->check() ){
            $parent = ParentUser::findOrFail(auth()->id());
        }
        $provinces = DB::table('provinces')->get();
        $districts = DB::table('districts')->get();
        $cities = DB::table('cities')->get();

        $data = [
            'pageTitle'=>'Parent Profile',
            'client'=>$parent,
            'provinces'=>$provinces,
            'districts'=>$districts,
            'cities'=>$cities
        ];

        return view('back.pages.parent.profile', $data);
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

        $parent = ParentUser::findOrFail(auth()->id());
        $parent->name = $request->name;
        $parent->email = $request->email;
        $parent->username = $request->username;
        $parent->address = $request->address;
        $parent->phone = $request->phone;
        $parent->provinces = $request->provinces;
        $parent->districts = $request->districts;
        $parent->cities = $request->cities;
        $saved = $parent->save();

        if($saved){
            return redirect()->route('parent.profile')->with('success','Profile updated successfully');
        }else{
            return redirect()->route('parent.profile')->with('fail','Something went wrong.');
        }
    }

    //baby list
    public function baby(Request $request){
        $babys = DB::table('baby as a')->selectRaw('a.*, b.name as midwifename, c.name as parentname')
            ->leftJoin('midwives as b', 'b.id', '=', 'a.midwife_id')
            ->leftJoin('parents as c', 'c.id', '=', 'a.parent_id')
            ->where('a.parent_id', auth()->id())
            ->get();

        $parents = DB::table('parents as a')->get();
        $midwife = DB::table('midwives as a')->get();

        $data = [
            'pageTitle'=>'My Babies',
            'babys'=>$babys,
            'parents'=>$parents,
            'midwife'=>$midwife,
            'midwifedetails'=>null,
        ];

        return view('back.pages.parent.baby',$data);
    }

    //report page
    public function report(Request $request){
        $midwives = DB::table('midwives as a')->selectRaw('a.*, 1 as baby_count, c.division_name, d.name_en AS cities')
            ->leftJoin('divisions as c','c.division_id', '=' ,'a.division_id')
            ->leftJoin('cities as d','d.id', '=' ,'a.cities')
            ->get();

        $babies = Baby::where('parent_id', auth()->id())->get();

        $data = [
            'pageTitle'=>'Report',
            'sellers'=>$midwives,
            'babies'=>$babies
        ];
        return view('back.pages.parent.report',$data);
    }

    //height report page
    public function heightReport(Request $request){
        $midwives = DB::table('midwives as a')->selectRaw('a.*, 1 as baby_count, c.division_name, d.name_en AS cities')
            ->leftJoin('divisions as c','c.division_id', '=' ,'a.division_id')
            ->leftJoin('cities as d','d.id', '=' ,'a.cities')
            ->get();

        $babies = Baby::where('parent_id', auth()->id())->get();

        $data = [
            'pageTitle'=>'Height Report',
            'sellers'=>$midwives,
            'babies'=>$babies
        ];
        return view('back.pages.parent.height-report',$data);
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

    //change password
    public function changePassword(Request $request){
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:5|max:45|required_with:new_password_confirmation|same:new_password_confirmation',
            'new_password_confirmation' => 'required'
        ]);

        $parent = ParentUser::findOrFail(auth()->id());

        if(Hash::check($request->current_password, $parent->password)){
            $parent->update(['password'=>Hash::make($request->new_password)]);
            return redirect()->route('parent.profile')->with('success','Password updated successfully');
        }else{
            return redirect()->route('parent.profile')->with('fail','Old password is incorrect');
        }
    }
}
