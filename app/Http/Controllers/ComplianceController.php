<?php

namespace App\Http\Controllers;

use App\Liantech\Helpers\ComplianceRiskReports;
use App\Models\Deposit;
use Carbon\Carbon;
use App\Models\CustomerComment;
use Illuminate\Http\Request;

class ComplianceController extends Controller
{
    public function getReports()
    {
        $repo = new ComplianceRiskReports();

        $users = $repo->getUsersWithStats();

        return view('pages.compliance.reports', compact('users'));
    }

    public function showPending()
    {
        return view('pages.compliance.pending');
    }

    public function getPendingDeposits(Request $request)
    {
        return Deposit::byBroker()
            ->where("approved", false)
            ->where("paymentMethod", "!=", "Bonus")
            ->orderBy('deposits_crm_id', 'desc')
            ->paginate(25);
    }


    public function getSearchResults(Request $request)
    {
        $start = ($request->startDate) ? Carbon::parse($request->startDate)->startOfDay() : null;
        $end = ($request->endDate) ? Carbon::parse($request->endDate)->endOfDay() : null;
        $depositsQuery = Deposit::byBroker()
            ->where("approved", false)
            ->where("paymentMethod", "!=", "Bonus")
            ->where(function ($query) use ($request, $start, $end) {
                if ($request->customerId) {
                    $query->where('customerId', "LIKE", "%$request->customerId%"); //search by customer id
                }
                if ($start && $end) {//search by date(dates)
                    $query->whereBetween("confirmTime", [$start, $end]);
                } else if ($start) {
                    $query->where("confirmTime", ">=", $start);
                } else if ($end) {
                    $query->where("confirmTime", "<=", $end);
                }
            })
            ->orderBy('deposits_crm_id', 'desc');

        return $depositsQuery->paginate(25);
    }

}
