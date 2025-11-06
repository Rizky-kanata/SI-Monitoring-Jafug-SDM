<?php

namespace App\Http\Controllers;

use App\Models\Kepangkatan;
use App\Models\Profil;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $totalProfil = Profil::count();
        $totalKepangkatan = Kepangkatan::count();
        $recentKepangkatan = Kepangkatan::query()
            ->with('profil')
            ->latest('updated_at')
            ->take(5)
            ->get();

        return view('dashboard.index', [
            'user' => Auth::user(),
            'totalProfil' => $totalProfil,
            'totalKepangkatan' => $totalKepangkatan,
            'recentKepangkatan' => $recentKepangkatan,
        ]);
    }
}
