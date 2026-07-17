<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\JenisDokumen;
use App\Models\ProfilOrganisasi;
use App\Models\Staff;

class HomeController extends Controller
{
    public function index()
    {
        $jenisDokumen = JenisDokumen::aktif()->get();
        return view('public.home', compact('jenisDokumen'));
    }

    public function profil()
    {
        $profil = ProfilOrganisasi::all()->keyBy('key');
        $staff  = Staff::aktif()->get();
        return view('public.profil', compact('profil', 'staff'));
    }
}
