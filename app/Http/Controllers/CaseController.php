<?php

namespace App\Http\Controllers;

use App\Models\Cases;
use App\Models\CasesFailureCodes;
use App\Models\CasesParameters;
use App\Models\CasesSystems;
use App\Utils\imageUploader;
use App\Models\FailureCodes;
use App\Models\Parameters;
use App\Models\Systems;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Input;
use Mockery\Exception;
use Yajra\DataTables\DataTables;

class CaseController extends BaseController
{
    public function index(){
        $template = array(
            "menu_active" => "cases",
            "smenu_active" => "",
            "page_title" => "Casos",
            "page_subtitle" => "",
            "user" => session('user')
        );
        return view('case.index',$template);
    }

    public function change_status(){
        try{
            Cases::update(['status',0]);
            $id = Input::get('id');
            $status = intval(Input::get('status'));
            $case = Cases::find($id);
            $case->status = $status;
            $case->save();
            return response(json_encode(array("error" => 0)), 200);
        }catch(Exception $exception){
            return response(json_encode(array("error" => 1)), 200);
        }
    }

    public function load(){
        $cases = DB::select(DB::raw("select m.id, m.name, m.created_at, m.updated_at, m.status 
                                      from cases m
                                      order by m.id ASC"));
        return DataTables::of($cases)
            ->make(true);
    }

    public function detail($id = 0){
        $template = array(
            "menu_active" => "cases",
            "smenu_active" => "",
            "page_title" => "Cases",
            "page_subtitle" => ($id == 0 ? "Nuevo" : "Editar" ),
            "user" => session('user')
        );
        $template["systems"] = Systems::get(["id","name"]);
        $template["parameters"] = Parameters::get(["id","name"]);
        $template["codes"] = FailureCodes::get(["id","name","description"]);
        if($id != 0){
            $case = Cases::find($id);
            $template['item'] = $case;
            $template["cases_systems"] = DB::select(DB::raw("select s.id, s.name 
                                                                     from cases_systems cs
                                                                     join system s on s.id = cs.system_id
                                                                     where cs.case_id =".$id));
            $template["cases_codes"] = DB::select(DB::raw("select fc.id, fc.name, fc.description
                                                                     from cases_failurescodes cf
                                                                     join failure_codes fc on fc.id = cf.failure_codes_id
                                                                     where cf.case_id =".$id));
            $template["cases_parameters"] = DB::select(DB::raw("select p.id, p.name, cp.* 
                                                                     from cases_parameters cp
                                                                     join parameters p on p.id = cp.parameter_id
                                                                     where cp.case_id =".$id));
        }
        return view('case.detail',$template);
    }

    public function save(Request $request){
        try{
            $id = Input::get('id');
            $name = Input::get('name');
            $codes = Input::get('codes');
            $parameters = Input::get('parameters');
            $values = Input::get('values');
            $ranges = Input::get('ranges');
            $second_values = Input::get('second_values');
            $second_ranges = Input::get('second_ranges');
            $systems = Input::get('systems');
            $image = $request->file('image');
            
            $canbus_case = Input::get('canbus_case');

            if($id != 0) {
                $case = Cases::find($id);
            }
            else{
                $case  = new Cases();
                $case->status = 0;
                $case->image = "";
            }

            $case->name = $name;
            $case->canbus_case = !is_null($canbus_case);
            $case->save();

            if (!is_null($image)) {
                $path = imageUploader::upload($case, $image, "case");
                $case->image = $path;
                $case->save();
            }

            CasesFailureCodes::where("case_id",$case->id)->delete();
            foreach($codes as $code){
                $caseFailureCode = new CasesFailureCodes();
                $caseFailureCode->case_id = $case->id;
                $caseFailureCode->failure_codes_id = $code;
                $caseFailureCode->save();
            }

            CasesParameters::where("case_id",$case->id)->delete();
            for($i = 0 ; $i < count($parameters); $i++){
                $caseParameter = new CasesParameters();
                $caseParameter->case_id = $case->id;
                $caseParameter->parameter_id = $parameters[$i];
                $caseParameter->value = $values[$i];
                $caseParameter->range = $ranges[$i];
                $caseParameter->second_range = $second_ranges[$i];
                $caseParameter->second_value = $second_values[$i];
                $caseParameter->save();
            }

            CasesSystems::where("case_id",$case->id)->delete();
            foreach($systems as $system){
                $caseSystem = new CasesSystems();
                $caseSystem->case_id = $case->id;
                $caseSystem->system_id = $system;
                $caseSystem->save();
            }

            return response(json_encode(array("error" => 0,"id" => $case->id)), 200);

        }catch(Exception $exception){
            return response(json_encode(array("error" => 1)), 200);
        }
    }
}
