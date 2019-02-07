<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Input;
use Mockery\Exception;
use Yajra\DataTables\DataTables;


class BrandController extends BaseController
{
    public function index(){
        $template = array(
            "menu_active" => "brands",
            "smenu_active" => "",
            "page_title" => "Marcas",
            "page_subtitle" => "",
            "user" => session('user')
        );
        return view('brand.index',$template);
    }

    public function change_status(){
        try{
            $id = Input::get('id');
            $status = intval(Input::get('status'));
            $brand = Brand::find($id);
            $brand->status = $status;
            $brand->save();

            return response(json_encode(array("error" => 0)), 200);
        }catch(Exception $exception){
            return response(json_encode(array("error" => 1)), 200);
        }
    }

    public function load(){
        $brands = DB::select(DB::raw("select m.id, m.name, m.created_at, m.updated_at, m.status 
                                      from brand m
                                      order by m.id ASC"));
        return DataTables::of($brands)
            ->make(true);
    }

    public function detail($id = 0){
        $template = array(
            "menu_active" => "brands",
            "smenu_active" => "",
            "page_title" => "Marcas",
            "page_subtitle" => ($id == 0 ? "Nuevo" : "Editar" ),
            "user" => session('user')
        );

        if($id != 0){
            $user = Brand::find($id);
            $template['item'] = $user;
        }

        return view('brand.detail',$template);
    }

    public function save(Request $request){
        try{
            $id = Input::get('id');
            $name = Input::get('name');


            if($id != 0) {
                $brand = Brand::find($id);
                $brand->updated_at = date('Y-m-d H:i:s');
            }
            else{
                $brand  = new Brand();
                $brand->status = 1;
                $brand->created_at = date('Y-m-d H:i:s');
            }
            $brand->name = $name;
            $brand->save();

            return response(json_encode(array("error" => 0,"id" => $brand->id)), 200);

        }catch(Exception $exception){
            return response(json_encode(array("error" => 1)), 200);
        }
    }
}
