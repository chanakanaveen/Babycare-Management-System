<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Admin;
use App\Models\Client;
use App\Models\Seller;
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
        if( Auth::guard('admin')->check() ){
            $this->admin = Admin::findOrFail(auth()->id());
        }
        if( Auth::guard('seller')->check() ){
            $this->seller = Seller::findOrFail(auth()->id());
        }
        if( Auth::guard(('client'))->check() ){
            $this->client = Client::findOrFail(auth()->id());
        }
    }

    public function render()
    {
        return view('livewire.admin-seller-header-profile');
    }
}
