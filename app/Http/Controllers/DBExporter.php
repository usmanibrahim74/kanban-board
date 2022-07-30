<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DBExporter extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $fileName = time().".sql";
        \Spatie\DbDumper\Databases\MySql::create()
        ->setDbName(env('DB_DATABASE'))
        ->setUserName(env('DB_USERNAME'))
        ->setPassword(env('DB_PASSWORD'))
        ->dumpToFile($fileName);
        
        return response()->json([
            'file' => $fileName
        ]);
    }
}
