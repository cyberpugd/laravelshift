<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

use App\ChangeTicket;
use App\Ticket;
use App\User;
use App\WorkOrder;
use Illuminate\Support\Facades\URL;

Route::get('/', 'PagesController@postLogin');
Route::post('login', 'PagesController@postLogin');
Route::get('logout', 'PagesController@logout');
// Route::get('emailtest', 'EmailController@getEmail');
Route::get('active-directory', 'PagesController@activeDirectory');

Route::get('testing-templates', function() {
	$ticket = Ticket::find(20);
	$ticketURL = URL::to('/') . '/tickets/' . $ticket->id;
	$sender = User::find(1);
	$message = "A nice message here.";

	$data = [
		'ticketurl' => $ticketURL,
		'ticket' => $ticket,
		'agent' =>  $ticket->assignedTo,
		'sender' => $sender->first_name . ' ' . $sender->last_name,
		'theMessage' => $message
	];

	return view('app.emails.notify_private_message', $data);
});


Route::group(['middleware' => ['auth']], function () {

//Routes for end users
    Route::group(['middleware' => ['permission:help_desk_portal']], function () {
        Route::get('helpdesk/tickets/create-ticket', 'Portal\TicketsController@createTicket')->middleware(['permission:create_portal_ticket']);
        Route::post('tickets/create-ticket', 'Portal\TicketsController@save')->middleware(['permission:create_portal_ticket']);
        Route::get('helpdesk/tickets/my-tickets', 'Portal\TicketsController@myTickets')->middleware(['permission:create_portal_ticket']);
        Route::get('helpdesk/tickets/closed-tickets', 'DashboardController@closedTickets')->middleware(['permission:view_tickets']);
        Route::get('helpdesk/tickets/{id}', 'Portal\TicketsController@show')->middleware(['permission:view_tickets']);
        Route::get('helpdesk/dashboard', 'DashboardController@portalDashboard')->middleware(['permission:view_tickets']);
        Route::get('helpdesk/user-settings/profile', 'Portal\UsersController@showProfile');
        Route::post('helpdesk/user-settings/profile', 'Portal\UsersController@updateProfile');
        Route::post('helpdesk/tickets/{id}/attachments', 'TicketsController@addAttachment')->middleware(['permission:add_attachment_ticket']);
        Route::post('helpdesk/tickets/post-message/{id}', 'TicketsController@postMessage')->middleware(['permission:add_conversation_message']);
        Route::post('helpdesk/tickets/{id}/close', 'Portal\TicketsController@close')->middleware(['permission:view_tickets']);
        Route::get('forms/{slug}', 'AdminController@showUserForm')->middleware(['permission:create_portal_ticket']);
        Route::post('forms/{slug}', 'TicketsController@postUserForm')->middleware(['permission:create_portal_ticket']);
        Route::get('survey', 'Portal\SurveyController@show');
    });

    //Helpdesk routes for both admin and agent
    Route::group(['middleware' => ['permission:agent_portal']], function () {
        Route::get('dashboard', 'DashboardController@dashboard');
        Route::get('tickets/open-tickets', ['as' => 'myOpenTickets', 'uses' => 'TicketsController@myOpenTickets'])->middleware(['permission:view_tickets']);
        Route::get('tickets/team-tickets', 'TicketsController@myTeamsTickets')->middleware(['permission:view_tickets']);
        Route::post('tickets/mass-assign', 'TicketsController@massAssign')->middleware(['permission:create_ticket']);
        Route::get('tickets/print/{id}', 'TicketsController@printTicket')->middleware(['permission:view_tickets']);
        Route::get('tickets/create', 'TicketsController@create')->middleware(['permission:create_ticket']);
        Route::post('tickets/assignToMe/{id}', 'TicketsController@assignToMe')->middleware(['permission:create_ticket']);
        Route::post('tickets/create', 'TicketsController@save')->middleware(['permission:create_ticket']);
        Route::get('tickets/all', 'TicketsController@showAll')->middleware(['permission:view_tickets']);
        Route::post('tickets/attachment/{id}', 'TicketsController@deleteAttachment')->middleware(['permission:change_ticket_auditor']);
        Route::post('tickets/{id}/attachments', 'TicketsController@addAttachment')->middleware(['permission:add_attachment_ticket']);
        Route::post('tickets/{id}/work-order/{woid}/attachments', 'TicketsController@addAttachmentFromWorkOrder')->middleware(['permission:add_attachment_ticket']);
        Route::post('tickets/post-message/{id}', 'TicketsController@postMessage')->middleware(['permission:add_conversation_message']);
        Route::post('tickets/post-message-private/{id}', 'TicketsController@postPrivateMessage')->middleware(['permission:create_ticket']);
        Route::post('tickets/post-message-private-notify/{id}', 'TicketsController@postPrivateMessageNotify')->middleware(['permission:create_ticket']);
        Route::get('tickets/work-orders', 'TicketsController@myOpenWorkOrders')->middleware(['permission:view_work_orders']);
        Route::post('tickets/close/{id}', 'TicketsController@closeTicket')->middleware(['permission:close_ticket']);
        Route::post('tickets/open/{id}', 'TicketsController@reopenTicket')->middleware(['permission:create_ticket']);
        Route::get('tickets/resolution/{id}', 'TicketsController@provideResolution')->middleware(['permission:close_ticket']);
        Route::get('tickets/{id}', 'TicketsController@show')->middleware(['permission:view_tickets']);
        Route::post('tickets/{id}', 'TicketsController@update')->middleware(['permission:update_ticket']);
        Route::post('tickets/{id}/work-order/create', 'TicketsController@createWorkOrder')->middleware(['permission:create_work_orders']);
        Route::get('user-settings/wo-template', 'UsersController@createWorkOrderTemplate')->middleware(['permission:create_work_orders']);
        Route::get('/user-settings/wo-template/{id}', 'UsersController@updateWorkOrderTemplate')->middleware(['permission:create_work_orders']);
        Route::post('/user-settings/wo-template/{id}', 'UsersController@deleteWorkOrderTemplate')->middleware(['permission:create_work_orders']);
        Route::post('/user-settings/wo-template/edit/{id}', 'UsersController@editTemplateName')->middleware(['permission:create_work_orders']);
        Route::post('user-settings/create-wo-template', 'UsersController@saveWorkOrderTemplate')->middleware(['permission:create_work_orders']);
        Route::get('user-settings/cc-templates', 'UsersController@showCCTemplates')->middleware(['permission:create_cc_ticket']);
        Route::get('user-settings/cc-template', 'UsersController@createCCTemplate')->middleware(['permission:create_cc_ticket']);
        Route::post('user-settings/cc-template', 'UsersController@saveCCTemplate')->middleware(['permission:create_cc_ticket']);
        Route::post('/user-settings/cc-template/delete/{id}', 'UsersController@deleteCCTemplate')->middleware(['permission:create_cc_ticket']);
        Route::get('user-settings/cc-template/{id}', 'UsersController@showCCTemplate')->middleware(['permission:create_cc_ticket']);
        Route::post('user-settings/cc-template/{id}', 'UsersController@updateCCTemplate')->middleware(['permission:create_cc_ticket']);
        Route::post('user-settings/wo-detail/create', 'UsersController@saveWorkOrderDetail')->middleware(['permission:create_work_orders']);
        Route::post('/user-settings/wo-detail/update/{id}', 'UsersController@updateWorkOrderDetail')->middleware(['permission:create_work_orders']);
        Route::post('/user-settings/wo/{id}', 'UsersController@deleteWOFromTemplate')->middleware(['permission:create_work_orders']);
        Route::get('/user-settings/wo/{id}', 'UsersController@getWorkOrderForTemplate')->middleware(['permission:create_work_orders']);
        Route::get('user-settings/profile', 'UsersController@showProfile');
        Route::post('user-settings/profile', 'UsersController@updateProfile');
        Route::get('/user-settings/query-builder', 'UsersController@showQueryBuilder');
        Route::post('/user-settings/create-view/ticket', 'UsersController@createTicketView');
        Route::post('/user-settings/create-view', 'UsersController@createUserView');
        Route::post('/user-settings/save-menu-state', 'UsersController@saveMenuState');
        Route::post('tickets/{id}/work-order/apply-template', 'TicketsController@applyWOTemplate')->middleware(['permission:create_work_orders']);
        Route::get('tickets/work-order/{id}', 'TicketsController@showWorkOrder')->middleware(['permission:view_work_orders']);
        Route::post('/tickets/send-work-order-emails/{id}', 'TicketsController@emailWorkOrders')->middleware(['permission:create_work_orders']);
        Route::get('change-control/work-order/{id}', 'TicketsController@showWorkOrder')->middleware(['permission:view_work_orders']);
        Route::post('tickets/work-order/{id}', 'TicketsController@updateWorkOrder')->middleware(['permission:create_work_orders']);
        Route::post('tickets/work-order/open/{id}', 'TicketsController@openWorkOrder')->middleware(['permission:create_work_orders']);
        Route::get('views/{id}', ['as' => 'myView', 'uses' => 'ViewController@show']);
        Route::get('views/print/{id}', 'ViewController@printView');
        Route::get('views/edit/{id}', 'ViewController@editView');
        Route::post('views/edit/{id}', 'ViewController@updateView');
        Route::post('views/delete/{id}', 'ViewController@deleteView');
        Route::post('change-control/apply-template', 'ChangeController@applyCCTemplate');
        Route::get('change-control/print/{id}', 'ChangeController@printTicket');
        Route::get('change-control/create', 'ChangeController@create')->middleware(['permission:create_cc_ticket']);
        Route::get('/change-control/needs-approval', 'ChangeController@needsApproval')->middleware(['permission:approve_change_ticket']);
        Route::get('change-control/my-open', 'ChangeController@myOpen')->middleware(['permission:create_cc_ticket']);
        Route::get('change-control/all', ['as' => 'allChangeTickets', 'uses' => 'ChangeController@all'])->middleware(['permission:create_cc_ticket']);
        Route::post('change-control/create', 'ChangeController@save')->middleware(['permission:create_cc_ticket']);
        Route::get('change-control/work-orders', 'ChangeController@myOpenWorkOrders')->middleware(['permission:view_work_orders']);
        Route::get('change-control/work-order/{id}', 'TicketsController@showWorkOrder')->middleware(['permission:view_work_orders']);
        Route::post('/change-control/send-work-order-emails/{id}', 'ChangeController@emailWorkOrders')->middleware(['permission:view_work_orders']);
        Route::post('change-control/{id}/work-order/create', 'ChangeController@createWorkOrder')->middleware(['permission:create_cc_ticket']);
        Route::post('change-control/{id}/work-order/apply-template', 'ChangeController@applyWOTemplate')->middleware(['permission:create_work_orders']);
        Route::post('change-control/{id}/toggle-audit', 'ChangeController@toggleAudit')->middleware(['permission:change_ticket_auditor']);
        Route::get('change-control/{id}', 'ChangeController@show')->middleware(['permission:create_cc_ticket, approve_change_ticket']);
        Route::post('change-control/{id}', 'ChangeController@update')->middleware(['permission:create_cc_ticket']);
        Route::post('/change-control/reject/{id}', 'ChangeController@reject')->middleware(['permission:approve_change_ticket']);
        Route::post('/change-control/approve/{id}', 'ChangeController@approve')->middleware(['permission:approve_change_ticket']);
        Route::post('/change-control/start-work/{id}', 'ChangeController@startWork')->middleware(['permission:create_cc_ticket']);
        Route::post('/change-control/propose/{id}', 'ChangeController@propose')->middleware(['permission:create_cc_ticket']);
        Route::post('/change-control/amend/{id}', 'ChangeController@update')->middleware(['permission:create_cc_ticket']);
        Route::post('/change-control/{id}/attachments', 'ChangeController@addAttachment');
        Route::post('/change-control/{id}/work-order/{woid}/attachments', 'ChangeController@addAttachmentFromWorkOrder');
        Route::get('/change-control/cancel/{id}', 'ChangeController@showCancel')->middleware(['permission:create_cc_ticket']);
        Route::post('/change-control/cancel/{id}', 'ChangeController@cancelTicket')->middleware(['permission:create_cc_ticket']);
        Route::get('/change-control/close/{id}', 'ChangeController@showClose')->middleware(['permission:create_cc_ticket']);
        Route::post('/change-control/close/{id}', 'ChangeController@closeTicket')->middleware(['permission:create_cc_ticket']);
        Route::post('/change-control/clone/{id}', 'ChangeController@cloneTicket')->middleware(['permission:clone_change_ticket']);
        Route::post('/change-control/wo/remove/{id}', 'ChangeController@removeWorkOrder')->middleware(['permission:create_work_orders']);



        //Routes for administrators
          Route::get('admin/form-builder', 'AdminController@showFormBuilder')->middleware(['permission:build_forms']); //Create permission to build forms
          Route::post('admin/form-builder', 'AdminController@saveFormBuilder')->middleware(['permission:build_forms']); //Create permission to build forms
          Route::get('admin/forms', 'AdminController@showForms')->middleware(['permission:build_forms']); //Create permission to build forms
          Route::post('admin/forms/toggleActive/{id}', 'AdminController@toggleFormStatus')->middleware(['permission:build_forms']); //Create permission to build forms
          Route::post('admin/forms/remove/{id}', 'AdminController@removeForm')->middleware(['permission:build_forms']); //Create permission to build forms
          Route::post('admin/forms/copy', 'AdminController@copyForm')->middleware(['permission:build_forms']); //Create permission to build forms
          Route::get('admin/forms/{id}', 'AdminController@editForm')->middleware(['permission:build_forms', 'shared_with_me']); //Create permission to build forms
          Route::post('admin/forms/{id}', 'AdminController@updateForm')->middleware(['permission:build_forms', 'shared_with_me']); //Create permission to build forms
          Route::get('admin/get-slug/{field}', 'AdminController@getSlug')->middleware(['permission:build_forms']); //Create permission to build forms
          Route::get('admin/category-management', 'CategoryController@index')->middleware(['permission:manage_categories']);
        Route::post('admin/category-management/create-category', 'CategoryController@save')->middleware(['permission:manage_categories']);
        Route::post('admin/category-management/edit/{id}', 'CategoryController@editCategory')->middleware(['permission:manage_categories']);
        Route::post('admin/category-management/add-subcategory/{id}', 'CategoryController@saveSubcategory')->middleware(['permission:manage_categories']);
        Route::get('admin/category-management/{id}', 'CategoryController@showCategory')->middleware(['permission:manage_categories']);
        Route::post('admin/subcategory-management/delete/{id}', 'CategoryController@removeSubcategory')->middleware(['permission:manage_categories']);
        // Route::post('admin/subcategory-management/{id}', 'CategoryController@updateSubcategory')->middleware(['permission:manage_categories']);
        Route::get('/admin/users', 'UsersController@index')->middleware(['permission:manage_users']);
        Route::post('/admin/users/add', 'UsersController@add')->middleware(['permission:manage_users']);
        Route::get('/admin/users/{id}', 'UsersController@show')->middleware(['permission:manage_users']);
        Route::post('/admin/users/{id}', 'UsersController@update')->middleware(['permission:manage_users']);
        Route::post('/admin/category-management/inactivate-subcategory/{id}', 'CategoryController@inactivateSubcategory')->middleware(['permission:manage_categories']);
        Route::post('/admin/category-management/activate-subcategory/{id}', 'CategoryController@activateSubcategory')->middleware(['permission:manage_categories']);
        Route::post('/admin/category-management/inactivate-category/{id}', 'CategoryController@inactivateCategory')->middleware(['permission:manage_categories']);
        Route::post('/admin/category-management/activate-category/{id}', 'CategoryController@activateCategory')->middleware(['permission:manage_categories']);
        Route::post('/admin/category-management/edit-subcategory/{id}', 'CategoryController@editSubcategory')->middleware(['permission:manage_categories']);
        Route::post('/admin/category-management/delete/{id}', 'CategoryController@deleteCategory')->middleware(['permission:manage_categories']);
        Route::get('/admin/mail-setup', 'AdminController@showMailSetup')->middleware(['permission:manage_imap']);
        Route::post('/admin/mail-setup', 'AdminController@postMailSetup')->middleware(['permission:manage_imap']);
        Route::get('/admin/roles', 'AdminController@showRoles')->middleware(['permission:manage_roles']);
        Route::post('/admin/roles/edit/{id}', 'AdminController@editRole')->middleware(['permission:manage_roles']);
        Route::post('/admin/roles/update-permissions/{id}', 'AdminController@updateRolePermissions')->middleware(['permission:manage_roles']);
        Route::post('/admin/role-management/create-role', 'AdminController@saveRole')->middleware(['permission:manage_roles']);
        Route::get('/admin/audit-units', 'AdminController@manageAuditUnit')->middleware(['permission:manage_audit_units']);
        Route::post('/admin/audit-units/create', 'AdminController@createAuditUnit')->middleware(['permission:manage_audit_units']);
        Route::post('/admin/audit-units/edit/{id}', 'AdminController@editAuditUnit')->middleware(['permission:manage_audit_units']);
        Route::get('/admin/locations', 'AdminController@showLocations')->middleware(['permission:manage_locations']);
        Route::post('/admin/locations/add', 'AdminController@addLocation')->middleware(['permission:manage_locations']);
        Route::post('/admin/locations/{id}', 'AdminController@updateLocation')->middleware(['permission:manage_locations']);
        Route::get('/admin/holidays', 'AdminController@showHolidays');
        Route::post('/admin/holiday/remove/{id}', 'AdminController@removeHoliday');
        Route::post('/admin/holiday/add', 'AdminController@addHoliday');
        Route::post('/admin/holiday/update/{id}', 'AdminController@updateHoliday');
        Route::get('/admin/teams', 'AdminController@showTeams');
        Route::post('/admin/teams/create', 'AdminController@createTeam');
        Route::post('/admin/teams/edit/{id}', 'AdminController@editTeam');
        Route::post('/admin/teams/{id}/sync-users', 'AdminController@syncUsersWithTeam');
        Route::get('/admin/teams/{id}', 'AdminController@showTeam');
        Route::post('/admin/teams/{teamId}/remove-user/{userId}', 'AdminController@removeUserFromTeam');
        Route::get('/admin/announcements', 'AdminController@announcements');
        Route::post('/admin/announcements/create', 'AdminController@createAnnouncement');
        Route::post('/admin/announcements/delete/{id}', 'AdminController@deleteAnnouncement');
        Route::post('/admin/announcements/edit/{id}', 'AdminController@editAnnouncement');
        Route::post('/admin/announcements/expire/{id}', 'AdminController@expireAnnouncement');
        Route::post('/admin/teams/subcategories/sync/{id}', 'AdminController@syncTeamWithSubcategories');
        Route::post('/admin/login-as/{id}', 'AdminController@loginAsUser')->middleware(['permission:manage_users']);
        Route::get('/admin/survey-groups', 'SurveyGroupController@index');
        Route::post('/admin/survey-groups', 'SurveyGroupController@save');
        Route::get('/admin/survey-groups/{id}', 'SurveyGroupController@show');
        Route::post('/admin/survey-groups/{id}', 'SurveyGroupController@update');
        Route::post('/admin/survey-groups/{id}/delete', 'SurveyGroupController@destroy');
    });
});
