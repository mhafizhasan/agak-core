<?php

namespace Mhafizhasan\AgakCore;

use Illuminate\Support\Facades\Session;
use DB;

/**
 *
 */
class AgakAPI
{
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
            $nodeId = Session::get('agency_code');
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

    public static function upperNode() {

        if($nodeId == "") {
            $nodeId = Session::get('agency_code');
        }

        $master_tree_upper = DB::select('SELECT parent.code, parent.description
                                        FROM master_tree AS node, master_tree AS parent
                                        WHERE node.lft BETWEEN parent.lft AND parent.rgt
                                        AND node.code = :nodeId AND parent.id > 1
                                        AND parent.code <> :nodeId2
                                        ORDER BY parent.lft', ['nodeId' => $nodeId, 'nodeId2' => $nodeId]);

        return $master_tree_upper;
    }

    public static function belowNode() {

        if($nodeId == "") {
            $nodeId = Session::get('agency_code');
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
