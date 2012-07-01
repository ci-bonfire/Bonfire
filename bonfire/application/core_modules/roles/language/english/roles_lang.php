<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
	Copyright (c) 2011 Lonnie Ezell

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in
	all copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
	THE SOFTWARE.
*/

$lang['roles_intro']					= 'Roles allow you to define the abilities that a user can have.';
$lang['roles_manage']				= 'Manage User Roles';
$lang['roles_list']				= 'Roles List';
$lang['roles_create_success']		= 'Roles successfully created';
$lang['roles_create_failure']		= 'There was a problem creating the Role: ';
$lang['roles_create_heading']	= 'Create New Role';
$lang['roles_create_note']			= 'Every user needs a role. Make sure you have all that you need.';
$lang['roles_invalid_id']			= 'Invalid roles ID.';
$lang['roles_edit_success']		= 'Roles successfully saved';
$lang['roles_edit_failure']		= 'There was a problem saving the Roles: ';
$lang['roles_delete_success']		= 'The role was successfully deleted.';
$lang['roles_delete_failure']		= 'We could not delete the Role: ';
$lang['roles_edit_heading']		= 'Edit Role';
$lang['roles_description']			= 'Description';
$lang['roles_details']				= 'Role Details';

$lang['roles_name']					= 'Role Name';
$lang['roles_max_desc_length']		= 'Max. 255 characters.';
$lang['roles_default_role']			= 'Default Role';
$lang['roles_default_note']			= 'This role should be assigned to all new users.';
$lang['roles_permissions']			= 'Permissions';
$lang['roles_permissions_check_note']= 'Check all permissions that apply to this Role.';
$lang['roles_action_save_role']				= 'Save Role';
$lang['roles_action_delete_role']			= 'Delete this Role';
$lang['roles_delete_confirm']		= 'Are you sure you want to delete these logs?';
$lang['roles_delete_note']			= 'Deleting this role will convert all users that are currently assigned it to the site&rsquo;s default role.';
$lang['roles_can_delete_role']   	= 'Removable?';
$lang['roles_can_delete_note']    	= 'Can this role be deleted?';
$lang['roles_can_not_delete']    	= 'This role can not be deleted.';
$lang['roles_no_role_given']    	= 'No Role given.';
$lang['roles_no_permission_given']    	= 'No Permission given.';

$lang['roles_roles']					= 'Roles';

$lang['roles_new_permission_message']	= 'You will be able to edit permissions once the role has been created.';
$lang['roles_not_used']				= 'Not used';

$lang['roles_login_destination']		= 'Login Destination';
$lang['roles_destination_note']		= 'The site URL to redirect to on successful login.';

$lang['roles_permission_matrix']				= 'Permission Matrix';
$lang['matrix_permission']			= 'Permission';
$lang['matrix_role']				= 'Role';
$lang['matrix_note']				= 'Instant permission editing. Toggle a checkbox to add or remove that permission for that role.';
$lang['matrix_insert_success']		= 'Permission added for role.';
$lang['matrix_insert_failure']			= 'There was a problem adding the permission for the role: ';
$lang['matrix_delete_success']		= 'Permission removed from the role.';
$lang['matrix_delete_failure']			= 'There was a problem deleting the permission for the role: ';
$lang['matrix_auth_failure']			= 'Authentication: You do not have the ability to manage the access control for this role.';

$lang['roles_permission_manage_description'] = 'To manage the access control permissions for the %s role.';
$lang['roles_acl_permission_failure'] = 'There was an error creating the ACL permission.';
$lang['roles_permission_save_failure'] = 'There was an error saving the permissions.';

$lang['roles_email_in_use'] = 'The %s address is already in use. Please choose another.';
$lang['roles_role_in_use'] = 'The %s role is already in use. Please choose another.';

$lang['roles_new_permission_message']	= 'You will be able to edit permissions once the role has been created.';

//--------------------------------------------------------------------
// Core_modules permissions
//--------------------------------------------------------------------
$lang['roles_matrix_site_signin']		= 'Signin';
$lang['roles_matrix_activities_own']	= 'Own activity';
$lang['roles_matrix_activities_user']	= 'User activity';
$lang['roles_matrix_activities_module']	= 'Activity per modules';
$lang['roles_matrix_activities_date']	= 'Activity per dates';
$lang['roles_matrix_action_offline']			= 'Offline Access';
$lang['roles_matrix_action_delete']			= 'Delete';
$lang['roles_matrix_action_allow']			= 'Allow';
$lang['roles_matrix_action_view']			= 'View';
$lang['roles_matrix_action_add']			= 'Add';
$lang['roles_matrix_action_manage']			= 'Manage';

//--------------------------------------------------------------------
// Sub nav
//--------------------------------------------------------------------
$lang['roles_s_roles']					= 'Roles';
$lang['roles_s_new_role']					= 'New Role';
$lang['roles_s_matrix_permission']					= 'Permission Matrix';