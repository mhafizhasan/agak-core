<?php

namespace Mhafizhasan\AgakCore;

use DB;

/**
 *
 */
class AgakLogger
{
    public static function log($uid, $type, $description, $affected_uid, $scope, $mode) {

        $sc = '';
        if(isset($scope) && is_array($scope)) {
            $sc = "|";
            foreach($scope as $key => $val) {
                $sc .= $val . "|";
            }
        }

        $id = DB::table('activity_log')
            ->insertGetId([
                'uid' => $uid,
                'type' => $type,
                'description' => $description,
                'affected_uid' => $affected_uid,
                'scope' => $sc,
                'mode' => $mode
            ]);

        return $id;
    }
}
