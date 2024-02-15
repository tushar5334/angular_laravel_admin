<?php

namespace App\Exports\Admin;

use App\Models\Admin\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserReportExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function __construct(public $requestData)
    {
        $this->requestData = $requestData;
    }

    use Exportable;

    public function collection()
    {
        $users = User::SELECT(
            'name',
            'email',
            DB::RAW('IF(status=1, "Active", "Inactive") as user_status'),
            DB::RAW('DATE_FORMAT(created_at, "%d-%m-%Y") as createdAt'),
        );
        if (!empty($this->requestData['globalFilter']) && $this->requestData['globalFilter'] != "null") {
            $globalFilter = $this->requestData['globalFilter'];
            $users->where(function ($query) use ($globalFilter) {
                $query->WHERE('name', 'LIKE', ["{$globalFilter}%"]);
                $query->ORWHERE('email', 'LIKE', ["{$globalFilter}%"]);
            });
        }

        if (!empty($this->requestData['startDate']) && $this->requestData['startDate'] != "null") {
            $users->where('created_at', '>=', $this->requestData['startDate'] . " 00:00:00");
        }

        if (!empty($this->requestData['endDate']) && $this->requestData['endDate'] != "null") {
            $users->where('created_at', '<=', $this->requestData['endDate'] . " 11:59:59");
        }

        $users = $users->orderby($this->requestData['sortField'], $this->requestData['sortDirection'])->get()->makeHidden(['display_picture']);
        return $users;
        /* $db_prefix = getenv('DB_PREFIX');
        $userData = User::select(
            'name',
            'email',
            DB::RAW('DATE_FORMAT(created_at, "%d-%m-%Y") as createdAt'),
            //'status'
            DB::RAW('IF(' . $db_prefix . 'users.status="1", "Active", "Inactive") as status')
            //DB::RAW('IF(users.status="1", "Active", "Inactive") as status')
        );
        if (!empty($this->requestData['startDate']) && !empty($this->requestData['endDate'])) {
            $endDate = Carbon::createFromFormat('d/m/Y', $this->requestData['endDate'])->format('Y-m-d');
            $startDate = Carbon::createFromFormat('d/m/Y', $this->requestData['startDate'])->format('Y-m-d');
            $userData->whereBetween(DB::RAW('DATE_FORMAT(created_at, "%Y-%m-%d")'), [$startDate, $endDate]);
        }
        if (!empty($this->requestData['startDate']) && empty($this->requestData['endDate'])) {
            $startDate = Carbon::createFromFormat('d/m/Y', $this->requestData['startDate'])->format('Y-m-d');
            $userData->whereDate('created_at', '>=', $startDate);
        }
        if (!empty($this->requestData['endDate']) && empty($this->requestData['startDate'])) {
            $endDate = Carbon::createFromFormat('d/m/Y', $this->requestData['endDate'])->format('Y-m-d');
            $userData->whereDate('created_at', '<=', $endDate);
        }
        if (!empty($this->requestData['searchData'])) {
            $keyword = $this->requestData['searchData'];
            $userData->where(function ($query) use ($keyword) {
                $query->where('name', 'like', '%' . $keyword . '%');
                $query->orWhere('email', 'like', '%' . $keyword . '%');
                $query->orWhere(DB::raw('DATE_FORMAT(created_at, "%d-%m-%Y")'), 'like', '%' . $keyword . '%');
            });
        }
        $userData = $userData->get();
        return $userData; */
    }
    public function headings(): array
    {
        return ['Name', 'Email', 'User Status', 'Created At'];
    }
}
