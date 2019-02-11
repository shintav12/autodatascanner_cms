@extends('layout.master')

@section('css')
    <link href="{{asset("assets/global/plugins/file-input/css/fileinput.css")}}" rel="stylesheet" type="text/css" />
    <link href="{{asset("assets/global/plugins/select2/css/select2.css")}}" rel="stylesheet" type="text/css" />
    <link href="{{asset("assets/global/plugins/select2/css/select2-bootstrap.min.css")}}" rel="stylesheet" type="text/css" />
    <link href="{{asset("assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css")}}" rel="stylesheet" type="text/css" />
@endsection

@section('scripts')
    <script src="{{asset("assets/global/plugins/jquery-validation/js/jquery.validate.js")}}" type="text/javascript"></script>
    <script src="{{asset("assets/global/plugins/jquery-validation/js/additional-methods.js")}}" type="text/javascript"></script>
    <script src="{{asset("assets/global/plugins/file-input/js/fileinput.js")}}" type="text/javascript"></script>
    <script src="{{asset("assets/global/plugins/select2/js/select2.js")}}" type="text/javascript"></script>
    <script src="{{asset("assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js")}}" type="text/javascript"></script>
    <script class="text/javascript">
        var selectedSystems = [];
        $(document).ready(function(){
            $("#form-user").validate({
                errorPlacement: function errorPlacement(error, element) {
                    element.after(error);
                },
                rules: {
                    name: "required"
                },
                messages: {
                    name: "Campo requerido"
                },
                submitHandler: function (form) {
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: "{{ route('case_save') }}",
                        data: new FormData($("#form-user")[0]),
                        contentType: false,
                        processData: false,
                        beforeSend: function () {
                            swal({
                                title: 'Cargando...',
                                timer: 10000,
                                showCancelButton: false,
                                showConfirmButton: false,
                                onOpen: function () {
                                    swal.showLoading()
                                }
                            });
                        },
                        success: function (data) {
                            var error = data.error;
                            if (error == 0) {
                                window.location = "{{ url(route('cases'))}}";
                            } else {
                                swal.close();
                                swal(
                                    'Oops...',
                                    'Algo ocurrió!',
                                    'error'
                                );
                            }
                        }, error: function () {
                            swal.close();
                            swal(
                                'Oops...',
                                'Algo ocurrió!',
                                'error'
                            );
                        }
                    });

                }
            });
        });

        $(document).on('click','.deleteSystem', function(e){
            var deletedSystem = $(this).parent().parent().find("label");
            var aux = selectedSystems.find(x => x.id == deletedSystem.data("id"));
            selectedSystems.splice(aux, 1);
            $(this).parent().parent().parent().remove();
        });
        $(document).on('click','#addSystem', function(e){
            var selectedSystemText = $('#systemSelect').find(":selected").text();
            var selectedSystemVal = $('#systemSelect').val();
            var html=       '<div class="col-xs-12" style="margin-left: 0px; margin-top: 15px ">' +
                                '<div class="col-xs-3">'+
                                    '<div class="col-xs-3">'+
                                        '<label class="systems" data-id="'+ selectedSystemVal +'" data-name="' + selectedSystemText + '" name="systems[]">' + selectedSystemText +  '</label>'+
                                    '</div>'+
                                    '<div class="col-xs-1">'+
                                        '<button id="deleteSystem" type="button" class="btn btn-primary deleteSystem"><i class="fa fa-trash"></i></button>'+
                                    '</div>'+
                                '</div>'+
                            '</div>';
            selectedSystems.push({ id: selectedSystemVal, text: selectedSystemText });
            $("#systemsContainer").append(html);
            console.log(selectedSystems);
        });

    </script>
@endsection

@section('body')
    <h1 class="page-title"> {{$page_title}}
        <small>{{$page_subtitle}}</small>
    </h1>
    <div class="row">
        <div class="col-xs-12">
            <div class="portlet light portlet-fit portlet-datatable bordered">
                <div class="portlet-body form">
                    <form class="form-horizontal" role="form" id="form-user">
                        {{csrf_field()}}
                        <ul class="nav nav-tabs">
                            <li id="tab_li_1" class="tab-trigger active">
                                <a id="tab_1" href="#tab_1_1" data-toggle="tab"> General </a>
                            </li>
                            <li id="tab_li_2" class="tab-trigger">
                                <a id="tab_2" href="#tab_1_2" data-toggle="tab"> Sistemas </a>
                            </li>
                            <li id="tab_li_3" class="tab-trigger">
                                <a id="tab_3" href="#tab_1_3" data-toggle="tab"> Parametros </a>
                            </li>
                            <li id="tab_li_4" class="tab-trigger">
                                <a id="tab_4" href="#tab_1_3" data-toggle="tab"> Codigos de Falla </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="tab_1_1">
                                <input hidden name="id" value="<?php if( isset($item) )  echo $item->id; else echo 0;?>" />
                                <input hidden name="engines" value="<?php if( isset($item) )  ?>">
                                <div class="row form-body">
                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <div class="col-xs-12">
                                                    <label>Nombre</label>
                                                    <input type="text" class="form-control" name="name" value="<?php if( isset($item) )  echo $item->name;?>" placeholder="Ingrese el nombre de la marca">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="tab_1_2">
                                <div class="row form-body">
                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <div class="col-xs-11">
                                                    <label>Sistema</label>
                                                    <select id="systemSelect" class="form-control select2">
                                                        <?php foreach ($systems as $system){?>
                                                            <option value="{{$system->id}}">{{$system->name}}</option>
                                                        <?php }?>
                                                    </select>
                                                </div>
                                                <div class="col-xs-1">
                                                    <a id="addSystem" class="btn btn-primary">A&nacute;adir</a>
                                                </div>
                                            </div>
                                            <div id="systemsContainer">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="tab_1_3">
                                <div class="row form-body">
                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <div class="col-xs-11">
                                                    <select id="systemSelect" class="form-control select2">
                                                        <?php foreach ($parameters as $parameter){?>
                                                        <option value="{{$parameter->id}}">{{$parameter->name}}</option>
                                                        <?php }?>
                                                    </select>
                                                </div>
                                                <div class="col-xs-1">
                                                    <a id="addParameter" class="btn btn-primary">A&nacute;adir</a>
                                                </div>
                                            </div>
                                            <div id="parameterContainer">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="col-md-3 col-md-9">
                                        <button type="submit" class="btn btn-primary">Guardar</button>
                                        <a type="button" href="{{route('brands')}}" class="btn default">Cancel</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

