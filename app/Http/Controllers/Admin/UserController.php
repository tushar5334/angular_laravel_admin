<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EditProfileRequest;
use App\Http\Requests\Admin\UserRequest;
use Illuminate\Http\Request;
use App\Models\Admin\User;

use Exception;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use App\Exports\Admin\UserReportExport;
use App\Services\ImageService;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //$cuurentLoggedInUser = $request->user();
        try {

            $pageSize = $request->get('pageSize', 10);
            $currentPage = $request->get('currentPage', 0);
            $offsetNumber =  ($currentPage) * $pageSize;;
            $sortField = $request->get('sortField', 'created_at');
            $sortOrder = $request->get('sortDirection', 'desc');
            $globalFilter = $request->get('globalFilter');
            $startDate = $request->get('startDate');
            $endDate = $request->get('endDate');

            $users = User::SELECT('user_id', 'name', 'email', 'profile_picture', 'user_type', 'status', 'created_at');
            if (!empty($globalFilter) && $globalFilter != "null") {
                $users->where(function ($query) use ($globalFilter) {
                    $query->WHERE('name', 'LIKE', ["{$globalFilter}%"]);
                    $query->ORWHERE('email', 'LIKE', ["{$globalFilter}%"]);
                });
            }

            if (!empty($startDate) && $startDate != "null") {
                $users->where('created_at', '>=', $startDate . " 00:00:00");
            }

            if (!empty($endDate) && $endDate != "null") {
                $users->where('created_at', '<=', $endDate . " 11:59:59");
            }

            $users->where('user_id', '<>', $request->user()->user_id);

            $users = $users->orderby($sortField, $sortOrder);

            //Get total count
            $totalCount = $users->count();

            //Implement pagination
            $usersData = $users
                ->skip($offsetNumber)
                ->take($pageSize)->get();
            return response()->json([
                'data' => $usersData,
                'totalCount' => $totalCount,
                'message' => 'Users fetched successfully.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'data' => [],
                'totalCount' => 0,
                'message' => 'Something went wrong, Please try again later.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function createOrUpdate(UserRequest $request, ImageService $imageService)
    {
        try {
            // Default variables
            $userData = [];
            $fileName = "";

            $requestData = $request->validated();

            // Prepare data to insert start

            // Set status variable
            $requestData['status'] = (isset($requestData['status']) &&  $requestData['status'] == 'true') ? 1 : 0;

            // Remove password field if password field is empty
            if (!isset($requestData['password'])) {
                unset($requestData['password']);
            }
            // Remove password_confirmation field if password_confirmation field is empty
            unset($requestData['password_confirmation']);

            // Upload files

            if (!empty($request->file('filesArray')) && $request->hasFile('filesArray')) {
                foreach ($request->file('filesArray') as $file) {
                    $fileName = $imageService->handleFileUpload('user_images', $file, $requestData['profile_picture'], 'public');
                }
                $requestData['profile_picture'] = $fileName;
            }

            // Prepare data to insert end

            if ($requestData['user_id'] == '0') {
                // Insert logic
                $message = 'User is added successfully.';
                $requestData['created_by'] = $request->user()->user_id;
            } else {
                // Update logic
                $message = 'User is updated successfully.';
                $requestData['updated_by'] = $request->user()->user_id;
            }

            $userObj = DB::transaction(function () use ($requestData) {
                return User::createUpdateUser($requestData, $requestData['user_id']);
            }, 1);

            if (!empty($userObj)) {
                $userData = ['name' => $userObj->name ?? '', 'email' => $userObj->email ?? '', 'display_picture' => $userObj->display_picture ?? ''];
            }
            return response(['message' => $message, 'user' => $userData], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response(['message' => 'Something went wrong, Please try again later.'], Response::HTTP_INTERNAL_SERVER_ERROR);
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
    public function edit(string $user_id)
    {
        try {
            $user = User::find($user_id);
            return response()->json([
                'data' => $user,
                'message' => 'Initial data fetched successfully.'
            ]);
        } catch (Exception $e) {
            return response(['message' => 'Something went wrong, Please try again later.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                User::destroy($request->user_id);
            }, 1);
            //return response(['message' => 'Record deleted successfully.'], Response::HTTP_NO_CONTENT);
            return response(['message' => 'Record deleted successfully.'], Response::HTTP_OK);
        } catch (Exception $e) {
            return response(['message' => 'Something went wrong, Please try again later.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function multipleDelete(Request $request)
    {
        $postData = $request->all();
        try {
            DB::transaction(function () use ($postData) {
                User::destroy($postData['Ids']);
            }, 1);
            return response(['message' => 'Selected records deleted successfully.'], Response::HTTP_OK);
        } catch (Exception $e) {
            return response(['message' => 'Something went wrong, Please try again later.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function changeStatus(Request $request, string $user_id)
    {
        try {
            $userObj = User::findOrFail($user_id);
            DB::transaction(function () use ($userObj) {
                $status = ($userObj->status == 1) ? 0 : 1;
                //Update user status
                $userObj->update(['status' => $status]);
            }, 1);
            return response(['message' => 'Status updated successfully.'], Response::HTTP_OK);
        } catch (Exception $e) {
            return response(['message' => 'Something went wrong, Please try again later.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function multipleChangeStatus(Request $request)
    {
        $postData = $request->all();
        try {
            //Find and update multiple users status
            DB::transaction(function () use ($postData) {
                $status = ($postData['status'] == 1) ? 1 : 0;
                User::whereIn('user_id', $postData['Ids'])->update(['status' => $status]);
            }, 1);
            return response(['message' => 'Selected records status is updated successfully.'], Response::HTTP_OK);
        } catch (\Exception $e) {

            return response(['message' => 'Something went wrong, Please try again later.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function exportUser(Request $request)
    {
        $requestData = $request->all();
        $now = Carbon::now()->format('Y-m-d-H-i-s');

        $fileName = "user-export-" . $now . ".xlsx";

        return Excel::download(new UserReportExport($requestData), $fileName);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function currentUserDetail(Request $request)
    {
        try {
            $currentUserDetail = $request->user();
            $user = [
                'name' => $currentUserDetail->name,
                'email' => $currentUserDetail->email,
                'profile_picture' => $currentUserDetail->profile_picture,
                'display_picture' => $currentUserDetail->display_picture,
            ];
            return response()->json([
                'data' => $user,
                'message' => 'User detail fetched successfully.'
            ]);
        } catch (Exception $e) {
            return response(['message' => 'Something went wrong, Please try again later.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(EditProfileRequest $request, ImageService $imageService)
    {
        try {
            // Default variables
            $userData = [];
            $fileName = "";
            $user_id = $request->user()->user_id;
            $requestData = $request->validated();

            // Prepare data to insert start

            // Set status variable
            $requestData['status'] = (isset($requestData['status']) &&  $requestData['status'] == 'true') ? 1 : 0;

            // Remove password field if password field is empty
            if (!isset($requestData['password'])) {
                unset($requestData['password']);
            }
            // Remove password_confirmation field if password_confirmation field is empty
            unset($requestData['password_confirmation']);

            // Upload files

            if (!empty($request->file('filesArray')) && $request->hasFile('filesArray')) {
                foreach ($request->file('filesArray') as $file) {
                    $fileName = $imageService->handleFileUpload('user_images', $file, $requestData['profile_picture'], 'public');
                }
                $requestData['profile_picture'] = $fileName;
            }

            // Prepare data to insert end
            $message = 'Profile is updated successfully!';
            $requestData['updated_by'] =  $user_id;

            $userObj = DB::transaction(function () use ($requestData,  $user_id) {
                return User::createUpdateUser($requestData,  $user_id);
            }, 1);

            if (!empty($userObj)) {
                $userData = ['name' => $userObj->name ?? '', 'email' => $userObj->email ?? '', 'display_picture' => $userObj->display_picture ?? ''];
            }
            return response(['message' => $message, 'user' => $userData], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response(['message' => 'Something went wrong, Please try again later.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
