<?php

namespace App\Http\Controllers;

use App\Models\Parameters;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Input;
use Mockery\Exception;
use Yajra\DataTables\DataTables;

class ParameterController extends BaseController
{
    public function index(){
        $template = array(
            "menu_active" => "parameters",
            "smenu_active" => "",
            "page_title" => "Parametros",
            "page_subtitle" => "",
            "user" => session('user')
        );
        return view('parameter.index',$template);
    }

    public function load(){
        $parameters = DB::select(DB::raw("select m.id, m.name, m.created_at, m.updated_at 
                                      from parameters m
                                      order by m.id ASC"));
        return DataTables::of($parameters)
            ->make(true);
    }

    public function detail($id = 0){
        $template = array(
            "menu_active" => "parameters",
            "smenu_active" => "",
            "page_title" => "Parametros",
            "page_subtitle" => ($id == 0 ? "Nuevo" : "Editar" ),
            "user" => session('user')
        );

        if($id != 0){
            $user = Parameters::find($id);
            $template['item'] = $user;
        }

        return view('parameter.detail',$template);
    }

    public function save(Request $request){
        try{
            $id = Input::get('id');
            $name = Input::get('name');

            if($id != 0) {
                $parameter = Parameters::find($id);
                $parameter->updated_at = date('Y-m-d H:i:s');
            }
            else{
                $parameter  = new Parameters();
                $parameter->created_at = date('Y-m-d H:i:s');
            }

            $parameter->name = $name;
            $parameter->save();

            return response(json_encode(array("error" => 0,"id" => $parameter->id)), 200);

        }catch(Exception $exception){
            return response(json_encode(array("error" => 1)), 200);
        }
    }
}
