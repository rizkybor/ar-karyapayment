<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataFeed;

class DashboardController extends Controller
{
    public function index()
    {
        $dataFeed = new DataFeed();

        // dd(auth()->user()->getRoleNames());

        return view('pages/dashboard/dashboard', compact('dataFeed'));
    }

    public function managementFee()
    {
        return view('pages/dashboard/management-fee');
    }

    public function createManagementFee()
    {
        return view('pages/dashboard/create-management-fee');
    }

    public function detailManagementFee()
    {
        return view('pages/dashboard/detail-management-fee');
    }

    /**
     * Displays the analytics screen
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function analytics()
    {
        return view('pages/dashboard/analytics');
    }

    /**
     * Displays the fintech screen
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function fintech()
    {
        return view('pages/dashboard/fintech');
    }
}
