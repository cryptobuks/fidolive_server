<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Config;

class SiteController extends Controller
{
	public function index(Request $request)
    {
    	$texts = [];
    	$texts['slideMenu'] = trans('slideMenu');
    	$texts['pageName'] = trans('pageName');
    	$texts['pageFn'] = trans('pageFunction');
    	$texts['msg'] = trans('message');
    	$texts = json_encode($texts);

        $db_host = Config::get('database.connections.mysql.host');
        $db_database = Config::get('database.connections.mysql.database');
        $ev = 'official';
        if($db_host === 'test-fidodartsdb.c8xsd1mr8tog.ap-northeast-1.rds.amazonaws.com') {
            if($db_database === 'fidodarts_rds2') {
                $ev = 'test2';
            } else {
                $ev = 'test1';
            }
        }

    	return view('index', compact('texts', 'ev'));
    }

    public function info(Request $request)
    {
        echo phpinfo();
    }
}