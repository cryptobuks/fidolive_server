<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiEquipmentController extends Controller
{
	public function getListData(Request $request)
    {
    	$data = [];
        $data['errorCode'] = 'er0000';
        if($request->distributor_id === null || $request->distributor_id === '') {
        	$sql = 'SELECT pi.id AS id, pi.no, pi.mac, pi.name, pi.store_id, pi.distributor_id, pi.machine_id, pi.machine_name, cpi.status AS status, cpi.ipv4, cpi.z_mac, cpi.z_ipv4, cpi.version FROM iteam_pi AS pi LEFT JOIN iteam_connect_pi AS cpi ON cpi.pi_id=pi.id  WHERE pi.is_delete=0 ORDER BY cpi.status DESC, pi.id ASC';
        	$data['data'] = DB::connection('mysql_video')->select($sql);

        	if($data['data']) {
        		$sql = 'SELECT id, fidoStoreId, name FROM store';
        		$store = DB::connection('mysql')->select($sql);
        	}
        } else {
        	$sql = 'SELECT pi.id AS id, pi.no, pi.mac, pi.name, pi.store_id, pi.distributor_id, pi.machine_id, pi.machine_name, cpi.status AS status, cpi.ipv4, cpi.z_mac, cpi.z_ipv4, cpi.version FROM iteam_pi AS pi LEFT JOIN iteam_connect_pi AS cpi ON cpi.pi_id=pi.id WHERE pi.distributor_id=:distributor_id AND pi.is_delete=0 ORDER BY cpi.status DESC, pi.id ASC';
        	$data['data'] = DB::connection('mysql_video')->select($sql, ['distributor_id' => $request->distributor_id]);

        	if($data['data']) {
        		$sql = 'SELECT id, fidoStoreId, name FROM store WHERE distributorId=:distributor_id';
        		$store = DB::connection('mysql')->select($sql, ['distributor_id' => $request->distributor_id]);
        	}
        }

        if($data['data']) {
        	$sql = 'SELECT id, name FROM distributor';
        	$distributor = DB::connection('mysql')->select($sql);

        	foreach ($data['data'] as $key => $value) {
        		$d = collect($distributor)->where('id', $value->distributor_id)->first();
        		$value->d_name = $d->name;
        		if($value->store_id) {
        			$s = collect($store)->where('id', $value->store_id)->first();
                    if($s) {
                        $value->s_name = $s->name.'('.$s->fidoStoreId.')';
                    }
        		} else {
        			$value->s_name = '';
        		}
        	}
        }
        
        return compact('data');
    }

    public function getDistributorData(Request $request)
    {
    	$data = [];
        $data['errorCode'] = 'er0000';
        $data['data'] = [];
        $sql = 'SELECT id, name, nation, CONCAT( name, " ", nation ) AS fullname  FROM distributor';
        $distributor = DB::connection('mysql')->select($sql);
        if($distributor) {
        	foreach ($distributor as $key => $value) {
        		if($request->name) {
        			//if(strpos(strtolower($value->name), strtolower($request->name)) !== false || strpos(strtolower($value->nation), strtolower($request->name)) !== false) {
        				array_push($data['data'], ['id' => $value->id, 'value' => $value->fullname]);
        			//}
        		} else {
        			array_push($data['data'], ['id' => $value->id, 'value' => $value->fullname]);
        		}
        	}
        }

        return compact('data');
    }

    public function getStoreData(Request $request)
    {
    	$data = [];
        $data['errorCode'] = 'er0000';
        $data['data'] = [];
        $sql = 'SELECT id, fidoStoreId, name, CONCAT( name, " ", fidoStoreId ) AS fullname, distributorId  FROM store WHERE distributorId=:distributorId';
        $store = DB::connection('mysql')->select($sql, ['distributorId' => $request->distributorId]);
        if($store) {
        	foreach ($store as $key => $value) {
        		if($request->name) {
        			//if(strpos(strtolower($value->name), strtolower($request->name)) !== false || strpos(strtolower($value->fidoStoreId), strtolower($request->name)) !== false) {
        				array_push($data['data'], ['id' => $value->id, 'value' => $value->fullname, 'distributorId' => $value->distributorId]);
        			//}
        		} else {
        			array_push($data['data'], ['id' => $value->id, 'value' => $value->fullname, 'distributorId' => $value->distributorId]);
        		}
        	}
        }

        return compact('data');
    }

    public function getMachineData(Request $request)
    {
        $data = [];
        $data['errorCode'] = 'er0000';
        $data['data'] = [];
        $data['use'] = [];
        $sql = 'SELECT id, storeId, distributorId, CONCAT( id, " ", name ) AS fullname  FROM machine WHERE storeId=:storeId';
        $machine = DB::connection('mysql')->select($sql, ['storeId' => $request->storeId]);
        $sql = 'SELECT machine_id  FROM iteam_pi WHERE machine_id IS NOT NULL AND is_delete=0 AND store_id=:storeId';
        $machineAsUse = DB::connection('mysql_video')->select($sql, ['storeId' => $request->storeId]);
        $mUse = [];
        foreach($machineAsUse as $key => $item) {
            array_push($mUse, $item->machine_id);
        }
        $data['use'] = $mUse;
        if($machine) {
            foreach ($machine as $key => $value) {
                //if(!in_array($value->id,$mUse)){
                    array_push($data['data'], ['id' => $value->id, 'value' => $value->fullname, 'storeId' => $value->storeId, 'distributorId' => $value->distributorId]);
                //}
                /*if($request->name) {
                    if(strpos(strtolower($value->id), strtolower($request->name)) !== false) {

                        array_push($data['data'], ['id' => $value->id, 'value' => $value->id, 'storeId' => $value->storeId, 'distributorId' => $value->distributorId]);
                    }
                } else {
                    array_push($data['data'], ['id' => $value->id, 'value' => $value->id, 'storeId' => $value->storeId, 'distributorId' => $value->distributorId]);
                }*/

            }
        }

        /*if(!$data['data']) {
            $msg = trans('message');
            array_push($data['data'], ['id' => -1, 'value' => $msg['data']['d0004'], 'storeId' => '', 'distributorId' => '']);
        }*/

        return compact('data');
    }

    public function add(Request $request)
    {
    	$data = [];
    	//$no = 'v-'.date("yW");
        $no = 'v';
    	$sql = 'SELECT no FROM iteam_pi WHERE no LIKE :no ORDER BY id DESC LIMIT 1';
    	//$result = DB::connection('mysql')->select($sql, ['no' => $no.'%']);
        $result = DB::connection('mysql_video')->select($sql, ['no' => $no.'0%']);
    	if($result) {
    		$s = collect($result)->first();
    		//$s = explode('-',$s->no);
            $s = explode('v',$s->no);
    		//$s = $s[2] * 1 + 1;
            $s = $s[1] * 1 + 1;
    		//$no = $no.'-'.str_pad($s, 4, '0', STR_PAD_LEFT);
            $no = $no.str_pad($s, 4, '0', STR_PAD_LEFT);
    	} else {
    		//$no = $no.'-0001';
            $no = $no.'0001';
    	}
    	$sql = 'INSERT INTO iteam_pi (mac, no, name, distributor_id, store_id, machine_id, machine_name, description) VALUES (:mac, :no, :name, :distributo, :store, :machine, :machine_name, :description)';
    	$result = DB::connection('mysql_video')->insert($sql, [
    		'mac' => $request->mac,
    		'no' => $no,
    		'name' => $request->name,
    		'distributo' => $request->distributorId,
    		'store' => $request->storeId,
            'machine' => $request->machineId,
            'machine_name' => $request->machineName,
    		'description' => $request->description
    	]);
    	if($result) {
            $data['errorCode'] = 'er0000';
        } else {
            $data['errorCode'] = 'er0001';
        }
        return compact('data');
    }

    public function getData(Request $request)
    {
        $data = [];
        $data['errorCode'] = 'er0000';
        $data['data'] = [];
        $sql = 'SELECT id, mac, name, distributor_id AS distributorId, store_id AS storeId, machine_id AS machineId, machine_name AS machineName, description FROM iteam_pi WHERE id=:id';
        $result = DB::connection('mysql_video')->select($sql, [ 'id' => $request->id ]);
        if($result) {
            /*$result[0]->distributor = '';
            $sql = 'SELECT CONCAT( name, " ", nation ) AS fullname FROM distributor WHERE id=:id';
            $distributor = DB::connection('mysql')->select($sql, [ 'id' => $result[0]->distributorId ]);
            if($distributor) {
                $result[0]->distributor = $distributor[0]->fullname;
            }*/
            /*if($result[0]->storeId) {
                $sql = 'SELECT CONCAT( name, " ", fidoStoreId ) AS fullname  FROM store WHERE id=:id';
                $store = DB::connection('mysql')->select($sql, [ 'id' => $result[0]->storeId ]);
                $result[0]->store = $store[0]->fullname;
            } else {
                $result[0]->store = '';
            }*/
            /*if($result[0]->machineId) {
                $sql = 'SELECT CONCAT( id, " ", name ) AS fullname  FROM machine WHERE id=:id';
                $machine = DB::connection('mysql')->select($sql, [ 'id' => $result[0]->machineId ]);
                $result[0]->machineValue = $machine[0]->fullname;
            }*/
            $data['data'] = $result[0];            
        }
        
        return compact('data');
    }

    public function update(Request $request)
    {
        $data = [];
        $sql = 'UPDATE iteam_pi SET mac=:mac, name=:name, distributor_id=:distributor_id, store_id=:store_id, machine_id=:machine_id, machine_name=:machine_name, description=:description WHERE id=:id';
        $result = DB::connection('mysql_video')->update($sql, [
            'mac' => $request->mac,
            'name' => $request->name,
            'distributor_id' => $request->distributorId,
            'store_id' => $request->storeId,
            'machine_id' => $request->machineId,
            'machine_name' => $request->machineName,
            'description' => $request->description,
            'id' => $request->id
        ]);
        if($result <= 1) {
            $data['errorCode'] = 'er0000';
        } else {
            $data['errorCode'] = 'er0001';
        }

        return compact('data');
    }

    public function delete(Request $request)
    {
    	$data = [];
        $sql = 'UPDATE iteam_pi SET is_delete=1 WHERE id=:id';
        $result = DB::connection('mysql_video')->update($sql, ['id' => $request->id]);
        if($result) {
            $data['errorCode'] = 'er0000';
        } else {
            $data['errorCode'] = 'er0001';
        }
        return compact('data');
    }

    public function getStatus(Request $request)
    {
        $data = [];
        $data['errorCode'] = 'er0000';
        $data['data'] = 0;
        $sql = 'SELECT status FROM iteam_connect_pi WHERE pi_id=:id';
        $result = DB::connection('mysql')->select($sql, ['id' => $request->id]);
        if($result) {
            $data['data'] = $result[0]->status;
        }
        
        return compact('data');
    }
    public function setStatus(Request $request)
    {
        $data = [];
        $sql = 'UPDATE iteam_connect_pi SET ipv4=NULL, mac=NULL, z_ipv4=NULL, z_mac=NULL, status=0, version=NULL';
        $result = DB::connection('mysql_video')->update($sql);
        if($result) {
            $data['errorCode'] = 'er0000';
        } else {
            $data['errorCode'] = 'er0001';
        }
        return compact('data');
    }

    public function getPort(Request $request)
    {
        $data = [];
        $data['errorCode'] = 'er0000';
        $data['data'] = [];
        $sql = 'SELECT port_no, port_name FROM iteam_port_used WHERE pi_id=:id';
        $result = DB::connection('mysql_video')->select($sql, ['id' => $request->id]);
        if($result) {
            $data['data'] = $result;
        }
        return compact('data');
    }

    public function getName(Request $request)
    {
        $data = [];
        $data['errorCode'] = 'er0000';
        $data['data'] = [];
        $sql = 'SELECT no, name, machine_id, machine_name FROM iteam_pi  WHERE id=:id';
        $result = DB::connection('mysql_video')->select($sql, ['id' => $request->id]);
        if($result) {
            $data['data'] = $result;
        }
        return compact('data');
    }

    public function setAudio(Request $request)
    {
        $data = [];
        $sql = 'UPDATE iteam_pi SET audio=:audio WHERE id=:id';
        $result = DB::connection('mysql')->update($sql, [
            'audio' => $request->audio===1 ? 0 : 1,
            'id' => $request->id
        ]);
        if($result) {
            $data['errorCode'] = 'er0000';
        } else {
            $data['errorCode'] = 'er0001';
        }
        return compact('data');

    }
}