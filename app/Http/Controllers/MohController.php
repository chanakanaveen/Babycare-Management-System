<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Moh;
use App\Models\ParentUser;
use App\Models\Midwife;
use App\Models\Notice;
use App\Models\VaccinationSchedule;
use constGuards;
use constDefaults;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;

class MohController extends Controller
{
    //login
    public function loginHandler(Request $request){

        $fieldType = filter_var($request->login_id, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if($fieldType == 'email'){
            $request->validate([
                'login_id' => 'required|email|exists:mohs,email',
                'password' => 'required|min:6'
            ],[
                'login_id.required' => 'Email or Username is required',
                'login_id.email' => 'Invalid email address',
                'login_id.exists' => 'Email not found',
                'password.required' => 'Password is required',
            ]);
        }else{
            $request->validate([
                'login_id' => 'required|exists:mohs,username',
                'password' => 'required|min:6'
            ],[
                'login_id.required' => 'Email or Username is required',
                'login_id.exists' => 'Username not found',
                'password.required' => 'Password is required',
            ]);
        }

        $credentials = [
            $fieldType => $request->login_id,
            'password' => $request->password
        ];

        if (Auth::guard('moh')->attempt($credentials)) {
            return redirect()->route('moh.home');
        }else{
            session()->flash('fail','Invalid credentials');
            return redirect()->route('moh.login');
        }

    }

    //home page
    public function home(Request $request){
        $moh = null;
        if( Auth::guard('moh')->check() ){
            $moh = Moh::findOrFail(auth()->id());
        }

        $parentCount  = DB::table('parents')->count();
        $midwifeCount = DB::table('midwives')->count();
        $babyCount    = DB::table('baby')->count();
        $vaccineCount = DB::table('vaccination_schedules')->count();

        $data = [
            'pageTitle'  => 'MOH Dashboard',
            'moh'        => $moh,
            'parents'    => $parentCount,
            'midwives'   => $midwifeCount,
            'babies'     => $babyCount,
            'vaccines'   => $vaccineCount,
        ];

        return view('back.pages.moh.home', $data);
    }

    //logout
    public function logoutHandler(Request $request){
        Auth::guard('moh')->logout();
        session()->flash('fail','You are logged out');
        return redirect()->route('moh.login');
    }

    //send password reset link
    public function sendPasswordResetLink(Request $request){
        $request->validate([
            'email' => 'required|email|exists:mohs,email'
        ],[
            'email.required' => 'Email is required',
            'email.email' => 'Invalid email address',
            'email.exists' => 'Email not found',
        ]);

        //get moh details
        $moh = Moh::where('email',$request->email)->first();

        //generate token
        $token = base64_encode(Str::random(64));

        //check if there is an existing reset password token
        $oldToken = DB::table('password_resets')
                    ->where('email', $request->email)
                    ->where('guard', constGuards::MOH)
                    ->first();

        if($oldToken){
            //update the token
            DB::table('password_resets')
                ->where('email', $request->email)
                ->where('guard', constGuards::MOH)
                ->update([
                    'token' => $token,
                    'created_at' => Carbon::now()
                ]);
        }else{
            //insert the token
            DB::table('password_resets')
            ->insert([
                'email' => $request->email,
                'token' => $token,
                'created_at' => Carbon::now(),
                'guard' => constGuards::MOH
            ]);
        }

        $actionLink = route('moh.reset-password',['token'=>$token,'email'=>$request->email]);

        $data = array(
            'actionLink' => $actionLink,
            'admin' => $moh
        );

        $mail_body = view('email-templates.moh-forgot-email-template',$data)->render();

        $mailConfig = array(
            'mail_from_email' => env('EMAIL_FROM_ADDRESS'),
            'mail_from_name' => env('EMAIL_FROM_NAME'),
            'mail_recipient_email' => $moh->email,
            'mail_recipient_name' => $moh->name,
            'mail_subject' => 'Reset Password',
            'mail_body' => $mail_body
        );

        if(sendEmail($mailConfig)){
            session()->flash('success','Password reset link sent to your email');
            return redirect()->route('moh.forgot-password');
        }else{
            session()->flash('fail','Something went wrong');
            return redirect()->route('moh.forgot-password');
        }
    }

    //reset password
    public function resetPassword(Request $request, $token = null){
        $checkToken = DB::table('password_resets')
                    ->where('token', $request->token)
                    ->where('guard', constGuards::MOH)
                    ->first();

        if($checkToken){
            //check if token is expired
            $diffMins = Carbon::createFromFormat('Y-m-d H:i:s',$checkToken->created_at)->diffInMinutes(Carbon::now());

            if($diffMins > constDefaults::tokenExpiredMinutes){
                session()->flash('fail','Token expired, request another link');
                return redirect()->route('moh.forgot-password',['token' => $token]);
            }else{
                return view('back.pages.moh.auth.reset-password')->with(['token' => $token]);
            }

        }else{
            session()->flash('fail','Invalid token, request another link');
            return redirect()->route('moh.forgot-password',['token' => $token]);
        }
    }

    public function resetPasswordHandler(Request $request){
        $request->validate(([
            'new_password' => 'required|min:5|max:45|required_with:new_password_confirmation|same:new_password_confirmation',
            'new_password_confirmation' => 'required'
        ]));

        $token = DB::table('password_resets')
                    ->where('token', $request->token)
                    ->where('guard', constGuards::MOH)
                    ->first();

        //get moh details
        $moh = Moh::where('email',$token->email)->first();

        //update password
        Moh::where('email',$token->email)->update([
            'password' => Hash::make($request->new_password)
        ]);

        //delete token
        DB::table('password_resets')
            ->where('email', $moh->email)
            ->where('token', $request->token)
            ->where('guard', constGuards::MOH)
            ->delete();

        //send email
        $data = array(
            'admin' => $moh,
            'new_password' => $request->new_password
        );

        $mail_body = view('email-templates.moh-reset-email-template',$data)->render();

        $mailConfig = array(
            'mail_from_email' => env('EMAIL_FROM_ADDRESS'),
            'mail_from_name' => env('EMAIL_FROM_NAME'),
            'mail_recipient_email' => $moh->email,
            'mail_recipient_name' => $moh->name,
            'mail_subject' => 'Password Reset Successful',
            'mail_body' => $mail_body
        );

        sendEmail($mailConfig);
        return redirect()->route('moh.login')->with('success','Done! Password reset successful. Use new password to login system');
    }

    //profile view
    public function profileView(Request $request){
        $moh = null;
        if( Auth::guard('moh')->check() ){
            $moh = Moh::findOrFail(auth()->id());
        }
        return view('back.pages.moh.profile', compact('moh'));
    }

    //update profile
    public function updateProfile(Request $request){
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'username' => 'required',
        ]);

        $moh = Moh::findOrFail(auth()->id());
        $moh->name = $request->name;
        $moh->email = $request->email;
        $moh->username = $request->username;
        $moh->save();

        return redirect()->route('moh.profile')->with('success','Profile updated successfully');
    }

    public function showToastr($type, $message){
        return $this->dispatch('showToastr',[
             'type'=>$type,
             'message'=>$message
        ]);
    }

    //change profile picture
    public function changeProfilePicture(Request $request){
        try {
            $moh = Moh::findOrFail(auth()->id());
            $path = 'images/users/admins/';

            if($request->hasFile('mohProfilePictureFile')){
                $file = $request->file('mohProfilePictureFile');
                $old_picture = $moh->getAttributes()['picture'];
                $file_path = $path.$old_picture;
                $filename = 'MOH_IMG_'.rand(2,1000).$moh->id.time().uniqid().'.jpg';

                $upload = $file->move(public_path($path),$filename);
                if($upload){
                    if( $old_picture != null && File::exists(public_path($path.$old_picture)) ){
                        File::delete(public_path($path.$old_picture));
                    }
                    $moh->update(['picture'=>$filename]);
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

    //change password
    public function changePassword(Request $request){
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:5|max:45|required_with:new_password_confirmation|same:new_password_confirmation',
            'new_password_confirmation' => 'required'
        ]);

        $moh = Moh::findOrFail(auth()->id());

        if(Hash::check($request->current_password, $moh->password)){
            $moh->update(['password'=>Hash::make($request->new_password)]);
            return redirect()->route('moh.profile')->with('success','Password updated successfully');
        }else{
            return redirect()->route('moh.profile')->with('fail','Old password is incorrect');
        }
    }

    //baby profiles page
    public function users(Request $request){
        $moh = null;
        if( Auth::guard('moh')->check() ){
            $moh = Moh::findOrFail(auth()->id());
        }
        $babies = DB::table('baby as a')->selectRaw('a.*, b.name as midwifename, c.name as parentname')
            ->leftJoin('midwives as b', 'b.id', '=', 'a.midwife_id')
            ->leftJoin('parents as c', 'c.id', '=', 'a.parent_id')
            ->get();

        $data = [
            'pageTitle'=>'Baby Profiles',
            'client'=>$babies,
            'moh'=>$moh,
        ];

        return view('back.pages.moh.users', $data);
    }

    //registered parents page
    public function sellers(Request $request){
        $moh = null;
        if( Auth::guard('moh')->check() ){
            $moh = Moh::findOrFail(auth()->id());
        }

        $parents = DB::table('parents as a')->selectRaw('a.*, 1 as baby_count, c.division_name, d.name_en AS cities')
            ->leftJoin('divisions as c','c.division_id', '=' ,'a.division_id')
            ->leftJoin('cities as d','d.id', '=' ,'a.cities')
            ->get();

        $data = [
            'pageTitle'=>'Registered Parents',
            'sellers'=>$parents,
            'moh'=>$moh,
        ];

        return view('back.pages.moh.sellers', $data);
    }

    //pending midwives page
    public function pendingMidwives(Request $request){
        $moh = null;
        if( Auth::guard('moh')->check() ){
            $moh = Moh::findOrFail(auth()->id());
        }
        $midwives = DB::table('midwives as a')
            ->selectRaw('a.id, a.name, a.email, a.phone, a.address, a.picture, a.is_approved, IF(a.is_approved = 1, "Approved", "Not Approved") as approval_status, DATE_FORMAT(a.created_at, "%Y-%m-%d") as date, c.division_name, d.name_en AS cities, b.baby_count')
            ->leftJoin('divisions as c','c.division_id', '=' ,'a.division_id')
            ->leftJoin('cities as d','d.id', '=' ,'a.cities')
            ->leftjoin(DB::raw('(SELECT midwife_id, COUNT(*) as baby_count FROM baby GROUP BY midwife_id) as b'), 'b.midwife_id', '=', 'a.id')
            ->get();

        $data = [
            'pageTitle'=>'Midwife Management',
            'sellers'=>$midwives,
            'moh'=>$moh,
        ];

        return view('back.pages.moh.pending-sellers', $data);
    }

    //approve midwife
    public function approveMidwife(Request $request){
        try {
            $midwife = Midwife::findOrFail($request->seid);
            $midwife->status = 1;
            $midwife->is_approved = true;
            $saved = $midwife->save();

            if($saved){
                $data = array(
                    'seller' => $midwife,
                );

                $mail_body = view('email-templates.midwife-verified-template',$data)->render();

                $mailConfig = array(
                    'mail_from_email' => env('EMAIL_FROM_ADDRESS'),
                    'mail_from_name' => env('EMAIL_FROM_NAME'),
                    'mail_recipient_email' => $midwife->email,
                    'mail_recipient_name' => $midwife->name,
                    'mail_subject' => 'Midwife Account Approved',
                    'mail_body' => $mail_body
                );

                sendEmail($mailConfig);

                return response()->json(['status'=>1,'msg'=>'Midwife approved successfully']);
            }else{
                return response()->json(['status'=>0,'msg'=>'Something went wrong']);
            }
        } catch (\Exception $e) {
            return response()->json(['status'=>0,'msg'=>'Error: '.$e->getMessage()]);
        }
    }

    //revoke midwife approval
    public function revokeMidwife(Request $request){
        try {
            $midwife = Midwife::findOrFail($request->seid);
            $midwife->status = 2;
            $midwife->is_approved = false;
            $saved = $midwife->save();

            if($saved){
                $data = array(
                    'seller' => $midwife,
                );

                $mail_body = view('email-templates.midwife-unverified-template',$data)->render();

                $mailConfig = array(
                    'mail_from_email' => env('EMAIL_FROM_ADDRESS'),
                    'mail_from_name' => env('EMAIL_FROM_NAME'),
                    'mail_recipient_email' => $midwife->email,
                    'mail_recipient_name' => $midwife->name,
                    'mail_subject' => 'Midwife Account Approval Revoked',
                    'mail_body' => $mail_body
                );

                sendEmail($mailConfig);

                return response()->json(['status'=>1,'msg'=>'Midwife approval revoked successfully']);
            }else{
                return response()->json(['status'=>0,'msg'=>'Something went wrong']);
            }
        } catch (\Exception $e) {
            return response()->json(['status'=>0,'msg'=>'Error: '.$e->getMessage()]);
        }
    }

    //vaccination schedules page
    public function vaccination(Request $request){
        $moh = null;
        if( Auth::guard('moh')->check() ){
            $moh = Moh::findOrFail(auth()->id());
        }
        $vaccination = DB::table('vaccination_schedules')->get();

        $data = [
            'pageTitle'=>'Vaccination Schedules',
            'vaccines'=>$vaccination,
            'moh'=>$moh,
        ];

        return view('back.pages.moh.vaccine', $data);
    }

    //add vaccine
    public function addVaccine(Request $request) {
        try {
            $request->validate([
                'vaccine_name' => 'required|string|max:255',
                'description' => 'required|string',
                'recommended_age_months' => 'required|integer',
                'doses_required' => 'required|integer',
            ]);

            DB::table('vaccination_schedules')->insert([
                'vaccine_name' => $request->vaccine_name,
                'description' => $request->description,
                'recommended_age_months' => $request->recommended_age_months,
                'doses_required' => $request->doses_required,
                'created_at' => now(),
            ]);

            return redirect()->back()->with('success', 'Vaccine schedule added successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('fail', 'Error: '.$e->getMessage());
        }
    }

    //notice page
    public function notice(Request $request){
        $moh = null;
        if( Auth::guard('moh')->check() ){
            $moh = Moh::findOrFail(auth()->id());
        }
        $notice = DB::table('notice')->get();

        $data = [
            'pageTitle'=>'Notice',
            'notice'=>$notice,
            'moh'=>$moh,
        ];

        return view('back.pages.moh.notice', $data);
    }

    //add notice
    public function addNotice(Request $request) {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'notice_type' => 'required|in:general,urgent,reminder',
                'target_group' => 'required|in:parents,midwives,all',
                'scheduled_at' => 'nullable|date',
                'expires_at' => 'nullable|date|after:scheduled_at',
            ]);

            Notice::create([
                'title' => $request->title,
                'content' => $request->content,
                'sender_type' => 'MOH',
                'sender_id' => auth()->id(),
                'notice_type' => $request->notice_type,
                'target_group' => $request->target_group,
                'scheduled_at' => $request->scheduled_at,
                'expires_at' => $request->expires_at,
            ]);

            return redirect()->back()->with('success', 'Notice added successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('fail', 'Error: '.$e->getMessage());
        }
    }
}
