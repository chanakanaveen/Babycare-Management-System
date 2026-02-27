<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Moh;
use App\Models\ParentUser;
use App\Models\Midwife;
use Illuminate\Support\Facades\Auth;

class AdminSellerHeaderProfile extends Component
{

    public $admin;
    public $seller;
    public $client;

    public $listeners = [
        'updateAdminSellerHeaderInfo'=>'$refresh'
    ];

    public function mount(){
        if( Auth::guard('moh')->check() ){
            $this->admin = Moh::findOrFail(auth()->id());
        }
        if( Auth::guard('midwife')->check() ){
            $this->seller = Midwife::findOrFail(auth()->id());
        }
        if( Auth::guard('parent')->check() ){
            $this->client = ParentUser::findOrFail(auth()->id());
        }
    }

    public function render()
    {
        return view('livewire.admin-seller-header-profile');
    }
}
