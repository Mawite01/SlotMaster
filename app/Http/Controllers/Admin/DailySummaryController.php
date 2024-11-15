<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\DailySummary;

class DailySummaryController extends Controller
{
     public function index()
    {
        // Fetch all daily summary records, or you could use pagination
        $summaries = DailySummary::orderBy('summary_date', 'desc')->paginate(10);

        // Pass the data to the view
        return view('admin.reports.get_day_summary.index', compact('summaries'));
    }
}