<?php

namespace Mhafizhasan\AgakCore;

use App\Modules\Core\Models\User;
use App\Modules\Core\Models\Role;

use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

use DB;
use AgakLogger;

/**
 *
 */
class AgakAPI
{

    // Save New User
    // TODO: Merge TreeController->saveTreeUser + AdminController->saveUser

    public function saveUser(Request $request)
    {
        $user = User::where('nric', $request['user']['nric'])->first();

        if($user) {

            // UPDATE

            $user->name = $request['user']['name'];
            $user->nric = $request['user']['nric'];
            $user->email = $request['user']['email'];

            $user->name_slug = $this->generateSlug($user->email);

            $user->role = $request['role'];
            $user->mobile = $request['user']['mobile'];
            $user->org_code = $request['nodeMaster'];

            // password
            if(isset($request['user']['password']))
                $user->password = \Hash::make($request['user']['password']);

            // reset role
            $reset_role = DB::table('role_user')
                            ->where('user_id', $user->id)
                            ->delete();

            // re-assign role
            $arr_role = $request['role'];

            for($x = 0; $x < count($arr_role); $x++) {
                $role = Role::where('id', '=', $arr_role[$x])->first();
                $user->roles()->attach($role->id);
            }

            $user->save();

            AgakLogger::activity(
                Session::get('nric'),
                Session::get('uid'),
                'core',
                'update_user',
                'update user: '.$user->name,
                url()->current(),
                $user->uid
            );

        } else {

            // INSERT

            $user = new User;
            $user->uid = uniqid('', true);
            $user->name = $request['user']['name'];
            $user->nric = $request['user']['nric'];
            $user->email = $request['user']['email'];

            $user->name_slug = $this->generateSlug($user->email);

            $user->role = $request['role'];
            $user->mobile = $request['user']['mobile'];
            $user->org_code = $request['nodeMaster'];
            // $user->master_admin = 1;

            // password
            if(isset($request['user']['password']))
                $user->password = \Hash::make($request['user']['password']);

            $user->save();

            AgakLogger::activity(
                Session::get('nric'),
                Session::get('uid'),
                'core',
                'create_user',
                'create user: '.$user->name,
                url()->current(),
                $user->uid
            );

            // assign role
            $arr_role = $request['role'];

            for($x = 0; $x < count($arr_role); $x++) {
                $role = Role::where('id', '=', $arr_role[$x])->first();
                $user->roles()->attach($role->id);
            }

        }

        return response()->json('200');
    }

    ///////////////////
    // Generate Slug //
    ///////////////////

    public static function generateSlug($val = '')
    {
        $val = str_ireplace('.gov.my', '', $val);
        $val = preg_replace('/@|\.|_/', '-', $val);

        return $val;
    }

    ///////////////////////
    // Organisation Tree //
    ///////////////////////

    public static function masterTree($nodeId = "")
    {
        if($nodeId == "") {
            $nodeId = Session::get('org_code');
        }

        $master_tree = DB::select('SELECT node.code, node.description, (COUNT(parent.description) - (sub_tree.depth + 1)) AS depth
                        FROM master_tree AS node,
                                master_tree AS parent,
                                master_tree AS sub_parent,
                                (
                                        SELECT node.code, node.description, (COUNT(parent.description) - 1) AS depth
                                        FROM master_tree AS node,
                                        master_tree AS parent
                                        WHERE node.lft BETWEEN parent.lft AND parent.rgt
                                        AND node.code = :nodeId
                                        GROUP BY node.description
                                        ORDER BY node.lft
                                )AS sub_tree
                        WHERE node.lft BETWEEN parent.lft AND parent.rgt
                                AND node.lft BETWEEN sub_parent.lft AND sub_parent.rgt
                                AND sub_parent.code = sub_tree.code
                        GROUP BY node.code
                        ORDER BY node.lft',['nodeId' => $nodeId]);

        return $master_tree;
    }

    public function fullNode($nodeId = "")
    {
        if($nodeId == "") {
            $nodeId = Session::get('org_code');
        }

        $pNode = $this->parentNode($nodeId);
        $cNode = $this->childNode($nodeId);

        $array_all = array_merge($pNode, $cNode);

        return $array_all;
        // return response()->json($array_all);
    }

    public static function parentNode($nodeId = "") {

        if($nodeId == "") {
            $nodeId = Session::get('org_code');
        }

        $master_tree_upper = DB::select('SELECT parent.code, parent.description
                                        FROM master_tree AS node, master_tree AS parent
                                        WHERE node.lft BETWEEN parent.lft AND parent.rgt
                                        AND node.code = :nodeId AND parent.id > 1
                                        AND parent.code <> :nodeId2
                                        ORDER BY parent.lft', ['nodeId' => $nodeId, 'nodeId2' => $nodeId]);

        return $master_tree_upper;
    }

    public static function childNode($nodeId = "") {

        if($nodeId == "") {
            $nodeId = Session::get('org_code');
        }

        $master_tree = DB::select('SELECT node.code, node.description, (COUNT(parent.description) - (sub_tree.depth + 1)) AS depth
                        FROM master_tree AS node,
                                master_tree AS parent,
                                master_tree AS sub_parent,
                                (
                                        SELECT node.code, node.description, (COUNT(parent.description) - 1) AS depth
                                        FROM master_tree AS node,
                                        master_tree AS parent
                                        WHERE node.lft BETWEEN parent.lft AND parent.rgt
                                        AND node.code = :nodeId
                                        GROUP BY node.description
                                        ORDER BY node.lft
                                )AS sub_tree
                        WHERE node.lft BETWEEN parent.lft AND parent.rgt
                                AND node.lft BETWEEN sub_parent.lft AND sub_parent.rgt
                                AND sub_parent.code = sub_tree.code
                        GROUP BY node.code
                        ORDER BY node.lft',['nodeId' => $nodeId]);


        return $master_tree;
    }
}
