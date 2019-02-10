<?php

namespace App\Http\Controllers;

use App\Models\Systems;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Input;
use Mockery\Exception;
use Yajra\DataTables\DataTables;

class SystemController extends BaseController
{
    public function index(){
        $template = array(
            "menu_active" => "systems",
            "smenu_active" => "",
            "page_title" => "Sistemas",
            "page_subtitle" => "",
            "user" => session('user')
        );
        return view('system.index',$template);
    }

    public function load(){
        $systems = DB::select(DB::raw("select m.id, m.name, m.created_at, m.updated_at 
                                      from system m
                                      order by m.id ASC"));
        return DataTables::of($systems)
            ->make(true);
    }

    public function detail($id = 0){
        $template = array(
            "menu_active" => "systems",
            "smenu_active" => "",
            "page_title" => "Sistemas",
            "page_subtitle" => ($id == 0 ? "Nuevo" : "Editar" ),
            "user" => session('user')
        );

        if($id != 0){
            $user = Systems::find($id);
            $template['item'] = $user;
        }

        return view('system.detail',$template);
    }

    public function save(Request $request){
        try{
            $id = Input::get('id');
            $name = Input::get('name');


            if($id != 0) {
                $system = Systems::find($id);
                $system->updated_at = date('Y-m-d H:i:s');
            }
            else{
                $system  = new Systems();
                $system->created_at = date('Y-m-d H:i:s');
            }
            $system->name = $name;
            $system->save();

            return response(json_encode(array("error" => 0,"id" => $system->id)), 200);

        }catch(Exception $exception){
            return response(json_encode(array("error" => 1)), 200);
        }
    }
}
