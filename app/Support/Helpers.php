<?php

use App\Models\BlockActivity;
use App\Models\BlockUser;
use App\Models\CompanyDevice;
use App\Models\Device;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\User;
use App\Models\VisitorDetail;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use Shuchkin\SimpleXLSXGen;
use Firebase\JWT\JWT;
use Firebase\JWT\JWK;

define('DB_DATE', 'Y-m-d');
define('DB_TIME', 'H:i:s');
define('DB_DATETIME', 'Y-m-d H:i:s');

define('DISPLAY_DATE', 'd-m-Y');
define('DISPLAY_DATE2', 'd-m-Y');
define('DISPLAY_DATE3', 'd/M/Y');
define('DISPLAY_DATETIME', 'd-m-Y h:i A');
define('DISPLAY_DATETIME2', 'd-m-Y-h-i-A');
define('DISPLAY_DATETIME3', 'd-m-Y h:i:s');
define('DISPLAY_DATETIME4', 'd-M-Y h:i:s A');
define('DISPLAY_TIME', 'h:i A');

define('DEFAULT_IMG', 'default.png');
define('DEFAULT_USER_IMG', 'default-user.png');
define('USER_PATH', 'uploads/users/');
define('PRODUCTS_PATH', 'uploads/products/');
define('OFFER_BANNERS_PATH', 'uploads/offer_banners/');
define('REFUND_PATH', 'uploads/refunds/');
define('CUSTOMERS_PATH', 'uploads/customers/');
define('FLAT_PROMO_TYPE', '2');
define('PERCENT_PROMO_TYPE', '1');

define('REGISTRATION_PATH', 'uploads/registration/');
define('VISITOR_PATH', 'uploads/visitor/');
define('COMPANY_EMPLOYEE_PATH', 'uploads/company-employee/');
define('PDF_PATH', 'uploads/pdf/');
define('QR_CODE_PATH', 'uploads/qrcode/');
define('CONTACT_PATH', 'uploads/contact/');
define('TICKET_PATH', 'uploads/ticket/');


define('DIAL_CODE', '+264');
define('DIAL_CODE_ISO', 'NA');
define('TOOL_OTHER_ID', 5);

define('ADMIN_ID', 1);

define('USER_TYPE_ADMIN', 1); // company admin
define('USER_TYPE_RECEPTION', 2); // reception
define('USER_TYPE_APPROVER', 3); // section approver
define('USER_TYPE_MANAGER', 4); // department approver
define('USER_TYPE_COMPANY', 5); // contractor
define('USER_TYPE_ADMIN_MANAGER', 6); // manager who will act like admin
define('USER_TYPE_FACILITY_EMPLOYEE', 7); // facility employee
define('USER_TYPE_SUPER_ADMIN', 8); // company admin
define('USER_TYPE_SUB_ADMIN', 9); // admin's sub admin

define('TICKET_OPEN', 1);
define('TICKET_IN_PROGRESS', 2);
define('TICKET_CLOSED', 3);

define('FCM_PROJECT_ID', 'timetec-3450a');

// visitation form type
define('VISITATION_PRE_REG', 1);
define('VISITATION_WALK_IN', 2);
define('VISITATION_PUBLIC_PRE_REG', 3);

//New
define('CUSTOMER_QR_CODE_PATH', 'customer_qrcodes/');
define('VENDOR_QR_CODE_PATH', 'vendor_qrcodes/');
define('WITHDRAW_MONEY', 1);
define('TRANSFER_MONEY', 2);
define('CURRENCY', 'KR');
define('SLIDER_BANNERS_PATH', 'uploads/sliders/');

function submissionTypeName($type = 0)
{
    $types = [
        VISITATION_PRE_REG => __('contractor_visit'),
        VISITATION_WALK_IN => __('walk_in_visit'),
        VISITATION_PUBLIC_PRE_REG => __('public_reg_visit'),
    ];

    return $types[$type] ?? '--';
}

function userTypes($heading = true, $title = '')
{
    $types = [
        USER_TYPE_ADMIN => __('admin'),
        USER_TYPE_RECEPTION => __('access_control'),
        USER_TYPE_APPROVER => __('section_approver'),
        USER_TYPE_MANAGER => __('department_approver'),
    ];

    $title = empty($title) ? __('select') : $title;
    if ($heading) {
        $types = ['' => $title] + $types;
    }
    return $types;
}

function reportTypes($heading = true, $title = '')
{
    $types = [
        'walk-in-checked-in-out' => __('report_walk_in_check_in_out'),
        'contractor-checked-in-out' => __('report_contractor_check_in_out'),
        'walk-in-profile-summary' => __('report_walk_in_profile'),
        'contractor-profile-summary' => __('report_contractor_profile'),
        'blocking-activity-log' => __('report_blocking_activity_log'),
    ];

    $title = empty($title) ? __('select') : $title;
    if ($heading) {
        $types = ['' => $title] + $types;
    }
    return $types;
}
function customReportTypes($heading = true, $title = '')
{
    $types = [
        'walk-in-checked-in-out' => __('report_walk_in_check_in_out'),
        'contractor-checked-in-out' => __('report_contractor_check_in_out'),
    ];

    $title = empty($title) ? __('select') : $title;
    if ($heading) {
        $types = ['' => $title] + $types;
    }
    return $types;
}

function reportFormatTypes($heading = true, $title = '')
{
    $types = [
        'pdf' => __('pdf'),
        'excel' => __('excel'),
        'csv' => __('csv'),
    ];

    $title = empty($title) ? __('select') : $title;
    if ($heading) {
        $types = ['' => $title] + $types;
    }
    return $types;
}

function employeeUserTypes($heading = true, $title = '')
{
    $types = [
        USER_TYPE_RECEPTION => __('access_control'),
        USER_TYPE_APPROVER => __('section_approver'),
        USER_TYPE_SUB_ADMIN => __('admin'),
        // USER_TYPE_MANAGER => __('department_approver'),
        // USER_TYPE_ADMIN_MANAGER => __('manager'),
        // USER_TYPE_FACILITY_EMPLOYEE => __('facility_employee'),
    ];

    $title = empty($title) ? __('select') : $title;
    if ($heading) {
        $types = ['' => $title] + $types;
    }
    return $types;
}

function employeeUserTypesText($type = 0)
{
    $types = [
        USER_TYPE_RECEPTION => __('access_control'),
        USER_TYPE_APPROVER => __('section_approver'),
        // USER_TYPE_MANAGER => __('department_approver'),
        // USER_TYPE_ADMIN_MANAGER => __('manager'),
        // USER_TYPE_FACILITY_EMPLOYEE => __('facility_employee'),
        USER_TYPE_SUB_ADMIN => __('admin'),
    ];
    return $types[$type] ?? '--';
}
function allUserTypesText($type = 0)
{
    $types = [
        USER_TYPE_ADMIN => __('admin'),
        USER_TYPE_RECEPTION => __('access_control'),
        USER_TYPE_APPROVER => __('section_approver'),
        USER_TYPE_MANAGER => __('manager'),
        USER_TYPE_COMPANY => __('company'),
        USER_TYPE_ADMIN_MANAGER => __('admin_manager'),
        USER_TYPE_FACILITY_EMPLOYEE => __('facility_employee'),
        USER_TYPE_SUPER_ADMIN => __('super_admin'),
        USER_TYPE_SUB_ADMIN => __('admin'),
    ];

    return $types[$type] ?? '--';
}

function ticketStatus($heading = true, $title = '')
{
    $title = empty($title) ? __('select') : $title;
    $types = [
        TICKET_OPEN => __('open'),
        TICKET_IN_PROGRESS => __('in_progress'),
        TICKET_CLOSED => __('closed'),
    ];

    if ($heading) {
        $types = ['' => $title] + $types;
    }
    return $types;
}


function ticketStatusText($type = 0)
{
    $types = [
        TICKET_OPEN => '<span class="badge bg-primary-subtle text-primary p-2">' . __('open') . '</span>',
        TICKET_IN_PROGRESS => '<span class="badge bg-success-subtle text-success p-2">' . __('in_progress') . '</span>',
        TICKET_CLOSED => '<span class="badge bg-danger-subtle text-danger p-2">' . __('closed') . '</span>',
    ];
    return $types[$type] ?? '--';
}

function adminId()
{
    return ADMIN_ID;
}

function authId()
{
    return Auth::user()->id;
}

function authDetail()
{
    return Auth::user();
}

function authUserType()
{
    return Auth::user()->user_type;
}

function authCompanyId()
{
    return Auth::user()->company_id;
}

function authName()
{
    return Auth::user()->name;
}

function authEmail()
{
    return Auth::user()->email;
}

function authPermissionChanged()
{
    return Auth::user()->permission_changed;
}

function authSectionId()
{
    return Auth::user()->section_id;
}

function authPermissionIds()
{
    return Auth::user()->permission_ids;
}

function authSectionIds()
{
    return Auth::user()->section_id;
}
function authSectionAccessType()
{
    return Auth::user()->section_access_type;
}

// reception permission code start
define('PERMISSION_PRE_REGISTRATION', 1);
define('PERMISSION_WALK_IN', 2);
define('PERMISSION_CHECK_INOUT', 3);
define('PERMISSION_BLOCK_VISITOR', 4);

function hasPermission($permission)
{
    $permissionIds = authPermissionIds();
    $permissionIds = explode(',', $permissionIds);
    return (bool) in_array($permission, $permissionIds);
}
// reception permission code end
function attributeTypes()
{
    return collect([
        'Text',
        'Textarea',
        'Number',
        'Date',
        'Email',
        'File',
        'Password',
        'Dropdown',
        'Radio',
        'Checkbox',
        'Heading',
        'Number'
    ])->mapWithKeys(function ($item) {
        return [$item => $item];
    })->toArray();
}

function jsonResponse($status, $statusCode, $message, $extra = [])
{
    $response = ['success' => $status, 'status' => $statusCode];
    if ($statusCode == 206) {
        $response['message'] = jsonErrors($message);
    } elseif ($statusCode == 207 || $statusCode == 201 || $statusCode == 204) {
        $response['message'] = $message;
    }

    $response['extra'] = $extra;
    return response()->json($response);
}
function jsonResponseError($e)
{
    if (in_array(env('APP_ENV'), ['local'])) {
        return response()->json([
            'status' => false,
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ], 500);
    } else {
        $response = ['success' => false, 'status' => 500];
        $response['message'] = 'internal server error';
        return response()->json($response);
    }
}

function jsonErrors($errors)
{
    $error = [];
    foreach ($errors->toArray() as $key => $value) {
        $error[$key] = $value[0];
    }
    return $error;
}

function dumper(...$dump)
{
    echo '<pre>';
    foreach ($dump as $d) {
        print_r($d);
        echo '<br/><br/>';
    }
    echo '</pre>';
    die;
}

function status($heading = true, $title = '')
{
    $active = __('active');
    $inactive = __('inactive');
    $title = empty($title) ? __('select') : $title;
    $types = [
        0 => $inactive,
        1 => $active,
    ];

    if ($heading) {
        $types = ['' => $title] + $types;
    }
    return $types;
}
function customFieldFor($heading = true, $title = '')
{
    $title = empty($title) ? __('select') : $title;
    $types = [
        1 => __('registration_details'),
        2 => __('personal_details'),
    ];

    if ($heading) {
        $types = ['' => $title] + $types;
    }
    return $types;
}

function statusText($type = 0)
{
    $active = __('active');
    $inactive = __('inactive');
    $types = [
        0 => '<span class="badge bg-danger-subtle text-danger p-2">' . $inactive . '</span>',
        1 => '<span class="badge bg-primary-subtle text-primary p-2">' . $active . '</span>',
    ];
    return $types[$type] ?? '--';
}
function fieldForText($type = 0)
{
    $types = [
        1 => __('registration_details'),
        2 => __('personal_details'),
    ];
    return $types[$type] ?? '--';
}

function deviceType($heading = true, $title = '')
{
    $mobile = __('mobile');
    $web = __('web');
    $title = empty($title) ? __('select') : $title;
    $types = [
        'mobile' => $mobile,
        'web' => $web,
    ];

    if ($heading) {
        $types = ['' => $title] + $types;
    }
    return $types;
}

function deviceTypeText($type = 0)
{
    $mobile = __('mobile');
    $web = __('web');

    $types = [
        'mobile' => '<span class="badge bg-success-subtle text-success p-2">' . $mobile . '</span>',
        'web' => '<span class="badge bg-info-subtle text-info p-2">' . $web . '</span>',
    ];
    return $types[$type] ?? '--';
}

function identificationTypes($heading = true, $title = '')
{
    $title = empty($title) ? __('select') : $title;
    $types = [
        1 => __('identification_qatar_id'),
        2 => __('identification_passport'),
        3 => __('driving_licence'),
    ];

    if ($heading) {
        $types = ['' => $title] + $types;
    }
    return $types;
}

function identificationCompareText($match)
{
    $types = [
        1 => 'Qatar ID',
        2 => 'Passport',
        3 => 'Driving Licence',
    ];
    return isset($types[$match]) ? $types[$match] : 'N/A';
}

function identificationTypesText($type = 0)
{
    $types = [
        1 => __('identification_qatar_id'),
        2 => __('identification_passport'),
        3 => __('identification_driving_licence'),
    ];
    return $types[$type] ?? '--';
}

define('SECTION_TYPE_MALE', 1);
define('SECTION_TYPE_FEMALE', 2);

function sectionTypes($heading = true, $title = '')
{

    $title = empty($title) ? __('select') : $title;
    $types = [
        SECTION_TYPE_MALE => __('male'),
        SECTION_TYPE_FEMALE => __('female'),
    ];

    if ($heading) {
        $types = ['' => $title] + $types;
    }
    return $types;
}

function sectionTypesText($type = 0)
{
    $types = [
        SECTION_TYPE_MALE => __('male'),
        SECTION_TYPE_FEMALE => __('female'),
    ];
    return $types[$type] ?? '--';
}

define('VISITOR_STATUS_PENDING', 1);
define('VISITOR_STATUS_LOCK', 2);
define('VISITOR_STATUS_REVISE', 3);

define('VISITOR_STATUS_CHECKED_IN', 4);
define('VISITOR_STATUS_CHECKED_OUT', 5);

function visitorStatus($heading = true, $title = '')
{

    $title = empty($title) ? __('select') : $title;
    $types = [
        VISITOR_STATUS_PENDING => 'Under Review',
        VISITOR_STATUS_LOCK => 'Submitted',
        VISITOR_STATUS_REVISE => 'Revise',
    ];

    if ($heading) {
        $types = ['' => $title] + $types;
    }
    return $types;
}

function visitorStatusText($type = 0)
{
    $types = [
        VISITOR_STATUS_PENDING => __('under_review'),
        VISITOR_STATUS_LOCK => __('submitted'),
        VISITOR_STATUS_REVISE => __('need_to_be_revised'),
    ];
    return $types[$type] ?? '--';
}

define('VISIT_PENDING', 1);
define('VISIT_DEPARTMENT_ACCEPTED', 2);
define('VISIT_DEPARTMENT_REJECTED', 3);

define('VISIT_APPROVER_ACCEPTED', 1);
define('VISIT_APPROVER_REJECTED', 2);

define('VISIT_MANAGER_ACCEPTED', 2);
define('VISIT_MANAGER_REJECTED', 3);

function managerStatusText($type = 0)
{
    $types = [
        VISIT_PENDING => __('under_review'),
        VISIT_MANAGER_ACCEPTED => __('approved'),
        VISIT_MANAGER_REJECTED => __('rejected'),
    ];
    return $types[$type] ?? '--';
}

function departmentStatusText($type = 0)
{
    $types = [
        VISIT_PENDING => __('under_review'),
        VISIT_DEPARTMENT_ACCEPTED => __('approved'),
        VISIT_DEPARTMENT_REJECTED => __('rejected'),
    ];
    return $types[$type] ?? '--';
}

function approverStatusText($type = 0)
{
    $types = [
        0 => 'Under Review',
        VISIT_APPROVER_ACCEPTED => __('approved'),
        VISIT_APPROVER_REJECTED => __('rejected'),
    ];
    return $types[$type] ?? '--';
}


function visitStatusText($type = 0)
{
    $types = [
        VISIT_PENDING => __('under_review'),
        VISIT_DEPARTMENT_ACCEPTED => __('approved'),
        VISIT_DEPARTMENT_REJECTED => __('rejected'),
        VISIT_APPROVER_ACCEPTED => __('approved'),
        VISIT_APPROVER_REJECTED => __('rejected'),
    ];
    return $types[$type] ?? '--';
}

define('CHECKED_IN', 1);
define('CHECKED_OUT', 2);

function checkedText($type = 0)
{
    $types = [
        CHECKED_IN => __('checked_in'),
        CHECKED_OUT => __('checked_out'),
    ];
    return $types[$type] ?? '--';
}

define('LONG_TERM_VISIT', 'long_term_visit');
define('SHORT_TERM_VISIT', 'short_term_visit');

function inOutTypes($heading = true, $title = '')
{

    $title = empty($title) ? __('select') : $title;
    $types = [
        SHORT_TERM_VISIT => __('short_term_visit'),
        LONG_TERM_VISIT => __('long_term_visit'),
    ];

    if ($heading) {
        $types = ['' => $title] + $types;
    }
    return $types;
}

function inOutTypesText($type = 0)
{
    $types = [
        SHORT_TERM_VISIT => __('short_term_visit'),
        LONG_TERM_VISIT => __('long_term_visit'),
    ];
    return $types[$type] ?? '--';
}

define('PURPOSE_MEETING', 1);
define('PURPOSE_CONTRACTOR', 2);
define('PURPOSE_OTHERS', 3);

function purposeOfVisit($heading = true, $title = '')
{

    $title = empty($title) ? __('select') : $title;
    $types = [
        PURPOSE_MEETING => __('meeting'),
        PURPOSE_CONTRACTOR => __('contractor'),
        PURPOSE_OTHERS => __('others'),
    ];

    if ($heading) {
        $types = ['' => $title] + $types;
    }
    return $types;
}

function purposeOfVisitText($type = 0)
{
    $types = [
        PURPOSE_MEETING => __('meeting'),
        PURPOSE_CONTRACTOR => __('contractor'),
        PURPOSE_OTHERS => __('others'),
    ];
    return $types[$type] ?? '--';
}

define('ZONE_BLACK', 1);
define('ZONE_BLUE', 2);
define('ZONE_GREEN', 3);
define('ZONE_YELLOW', 4);

function zoneTypes($heading = true, $title = '')
{

    $title = empty($title) ? __('select') : $title;
    $types = [
        ZONE_BLACK => __('black'),
        ZONE_BLUE => __('blue'),
        ZONE_GREEN => __('green'),
        ZONE_YELLOW => __('yellow'),
    ];

    if ($heading) {
        $types = ['' => $title] + $types;
    }
    return $types;
}

function zoneTypesText($type = 0)
{

    $types = [
        ZONE_BLACK => __('black'),
        ZONE_BLUE => __('blue'),
        ZONE_GREEN => __('green'),
        ZONE_YELLOW => __('yellow'),
    ];

    $response = '';
    $zoneTypes = !empty($type) ? explode(',', $type) : [];
    if (isset($zoneTypes) && count($zoneTypes) > 0) {
        foreach ($zoneTypes as $zt) {
            $response .= $types[$zt] . ', ';
        }
        $response = rtrim($response, ', ');
    } else {
        $response = '--';
    }
    return $response;
}

function zoneTypesTextShort($type = 0)
{

    $types = [
        ZONE_BLACK => __('black_short'),
        ZONE_BLUE => __('blue_short'),
        ZONE_GREEN => __('green_short'),
        ZONE_YELLOW => __('yellow_short'),
    ];

    $response = '';
    $zoneTypes = !empty($type) ? explode(',', $type) : [];
    if (isset($zoneTypes) && count($zoneTypes) > 0) {
        foreach ($zoneTypes as $zt) {
            $response .= $types[$zt] . ', ';
        }
        $response = rtrim($response, ', ');
    } else {
        $response = '--';
    }
    return $response;
}

function yesNo($title = '', $heading = false)
{
    if (empty($title)) {
        $title = __('select');
    }

    $types = [
        0 => __('no'),
        1 => __('yes'),
    ];

    if ($heading) {
        $types = ['' => $title] + $types;
    }
    return $types;
}

function yesNoText($type = null)
{
    return (isset($type) && $type == 1) ? __('yes') : __('no');
}

function uploadFileFrCustomFields($file, $path = 'uploads/', $oldFile = '')
{
    $uploadFile = null;

    if ($file && $file->isValid()) {
        $extension = $file->getClientOriginalExtension();
        $filename = Str::random(8) . time() . '.' . $extension;

        $movePath = public_path($path);
        if (!file_exists($movePath)) {
            mkdir($movePath, 0755, true);
        }

        if ($file->move($movePath, $filename)) {
            $uploadFile = $filename;
        }

        if (!empty($oldFile)) {
            $oldFilePath = public_path($path . $oldFile);
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath);
            }
        }
    }

    return $uploadFile;
}


function uploadFile($name, $path = 'uploads/', $oldFile = '')
{
    $uploadFile = null;
    $request = request();
    if ($request->hasFile($name)) {
        $file = $request->file($name);
        $extension = $file->getClientOriginalExtension();
        $filename = Str::random(8) . time() . '.' . $extension;

        $movePath = public_path($path);
        if ($file->move($movePath, $filename)) {
            $uploadFile = $filename;
        }

        if (!empty($oldFile)) {
            $oldFilePath = public_path($path . $oldFile);
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath);
            }
        }
    }
    return $uploadFile;
}

function uploadMultipleFile($name, $path = 'uploads/', $withFullPath = false)
{
    $uploadFiles = [];
    $request = request();
    if ($request->hasFile($name)) {
        $files = $request->file($name);
        $path = public_path($path);
        foreach ($files as $i => $file) {
            $extension = $file->getClientOriginalExtension();
            $filename = Str::random(8) . time() . '.' . $extension;

            if ($file->move($path, $filename)) {
                $uploadFiles[$i] = ($withFullPath) ? ($path . $filename) : $filename;
            }
        }
    }
    return $uploadFiles;
}

function removeImg($img, $path)
{
    $file = public_path($path . $img);
    if ($img != '' && file_exists($file)) {
        unlink($file);
    }
}

function srNo($count = 0, $prefix = '', $length = 4)
{
    $count++;
    return $prefix . str_pad($count, $length, 0, STR_PAD_LEFT);
}

function defaultPerPage()
{
    return 10;
}

function pageDetail($pagination)
{
    $from = ((($pagination->currentPage() - 1) * $pagination->perPage()) + 1);
    $to = (($pagination->currentPage()) * $pagination->perPage());
    $total = $pagination->total();
    if ($to > $total) {
        $to = $pagination->total();
    }
    return "<p class='mb-0 text-muted'>" . __('admin.showing') . "  <b>" . $from . "</b> " . __('admin.to') . " <b>" . $to . "</b> " . __('admin.of') . " <b>" . $total . "</b> " . __('admin.results') . "</p>";
}

function pageIndex($pagination)
{
    return (($pagination->currentPage() - 1) * $pagination->perPage()) + 1;
}

function perPage()
{
    return [
        10 => 10,
        20 => 20,
        50 => 50,
        100 => 100,
        500 => 500
    ];
}

function sorting($sortEntity, $name, $sortOrder, $sortEnt = '')
{
    $fa = 'bx bx-sort';

    if ($sortEntity == $sortEnt) {
        if ($sortOrder == 'asc') {
            $fa = 'bx bx-sort-down';
        } else {
            $fa = 'bx bx-sort-up';
        }
    }

    return '<a href="javascript:void(0);" data-sortEntity="' . $sortEntity . '" data-sortOrder="' . ($sortOrder == 'desc' ? 'asc' : 'desc') . '">
        ' . $name . ' <i class="' . $fa . '"></i> </a>';
}

function image($img, $path = 'uploads/', $default = 'default.png')
{
    $image = asset('uploads/' . $default);
    $file = public_path($path . $img);
    if ($img != '' && file_exists($file)) {
        $image = asset($path . $img);
    }

    return $image;
}
function imagePreReg($img, $path = 'uploads/', $default = 'logo-dark.png')
{
    $image = asset('admins/images/' . $default);
    $file = public_path($path . $img);
    if ($img != '' && file_exists($file)) {
        $image = asset($path . $img);
    }

    return $image;
}

function apiResponse($status, $message, $data = null, $errors = false)
{
    if ($errors) {
        $message = Arr::first($message->toArray())[0];
    }

    $response = [
        'status' => (int) $status ?? 0,
        'message' => (string) $message ?? '',
        'data' => $data
    ];
    if (!is_null($data)) {
        $response['data'] = $data;
    }
    return response()->json($response);
}

function apiPaginationResponse($status, $message, $data, $errors = false)
{
    if ($errors) {
        $message = Arr::first($message->toArray())[0];
    }

    $makeData = [
        'status' => (bool) $status ?? false,
        'message' => (string) $message ?? '',
        'next_page_url' => null
    ];

    if (empty($data)) {
        $makeData['data'] = (array) [];
    }

    $response = collect($makeData)->merge($data);
    return response()->json($response);
}

function errors($errors)
{
    $result = [];
    if (isset($errors) && count($errors) > 0) {
        foreach ($errors->toArray() as $key => $error) {
            $result[] = ['field' => $key, 'error' => $error[0]];
        }
    }
    return $result;
}

function apiImg($img, $path, $default = 'uploads/default.png')
{
    $image = asset($default);
    $file = public_path($path . $img);
    if ($img != '' && file_exists($file)) {
        $image = asset($path . $img);
    }
    return $image;
}

function roundOff($number)
{
    $n1 = (int) $number ?? 0;
    $n2 = (float) $number ?? 0;

    if ($n2 > $n1) {
        $number = round($n2, 2);
    } else {
        $number = (int) $n1;
    }
    return (string) $number;
}

function arrayPaginator($array, $request, $perPage = 20)
{
    $page = $request->get('page', 1);
    $offset = ($page * $perPage) - $perPage;

    return new LengthAwarePaginator(
        array_values(array_slice($array, $offset, $perPage, true)),
        count($array),
        $perPage,
        $page,
        [
            'path' => $request->url(),
            'query' => $request->query()
        ]
    );
}

function replaceSpecialChar($str = '')
{
    $unwanted_array = array(
        'Š' => 'S',
        'š' => 's',
        'Ž' => 'Z',
        'ž' => 'z',
        'À' => 'A',
        'Á' => 'A',
        'Â' => 'A',
        'Ã' => 'A',
        'Ä' => 'A',
        'Å' => 'A',
        'Æ' => 'A',
        'Ç' => 'C',
        'È' => 'E',
        'É' => 'E',
        'Ê' => 'E',
        'Ë' => 'E',
        'Ì' => 'I',
        'Í' => 'I',
        'Î' => 'I',
        'Ï' => 'I',
        'Ñ' => 'N',
        'Ò' => 'O',
        'Ó' => 'O',
        'Ô' => 'O',
        'Õ' => 'O',
        'Ö' => 'O',
        'Ø' => 'O',
        'Ù' => 'U',
        'Ú' => 'U',
        'Û' => 'U',
        'Ü' => 'U',
        'Ý' => 'Y',
        'Þ' => 'B',
        'ß' => 'Ss',
        'à' => 'a',
        'á' => 'a',
        'â' => 'a',
        'ã' => 'a',
        'ä' => 'a',
        'å' => 'a',
        'æ' => 'a',
        'ç' => 'c',
        'è' => 'e',
        'é' => 'e',
        'ê' => 'e',
        'ë' => 'e',
        'ì' => 'i',
        'í' => 'i',
        'î' => 'i',
        'ï' => 'i',
        'ð' => 'o',
        'ñ' => 'n',
        'ò' => 'o',
        'ó' => 'o',
        'ô' => 'o',
        'õ' => 'o',
        'ö' => 'o',
        'ø' => 'o',
        'ù' => 'u',
        'ú' => 'u',
        'û' => 'u',
        'ý' => 'y',
        'þ' => 'b',
        'ÿ' => 'y'
    );
    return strtr($str, $unwanted_array);
}

function clean($string = '')
{
    return trim(preg_replace('/\r\n/', '', $string));
}

function dateFormat($date, $format, $default = '')
{
    return (!empty($date)) ? date($format, strtotime($date)) : $default;
}

function isValidTime($startTime, $endTime)
{
    $response = true;
    $startTime = strtotime($startTime);
    $endTime = strtotime($endTime);
    $difference = (int) ($endTime - $startTime) / 60;
    if ($difference <= 0) {
        $response = false;
    }
    return $response;
}

function calPercentage($portion, $total)
{
    if ($total > 0) {
        return ($portion / $total) * 100;
    }
    return 0;
}

function adminFetch()
{
    $adminId = adminId();
    return (new User)->fetch($adminId);
}

function authFetch()
{
    return Auth::user();
}

function gender($heading = true, $type = '', $title = '')
{
    if (empty($title)) {
        $title = __('select');
    }

    $types = [
        'male' => __('male'),
        'female' => __('female'),
    ];

    if ($type != '') {
        return $types[$type];
    }

    if ($heading) {
        $types = ['' => $title] + $types;
    }
    return $types;
}

function wordsLimit($text, $limit = 10, $removeHtml = true)
{
    if ($removeHtml) {
        $text = strip_tags($text);
    }

    if (str_word_count($text, 0) > $limit) {
        $words = str_word_count($text, 2);
        $pos = array_keys($words);
        $text = substr($text, 0, $pos[$limit]) . '...';
    }
    return $text;
}

function isValidNumber($dialCode, $dialCodeISO, $mobileNumber)
{
    $phoneUtil = PhoneNumberUtil::getInstance();
    try {
        $fullMobileNumber = $dialCode . $mobileNumber;
        $swissNumberProto = $phoneUtil->parse($fullMobileNumber, $dialCodeISO);
        $isValid = $phoneUtil->isValidNumber($swissNumberProto);
        if ($isValid) {
            return true;
        }
    } catch (NumberParseException $e) {
        return false;
    }
    return false;
}

function isSuperAdmin()
{
    return (Auth::user()->user_type == USER_TYPE_SUPER_ADMIN);
}

function isAdmin()
{
    return (Auth::user()->user_type == USER_TYPE_ADMIN);
}

function isSubAdmin()
{
    return (Auth::user()->user_type == USER_TYPE_SUB_ADMIN);
}

function isAdminManager()
{
    return (Auth::user()->user_type == USER_TYPE_ADMIN_MANAGER);
}

function isReception()
{
    return (Auth::user()->user_type == USER_TYPE_RECEPTION);
}

function isApprover()
{
    return (Auth::user()->user_type == USER_TYPE_APPROVER);
}

function isDepartmentApprover()
{
    return (Auth::user()->user_type == USER_TYPE_MANAGER);
}

function isCompany()
{
    return (Auth::user()->user_type == USER_TYPE_COMPANY);
}

function buildTree($elements, $parentId = 0)
{
    $branch = [];
    foreach ($elements as $element) {
        if ($element['parent_id'] == $parentId) {
            $children = buildTree($elements, $element['id']);
            if ($children) {
                $element['children'] = $children;
            }

            if (!isset($element['children'])) {
                $element['children'] = [];
            }
            $branch[] = $element;
        }
    }
    return $branch;
}

function authRoleId()
{
    return Auth::user()->role_id;
}

function getMenu()
{
    $layout = [];
    $search = [
        'company_id' => authCompanyId(),
        'role_id' => authRoleId(),
        'show_in_sidebar' => 1,
    ];

    $response = (new Role)->getMenu($search);
    if (isset($response) && count($response) > 0) {
        $layout = buildTree($response->toArray());
    }
    return $layout;
}

function setPermission()
{
    $permissions = [];
    $search = [
        'company_id' => authCompanyId(),
        'role_id' => authRoleId(),
        'not_empty_route' => 1,
    ];

    $response = (new Role)->getMenu($search);
    if (isset($response) && count($response) > 0) {
        $permissions = array_column($response->toArray(), 'route_name');

        $dependentPermissions = array_filter(array_column($response->toArray(), 'dependent_route'));
        if (isset($dependentPermissions) && count($dependentPermissions) > 0) {
            foreach ($dependentPermissions as $dep) {
                $exp = explode(',', $dep);
                $permissions = array_merge($permissions, $exp);
            }
        }
    }

    session()->put('permissions', $permissions);

    $authId = authId();
    (new User)->updateUserPermission($authId);

    return $permissions;
}

function checkPermission($routeName)
{
    if (isAdmin())
        return true;

    $permissions = session('permissions', []);
    if (authPermissionChanged() == 1) {
        $permissions = setPermission();
    }
    return in_array($routeName, $permissions) ? true : false;
}

function hasAccess($routeName)
{
    if (isAdmin())
        return true;

    if (isSubAdmin()) {
        return (new Role)->hasAccess($routeName, authRoleId());
    }
    return false;
}

function stockType($heading = true, $title = '')
{
    $in = __('in');
    $out = __('out');
    $title = empty($title) ? __('select') : $title;
    $types = [
        'in' => $in,
        'out' => $out,
    ];

    if ($heading) {
        $types = ['' => $title] + $types;
    }
    return $types;
}

function stockTypeText($type = 0)
{
    $in = __('in');
    $out = __('out');
    $types = [
        'in' => '<span class="badge bg-primary-subtle text-primary p-2 text-uppercase">' . $in . '</span>',
        'out' => '<span class="badge bg-danger-subtle text-danger p-2 text-uppercase">' . $out . '</span>',
    ];
    return $types[$type] ?? '--';
}

function hasDuplicate($array = [])
{
    return count($array) != count(array_unique($array));
}

function downloadPdf($filename, $compactData = [], $downloadFileName = 'download.pdf', $viewOnly = false, $saveFile = false, $landscapeMode = false, $landscape = '', $pdfPath = PDF_PATH)
{
    $pdf = \PDF::loadView($filename, $compactData);
    if ($landscapeMode) {
        // $pdf->setPaper([0,0,175.68,282.96], 'landscape');
        $pdf->setPaper([0, 0, 165.6, 244.8], 'landscape');
    }

    if ($landscape) {
        $pdf->setPaper('a4', 'landscape');
    }

    if ($saveFile) {
        $publicPath = public_path($pdfPath);
        if (!\File::isDirectory($publicPath)) {
            \File::makeDirectory($publicPath, 0777, true, true);
        }
        $path = $publicPath . $downloadFileName;
        $pdf->save($path);
        return $path;
    }

    if ($viewOnly) {
        return $pdf->stream();
    }
    return $pdf->download($downloadFileName);
}

function downloadPdfA4($filename, $compactData = [], $downloadFileName = 'download.pdf', $viewOnly = false, $saveFile = false, $landscapeMode = false, $pdfPath = PDF_PATH)
{
    $pdf = \PDF::loadView($filename, $compactData);

    // ✅ Always A4
    $pdf->setPaper('a4', $landscapeMode ? 'landscape' : 'portrait');

    if ($saveFile) {
        $publicPath = public_path($pdfPath);
        if (!\File::isDirectory($publicPath)) {
            \File::makeDirectory($publicPath, 0777, true, true);
        }
        $path = $publicPath . $downloadFileName;
        $pdf->save($path);
        return $path;
    }

    if ($viewOnly) {
        return $pdf->stream();
    }

    return $pdf->download($downloadFileName);
}


function numberOfVisitor()
{
    $types = [];
    for ($i = 1; $i <= 50; $i++) {
        $types[$i] = $i;
    }
    return $types;
}

function numberOfDevices()
{
    $types = [];
    for ($i = 1; $i <= 100; $i++) {
        $types[$i] = $i;
    }
    return $types;
}

function isImage($file, $path = 'uploads/')
{
    $response = false;
    $filePath = public_path($path . $file);
    $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif'])) {
        $response = true;
    }
    return $response;
}

function prepareDropdown($name, $data = [], $default = '', $class = '')
{
    $class = !empty($class) ? $class : 'form-select';
    $select = '<select name="' . $name . '" class="' . $class . '">';
    if (isset($data) && count($data) > 0) {
        foreach ($data as $k => $row) {
            $attr = '';
            $text = $row;
            if (is_array($row)) {
                foreach ($row as $ak => $av) {
                    $attr .= $ak . '="' . $av . '"';
                }
                $text = $row['text'] ?? '';
            } else {
                $attr = 'value=""';
            }

            $selected = ($k == $default) ? 'selected="selected"' : '';
            $select .= '<option ' . $attr . ' ' . $selected . '>' . $text . '</option>';
        }
    }

    $select .= '</select>';
    return $select;
}

function sendMailBkp($view, $subject, $to, $data = [], $lang = 'en', $attachment = '', $cc = '')
{
    $response = false;
    // return view($view, compact('data'))->render();
    try {
        if (!in_array($lang, ['en', 'sv'])) {
            $lang = 'en';
        }

        app()->setLocale($lang);

        Mail::send($view, compact('data'), function ($message) use ($subject, $to, $cc, $attachment) {
            $message->to($to)->subject($subject);
            if (!empty($attachment)) {
                if (is_array($attachment)) {
                    foreach ($attachment as $att) {
                        $message->attach($att);
                    }
                } else {
                    $message->attach($attachment);
                }
            }
            if (!empty($cc)) {
                $message->cc($cc); // Add CC recipient
            }
        });

        if (count(Mail::failures()) == 0) {
            $response = true;
        }
        return $response;
    } catch (\Exception $e) {
        dd($e->getMessage());
        return $response;
    }
}

function sendMail($view, $subject, $to = '', $data = [], $lang = 'en', $attachment = '', $cc = '')
{
    $response = false;
    // return view($view, compact('data'));

    try {
        if (!empty($to)) {
            $hasPermission = true;
            $viewArray = [
                'email.block-user' => 'permission_block_user',
                'email.walk-in-visit' => 'permission_walk_in_visit',
                'email.accept-reject-visit' => 'permission_accept_reject_visit',
                'email.revise-visit' => 'permission_revise_visit',
                'email.employee-revoked' => 'permission_employee_revoked',
                'email.company-visit' => 'permission_company_visit',
                'email.contractor-revised-visit' => 'permission_contractor_revised_visit',
                'email.contract-revoked' => 'permission_contract_revoked',
            ];

            $result = (new User)->findByEmail($to);
            $permissionName = $viewArray[$view] ?? '';
            if ($result && !empty($permissionName)) {
                $hasPermission = (bool) $result->$permissionName ?? true;
            }

            if ($view == 'admin.contact.email') {
                $hasPermission = true;
            }

            // lang code start
            if (!in_array($lang, ['en', 'sv'])) {
                $lang = 'en';
            }
            app()->setLocale($lang);
            // lang code end

            if ($hasPermission) {
                Mail::send($view, compact('data'), function ($message) use ($subject, $to, $cc, $attachment) {
                    $message->to($to)->subject($subject);
                    if (!empty($attachment)) {
                        if (is_array($attachment)) {
                            foreach ($attachment as $att) {
                                $message->attach($att);
                            }
                        } else {
                            $message->attach($attachment);
                        }
                    }
                    if (!empty($cc)) {
                        $message->cc($cc); // Add CC recipient
                    }
                });

                if (count(Mail::failures()) == 0) {
                    $response = true;
                }
            }
        }

        return $response;
    } catch (\Exception $e) {
        // dd($e->getMessage(), $e->getFile(), $e->getLine());
        return $response;
    }
}

function toolText($result, $default = '')
{
    $response = '';
    $tools = explode(',', $result->tools);
    if (isset($tools) && count($tools) > 0) {
        foreach ($tools as $tool) {
            $tool = trim($tool);
            if ($tool == 'Others') {
                $response .= $tool . ' (' . $result->other_tools . '), ';
            } else {
                $response .= $tool . ', ';
            }
        }
    }
    $response = rtrim($response, ', ');
    return !empty($response) ? $response : $default;
}

function getUnreadNotification()
{
    return (new BlockActivity)->getUnreadNotification();
}

function getReceptionNotification()
{
    return (new VisitorDetail)->getReceptionNotification();
}

function friendlyTime($timestamp)
{
    $timestamp = strtotime($timestamp);
    $current_time = time();
    $time_diff = $current_time - $timestamp;
    $seconds = $time_diff;
    $minutes = round($seconds / 60);
    $hours = round($seconds / 3600);
    $days = round($seconds / 86400);
    $weeks = round($seconds / 604800);
    $months = round($seconds / 2629440);
    $years = round($seconds / 31553280);

    if ($seconds <= 60) {
        return __('just_now');
    } elseif ($minutes <= 60) {
        if ($minutes == 1) {
            return __('one_minute_ago');
        } else {
            return $minutes . ' ' . __('minute_ago');
        }
    } elseif ($hours <= 24) {
        if ($hours == 1) {
            return __('an_hour_ago');
        } else {
            return $hours . ' ' . __('hours_ago');
        }
    } elseif ($days <= 7) {
        if ($days == 1) {
            return __('yesterday');
        } else {
            return $days . ' ' . __('days_ago');
        }
    } elseif ($weeks <= 4.3) {
        if ($weeks == 1) {
            return __('a_week_ago');
        } else {
            return $weeks . ' ' . __('weeks_ago');
        }
    } elseif ($months <= 12) {
        if ($months == 1) {
            return __('a_month_ago');
        } else {
            return $months . ' ' . __('months_ago');
        }
    } else {
        if ($years == 1) {
            return __('a_year_ago');
        } else {
            return $years . ' ' . __('years_ago');
        }
    }
}

function getTimeDifference($datetime1, $datetime2, $format = '%H:%I:%S')
{
    // Create DateTime objects from the strings
    $date1 = new DateTime($datetime1);
    $date2 = new DateTime($datetime2);

    // Calculate the time difference
    $timeDiff = $date2->diff($date1);
    $day = ($timeDiff->d > 0) ? $timeDiff->d . 'd ' : '';

    return $day . $timeDiff->format($format);
}

/* function downloadExcel($filename, $data = [])
{
    ob_start();
    $xlsx = new SimpleXLSXGen;
    $xlsx->addSheet($data);
    $xlsx->downloadAs($filename);
    exit(0);
    ob_end_flush();
} */



function downloadExcel($filename, $data = [])
{
    // Fully clear all previous output buffers
    while (ob_get_level()) {
        ob_end_clean();
    }

    // Detect file extension
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if ($ext === 'csv') {
        // --- CSV Export ---
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');

        $output = fopen('php://output', 'w');
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        fclose($output);
    } else {
        // --- Excel Export ---
        $xlsx = SimpleXLSXGen::fromArray($data);
        $xlsx->downloadAs($filename);
    }

    exit;
}



function generateToken()
{
    return \Str::random(64);
}

function apiUserId()
{
    return API_USER_ID;
}

function apiToken()
{
    return API_TOKEN;
}

function apiCompanyId()
{
    return API_COMPANY_ID;
}

function apiSectionId()
{
    return API_SECTION_ID;
}
function apiSectionAccessType()
{
    return API_SECTION_ACCESS_TYPE;
}

function apiUserAllowOCR()
{
    return API_ALLOW_OCR;
}

function errorLog($from, $exception, $request, $saveInDB = false)
{
    $requestData = json_encode($request->all());
    $token = $request->header('token') ?? $_REQUEST['token'] ?? '';
    $device = $request->header('device') ?? $_REQUEST['device'] ?? '';
    $url = $request->url();
    $ip = $request->ip();
    $ignore = ['jpg', 'jpeg', 'css', 'js', 'png', 'map'];
    $extension = pathinfo($url, PATHINFO_EXTENSION);
    if (in_array($extension, $ignore)) {
        return true;
    }

    if ($saveInDB) {
        $message = ['Message => ' . $exception . ', Url => ' . $url . ' IP-Address => ' . $ip . ',RequestData => ' . $requestData . ',token' => $token];
        DB::table('logs')->insert([
            'log' => json_encode($message),
            'created_at' => date(DB_DATETIME)
        ]);
    } else {
        \Log::error($from . ' >>>>>>', ['Message => ' . $exception . ', Url => ' . $url . ' IP-Address => ' . $ip . ' Device => ' . $device . ',RequestData => ' . $requestData . ',token' => $token]);
    }
}

function generateQrCode($qrCode, $path, $size = 7, $margin = 2)
{
    require_once(app_path() . '/Support/phpqrcode/qrlib.php');

    $qrCode = (string) $qrCode;
    $qrImage = $qrCode . '.png';

    $publicPath = public_path($path);
    if (!\File::isDirectory($publicPath)) {
        \File::makeDirectory($publicPath, 0777, true, true);
    }

    \QRcode::png($qrCode, $publicPath . $qrImage, QR_ECLEVEL_L, $size, $margin);
}

function generateQrCodeBase64($qrCode, $size = 7, $margin = 2)
{
    require_once(app_path() . '/Support/phpqrcode/qrlib.php');

    ob_start();
    \QRcode::png($qrCode, null, QR_ECLEVEL_L, $size, $margin); // null = output to buffer
    $imageData = ob_get_contents();
    ob_end_clean();

    return 'data:image/png;base64,' . base64_encode($imageData);
}


function shortText($text, $len = 60)
{
    $response = (strlen($text) > $len) ? substr($text, 0, $len) . '...' : $text;
    return $response;
}

function blockDetail($companyId, $qidNumber)
{
    return (new BlockUser)->findByQid($companyId, $qidNumber);
}

function getBlueColorNotification($date, $time, $defaultColor = '', $myColor = '')
{
    $date1 = $date . ' ' . $time;
    $date2 = date(DB_DATETIME);

    $timestamp1 = strtotime($date1);
    $timestamp2 = strtotime($date2);

    // Check if the difference is greater than 24 hours (86400 seconds)
    $diffInSeconds = $timestamp2 - $timestamp1;
    if ($diffInSeconds > 86400) {
        $defaultColor = !empty($myColor) ? $myColor : '#dceffd';
    }
    return $defaultColor;
}

function checkInPeriods($heading = true)
{
    $types = [
        1800 => __('30_mins'),
        3600 => __('1_hour'),
        5400 => __('1_hour_30_mins'),
        7200 => __('2_hours'),
        9000 => __('2_hours_30_mins'),
        10800 => __('3_hours'),
        12600 => __('3_hours_30_mins'),
        14400 => __('4_hours'),
        16200 => __('4_hours_30_mins'),
        18000 => __('5_hours'),
    ];

    if ($heading) {
        $types = ['' => __('select')] + $types;
    }
    return $types;
}

function getDeviceId()
{
    if (!isset($_COOKIE['device_id'])) {
        // Generate a unique ID using uniqid() function
        $deviceId = uniqid() . time();

        // Set the cookie 'unique_id' to expire in 20 years (20 * 365 * 24 * 60 * 60)
        setcookie("device_id", $deviceId, time() + (20 * 365 * 24 * 60 * 60)); // 20 years
    } else {
        // Retrieve the unique number from the cookie
        $deviceId = $_COOKIE['device_id'];
    }
    return (new Device)->getUniqueNumber($deviceId, 'web');
}

function sendNotification($fcmIds, $title, $body, $payload = [])
{
    if (empty($fcmIds)) {
        return false;
    }

    $jsonData = [
        'message' => [
            'token' => $fcmIds,
            'notification' => [
                'title' => $title,
                'body' => $body
            ],
            // 'data' => $payload,
        ]
    ];

    if (!empty($payload)) {
        array_walk_recursive($payload, function (&$item) {
            if (is_int($item)) {
                $item = (string) $item;
            }
        });
        $jsonData['message']['data'] = $jsonData['message']['notification'] + $payload;
    }

    return sendNotificationCurl($jsonData);
}

function sendNotificationCurl($jsonData)
{
    $accessToken = getAccessToken();
    if (!empty($accessToken)) {
        $projectId = FCM_PROJECT_ID;
        $url = 'https://fcm.googleapis.com/v1/projects/' . $projectId . '/messages:send';

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($jsonData));

        $result = curl_exec($ch);

        if ($result === FALSE) {
            $error = curl_error($ch);
            curl_close($ch);
            // return ['success' => false, 'error' => $error];
            return false;
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            // return json_decode($result, true);
            return true;
        } else {
            // return ['error' => 'HTTP Error ' . $httpCode, 'response' => $result];
            return false;
        }
    } else {
        // return ['error' => 'Unable to generate access token'];
        return false;
    }
}

function getAccessToken()
{
    // Get the OAuth 2.0 access token
    $keyFilePath = app_path('firebase-service-account.json'); // Path to your JSON key file
    $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];

    $credentials = new ServiceAccountCredentials($scopes, $keyFilePath);
    $token = $credentials->fetchAuthToken();

    return $token['access_token'] ?? '';
}

function getDataTableLang()
{
    $lang = app()->getLocale();
    $lang_array = [
        'en' => asset('datatable_en.json'),
        'sv' => asset('datatable_ar.json'),
    ];
    return $lang_array[$lang];
}

function getLangDir()
{
    $lang = app()->getLocale();
    return ($lang == 'sv') ? 'rtl' : 'ltr';
}

function getLang()
{
    $lang = app()->getLocale();
    return ($lang == 'sv') ? 'sv' : 'en';
}

function isArabic()
{
    $lang = app()->getLocale();
    return ($lang == 'sv') ? true : false;
}

function getLangSymbol()
{
    $lang = app()->getLocale();
    return ($lang == 'sv') ? '&nbsp;&nbsp; ع &nbsp;&nbsp;' : 'ENG';
}

function getSubscriptionInfoBkp()
{

    $response = 'N/A';
    $companyId = authCompanyId();
    $result = (new Subscription)->getCurrentSubscription($companyId);
    if ($result) {
        $response = dateFormat($result->start_date, DISPLAY_DATE3) . ' - ' . dateFormat($result->end_date, DISPLAY_DATE3);
    }
    return $response;
}

function getSubscriptionInfo()
{
    $response = [
        'title' => __('subscription_detail'),
        'date' => 'N/A',
    ];

    $companyId = authCompanyId();
    $result = (new Subscription)->getCurrentSubscription($companyId);
    if ($result) {
        if (!empty($result->name)) {
            $response['title'] = __('subscription') . ' : ' . $result->name;
        }
        $response['date'] = dateFormat($result->start_date, DISPLAY_DATE3) . ' - ' . dateFormat($result->end_date, DISPLAY_DATE3);
    }
    return $response;
}

function getInitials($name = '')
{
    $initials = '';
    $words = explode(' ', trim($name));
    if (isset($words) && count($words) > 0) {
        foreach ($words as $word) {
            $initials .= strtoupper($word[0]);
            if (strlen($initials) >= 2)
                break;
        }
    }
    return $initials;
}

function fetchDevices($devices)
{
    return (new CompanyDevice)->fetchDevices($devices);
}
function verifyFirebaseToken(string $idToken)
{
    $projectId = config('services.firebase.project_id');

    $keys = Cache::remember('firebase_public_keys', 3600, function () {
        return Http::get(
            'https://www.googleapis.com/robot/v1/metadata/x509/securetoken@system.gserviceaccount.com'
        )->json();
    });

    $decoded = JWT::decode($idToken, JWK::parseKeySet($keys));

    if ($decoded->aud !== $projectId) {
        throw new Exception('Invalid Firebase audience');
    }

    return $decoded;
}
function statusSlider($route, $id, $check)
{
    $checked = ($check) ? 'checked="checked"' : '';
    return '<label class="switch">' .
        '<input type="checkbox" class="__status" ' . $checked . ' data-route="' . route($route, $id) . '">' .
        '<span class="slider round"></span>' .
        '</label>';
}
function orderStatus()
{
    return [
        0 => __('admin.pending'),
        1 => __('admin.ordered'),
        2 => __('admin.shipped'),
        3 => __('admin.delivered'),
        4 => __('admin.cancelled'),
        5 => __('admin.returned')


    ];
}
function getPromoTypeText($type)
{
    $ptype = [
        1 => __('admin.percentage'),
        2 => __('admin.flat')
    ];
    return $ptype[$type];
}
