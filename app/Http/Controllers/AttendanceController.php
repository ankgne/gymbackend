<?php

namespace App\Http\Controllers;

use App\Http\Resources\AccountResource;
use App\Http\Resources\AttendanceResource;
use App\Services\AttendanceService;
use App\Services\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return AccountResource
     */
    public function index()
    {
        try {
            $attendances = AttendanceService::getActiveCustomersWithAttendance();
            return AccountResource::collection($attendances);
        } catch (\Exception $exception) {
            return Helper::exceptionJSON($exception, 400, "attendance");
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return AttendanceResource
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $accountID = trim($request->account_id);
        $input = [
            "account_id" => $accountID,
        ];
        $rules = [
            "account_id" => "required|integer|exists:accounts,id",
        ];

        Validator::make(
            $input,
            $rules,
            $messages = [
                "required" =>
                    "The :attribute field is required for marking the attendance",
                "integer" => "The :attribute field should be a valid account.",
            ]
        )->validate();

        try {
            $attendance = AttendanceService::createInAttendance($request);
            return new AttendanceResource($attendance);
        } catch (\Exception $exception) {
            return Helper::exceptionJSON($exception, 422, "attendance");
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return AttendanceResource
     * @throws \Illuminate\Validation\ValidationException
     */
    public function captureOutTime(Request $request)
    {
        $accountID = trim($request->account_id);
        $input = [
            "account_id" => $accountID,
        ];
        $rules = [
            "account_id" => "required|integer|exists:accounts,id",
        ];

        Validator::make(
            $input,
            $rules,
            $messages = [
                "required" =>
                    "The :attribute field is required for marking the attendance",
                "integer" => "The :attribute field should be a valid account.",
            ]
        )->validate();

        try {
            $attendance = AttendanceService::createOutAttendance($request);
            return new AttendanceResource($attendance);
        } catch (\Exception $exception) {
            return Helper::exceptionJSON($exception, 422, "attendance");
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
        //
    }
}
