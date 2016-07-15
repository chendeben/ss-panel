<?php

namespace App\Models;

/**
 * Node Model
 */

use App\Utils\Tools;

class Node extends Model

{
    protected $table = "ss_node";


    public function getLastNodeInfoLog()
    {
        $id = $this->attributes['id'];
        $log = NodeInfoLog::where('node_id', $id)->orderBy('id', 'desc')->first();
        if ($log == null) {
            return null;
        }
        return $log;
    }

    public function getNodeUptime()
    {
        $log = $this->getLastNodeInfoLog();
        if ($log == null) {
            return "暂无数据";
        }
        return Tools::secondsToTime((int)$log->uptime);
    }

	  public function getNodeUpRate()
    {
        $id = $this->attributes['id'];
        $log = NodeOnlineLog::where('node_id', $id)->where('log_time', '>=',time()-86400)->count();
		
		return $log/1440;
    }
    
    public function getNodeLoad()
    {
        $id = $this->attributes['id'];
        $log = NodeInfoLog::where('node_id', $id)->orderBy('id', 'desc')->whereRaw('`log_time`%1800<60')->limit(48)->get();
        if ($log == null) {
            return "暂无数据";
        }
        return $log->load;
    }

    public function getLastNodeOnlineLog()
    {
        $id = $this->attributes['id'];
/*        $log = NodeOnlineLog::where('node_id', $id)->orderBy('id', 'desc')->first();*/
        $log = NodeOnlineLog::where('node_id', $id)->orderBy('id', 'desc')->whereRaw('`log_time`%1800<60')->limit(48)->get();
        if ($log == null) {
            return null;
        }
        return $log;
    }

    function getOnlineUserCount()
    {
/*        $log = $this->getLastNodeOnlineLog();*/
        $id = $this->attributes['id'];
        $log = NodeOnlineLog::where('node_id',$id)->orderBy('id', 'desc')->first();
        if ($log == null) {
            return "暂无数据";
        }
        return $log->online_user;
    }

    function getTrafficFromLogs()
    {
        $id = $this->attributes['id'];
        $traffic = TrafficLog::where('node_id', $id)->sum('u') + TrafficLog::where('node_id', $id)->sum('d');
        if ($traffic == 0) {
            return "暂无数据";
        }
        return Tools::flowAutoShow($traffic);
    }

}
