<?php

namespace Mhafizhasan\AgakCore\Middleware;

use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Illuminate\Support\Facades\Session;

use App\Modules\Core\Models\User;

use DB;
use Closure;
use AgakAPI;

/**
 *
 */
class AgakGateKeeper extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // If no Session redirect to logut page
        if(!Session::get('uid')) {
            return redirect('/');
        }


        $user = User::where('uid', '=', Session::get('uid'))->first();

        // $url = \Request::path();
        $url = \Request::segment(1); // Get first url segment

        $permitted_path = array(
            "dashboard",
            "profile",
            "module",
            "user"
        );

        if($user->hasRole(['developer','sysadmin'])) {

            $developer_path = array(
                "developer"
            );

            $permitted_path = array_merge($permitted_path, $developer_path);
        }

        if($user->hasRole(['admin','sysadmin'])) {

            $admin_path = array(
                "admin",
                "agensi"
            );

            $permitted_path = array_merge($permitted_path, $admin_path);
        }

        if($user->hasRole('sysadmin')) {

            $sysadmin_path = array(
                "role"
            );

            $permitted_path = array_merge($permitted_path, $sysadmin_path);
        }

        // Check subscribed modules
        if(!in_array($url, $permitted_path)) {

            $upper_tree = AgakAPI::parentNode($user->org_code, false);

            $tree_arr = array();
            foreach($upper_tree as $val) {
                array_push($tree_arr, $val->code);
            }
            // Add current user node to array
            array_push($tree_arr, $user->org_code);

            // Get modules subscribed by parent organisation
            $m_organisation = DB::table('modules')
                                ->join('module_subscriber', 'modules.code', '=', 'module_subscriber.module_code')
                                ->where('modules.scope','organisasi')
                                ->whereIn('module_subscriber.organisation_code', $tree_arr)
                                ->select('modules.url')
                                ->get();

            // Get individual modules
            $m_individu = DB::table('modules')
                                ->join('module_subscriber', 'modules.code', '=', 'module_subscriber.module_code')
                                ->where('modules.scope','individu')
                                ->where('module_subscriber.subscriber_uid', $user->uid)
                                ->select('modules.url')
                                ->get();

            $valid_path = array_merge($m_individu, $m_organisation);

            $ok = 0;
            foreach($valid_path as $vp) {
                if($url === $vp->url)
                    $ok = 1;
            }

            if($ok != 1) {
                abort(400, 'Unauthorized area.');
            }

        }

        return $next($request);
    }
}
