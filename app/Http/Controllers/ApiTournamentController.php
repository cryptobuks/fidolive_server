<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiTournamentController extends Controller
{

    public function getPlayersData(Request $request)
    {
        $data = [];
        $data['errorCode'] = 'er0000';
        $data['players'] = [];
        $sql = 'SELECT id, fidoID, name, nickName FROM users';
        $players = DB::connection('mysql')->select($sql);
        foreach ($players as $key => $value) {
            $name = base64_encode(base64_decode($value->name)) === $value->name? base64_decode($value->name) : $value->name;
            $nickName = base64_encode(base64_decode($value->nickName)) === $value->nickName? base64_decode($value->nickName) : $value->nickName;
            $fidoID = $value->fidoID;
            $d = '';
            if($name !== '') {
                $d = $name;
            }
            if($d === '') {
               $d = $nickName;
            } else {
               $d = $d.'('.$nickName.')';
            }
            $d = $d.' '.$fidoID;
            if(strpos(strtolower($d), strtolower($request->name)) !== false) {
                array_push($data['players'], ['id' => $value->id, 'value' => base64_encode($d)]);
            }
        }
        return compact('data');
    }

    public function getEquipmentData(Request $request) {
        $data = [];
        $data['errorCode'] = 'er0000';
        $data['equipments'] = [];
        if($request->distributor_id === null || $request->distributor_id === '') {
            $equipments = DB::connection('mysql')->select('SELECT id, name, store_id FROM iteam_pi');
        } else {
            $sql = 'SELECT id, name, store_id FROM iteam_pi WHERE distributor_id=:distributor_id';
            $equipments = DB::connection('mysql')->select($sql, ['distributor_id' => $request->distributor_id]);
        }
        if($request->distributor_id === null || $request->distributor_id === '') {
            $stores = DB::connection('mysql')->select('SELECT id, name, fidoStoreId FROM store');
        } else {
            $sql = 'SELECT id, name, fidoStoreId FROM store WHERE distributorId=:distributor_id';
            $stores = DB::connection('mysql')->select($sql, ['distributor_id' => $request->distributor_id]);
        }
        $s = [];
        foreach ($stores as $key => $value) {
            $s[$value->id] = [ 'fidoStoreId' => $value->fidoStoreId, 'name' => $value->name ];
        }
        foreach ($equipments as $key => $value) {
            $d = $value->name;
            if($value->store_id !== NULL) {
                $d = $d.' '.$s[$value->store_id]['name'].' '.$s[$value->store_id]['fidoStoreId'];
            }
            if(strpos(strtolower($d), strtolower($request->name)) !== false) {
                array_push($data['equipments'], ['id' => $value->id, 'value' => $d]);
            }
        }
        
        return compact('data');
    }

    public function addTournament(Request $request) {
        $data = [];
        $sql = 'INSERT INTO iteam_tournament (name) VALUES (:name)';
        $result = DB::connection('mysql')->insert($sql, ['name' => $request->data['name']]);
        if($result) {
            if(count($request->data['players']) > 0) {
                $id = DB::connection('mysql')->getPdo()->lastInsertId();
                $sql = 'INSERT INTO iteam_tournament_players (tournament_id, track, u_id, u_name, p_id, p_name) VALUES ';
                foreach ($request->data['players'] as $key => $value) {
                    $sql .= '('.$id.', '.($key+1).', \''.$value['u_id'].'\', \''.$value['u_name'].'\', '.$value['p_id'].', \''.$value['p_name'].'\')';
                    if($key < count($request->data['players'])-1) {
                        $sql .= ',';
                    }
                }
                $result = DB::connection('mysql')->insert($sql);
                if($result) {
                    $data['errorCode'] = 'er0000';
                } else {
                    $data['errorCode'] = 'er0001';
                }
            } else {
                $data['errorCode'] = 'er0000';
            }
        } else {
            $data['errorCode'] = 'er0001';
        }
        
        return compact('data');
    }

    public function getTournamentListData(Request $request) {
        /*$data = [];
        $data['errorCode'] = 'er0000';
        $sql = 'SELECT t.id, t.name, t.createTime, count(p.id) AS count FROM iteam_tournament AS t LEFT JOIN iteam_tournament_players AS p ON p.tournament_id=t.id WHERE is_delete=0 GROUP BY t.id';
        $data['data'] = DB::connection('mysql')->select($sql);
        return compact('data');*/
        $data = [];
        $data['errorCode'] = 'er0000';
        $sql = 'SELECT id, name, timezone FROM tournament';
        $data['data'] = DB::connection('mysql')->select($sql);
        if($request->distributor_id === null || $request->distributor_id === '') {
            $sql = 'SELECT id, name, store_id FROM iteam_pi WHERE is_delete=0 ORDER BY id ASC';
            $data['pi'] = DB::connection('mysql')->select($sql);
        } else {
            $sql = 'SELECT id, name, store_id FROM iteam_pi WHERE is_delete=0 AND distributor_id=:distributor_id ORDER BY id ASC';
            $data['pi'] = DB::connection('mysql')->select($sql, ['distributor_id' => $request->distributor_id]);
        }
        array_unshift($data['pi'] , (object)['id' => 0, "name" => "無設備", "store_id" => 0]);
        return compact('data');
    }

    public function getTournamentGroupData(Request $request)
    {
        $data = [];
        $data['errorCode'] = 'er0000';
        $sql = 'SELECT id, groupName FROM tournament_group WHERE tournamentId=:id';
        $data['data'] = DB::connection('mysql')->select($sql, ['id' => $request->id]);
        return compact('data');
    }

    public function getTournamentBattleData(Request $request)
    {
        $data = [];
        $data['errorCode'] = 'er0000';
        $sql = 'SELECT id, tournamentId, sequence, isNetworkGame, homeStoreId, awayStoreId, homeTeamId, awayTeamId, homeMachineId, awayMachineId FROM tournament_battle WHERE tournamentId=:id AND groupId=:groupId ORDER BY sequence ASC';
        $data['data'] = DB::connection('mysql')->select($sql, ['id' => $request->id, 'groupId' => $request->groupId]);
        $sql = 'SELECT id, name FROM store';
        $tStore = DB::connection('mysql')->select($sql);
        $sql = 'SELECT m.teamId, u.name, u.nickName FROM tournament_teammember AS m LEFT JOIN users AS u ON u.id=m.userId WHERE tournamentId=:id AND groupId=:groupId';
        $tTeam = DB::connection('mysql')->select($sql, ['id' => $request->id, 'groupId' => $request->groupId]);
        $sql = 'SELECT team_id, pi_id FROM iteam_tournament_pi';
        $tPi = DB::connection('mysql')->select($sql);
        $sql = 'SELECT id, machine_id FROM iteam_pi';
        $pi = DB::connection('mysql')->select($sql);
        $sql = 'SELECT b_id, audio FROM iteam_tournament_audio';
        $audio = DB::connection('mysql')->select($sql);
        foreach ($data['data'] as $key => $value) {
            $d = collect($tStore)->where('id', $value->homeStoreId)->first();
            $value->homeStoreName = '';
            if($d) {
                $value->homeStoreName = $d->name;
            }
            $d = collect($tStore)->where('id', $value->awayStoreId)->first();
            $value->awayStoreName = '';
            if($d) {
                $value->awayStoreName = $d->name;
            }

            $value->homeTeamName = '';
            $value->awayTeamName = '';
            foreach ($tTeam as $h_key => $h_value) {
                if($h_value->teamId === $value->homeTeamId) {
                    if($value->homeTeamName !== '') {
                        $value->homeTeamName .= '/';
                    }
                    $value->homeTeamName .= base64_decode($h_value->name);
                }

                if($h_value->teamId === $value->awayTeamId) {
                    if($value->awayTeamName !== '') {
                        $value->awayTeamName .= '/';
                    }
                    $value->awayTeamName .= base64_decode($h_value->name);
                }
            }

            $d = collect($tPi)->where('team_id', $value->homeTeamId)->first();
            $value->homePi = '';
            if($d) {
                $value->homePi = $d->pi_id;
            } else {
                if($value->homeMachineId !== '') {
                    $d = collect($pi)->where('machine_id', $value->homeMachineId)->first();
                    if($d) {
                        $value->homePi = $d->id;
                    }
                }
            }
            $d = collect($tPi)->where('team_id', $value->awayTeamId)->first();
            $value->awayPi = '';
            if($d) {
                $value->awayPi = $d->pi_id;
            } else {
                if($value->awayMachineId !== '') {
                    $d = collect($pi)->where('machine_id', $value->awayMachineId)->first();
                    if($d) {
                        $value->awayPi = $d->id;
                    }
                }
            }
            $value->audio = 0;
            $d = collect($audio)->where('b_id', $value->id)->first();
            if($d) {
                $value->audio = $d->audio;
            }
        }
        return compact('data');
    }

    public function updateTournamentPiData(Request $request)
    {
        $data = [];
        if(!($request->homePi === null || $request->homePi === '') && !($request->homePi === null || $request->homePi === '')) {
            $sql = 'SELECT id FROM iteam_tournament_audio WHERE b_id=:id';
            $d = DB::connection('mysql')->select($sql, ['id' => $request->id]);
            if($d) {
                $sql = 'UPDATE iteam_tournament_audio SET audio=:audio WHERE id=:id';
                DB::connection('mysql')->update($sql, [
                    'id' => $d[0]->id,
                    'audio' => $request->audio
                ]);
            } else {
                $sql = 'INSERT INTO iteam_tournament_audio (b_id, audio) VALUES (:id, :audio)';
                DB::connection('mysql')->insert($sql, [
                    'id' => $request->id,
                    'audio' => $request->audio
                ]);
            }
        }
        $sql = 'SELECT id, pi_id FROM iteam_tournament_pi WHERE team_id=:homeTeamId';
        $d = DB::connection('mysql')->select($sql, ['homeTeamId' => $request->homeTeamId]);
        if($d) {
            if(!($request->homePi === null || $request->homePi === '')) {
                if($d[0]->pi_id === $request->homePi) {
                    $result = 1;
                } else {
                    $sql = 'UPDATE iteam_tournament_pi SET pi_id=:homePi WHERE id=:id';
                    $result = DB::connection('mysql')->update($sql, [
                        'id' => $d[0]->id,
                        'homePi' => $request->homePi
                    ]);
                }
            } else {
                $sql = 'DELETE FROM iteam_tournament_pi WHERE id=:id';
                $result = DB::connection('mysql')->delete($sql, [
                    'id' => $d[0]->id
                ]);
            }
        } else {
            if(!($request->homePi === null || $request->homePi === '')) {
                $sql = 'INSERT INTO iteam_tournament_pi (team_id, pi_id) VALUES (:homeTeamId, :homePi)';
                $result = DB::connection('mysql')->insert($sql, [
                    'homeTeamId' => $request->homeTeamId,
                    'homePi' => $request->homePi
                ]);
            } else {
                $result = 1;
            }
        }
        if($result) {
            if($request->homeTeamId !== $request->awayTeamId) {
                $sql = 'SELECT id, pi_id FROM iteam_tournament_pi WHERE team_id=:awayTeamId';
                $d = DB::connection('mysql')->select($sql, ['awayTeamId' => $request->awayTeamId]);
                if($d) {
                    if(!($request->awayPi === null || $request->awayPi === '')) {
                        if($d[0]->pi_id === $request->awayPi) {
                            $result = 1;
                        } else {
                            $sql = 'UPDATE iteam_tournament_pi SET pi_id=:awayPi WHERE id=:id';
                            $result = DB::connection('mysql')->update($sql, [
                                'id' => $d[0]->id,
                                'awayPi' => $request->awayPi
                            ]);
                        }
                    } else {
                        $sql = 'DELETE FROM iteam_tournament_pi WHERE id=:id';
                        $result = DB::connection('mysql')->delete($sql, [
                            'id' => $d[0]->id
                        ]);
                    }
                } else {
                    if(!($request->awayPi === null || $request->awayPi === '')) {
                        $sql = 'INSERT INTO iteam_tournament_pi (team_id, pi_id) VALUES (:awayTeamId, :awayPi)';
                        $result = DB::connection('mysql')->insert($sql, [
                            'awayTeamId' => $request->awayTeamId,
                            'awayPi' => $request->awayPi
                        ]);
                    } else {
                        $result = 1;
                    }
                }
                if($result) {
                    $data['errorCode'] = 'er0000';
                } else {
                    $data['errorCode'] = 'er0001';
                }
            } else {
                $data['errorCode'] = 'er0000';
            }
        } else {
            $data['errorCode'] = 'er0001';
        }
        return compact('data');
    }

    public function getTmViewData(Request $request)
    {
        $data = [];
        $data['errorCode'] = 'er0000';
        $sql = 'SELECT timezone FROM tournament_battle AS t LEFT JOIN tournament AS tm ON t.tournamentId=tm.id WHERE t.id=:id';
        $t = DB::connection('mysql')->select($sql, ['id' => $request->id]);
        $sql = 'SELECT tm.name AS tournamentName, t.tournamentId, t.groupId, g.groupName, t.homeTeamId, t.awayTeamId, t.homeMachineId, t.awayMachineId, t.sequence, t.isNetworkGame, hs.name AS homeStoreName, ws.name AS awayStoreName, hs.city AS homeStoreCity, ws.city AS awayStoreCity, tr.name AS ruleName, t.roundName FROM tournament_battle AS t LEFT JOIN tournament AS tm ON tm.id=t.tournamentId LEFT JOIN tournament_group AS g ON g.id=t.groupId INNER JOIN ( SELECT id, name, city FROM store ) AS hs ON hs.id=t.homeStoreId INNER JOIN ( SELECT id, name, city FROM store ) AS ws ON ws.id=t.awayStoreId INNER JOIN tournament_rule AS tr ON tr.id=t.ruleId WHERE t.id=:id';
        /*$sql = 'SELECT lg.name AS leagueName, g.groupName, l.homeTeamId, l.awayTeamId, l.sequence, l.isNetworkGame, DATE_ADD(l.matchDate, INTERVAL :timezone hour) AS matchDate FROM league_battle AS l LEFT JOIN league AS lg ON lg.id=l.leagueId LEFT JOIN league_group AS g ON g.id=l.groupId WHERE l.id=:id';*/
        $data['data'] = DB::connection('mysql')->select($sql, ['id' => $request->id]);
        $sql = 'SELECT m.teamId, u.name, u.nickName, m.info1 FROM tournament_teammember AS m LEFT JOIN users AS u ON u.id=m.userId WHERE tournamentId=:id AND groupId=:groupId ORDER BY m.sequence';
        $tTeam = DB::connection('mysql')->select($sql, ['id' => $data['data'][0]->tournamentId, 'groupId' => $data['data'][0]->groupId]);
        $h_player = [];
        $h_info = [];
        $w_player = [];
        $w_info = [];
        foreach ($tTeam as $key => $value) {
            if($value->teamId === $data['data'][0]->homeTeamId) {
                array_push($h_player, base64_decode($value->name));
                array_push($h_info, $value->info1);
            }
            if($value->teamId === $data['data'][0]->awayTeamId) {
                array_push($w_player, base64_decode($value->name));
                array_push($w_info, $value->info1);
            }
        }
        $data['team'] = [];
        $data['team'][0] = [
            'storeName' => $data['data'][0]->homeStoreName,
            'city' => $data['data'][0]->homeStoreCity,
            'player' => $h_player,
            'info' => $h_info,
            'row' => 0,
            'pi' => -1,
            'status' => [true, true]
        ];
        $data['team'][1] = [
            'storeName' => $data['data'][0]->awayStoreName,
            'city' => $data['data'][0]->awayStoreCity,
            'player' => $w_player,
            'info' => $w_info,
            'row' => 0,
            'pi' => -1,
            'status' => [false, false]
        ];
        $sql = 'SELECT pi_id FROM iteam_tournament_pi WHERE team_id=:homeTeamId';
        $homePi = DB::connection('mysql')->select($sql, ['homeTeamId' => $data['data'][0]->homeTeamId]);
        if($homePi) {
            $data['team'][0]['pi'] = $homePi[0]->pi_id;
        } else {
            if($data['data'][0]->homeMachineId !== '') {
                $sql = 'SELECT id FROM iteam_pi WHERE machine_id=:homeMachineId';
                $homePi = DB::connection('mysql')->select($sql, ['homeMachineId' => $data['data'][0]->homeMachineId]);
                if($homePi) {
                    $data['team'][0]['pi'] = $homePi[0]->id;
                }
            }
        }
        $sql = 'SELECT pi_id FROM iteam_tournament_pi WHERE team_id=:awayTeamId';
        $awayPi = DB::connection('mysql')->select($sql, ['awayTeamId' => $data['data'][0]->awayTeamId]);
        if($awayPi) {
            $data['team'][1]['pi'] = $awayPi[0]->pi_id;
        } else {
            if($data['data'][0]->awayMachineId !== '') {
                $sql = 'SELECT id FROM iteam_pi WHERE machine_id=:awayMachineId';
                $awayPi = DB::connection('mysql')->select($sql, ['awayMachineId' => $data['data'][0]->awayMachineId]);
                if($awayPi) {
                    $data['team'][1]['pi'] = $awayPi[0]->id;
                }
            }
        }
        $sql = 'SELECT audio FROM iteam_tournament_audio WHERE b_id=:id';
        $audio = DB::connection('mysql')->select($sql, ['id' => $request->id]);
        if($audio) {
            $data['audio'] = $audio[0]->audio;
        } else {
            $data['audio'] = 0;
        }
        return compact('data');
    }

    public function deleteTournament(Request $request) {
        $data = [];
        $sql = 'UPDATE iteam_tournament SET is_delete=1 WHERE id=:id';
        $result = DB::connection('mysql')->update($sql, ['id' => $request->id]);
        if($result) {
            $data['errorCode'] = 'er0000';
        } else {
            $data['errorCode'] = 'er0001';
        }
        return compact('data');
    }

    public function getTournamentData(Request $request) {
        $data = [];
        $data['errorCode'] = 'er0000';
        $data['data'] = [];
        $data['data']['name'] = '';
        $data['data']['players'] = [];
        $sql = 'SELECT t.name, p.u_id, p.u_name, p.p_id, p.p_name FROM iteam_tournament AS t LEFT JOIN iteam_tournament_players AS p ON p.tournament_id=t.id WHERE t.id=:id ORDER BY p.track ASC';
        $result = DB::connection('mysql')->select($sql, ['id' => $request->id]);
        foreach ($result as $key => $value) {
            $data['data']['name'] = $value->name;
            if($value->u_id) {
                $data['data']['players'][$key] = [
                    'u_id' => $value->u_id,
                    'u_name' => $value->u_name,
                    'p_id' => $value->p_id,
                    'p_name' => $value->p_name
                ];
            }
        }
        return compact('data');
    }

    public function updateTournament(Request $request) {
        $data = [];
        $sql = 'UPDATE iteam_tournament SET name=:name WHERE id=:id';
        $result = DB::connection('mysql')->update($sql, ['name' => $request->name, 'id' => $request->id]);
        if($result <= 1) {
            $players = DB::connection('mysql')->select('SELECT track FROM iteam_tournament_players WHERE tournament_id=:id ORDER BY track ASC', ['id' => $request->id]);
            for($i=0; $i<=7; $i++){
                if(isset($request->players[$i])) {
                    if(isset($players[$i])) {
                        $sql = 'UPDATE iteam_tournament_players SET u_id=\''.$request->players[$i]['u_id'].'\', u_name=\''.$request->players[$i]['u_name'].'\', p_id='.$request->players[$i]['p_id'].', p_name=\''.$request->players[$i]['p_name'].'\' WHERE track='.($i+1).' AND tournament_id='.$request->id;
                        DB::connection('mysql')->update($sql);
                    } else {
                        $sql = 'INSERT INTO iteam_tournament_players (tournament_id, track, u_id, u_name, p_id, p_name) VALUES (:id, :track, :u_id, :u_name, :p_id, :p_name)';
                        DB::connection('mysql')->insert($sql, [
                            'id' => $request->id,
                            'track' => $i+1,
                            'u_id' => $request->players[$i]['u_id'],
                            'u_name' => $request->players[$i]['u_name'],
                            'p_id' => $request->players[$i]['p_id'],
                            'p_name' => $request->players[$i]['p_name']
                        ]);
                    }
                } else {
                    if(isset($players[$i])) {
                        $sql = 'DELETE FROM iteam_tournament_players WHERE tournament_id=:id AND track=:track';
                        DB::connection('mysql')->delete($sql, [
                            'id' => $request->id,
                            'track' => $i+1
                        ]);
                    }
                }
            }
            $data['errorCode'] = 'er0000';
        } else {
            $data['errorCode'] = 'er0001';
        }
        return compact('data');
    }

    public function getTournamentBracketData(Request $request) {
        $data = [];
        $data['errorCode'] = 'er0000';
        $data['name'] = '';
        $data['id'] = '';
        $data['data'] = [];
        $sql = 'SELECT t.id, t.name, p.track, p.u_id, p.u_name, p.p_id, p.p_name, pi.name AS pi_name FROM iteam_tournament AS t LEFT JOIN iteam_tournament_players AS p ON p.tournament_id=t.id LEFT JOIN iteam_pi AS pi ON pi.id=p.p_id WHERE t.is_delete=0 AND t.id=:id';
        $result = DB::connection('mysql')->select($sql, ['id' => $request->id]);
        foreach ($result as $key => $value) {
            $data['name'] = $value->name;
            $data['id'] = $value->id;
            $data['data'][$key] = [
                'track' => $value->track,
                'u_id' => $value->u_id,
                'u_name' => $value->u_name,
                'p_id' => $value->p_id,
                'p_name' => $value->pi_name
            ];
        }
        return compact('data');
    }
}