<?php

namespace Mhafizhasan\AgakCore;

use DB;

/**
 *
 */
class AgakLogger
{
    public static function activity($nric, $uid, $module, $action, $description, $url, $fuid) {

        $id = DB::table('activity_log')
            ->insertGetId([
                'nric' => $nric,
                'uid' => $uid,
                'module' => $module,
                'action' => $action,
                'description' => $description,
                'url' => $url,
                'fuid' => $fuid
            ]);

        return $id;
    }

    public static function feed($uid, $module, $action, $title, $description, $scope, $icon, $url) {

        $sc = '';
        if(isset($scope) && is_array($scope)) {
            $sc = "|";
            foreach($scope as $key => $val) {
                $sc .= $val . "|";
            }
        }

        $id = DB::table('feeds_log')
            ->insertGetId([
                'uid' => $uid,
                'module' => $module,
                'action' => $action,
                'title' => $title,
                'description' => $description,
                'scope' => $sc,
                'icon' => $icon,
                'url' => $url
            ]);

        return $id;
    }
}
