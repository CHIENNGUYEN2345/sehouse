@extends('admin.template')
@section('main')
   @include('admin.check_exp_date')
    <div class="content-wrapper" >
        <section class="content">
            <div class="row">
                @foreach($datas as $v)
                    <div class="col-xs-4 col-sm-3 col-lg-2">
                        <div style="@if(Auth::guard('admin')->user()->last_company_id == $v->id ) border-radius: 5px;border: 2px solid #469408b0;font-weight: bold;  @endif text-align: center; padding: 10px;margin-bottom: 15px;">
                            <a href="{{route('company.switch',['id'=>$v->id,'url'=>$v->url])}}"
                               title="{{$v->name}}"><img width="100%"
                                                         src="{{\App\Http\Helpers\CommonHelper::getUrlImageThumb($v->image,100,100)}}"
                                                         title="{{$v->name}}"
                                                         alt="{{$v->name}}">
                                <p style="text-transform: capitalize;">{{$v->short_name}}</p>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="row" style="margin-left: 0px; margin-right: 0px;">
                <div class="col-12">
                    <hr style="margin-bottom: 20px; margin-top: 0px;">
                </div>
            </div>
            {{--Công việc--}}
            <div class="row">
                <div class="col-xs-12">
                    <div class="row">
                        {{--<div class="col-xs-6 col-sm-4 col-lg-3">--}}
                            {{--<div class="small-box bg-teal">--}}
                                {{--<div class="inner">--}}
                                    {{--<h3>{{ number_format(count($project_doing), 0, '.', '.') }}</h3>--}}
                                    {{--<p>Dự án đang làm</p>--}}
                                {{--</div>--}}
                                {{--<div class="icon">--}}
                                    {{--<i class="fa fa-pencil-square-o" aria-hidden="true"></i>--}}
                                {{--</div>--}}
                                {{--<a href="project?status=2" class="small-box-footer">Xem thêm <i--}}
                                            {{--class="fa fa-arrow-circle-right"></i></a>--}}
                            {{--</div>--}}
                        {{--</div>--}}


                        <div class="col-xs-6 col-sm-4 col-lg-3">
                            <div class="small-box bg-aqua">
                                <div class="inner">
                                    <h3>{{ number_format(count($task_waitting), 0, '.', '.') }}</h3>
                                    <p>Nhiệm vụ chờ duyệt</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-list-alt" aria-hidden="true"></i>
                                </div>
                                <a href="task?status=1" class="small-box-footer">Xem thêm <i
                                            class="fa fa-arrow-circle-right"></i></a>
                            </div>
                        </div>

                        <div class="col-xs-6 col-sm-4 col-lg-3">
                            <div class="small-box bg-teal">
                                <div class="inner">
                                    <h3>{{ number_format(count($task_doing), 0, '.', '.') }}</h3>
                                    <p>Nhiệm vụ đang làm</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                </div>
                                <a href="task?status=2" class="small-box-footer">Xem thêm <i
                                            class="fa fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-4 col-lg-3">
                            <div class="small-box bg-yellow">
                                <div class="inner">
                                    <h3>{{ number_format(count($task_not_doing), 0, '.', '.') }}</h3>
                                    <p>Nhiệm vụ chưa làm</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-bell-o" aria-hidden="true"></i>
                                </div>
                                <a href="task?status=4" class="small-box-footer">Xem thêm <i
                                            class="fa fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-xs-12">
                            <div class="row" style="margin-left: 0px; margin-right: 0px;">
                                <div class="col-12">
                                    <hr style="margin-bottom: 20px; margin-top: 0px;">
                                </div>
                            </div>

                            @if(CommonHelper::has_permission(Auth::guard('admin')->user()->id, 'admin_view'))
                                <div class="row">
                                    <div class="col-xs-6 col-sm-4 col-lg-3">
                                        <div class="small-box bg-teal">
                                            <div class="inner">
                                                <h3>{{ number_format(\App\Models\Admin::where('company_ids','like','%|'.\Auth::guard('admin')->user()->last_company_id.'|%')->count(), 0, '.', '.') }}</h3>
                                                <p>Thành viên</p>
                                            </div>
                                            <div class="icon">
                                                <i class="fa fa-users" aria-hidden="true"></i>
                                            </div>
                                            <a href="/admin/admin" class="small-box-footer">Xem thêm <i
                                                        class="fa fa-arrow-circle-right"></i></a>
                                        </div>
                                    </div>
                                    @endif
                                    @if(CommonHelper::has_permission(Auth::guard('admin')->user()->id, 'admin_view'))
                                        <div class="col-xs-6 col-sm-4 col-lg-3">
                                            <div class="small-box bg-aqua">
                                                <div class="inner">
                                                    <h3>{{ number_format(\App\Models\User::whereIn('company_id',$company)->where('type',1)->count(), 0, '.', '.') }}</h3>
                                                    <p>Khách hàng</p>
                                                </div>
                                                <div class="icon">
                                                    <i class="fa fa-user" aria-hidden="true"></i>
                                                </div>
                                                <a href="user?type=1" class="small-box-footer">Xem thêm <i
                                                            class="fa fa-arrow-circle-right"></i></a>
                                            </div>
                                        </div>
                                        <div class="col-xs-6 col-sm-4 col-lg-3">
                                            <div class="small-box bg-aqua">
                                                <div class="inner">
                                                    <h3>{{ number_format(\App\Models\User::whereIn('company_id',$company)->where('type',2)->count(), 0, '.', '.') }}</h3>
                                                    <p>Đối tác</p>
                                                </div>
                                                <div class="icon">
                                                    <i class="fa fa-handshake-o" aria-hidden="true"></i>
                                                </div>
                                                <a href="user?type=2" class="small-box-footer">Xem thêm <i
                                                            class="fa fa-arrow-circle-right"></i></a>
                                            </div>
                                        </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <!-- LINE CHART -->
                            @include('admin.prompt')

                            @include('admin.internal_notification')
                        </div>
                    </div>
                </div>
            </div>


        </section>
    </div>
@endsection
@section('custom_header')
    <style type="text/css">
        @-webkit-keyframes chartjs-render-animation {
            from {
                opacity: 0.99
            }
            to {
                opacity: 1
            }
        }

        @keyframes chartjs-render-animation {
            from {
                opacity: 0.99
            }
            to {
                opacity: 1
            }
        }

        .chartjs-render-monitor {
            -webkit-animation: chartjs-render-animation 0.001s;
            animation: chartjs-render-animation 0.001s;
        }

        canvas {
            -moz-user-select: none;
            -webkit-user-select: none;
            -ms-user-select: none;
        }
    </style>
@endsection
