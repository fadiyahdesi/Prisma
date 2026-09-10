<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\EligibilityService;

class EligibilityController extends Controller
{
    protected EligibilityService $eligibilityService;

    public function __construct(EligibilityService $eligibilityService)
    {
        $this->eligibilityService = $eligibilityService;
    }

    /**
     * Return JSON eligibility assessment for specified scheme.
     */
    public function check(Request $request)
    {
        $scheme = $request->query('scheme', 'pdp');
        $user = Auth::user();

        $result = $this->eligibilityService->checkEligibility($user, $scheme);

        return response()->json($result);
    }
}

