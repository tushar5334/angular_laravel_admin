<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use Illuminate\Http\Request;
use App\Models\Admin\User;
use Carbon\Carbon;
//use Datatables;
use Illuminate\Support\Facades\DB;
//use Yajra\DataTables\DataTables;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\Admin\UserReportExport;
use App\Services\ImageService;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!Gate::allows('isSuperAdmin') && !Gate::allows('isAdmin')) {
            return redirect()->route('admin.get.dashboard')->with('error', 'Access denied for this page.');
        }

        if ($request->ajax()) {

            $userList = User::select(
                '*',
                //DB::RAW('DATE_FORMAT(created_at, "%d-%m-%Y") as createdAt')
            );
            if (!empty($request->get('startDate')) && !empty($request->get('endDate'))) {
                $endDate = Carbon::createFromFormat('d/m/Y', $request->get('endDate'))->format('Y-m-d');
                $startDate = Carbon::createFromFormat('d/m/Y', $request->get('startDate'))->format('Y-m-d');
                $userList->whereBetween(DB::RAW('DATE_FORMAT(created_at, "%Y-%m-%d")'), [$startDate, $endDate]);
            }
            if (!empty($request->get('startDate')) && empty($request->get('endDate'))) {
                $startDate = Carbon::createFromFormat('d/m/Y', $request->get('startDate'))->format('Y-m-d');
                $userList->whereDate('created_at', '>=', $startDate);
            }
            if (!empty($request->get('endDate')) && empty($request->get('startDate'))) {
                $endDate = Carbon::createFromFormat('d/m/Y', $request->get('endDate'))->format('Y-m-d');
                $userList->whereDate('created_at', '<=', $endDate);
            }
            $userList->where('user_type', "User");
            $userList = $userList;
            return DataTables::of($userList)
                /* ->addColumn('intro', function (User $userList) {
                    $str = "";
                    if (Gate::allows('isSuperAdmin')) {
                        $str .= 'Hi ';
                    }
                    return $str .= $userList->name . '!';
                }) */
                ->editColumn('created_at', function ($userList) {
                    //return $userList->created_at->diffForHumans();
                    return $userList->created_at->format('d-m-Y');
                })
                ->filterColumn('created_at', function ($query, $keyword) use ($userList) {
                    $query->whereRaw('DATE_FORMAT(created_at, "%d-%m-%Y") LIKE ?', ["%{$keyword}%"]);
                })
                ->toJson();
        }
        return view('admin.user.user_list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Gate::allows('isSuperAdmin') && !Gate::allows('isAdmin')) {
            return redirect()->route('admin.get.dashboard')->with('error', 'Access denied for this page.');
        }

        return view('admin.user.user_add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request, ImageService $imageService)
    {
        if (!Gate::allows('isSuperAdmin') && !Gate::allows('isAdmin')) {
            return redirect()->route('admin.get.dashboard')->with('error', 'Access denied for this page.');
        }
        try {
            $fileData = "";
            $requestData = $request->validated();
            $requestData['created_by'] = Auth::guard('admin')->id();
            if ($request->hasFile('profile_picture')) {
                $fileData = $imageService->handleFileUpload('user_images', $request->file('profile_picture'), '', 'public');
                $requestData['profile_picture'] = $fileData;
            }

            if (isset($requestData['password'])) {
                $requestData['password'] = Hash::make($requestData['password']);
            } else {
                unset($requestData['password']);
            }
            unset($requestData['confirm_password']);

            DB::transaction(function () use ($requestData) {
                User::createUpdateUser($requestData, '');
            }, 1);
            return redirect()->route('admin.users.index')->with('success', 'User is added successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        if (!Gate::allows('isSuperAdmin') && !Gate::allows('isAdmin')) {
            return redirect()->route('admin.get.dashboard')->with('error', 'Access denied for this page.');
        }

        return view('admin.user.user_add', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  object  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, User $user, ImageService $imageService)
    {
        if (!Gate::allows('isSuperAdmin') && !Gate::allows('isAdmin')) {
            return redirect()->route('admin.get.dashboard')->with('error', 'Access denied for this page.');
        }
        try {
            $fileData = $user->profile_picture;
            $requestData = $request->validated();
            $requestData['updated_by'] = Auth::guard('admin')->id();
            if ($request->hasFile('profile_picture')) {
                $fileData = $imageService->handleFileUpload('user_images', $request->file('profile_picture'), $user->profile_picture, 'public');
                $requestData['profile_picture'] = $fileData;
            } else {
                if (isset($request->isImgDel) &&  $request->isImgDel == 1) {
                    $fileData = $imageService->handleFileUpload('user_images', '', $user->profile_picture, 'public');
                }
                $requestData['profile_picture'] = $fileData;
            }

            if (isset($requestData['password'])) {
                $requestData['password'] = Hash::make($requestData['password']);
            } else {
                unset($requestData['password']);
            }

            unset($requestData['confirm_password']);

            DB::transaction(function () use ($requestData, $user) {
                User::createUpdateUser($requestData, $user->user_id);
            }, 1);

            return redirect()->route('admin.users.index')->with('success', 'User is updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!Gate::allows('isSuperAdmin') && !Gate::allows('isAdmin')) {
            return response()->json([
                'success' => false,
                'message'   => 'Access denied for this action.'
            ], 200);
        }
        try {
            DB::transaction(function () use ($id) {
                User::destroy($id);
            }, 1);
            return response()->json([
                'success' => true,
                'message'   => 'User is deleted successfully.'
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message'   => 'Something went wrong.'
            ], 200);
        }
    }

    public function multipleDelete(Request $request)
    {
        if (!Gate::allows('isSuperAdmin') && !Gate::allows('isAdmin')) {
            return response()->json([
                'success' => false,
                'message'   => 'Access denied for this action.'
            ], 200);
        }

        $postData = $request->all();
        try {
            DB::transaction(function () use ($postData) {
                User::destroy($postData['ids']);
            }, 1);
            //User::whereIn('user_id', $postData['id'])->delete();
            return response()->json([
                'success' => true,
                'message'   => 'Users are deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message'   => 'Something went wrong.'
            ], 200);
        }
    }
    public function multipleChangeStatus(Request $request)
    {
        if (!Gate::allows('isSuperAdmin') && !Gate::allows('isAdmin')) {
            return response()->json([
                'success' => false,
                'message'   => 'Access denied for this action.'
            ], 200);
        }

        $postData = $request->all();
        try {
            //Find and update multiple users status
            DB::transaction(function () use ($postData) {
                $status = ($postData['status'] == "active") ? 1 : 0;
                User::whereIn('user_id', $postData['ids'])->update(['status' => $status]);
            }, 1);
            return response()->json([
                'success' => true,
                'message'   => "Status is updated successfully."
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message'   => 'Something went wrong.'
            ], 200);
        }
    }
    public function changeStatus(Request $request)
    {
        if (!Gate::allows('isSuperAdmin') && !Gate::allows('isAdmin')) {
            return response()->json([
                'success' => false,
                'message'   => 'Access denied for this action.'
            ], 200);
        }

        $postData = $request->all();
        try {
            //Find user status
            $userObj = User::findOrFail($postData['id']);
            DB::transaction(function () use ($userObj) {
                $status = ($userObj->status == 1) ? 0 : 1;
                //Update user status
                $userObj->update(['status' => $status]);
            }, 1);
            return response()->json([
                'success' => true,
                'message'   => 'Status is updated successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message'   => 'Something went wrong.'
            ], 200);
        }
    }
    public function ExportUser(Request $request)
    {
        if (!Gate::allows('isSuperAdmin') && !Gate::allows('isAdmin')) {
            return redirect()->route('admin.get.dashboard')->with('error', 'Access denied for this page.');
        }
        $requestData = $request->all();
        $now = Carbon::now()->format('Y-m-d-H-i-s');

        $fileName = "user-export-" . $now . ".xlsx";

        return Excel::download(new UserReportExport($requestData), $fileName);
    }
}
