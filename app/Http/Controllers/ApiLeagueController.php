<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiLeagueController extends Controller
{
	public function getLeagueListData(Request $request)
    {
        $data = [];
        $data['errorCode'] = 'er0000';
        $sql = 'SELECT id, name, timezone FROM league WHERE isDeleted=0 AND status!=5';
        $data['data'] = DB::connection('mysql')->select($sql);
        if($request->distributor_id === null || $request->distributor_id === '') {
            $sql = 'SELECT id, name, store_id FROM iteam_pi WHERE is_delete=0 ORDER BY id ASC';
            $data['pi'] = DB::connection('mysql')->select($sql);
        } else {
            $sql = 'SELECT id, name, store_id FROM iteam_pi WHERE is_delete=0 AND distributor_id=:distributor_id ORDER BY id ASC';
            $data['pi'] = DB::connection('mysql')->select($sql, ['distributor_id' => $request->distributor_id]);
        }
        array_unshift($data['pi'] , (object)['id' => 0, 'name' => '無設備', 'store_id' => 0]);
        return compact('data');
    }

    public function getLeagueGroupData(Request $request)
    {
        $data = [];
        $data['errorCode'] = 'er0000';
        $sql = 'SELECT id, groupName FROM league_group WHERE leagueId=:id';
        $data['data'] = DB::connection('mysql')->select($sql, ['id' => $request->id]);
        return compact('data');
    }

    public function getLeagueBattleData(Request $request)
    {
        $data = [];
        $data['errorCode'] = 'er0000';
        /*$sql = 'SELECT l.id, l.leagueId, l.sequence, l.isNetworkGame, l.homeStoreId, l.awayStoreId, l.homeTeamId, l.awayTeamId, DATE_ADD(l.matchDate, INTERVAL :timezone hour) AS matchDate, hs.name AS homeStoreName, ws.name AS awayStoreName, ht.name AS homeTeamName, wt.name AS awayTeamName FROM league_battle AS l INNER JOIN ( SELECT id, name FROM store ) AS hs ON hs.id=l.homeStoreId INNER JOIN ( SELECT id, name FROM store ) AS ws ON ws.id=l.awayStoreId INNER JOIN ( SELECT id, name FROM league_team ) AS ht ON ht.id=l.homeTeamId INNER JOIN ( SELECT id, name FROM league_team ) AS wt ON wt.id=l.awayTeamId WHERE l.leagueId=:id AND l.groupId=:groupId ORDER BY l.sequence';*/
        /*$sql = 'SELECT id, leagueId, sequence, isNetworkGame, homeStoreId, awayStoreId, homeTeamId, awayTeamId, DATE_ADD(matchDate, INTERVAL :timezone hour) AS matchDate FROM league_battle WHERE leagueId=:id AND groupId=:groupId ORDER BY sequence, matchDate ASC';*/
        $sql = 'SELECT id, leagueId, sequence, gameResult, isNetworkGame, homeStoreId, awayStoreId, homeTeamId, awayTeamId, DATE_ADD(matchDate, INTERVAL :timezone hour) AS matchDate FROM league_battle WHERE leagueId=:id AND groupId=:groupId ORDER BY matchDate, sequence ASC';
        $data['data'] = DB::connection('mysql')->select($sql, ['id' => $request->id, 'groupId' => $request->groupId, 'timezone' => $request->timezone]);
        $sql = 'SELECT id, name FROM store';
        $tStore = DB::connection('mysql')->select($sql);
        $sql = 'SELECT id, name FROM league_team WHERE leagueId=:id AND groupId=:groupId';
        $tTeam = DB::connection('mysql')->select($sql, ['id' => $request->id, 'groupId' => $request->groupId]);
        $sql = 'SELECT team_id, pi_id FROM iteam_league_pi';
        $tPi = DB::connection('mysql')->select($sql);
        $sql = 'SELECT b_id, audio FROM iteam_league_audio';
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

            $d = collect($tTeam)->where('id', $value->homeTeamId)->first();
            $value->homeTeamName = '';
            if($d) {
                $value->homeTeamName = $d->name;
            }
            $d = collect($tTeam)->where('id', $value->awayTeamId)->first();
            $value->awayTeamName = '';
            if($d) {
                $value->awayTeamName = $d->name;
            }

            $d = collect($tPi)->where('team_id', $value->homeTeamId)->first();
            $value->homePi = '';
            if($d) {
                $value->homePi = $d->pi_id;
            }
            $d = collect($tPi)->where('team_id', $value->awayTeamId)->first();
            $value->awayPi = '';
            if($d) {
                $value->awayPi = $d->pi_id;
            }
            $value->audio = 0;
            $d = collect($audio)->where('b_id', $value->id)->first();
            if($d) {
                $value->audio = $d->audio;
            }
        }
        return compact('data');
    }

    public function updateLeaguePiData(Request $request)
    {
        $data = [];
        if(!($request->homePi === null || $request->homePi === '') && !($request->homePi === null || $request->homePi === '')) {
            $sql = 'SELECT id FROM iteam_league_audio WHERE b_id=:id';
            $d = DB::connection('mysql')->select($sql, ['id' => $request->id]);
            if($d) {
                $sql = 'UPDATE iteam_league_audio SET audio=:audio WHERE id=:id';
                DB::connection('mysql')->update($sql, [
                    'id' => $d[0]->id,
                    'audio' => $request->audio
                ]);
            } else {
                $sql = 'INSERT INTO iteam_league_audio (b_id, audio) VALUES (:id, :audio)';
                DB::connection('mysql')->insert($sql, [
                    'id' => $request->id,
                    'audio' => $request->audio
                ]);
            }
        }
        $sql = 'SELECT id, pi_id FROM iteam_league_pi WHERE team_id=:homeTeamId';
        $d = DB::connection('mysql')->select($sql, ['homeTeamId' => $request->homeTeamId]);
        if($d) {
            if(!($request->homePi === null || $request->homePi === '')) {
                if($d[0]->pi_id === $request->homePi) {
                    $result = 1;
                } else {
                    $sql = 'UPDATE iteam_league_pi SET pi_id=:homePi WHERE id=:id';
                    $result = DB::connection('mysql')->update($sql, [
                        'id' => $d[0]->id,
                        'homePi' => $request->homePi
                    ]);
                }
            } else {
                $sql = 'DELETE FROM iteam_league_pi WHERE id=:id';
                $result = DB::connection('mysql')->delete($sql, [
                    'id' => $d[0]->id
                ]);
            }
        } else {
            if(!($request->homePi === null || $request->homePi === '')) {
                $sql = 'INSERT INTO iteam_league_pi (team_id, pi_id) VALUES (:homeTeamId, :homePi)';
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
                $sql = 'SELECT id, pi_id FROM iteam_league_pi WHERE team_id=:awayTeamId';
                $d = DB::connection('mysql')->select($sql, ['awayTeamId' => $request->awayTeamId]);
                if($d) {
                    if(!($request->awayPi === null || $request->awayPi === '')) {
                        if($d[0]->pi_id === $request->awayPi) {
                            $result = 1;
                        } else {
                            $sql = 'UPDATE iteam_league_pi SET pi_id=:awayPi WHERE id=:id';
                            $result = DB::connection('mysql')->update($sql, [
                                'id' => $d[0]->id,
                                'awayPi' => $request->awayPi
                            ]);
                        }
                    } else {
                        $sql = 'DELETE FROM iteam_league_pi WHERE id=:id';
                        $result = DB::connection('mysql')->delete($sql, [
                            'id' => $d[0]->id
                        ]);
                    }
                } else {
                    if(!($request->awayPi === null || $request->awayPi === '')) {
                        $sql = 'INSERT INTO iteam_league_pi (team_id, pi_id) VALUES (:awayTeamId, :awayPi)';
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

    public function getLgViewData(Request $request)
    {
        $data = [];
        $data['errorCode'] = 'er0000';
        $sql = 'SELECT timezone FROM league_battle AS l LEFT JOIN league AS lg ON l.leagueId=lg.id WHERE l.id=:id';
        $t = DB::connection('mysql')->select($sql, ['id' => $request->id]);
        $sql = 'SELECT lg.name AS leagueName, g.groupName, l.homeTeamId, l.awayTeamId, l.sequence, l.isNetworkGame, DATE_ADD(l.matchDate, INTERVAL :timezone hour) AS matchDate, hs.name AS homeStoreName, ws.name AS awayStoreName, ht.name AS homeTeamName, wt.name AS awayTeamName, ht.fidoTeamId AS homefidoTeamId, wt.fidoTeamId AS awayfidoTeamId FROM league_battle AS l LEFT JOIN league AS lg ON lg.id=l.leagueId LEFT JOIN league_group AS g ON g.id=l.groupId INNER JOIN ( SELECT id, name FROM store ) AS hs ON hs.id=l.homeStoreId INNER JOIN ( SELECT id, name FROM store ) AS ws ON ws.id=l.awayStoreId INNER JOIN ( SELECT id, name, fidoTeamId FROM league_team ) AS ht ON ht.id=l.homeTeamId INNER JOIN ( SELECT id, name, fidoTeamId FROM league_team ) AS wt ON wt.id=l.awayTeamId WHERE l.id=:id';
        /*$sql = 'SELECT lg.name AS leagueName, g.groupName, l.homeTeamId, l.awayTeamId, l.sequence, l.isNetworkGame, DATE_ADD(l.matchDate, INTERVAL :timezone hour) AS matchDate FROM league_battle AS l LEFT JOIN league AS lg ON lg.id=l.leagueId LEFT JOIN league_group AS g ON g.id=l.groupId WHERE l.id=:id';*/
        $data['data'] = DB::connection('mysql')->select($sql, ['id' => $request->id, 'timezone' => $t[0]->timezone]);
        $data['team'] = [];
        $data['team'][0] = [
            'storeName' => $data['data'][0]->homeStoreName,
            'teamName' => $data['data'][0]->homeTeamName,
            'player' => [],
            'row' => -1,
            'pi' => -1,
            'status' => [true, true]
        ];
        $data['team'][1] = [
            'storeName' => $data['data'][0]->awayStoreName,
            'teamName' => $data['data'][0]->awayTeamName,
            'player' => [],
            'row' => -1,
            'pi' => -1,
            'status' => [false, false]
        ];
        $sql = 'SELECT pi_id FROM iteam_league_pi WHERE team_id=:homeTeamId';
        $homePi = DB::connection('mysql')->select($sql, ['homeTeamId' => $data['data'][0]->homeTeamId]);
        if($homePi) {
            $data['team'][0]['pi'] = $homePi[0]->pi_id;
        }
        $sql = 'SELECT pi_id FROM iteam_league_pi WHERE team_id=:awayTeamId';
        $awayPi = DB::connection('mysql')->select($sql, ['awayTeamId' => $data['data'][0]->awayTeamId]);
        if($awayPi) {
            $data['team'][1]['pi'] = $awayPi[0]->pi_id;
        }
        $sql = 'SELECT audio FROM iteam_league_audio WHERE b_id=:id';
        $audio = DB::connection('mysql')->select($sql, ['id' => $request->id]);
        if($audio) {
            $data['audio'] = $audio[0]->audio;
        } else {
            $data['audio'] = 0;
        }
        return compact('data');
    }
}