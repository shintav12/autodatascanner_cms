<?php

namespace App\Http\Controllers;

use App\Models\FailureCodes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Input;
use Mockery\Exception;
use Yajra\DataTables\DataTables;

class FailureCodeController extends BaseController
{
    public function index(){
        $template = array(
            "menu_active" => "failure_codes",
            "smenu_active" => "",
            "page_title" => "Codigos de Falla",
            "page_subtitle" => "",
            "user" => session('user')
        );
        return view('failure_code.index',$template);
    }

    public function load(){
        $failure_codes = DB::select(DB::raw("select m.id, m.name, m.created_at, m.updated_at 
                                      from failure_codes m
                                      order by m.id ASC"));
        return DataTables::of($failure_codes)
            ->make(true);
    }

    public function detail($id = 0){
        $template = array(
            "menu_active" => "failure_codes",
            "smenu_active" => "",
            "page_title" => "Codigos de Falla",
            "page_subtitle" => ($id == 0 ? "Nuevo" : "Editar" ),
            "user" => session('user')
        );

        if($id != 0){
            $user = FailureCodes::find($id);
            $template['item'] = $user;
        }

        return view('failure_code.detail',$template);
    }

    public function save(Request $request){
        try{
            $id = Input::get('id');
            $name = Input::get('name');
            $description = Input::get('description');


            if($id != 0) {
                $failure_codes = FailureCodes::find($id);
                $failure_codes->updated_at = date('Y-m-d H:i:s');
            }
            else{
                $failure_codes  = new FailureCodes();
                $failure_codes->created_at = date('Y-m-d H:i:s');
            }
            $failure_codes->name = $name;
            $failure_codes->description = $description;
            $failure_codes->save();

            return response(json_encode(array("error" => 0,"id" => $failure_codes->id)), 200);

        }catch(Exception $exception){
            return response(json_encode(array("error" => 1)), 200);
        }
    }
}
