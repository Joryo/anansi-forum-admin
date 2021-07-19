<?php

namespace App\Http\Controllers;

use App\Models\Member as MemberModel;
use App\Models\Status as StatusModel;

use Illuminate\Http\Request;

/**
 * Main page controller
 */
class MainController extends Controller
{
    private $model;

    public function __construct()
    {
        $this->middleware('auth');
        $this->memberModel = new MemberModel();
        $this->statusModel = new StatusModel();
    }

    /**
     * Display Api server status and stats, and administrators.
     * @param Request $request
     */
    public function index(Request $request)
    {
        $page = $request->input('offset', 0);

        $data['api'] = [
            'uri' => env('API_URI'),
        ];
        $data['status'] = (array) $this->statusModel->getStatus();
        $data['members'] = (array) $this->memberModel->getAdmins($page);

        return view('main', ['content' => $data]);
    }
}
