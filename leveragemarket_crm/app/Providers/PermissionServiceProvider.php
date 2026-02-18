<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class PermissionServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Make the function globally available
        \Illuminate\Support\Facades\Blade::directive('rolePermissions', function ($expression) {
            return "<?php echo app('permission')->appendRolePermissionsQry($expression); ?>";
        });
    }

    public function register()
    {
        $this->app->singleton('permission', function () {
            return new class {
                public function appendRolePermissionsQry($tableAlias = 'trs', $column = 'email', $type = 'user')
                {
                    $mt5GroupsList = [];
                    $userArray = session('userData');
                    $user = json_decode(json_encode($userArray));
                    $mt5Groups = json_decode($user->mt5_group_id ?? '[]', true);

                    if ($mt5Groups) {
                        $mt5GroupsList = implode(',', $mt5Groups);
                    } else {
                        $mt5GroupsList = 0;
                    }

                    $userGroups = json_decode($user->user_group_id ?? '[]', true);
                    $userGroupsList = null;

                    if ($userGroups) {
                        $userGroupsList = implode(',', $userGroups);
                    }

                    $roleId = $user->role_id ?? null;
                    $permissionCondition = '';
//   echo'<pre>';print_r($roleId);exit;
                    if ($type === 'user') {

                   
//                         if ($roleId === 2) {
//                              $permissionCondition .= " JOIN relationship_manager per_rm ON (per_rm.user_id = {$tableAlias}.{$column}) WHERE per_rm.rm_id='" . $user->email . "' AND ";
// } else
                        //    $permissionCondition .= "";
                        if ($roleId === 8) {
                            $permissionCondition .= " LEFT JOIN (SELECT DISTINCT email, account_type FROM liveaccount GROUP BY email) per_la
                            ON (per_la.email = {$tableAlias}.{$column})
                            LEFT JOIN aspnetusers per_user ON (per_user.email = {$tableAlias}.{$column})
                            WHERE ((per_la.account_type IN ($mt5GroupsList)) OR (per_user.group_id IN ($userGroupsList))) AND ";
                        } elseif ($roleId === 13) {
                            // $permissionCondition .= " JOIN relationship_manager per_rm ON (per_rm.user_id = {$tableAlias}.{$column})
                            // JOIN rm_mapping per_map ON (per_rm.rm_id = per_map.rm_id)
                            // WHERE per_map.supervisor_id='" . $user->email . "' AND ";
                            $permissionCondition .= "
        JOIN relationship_manager 
            ON relationship_manager.user_id = {$tableAlias}.{$column}
        WHERE relationship_manager.rm_id = '" . $user->email . "' AND
    ";
                        } else {
                            $permissionCondition .= " JOIN aspnetusers per_user ON (per_user.email = {$tableAlias}.{$column})
                            WHERE (per_user.group_id IN ($userGroupsList)) AND ";
                        }
                    } elseif ($type === 'admin') {
                        $conditions = [];
                        foreach ($userGroups as $value) {
                            $conditions[] = " JSON_CONTAINS(e.user_group_id, '\"$value\"')";
                        }
                        $query = implode(' OR ', $conditions);
                        $permissionCondition .= " WHERE ($query) AND ";
                    }
                    return $permissionCondition;
                }
            };
        });
    }
}
