<?php

$hasPermissionDeleteDate   = isset($hasPermissionDeleteDate) ? $hasPermissionDeleteDate : false;
$hasPermissionDeleteModule = isset($hasPermissionDeleteModule) ? $hasPermissionDeleteModule : false;
$hasPermissionDeleteOwn    = isset($hasPermissionDeleteOwn) ? $hasPermissionDeleteOwn : false;
$hasPermissionDeleteUser   = isset($hasPermissionDeleteUser) ? $hasPermissionDeleteUser : false;
$hasPermissionViewDate     = isset($hasPermissionViewDate) ? $hasPermissionViewDate : false;
$hasPermissionViewModule   = isset($hasPermissionViewModule) ? $hasPermissionViewModule : false;
$hasPermissionViewOwn      = isset($hasPermissionViewOwn) ? $hasPermissionViewOwn : false;
$hasPermissionViewUser     = isset($hasPermissionViewUser) ? $hasPermissionViewUser : false;

$activitiesReportsPage = SITE_AREA . '/reports/activities';
$activitiesReportsUrl = site_url($activitiesReportsPage);

?>
<div class="row-fluid">
    <?php if ($hasPermissionViewOwn) : ?>
	<div class="span3">
        <a href='<?php echo "{$activitiesReportsUrl}/{$pages['own']}"; ?>'>
            <span class="fa fa-user muted fa-3x pull-left"></span>
        </a>
        <p><strong><?php echo lang(str_replace('activity_', 'activities_', $pages['own'])); ?></strong><br />
            <span><?php echo lang(str_replace('activity_', 'activities_', "{$pages['own']}_description")); ?></span>
        </p>
    </div>
    <?php
    endif;
    if ($hasPermissionViewUser) :
    ?>
    <div class="span3">
        <a href='<?php echo "{$activitiesReportsUrl}/{$pages['user']}"; ?>'>
            <span class="fa fa-users muted fa-3x pull-left"></span>
        </a>
        <p><strong><?php echo lang(str_replace('activity_', 'activities_', "{$pages['user']}s")); ?></strong><br />
            <span><?php echo lang(str_replace('activity_', 'activities_', "{$pages['user']}s_description")); ?></span>
        </p>
    </div>
    <?php
    endif;
    if ($hasPermissionViewModule) :
    ?>
    <div class="span3">
        <a href='<?php echo "{$activitiesReportsUrl}/{$pages['module']}"; ?>'>
            <span class="fa fa-puzzle-piece muted fa-3x pull-left"></span>
        </a>
        <p><strong><?php echo lang(str_replace('activity_', 'activities_', "{$pages['module']}s")); ?></strong><br />
            <span><?php echo lang(str_replace('activity_', 'activities_', "{$pages['module']}_description")); ?></span>
        </p>
    </div>
    <?php
    endif;
    if ($hasPermissionViewDate) :
    ?>
    <div class="span3">
        <a href='<?php echo "{$activitiesReportsUrl}/{$pages['date']}"; ?>'>
			<span class="fa fa-calendar muted fa-3x pull-left"></span>
		</a>
        <p><strong><?php echo lang(str_replace('activity_', 'activities_', $pages['date'])); ?></strong><br />
            <span><?php echo lang(str_replace('activity_', 'activities_', "{$pages['date']}_description")); ?></span>
		</p>
	</div>
	<?php endif; ?>
</div>
<div class="row-fluid">
	<div class="span6">
		<!-- Active Modules -->
		<div class="admin-box">
			<h3><?php echo lang('activities_top_modules'); ?></h3>
            <?php if (! empty($top_modules) && is_array($top_modules)) : ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th><?php echo lang(str_replace('activity_', 'activities_', $pages['module'])); ?></th>
                        <th><?php echo lang('activities_logged'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($top_modules as $topModule) : ?>
                    <tr>
                        <td><strong><?php echo ucwords($topModule->module); ?></strong></td>
                        <td><?php echo $topModule->activity_count; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else : ?>
            <p><?php echo lang('activities_no_top_modules'); ?></p>
            <?php endif; ?>
		</div>
	</div>
	<div class="span6">
		<div class="admin-box">
			<!-- Active Users -->
			<h3><?php echo lang('activities_top_users'); ?></h3>
            <?php if (! empty($top_users) && is_array($top_users)) : ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th><?php echo lang(str_replace('activity_', 'activities_', $pages['user'])); ?></th>
                        <th><?php echo lang('activities_logged'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($top_users as $topUser) : ?>
                    <tr>
                        <td><strong><?php e($topUser->username == '' ? lang('activities_username_not_found') : $topUser->username); ?></strong></td>
                        <td><?php echo $topUser->activity_count; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else : ?>
            <p><?php echo lang('activities_no_top_users'); ?></p>
            <?php endif; ?>
		</div>
	</div>
</div>
<div class="admin-box">
	<h3><?php echo lang('activities_cleanup'); ?></h3>
    <?php
    if (! $hasPermissionDeleteOwn
        && ! $hasPermissionDeleteUser
        && ! $hasPermissionDeleteModule
        && ! $hasPermissionDeleteDate
    ) :
    ?>
    <p><?php echo lang('activities_none_found'); ?></p>
    <?php else : ?>
	<table class="table table-striped">
		<tbody>
            <?php if ($hasPermissionDeleteOwn) : ?>
            <tr>
                <?php echo form_open("{$activitiesReportsPage}/delete", array('id' => 'activity_own_form', 'class' => 'form-inline')); ?>
                    <td class='label-column'><label for="activity_own_select"><?php echo lang('activities_delete_own_note'); ?></label></td>
                    <td>
                        <input type="hidden" name="action" value="activity_own" />
                        <select name="which" id="activity_own_select">
                            <option value="<?php echo $current_user->id; ?>"><?php e($current_user->username); ?></option>
                        </select>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-block" id="delete-activity_own"><span class="fa fa-trash"></span>&nbsp;<?php echo lang('activities_own_delete'); ?></button>
                    </td>
                <?php echo form_close(); ?>
            </tr>
            <?php
            endif;
            if ($hasPermissionDeleteUser) :
            ?>
            <tr>
                <?php echo form_open("{$activitiesReportsPage}/delete", array('id' => 'activity_user_form', 'class' => 'form-inline')); ?>
                    <td class='label-column'><label for="activity_user_select"><?php echo lang('activities_delete_user_note'); ?></label></td>
                    <td>
                        <input type="hidden" name="action" value="activity_user" />
                        <select name="which" id="activity_user_select">
                            <option value="all"><?php echo lang('activities_all_users'); ?></option>
                            <?php foreach ($users as $au) : ?>
                            <option value="<?php echo $au->id; ?>"><?php e($au->username); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-block" id="delete-activity_user"><span class="fa fa-trash"></span>&nbsp;<?php echo lang('activities_user_delete'); ?></button>
                    </td>
                <?php echo form_close(); ?>
            </tr>
			<?php
            endif;

            if ($hasPermissionDeleteModule) :
            ?>
			<tr>
                <?php echo form_open("{$activitiesReportsPage}/delete", array('id' => 'activity_module_form', 'class' => 'form-inline')); ?>
                    <td class='label-column'><label for="activity_module_select"><?php echo lang('activities_delete_module_note'); ?></label></td>
                    <td>
                        <input type="hidden" name="action" value="activity_module" />
                        <select name="which" id="activity_module_select">
                            <option value="all"><?php echo lang('activities_all_modules'); ?></option>
                            <option value="core"><?php echo lang('activities_core'); ?></option>
                            <?php foreach ($modules as $mod) : ?>
                            <option value="<?php echo $mod; ?>"><?php echo $mod; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-block" id="delete-activity_module"><span class="fa fa-trash"></span>&nbsp;<?php echo lang('activities_module_delete'); ?></button>
                    </td>
                <?php echo form_close(); ?>
			</tr>
			<?php
            endif;

            if ($hasPermissionDeleteDate) :
            ?>
			<tr>
                <?php echo form_open("{$activitiesReportsPage}/delete", array('id' => 'activity_date_form', 'class' => 'form-inline')); ?>
                    <td class='label-column'><label for="activity_date_select"><?php echo lang('activities_delete_date_note'); ?></label></td>
                    <td>
                        <input type="hidden" name="action" value="activity_date" />
                        <select name="which" id="activity_date_select">
                            <option value="all"><?php echo lang('activities_all_dates'); ?></option>
                            <?php foreach ($activities as $activity) : ?>
                            <option value="<?php echo $activity->activity_id; ?>"><?php echo $activity->created_on; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-block" id="delete-activity_date"><span class="fa fa-trash"></span>&nbsp;<?php echo lang('activities_date_delete'); ?></button>
                    </td>
                <?php echo form_close(); ?>
			</tr>
			<?php endif; ?>
		</tbody>
	</table>
    <?php endif; ?>
</div>