<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Models\TicketModel as Tickets;
use App\Models\TicketFollowup;
use App\Models\TicketAssignee;
use App\Models\EmployeeList;
use App\Models\TicketStatus;
class Ticket extends Controller
{
    public function index()
    {

    }

    public function addTicket(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'subject_name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'discription' => 'required|string',
                'ticket_type_id' => 'required|integer',
                'ticket_status_id' => 'required|integer',
                'assignee_id' => 'required|integer'
            ]);
            $datacreate = [
                'subject_name' => $validatedData['subject_name'],
                'email_id' => $validatedData['email'],
                'discription' => $validatedData['discription'],
                'ticket_type_id' => $validatedData['ticket_type_id'],
                'ticket_status_id' => $validatedData['ticket_status_id'],
                'created_by' => session('userData')['client_index'],
            ];
            DB::beginTransaction();
            $ticket = Tickets::create([
                'subject_name' => $validatedData['subject_name'],
                'email_id' => $validatedData['email'],
                'discription' => $validatedData['discription'],
                'ticket_type_id' => $validatedData['ticket_type_id'],
                'ticket_status_id' => $validatedData['ticket_status_id'],
                'created_by' => session('userData')['client_index'],
            ]);
           
$dataassign = [
    'ticket_id' => $ticket->id,
                'assignee' => $validatedData['assignee_id'],
                'assigned_by' => session('userData')['client_index'],
];
            TicketAssignee::create([
                'ticket_id' => $ticket->id,
                'assignee' => $validatedData['assignee_id'],
                'assigned_by' => session('userData')['client_index'],
            ]);
                       

                        $datafollowup = [
                            'ticket_id' => $ticket->id,
                'remarks' => 'Ticket Created',
                'status' => $validatedData['ticket_status_id'],
                'assignee' => $validatedData['assignee_id'],
                'user_type' => 'admin',
                'admin_id' => session('userData')['client_index'],
                        ];
            TicketFollowup::create([
                'ticket_id' => $ticket->id,
                'remarks' => 'Ticket Created',
                'status' => $validatedData['ticket_status_id'],
                'assignee' => $validatedData['assignee_id'],
                'user_type' => 'admin',
                'admin_id' => session('userData')['client_index'],
            ]);
                                    addIpLog('Tickets Followup', $datafollowup);

            DB::commit();
             addIpLog('Create Tickets', $datacreate);
             addIpLog('Tickets Assign', $dataassign);
            return redirect()->back()->with("success", "New Ticket Created");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with("error", "Failed to create ticket. " . $e->getMessage());
        }
    }
 public function tickets(Request $request)
{
    // Determine route type
    $type = $request->route()->getName();
    $condition = match ($type) {
        'admin.closed_tickets' => 'Closed',
        'admin.open_tickets'   => 'Open',
        default                => 'All',
    };

    $pageTitle = match ($condition) {
        'Closed' => 'Closed Tickets',
        'Open'   => 'Open Tickets',
        default  => 'All Tickets',
    };

    // Ticket status and types
    $ticket_status = DB::table('ticket_status')->get();
    $ticket_types  = DB::table('ticket_types')->get();

    // Assign details
    $assign_details = DB::table('emplist as e')
        ->leftJoin('roles as r', 'r.role_id', '=', 'e.role_id')
        ->whereIn('r.role_name', ['Superadmin', 'Relationship Manager'])
        ->select('e.client_index', 'e.username')
        ->first();

    // Pagination
    $records_per_page = 10;
    $page = $request->input('page', 1);
    $start_from = ($page - 1) * $records_per_page;

    // Base tickets query
    $ticketsQuery = DB::table('tickets as t')
        ->leftJoin('ticket_status as ts', 't.ticket_status_id', '=', 'ts.id')
        ->leftJoin('ticket_types as tt', 't.ticket_type_id', '=', 'tt.id')
        ->leftJoin('aspnetusers as u', 't.email_id', '=', 'u.email')
        ->leftJoin('emplist as e', 't.created_by', '=', 'e.client_index')
        ->leftJoin('aspnetusers as c', 't.created_user', '=', 'c.id')
        ->leftJoin(DB::raw('(SELECT tf1.* FROM ticket_followup tf1
            INNER JOIN (
                SELECT ticket_id, MAX(added_at) as latest_followup
                FROM ticket_followup
                GROUP BY ticket_id
            ) tf2
            ON tf1.ticket_id = tf2.ticket_id
            AND tf1.added_at = tf2.latest_followup) tf'), 't.id', '=', 'tf.ticket_id')
        ->leftJoin('aspnetusers as fu', 'tf.user_id', '=', 'fu.id')
        ->leftJoin('emplist as fa', 'tf.admin_id', '=', 'fa.client_index')
        ->leftJoin('aspnetusers as per_user', 'per_user.email', '=', 't.email_id') // Role permission join
        ->whereIn('per_user.group_id', [1]); // Adjust group ID(s) as needed

    // Apply ticket filter based on page
    if ($condition === 'Closed') {
        $ticketsQuery->where('ts.ticket_status', 'Closed');
        $ticketCount = $ticketsQuery->count(); // Closed ticket count
    } elseif ($condition === 'Open') {
        $ticketsQuery->where(function ($q) {
            $q->whereNull('ts.ticket_status')
              ->orWhere('ts.ticket_status', '!=', 'Closed')
              ->orWhere('t.Status', 'Open');
        });
        $ticketCount = $ticketsQuery->count(); // Open ticket count
    } else {
        $ticketCount = $ticketsQuery->count(); // All ticket count
    }

    // Total records for pagination
    $total_records = $ticketsQuery->count();
    $total_pages = ceil($total_records / $records_per_page);

    // Fetch paginated tickets
    $tickets = $ticketsQuery
        ->select(
            't.id as ticket_id',
            't.subject_name',
            't.discription',
            't.live_account',
            't.Status',
            't.created_at',
            't.email_id',
            't.assignee',
            't.last_assign_up',
            'ts.ticket_status',
            'ts.ticket_label',
            'tt.ticket_type',
            'u.fullname',
            DB::raw('IF(t.created_by IS NULL, c.fullname, e.username) as created_user'),
            'tf.added_at as last_followup',
            'tf.user_type as followup_type',
            'fu.fullname as followup_user',
            'fa.username as followup_admin'
        )
        ->orderBy('t.created_at', 'desc')
        ->offset($start_from)
        ->limit($records_per_page)
        ->get();

    // Return to view
    return view('admin.tickets', [
        'tickets'        => $tickets,
        'ticket_status'  => $ticket_status,
        'ticket_types'   => $ticket_types,
        'assign_details' => $assign_details,
        'total_pages'    => $total_pages,
        'current_page'   => $page,
        'type'           => $type,
        'pageTitle'      => $pageTitle,
        'ticketCount'    => $ticketCount, // Pass the relevant ticket count for current page
    ]);
}



    // public function tickets(Request $request)
    // {
    //     $rmCondition = app('permission')->appendRolePermissionsQry('t', 'email_id') . " (1=1) ";
    //     $type = $request->route()->getName();
    //     $condition = match ($type) {
    //         'admin.closed_tickets' => 'Closed',
    //         'admin.open_tickets' => 'Open',
    //         default => 'All',
    //     };
    //     $ticket_status = DB::table('ticket_status')->get();
    //     $ticket_types = DB::table('ticket_types')->get();
    //     $assign_details = DB::table('emplist as e')
    //         ->leftJoin('roles as r', 'r.role_id', '=', 'e.role_id')
    //         ->whereIn('r.role_name', ['Superadmin', 'Relationship Manager'])
    //         ->select('e.client_index', 'e.username')
    //         ->first();

    //     $records_per_page = 10;
    //     $page = $request->input('page', 1);
    //     $start_from = ($page - 1) * $records_per_page;

    //     $ticketsQuery = DB::table('tickets as t')
    //         ->leftJoin('ticket_status as ts', 't.ticket_status_id', '=', 'ts.id')
    //         ->leftJoin('ticket_types as tt', 't.ticket_type_id', '=', 'tt.id')
    //         ->leftJoin('aspnetusers as u', 't.email_id', '=', 'u.email')
    //         ->leftJoin('emplist as e', 't.created_by', '=', 'e.client_index')
    //         ->leftJoin('aspnetusers as c', 't.created_user', '=', 'c.id')
    //         ->leftJoin(
    //             DB::raw('(SELECT tf1.* FROM ticket_followup tf1 INNER JOIN (
    //                              SELECT ticket_id, MAX(added_at) as latest_followup
    //                              FROM ticket_followup
    //                              GROUP BY ticket_id) tf2
    //                              ON tf1.ticket_id = tf2.ticket_id AND tf1.added_at = tf2.latest_followup) tf'),
    //             't.id',
    //             '=',
    //             'tf.ticket_id'
    //         )
    //         ->leftJoin('aspnetusers as fu', 'tf.user_id', '=', 'fu.id')
    //         ->leftJoin('emplist as fa', 'tf.admin_id', '=', 'fa.client_index')
    //         ->select(
    //             't.id as ticket_id',
    //             't.subject_name',
    //             't.discription',
    //             't.live_account',
    //             't.Status',
    //             't.created_at',
    //             't.email_id',
    //             't.assignee',
    //             't.last_assign_up',
    //             'ts.ticket_status',
    //             'ts.ticket_label',
    //             'tt.ticket_type',
    //             'u.fullname',
    //             DB::raw('IF(t.created_by IS NULL, c.fullname, e.username) as created_user'),
    //             'tf.added_at as last_followup',
    //             'tf.user_type as followup_type',
    //             'fu.fullname as followup_user',
    //             'fa.username as followup_admin'
    //         );

       
    //     $query = $ticketsQuery->toSql() . ' ' . $rmCondition;

    //     if ($condition != 'All') {
    //         if ($condition == 'Closed') {
    //             $query.=' AND ts.ticket_status="Closed"';
    //         } else {
    //             $query.=" AND ts.ticket_status != 'Closed'";
    //         }
    //     }

        
    //     $query_result = DB::select($query);
    //     $total_records = count($query_result);
    //     $total_pages = ceil($total_records / $records_per_page);
    //     $params = [
    //         'limit' => $records_per_page,
    //         'offset' => $start_from,
    //     ];
    //     $query = $query . " ORDER BY created_at DESC
    //     LIMIT :limit OFFSET :offset";
    //     $tickets = DB::select($query, $params);
    //     return view('admin.tickets', [
    //         'tickets' => $tickets,
    //         'ticket_status' => $ticket_status,
    //         'ticket_types' => $ticket_types,
    //         'assign_details' => $assign_details,
    //         'total_pages' => $total_pages,
    //         'current_page' => $page,
    //         'type' => $type
    //     ]);
    // }
    public function showDetails(Request $request)
    {
        $id = $request->input('id');
        if ($id) {
            $ticket = DB::table('tickets AS t')
                ->select(
                    't.id AS ticket_id',
                    't.subject_name',
                    't.discription',
                    't.live_account',
                    't.created_at',
                    't.email_id',
                    't.assignee',
                    't.last_assign_up',
                    'ts.ticket_status',
                    'ts.ticket_label',
                    'tt.ticket_type',
                    'u.fullname',
                    DB::raw('IF(t.created_by IS NULL, c.fullname, e.username) AS created_user'),
                    'tf.added_at AS last_followup',
                    'tf.user_type AS followup_type',
                    'fu.fullname AS followup_user',
                    'fa.username AS followup_admin'
                )
                ->leftJoin('ticket_status AS ts', 't.ticket_status_id', '=', 'ts.id')
                ->leftJoin('ticket_types AS tt', 't.ticket_type_id', '=', 'tt.id')
                ->leftJoin('aspnetusers AS u', 't.email_id', '=', 'u.email')
                ->leftJoin('emplist AS e', 't.created_by', '=', 'e.client_index')
                ->leftJoin('aspnetusers AS c', 't.created_user', '=', 'c.id')
                ->leftJoin(DB::raw('(SELECT tf1.* FROM ticket_followup tf1 INNER JOIN (SELECT ticket_id, MAX(added_at) AS latest_followup FROM ticket_followup GROUP BY ticket_id) tf2 ON tf1.ticket_id = tf2.ticket_id AND tf1.added_at = tf2.latest_followup) AS tf'), 't.id', '=', 'tf.ticket_id')
                ->leftJoin('aspnetusers AS fu', 'tf.user_id', '=', 'fu.id')
                ->leftJoin('emplist AS fa', 'tf.admin_id', '=', 'fa.client_index')
                ->where(DB::raw('md5(t.id)'), '=', $id)
                ->orderBy('t.created_at', 'DESC')
                ->first();
            $rm_details = DB::table('emplist as emp')
                ->leftJoin('roles as r', 'emp.role_id', '=', 'r.role_id')
                ->select('emp.client_index', 'emp.role_id', 'r.role_name', 'emp.username')
                ->where(function ($query) {
                    $query->where('r.role_name', 'Relationship Manager')
                        ->orWhere('r.role_name', 'Superadmin');
                })
                ->get();
                
            $ticket_status = TicketStatus::all();
         $users = DB::table('emplist')
    ->select('client_index', 'username', 'email')
    ->get();

            if ($ticket) {
                $assign_details = TicketAssignee::select('ticket_assignee.assignee', 'employee.username')
                    ->leftJoin('emplist AS employee', 'ticket_assignee.assignee', '=', 'employee.client_index')
                    ->where('ticket_assignee.ticket_id', $ticket->ticket_id)
                    ->first();
                return view('admin.ticket_details', compact('ticket', 'assign_details', 'rm_details', 'ticket_status','users'));
            }
        }

        $datalogs = [
            'client_index' => $rm_details->client_index,
        'role_name'    => $rm_details->role_name,
        'username'     => $rm_details->username,
        ];
         addIpLog('show Tickets',  $datalogs);

        return redirect()->back()->withErrors('Ticket not found.');
    }
    // public function fetchFollowups(Request $request)
    // {
    //     if ($request->has('id') && !empty($request->query('id'))) {
    //         $ticket_id = $request->query('id');
    //         $followups = TicketFollowup::select(
    //             'ticket_followup.remarks',
    //             'ticket_followup.attachment',
    //             'ticket_followup.user_type',
    //             'ticket_followup.added_at',
    //             'users.fullname AS client_name',
    //             'employees.username AS admin_name'
    //         )
    //             ->leftJoin('aspnetusers AS users', 'ticket_followup.user_id', '=', 'users.id')
    //             ->leftJoin('emplist AS employees', 'ticket_followup.admin_id', '=', 'employees.client_index')
    //             ->where(DB::raw('MD5(ticket_followup.ticket_id)'), $ticket_id)
    //             ->get();
    //     }
    //     return view('ticket_followups', compact('followups'));
    // }

  public function fetchFollowups(Request $request)
{
    if (!$request->filled('id')) {
        return response()->json([]);
    }

    $hashedTicketId = $request->query('id');

    // 🔹 Get original ticket id
    $ticketId = DB::table('tickets')
        ->where(DB::raw('MD5(id)'), $hashedTicketId)
        ->value('id');

    if (!$ticketId) {
        return response()->json([]);
    }

    // 🔹 Mark USER messages as SEEN (admin opened chat)
    TicketFollowup::where('ticket_id', $ticketId)
        ->where('user_type', 'admin')
        ->where('is_seen', 0)
        ->update(['is_seen' => 1]);

    // 🔹 Fetch followups
$datalogs = [ 'ticket_followups.remarks',
            'ticket_followups.attachment',
            'ticket_followups.user_type',
            'ticket_followups.added_at',
            'ticket_followups.is_seen',
            'ticket_followups.is_delivered',
            'users.fullname AS client_name',
            'employees.username AS admin_name'];
    $followups = TicketFollowup::select(
            'ticket_followups.remarks',
            'ticket_followups.attachment',
            'ticket_followups.user_type',
            'ticket_followups.added_at',
            'ticket_followups.is_seen',
            'ticket_followups.is_delivered',
            'users.fullname AS client_name',
            'employees.username AS admin_name'
        )
        ->leftJoin('aspnetusers AS users', 'ticket_followups.user_id', '=', 'users.id')
        ->leftJoin('emplist AS employees', 'ticket_followups.admin_id', '=', 'employees.client_index')
        ->where('ticket_followups.ticket_id', $ticketId)
        ->orderBy('ticket_followups.added_at', 'ASC')
        ->get();
                                         addIpLog('Fetch Followups Tickets', [
        'ticket_id' => $ticketId,
        'total_followups' => $followups->count(),
        'followups' => $followups->toArray()
    ]);

    return response()->json($followups);
}

    public function addRemark(Request $request)
    {
        $ticketId = $request->input('id');
        $request->validate([
            'remark' => 'required|string',
            'attachment' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:5120',
        ]);
        $ticket = Tickets::where(DB::raw('md5(id)'), $ticketId)->first();
        if (!$ticket) {
            return redirect()->back()->with("error", 'Ticket not found.');
        }
        $attachmentPath = '';

     if ($request->hasFile('attachment')) {
        $file = $request->file('attachment');
        $path = $file->store('uploads/chat_files', 'public'); // stored in storage/app/public/uploads/chat_files
        $attachmentPath = '/storage/' . $path; // prepend /storage for browser access
    }

$datalogs = [
    'ticket_id'    => $ticket->id,
    'remarks'      => $request->input('remark'),
    'attachment'   => $attachmentPath,
    'user_type'    => 'admin',
    'admin_id'     => session('userData')['client_index'],
    'is_delivered' => 1,
    'is_seen'      => 0,
];
        $followup = new TicketFollowup();
        $followup->ticket_id = $ticket->id;
        $followup->remarks = $request->input('remark');
        $followup->attachment = $attachmentPath;
        $followup->user_type = 'admin';
        $followup->admin_id = session('userData')['client_index'];
        $followup->is_delivered = 1; // message delivered
        $followup->is_seen = 0;      // not seen yet
        $followup->save();
                                                    addIpLog('Fetch Followups Tickets',  $datalogs);

        return redirect()->back();
    }
    // public function assignTicket(Request $request)
    // {
    //     $rm_details = DB::table('emplist as emp')
    //         ->leftJoin('roles as r', 'emp.role_id', '=', 'r.role_id')
    //         ->select('emp.client_index', 'emp.role_id', 'r.role_name', 'emp.username')
    //         ->where(function ($query) {
    //             $query->where('r.role_name', 'Relationship Manager')
    //                 ->orWhere('r.role_name', 'Superadmin');
    //         })
    //         ->get()->toArray();

    //     $ticket_id = $request->get('ticket_id');
    //     $assignee = $request->input('assignee');
    //     $updated = DB::table('ticket_assignee')
    //         ->where(DB::raw('md5(ticket_id)'), $ticket_id)
    //         ->update([
    //             'assignee' => $assignee,
    //             'assigned_by' => session('userData')['client_index'],
    //         ]);
    //     if ($updated) {
    //         $original_id = DB::table('tickets')
    //             ->where(DB::raw('md5(id)'), $ticket_id)
    //             ->value('id');

    //         if ($original_id) {
    //             $index = array_search($assignee, array_column($rm_details, 'client_index'));
    //             $remarks = "Ticket Reassigned to " . $rm_details[$index]->username;
    //             $userType = 'admin';
    //             DB::table('ticket_followup')->insert([
    //                 'ticket_id' => $original_id,
    //                 'remarks' => $remarks,
    //                 'assignee' => $assignee,
    //                 'user_type' => $userType,
    //                 'admin_id' => session('userData.client_index'),
    //             ]);
    //             return redirect()->back()->with('success', 'Ticket Reassigned Successfully');

    //         }
    //     }
    //     return redirect()->back()->with('error', 'Something went wrong.Please try again');

    // }
  public function updateStatus(Request $request)
{
    $request->validate([
        'ticket_status_id' => 'required|integer|exists:ticket_status,id',
    ]);

    $id = $request->input('ticket_id');
    $status_id = $request->input('ticket_status_id');
    $remarks = $request->input('remarks');



    // ticket fetch
    $ticket = Tickets::where(DB::raw('md5(id)'), $id)->first();

    if (!$ticket) {
        return redirect()->back()->with('error', 'Ticket not found');
    }

    // status name fetch
    $status = DB::table('ticket_status')->where('id', $status_id)->first();

    // save both id & name
    $ticket->ticket_status_id = $status_id;
      $ticket->Status = $status->ticket_status; // optional
  
     // 👈 status name
    $ticket->save();

    // followup remarks
    // $remarks = "Ticket Status updated to " . $status->ticket_status;
$datalogs =  [
    'ticket_id' => $ticket->id,
        'remarks' => $remarks,
        'status' => $status_id,
        'user_type' => 'admin',
        'admin_id' => session('userData.client_index'),
];
    TicketFollowup::create([
        'ticket_id' => $ticket->id,
        'remarks' => $remarks,
        'status' => $status_id,
        'user_type' => 'admin',
        'admin_id' => session('userData.client_index'),
    ]);

                                                        addIpLog('Update status  Tickets',  $datalogs);

    return redirect()->back()->with('success', 'Status Updated Successfully');
}


public function assignTicket(Request $request)
{
    // Validate input
    $request->validate([
        'ticket_id' => 'required|integer',
        'assignee'  => 'required|integer',
    ]);

    // Check if ticket exists
    $ticket = DB::table('tickets')->where('id', $request->ticket_id)->first();
    if (!$ticket) {
        return redirect()->back()->with('error', 'Ticket not found!');
    }

    // Get current logged-in user (assigned_by) fullname
    $assigned_by_name = DB::table('aspnetusers')
        ->where('id', session('user')->id ?? 0)
        ->value('fullname');

    // Get assigned user name (assigned_user) fullname
    // $assigned_user_name = DB::table('aspnetusers')
    //     ->where('id', $request->assignee)
    //     ->value('fullname');

    $assigned_user_name = DB::table('emplist')
        ->where('client_index', $request->assignee)
        ->value('username');

    // echo'<pre>';print_r($assigned_user_name);exit;

    // Check if ticket_assignee row exists
    $exists = DB::table('ticket_assignee')
        ->where('ticket_id', $request->ticket_id)
        ->first();

    if ($exists) {
        // ---- UPDATE ----
        DB::table('ticket_assignee')
            ->where('ticket_id', $request->ticket_id)
            ->update([
                'assignee' => $request->assignee,
                'assigned_by' => $assigned_by_name,
                'assigned_user' => $assigned_user_name,
            ]);
    } else {
        // ---- INSERT ----
        DB::table('ticket_assignee')->insert([
            'ticket_id' => $request->ticket_id,
            'assignee' => $request->assignee,
            'assigned_by' => $assigned_by_name,
            'assigned_user' => $assigned_user_name,
        ]);
    }
$current_assignee = DB::table('tickets')
    ->where('id', $request->ticket_id)
    ->value('assignee');
    // Update tickets table with last_followup info and assigned user name
    DB::table('tickets')
        ->where('id', $request->ticket_id)
        ->update([
            'assignee' => $assigned_user_name,
            'last_assign_up' => $current_assignee,
        ]);

    // Insert into followup log

    $datalogs = [
         'ticket_id' => $request->ticket_id,
        'remarks'   => "Ticket Reassigned from {$assigned_by_name} to {$assigned_user_name}",
        'assignee'  => $request->assignee,
        'user_type' => 'admin',
    ];
    DB::table('ticket_followup')->insert([
        'ticket_id' => $request->ticket_id,
        'remarks'   => "Ticket Reassigned from {$assigned_by_name} to {$assigned_user_name}",
        'assignee'  => $request->assignee,
        'user_type' => 'admin',
    ]);
 addIpLog('Assign Ticket  Tickets',  $datalogs);
    return redirect()->back()->with('success', 'Ticket Reassigned Successfully');
}

}
