<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Cars;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Input;
use Mockery\Exception;
use Yajra\DataTables\DataTables;


class CarController extends BaseController
{
    public function index(){
        $template = array(
            "menu_active" => "cars",
            "smenu_active" => "",
            "page_title" => "Carros",
            "page_subtitle" => "",
            "user" => session('user')
        );
        return view('car.index',$template);
    }

    public function change_status(){
        try{
            $id = Input::get('id');
            $status = intval(Input::get('status'));
            $car = Cars::find($id);
            $car->status = $status;
            $car->save();

            return response(json_encode(array("error" => 0)), 200);
        }catch(Exception $exception){
            return response(json_encode(array("error" => 1)), 200);
        }
    }

    public function load(){
        $cars = DB::select(DB::raw("select m.id, m.name, m.created_at, m.updated_at, m.status 
                                      from car m
                                      order by m.id ASC"));
        return DataTables::of($cars)
            ->make(true);
    }

    public function detail($id = 0){
        $template = array(
            "menu_active" => "cars",
            "smenu_active" => "",
            "page_title" => "Carros",
            "page_subtitle" => ($id == 0 ? "Nuevo" : "Editar" ),
            "user" => session('user')
        );
        $template["brands"] = Brand::get();
        if($id != 0){
            $user = Cars::find($id);
            $template['item'] = $user;
        }

        return view('car.detail',$template);
    }

    public function save(Request $request){
        try{
            $id = Input::get('id');
            $name = Input::get('name');
            $brand_id = Input::get('brand_id');
            $year = Input::get('year');
            $engine = Input::get('engine');

            if($id != 0) {
                $car = Cars::find($id);
                $car->updated_at = date('Y-m-d H:i:s');
            }
            else{
                $car  = new Cars();
                $car->status = 1;
                $car->created_at = date('Y-m-d H:i:s');
            }

            $car->name = $name;
            $car->engine = $engine;
            $car->brand_id = $brand_id;
            $car->year = $year;
            $car->save();

            return response(json_encode(array("error" => 0,"id" => $car->id)), 200);

        }catch(Exception $exception){
            return response(json_encode(array("error" => 1)), 200);
        }
    }
}
