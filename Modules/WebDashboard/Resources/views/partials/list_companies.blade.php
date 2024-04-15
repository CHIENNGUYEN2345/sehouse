<style>
    .company-item {
        cursor: pointer;
    }

    .company-item .company-action {
        text-align: right;
        font-size: 18px;
        opacity: 0;
    }

    .company-item:hover .company-action {
        opacity: 1;
    }
</style>
<div class="kt-portlet kt-portlet--height-fluid">
    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title bold uppercase">
                <i class="icon-cursor font-dark hide"></i>
                Công ty đang tham gia
            </h3>
        </div>
    </div>
    <div class="kt-portlet__body">
        <div class="kt-widget12">
            <div class="row">
                <?php
                $company_ids = \Modules\EworkingJob\Models\RoleAdmin::where('status', 1)->where('admin_id', \Auth::guard('admin')->user()->id)->pluck('company_id')->toArray();
                $data = \Modules\EworkingCompany\Models\Company::whereIn('id', $company_ids)->select('short_name', 'image', 'name', 'id')->get();
                ?>
                @foreach($data as $v)
                    <?php
                    $permissions_company = \DB::table('permissions')
                        ->join('permission_role', 'permission_role.permission_id', '=', 'permissions.id')
                        ->join('role_admin', 'permission_role.role_id', '=', 'role_admin.role_id')
                        ->whereIn('permissions.name', ['company_delete'])
                        ->where(function ($query) use ($v) {
                            $query->orWhere('role_admin.company_id', $v->id);
                            $query->orWhereNull('role_admin.company_id');
                        })
                        ->where('role_admin.admin_id', \Auth::guard('admin')->user()->id)
                        ->pluck('permissions.name')->toArray();
                    ?>
                    <div class="col-md-4 col-sm-3 company-item">
                        <div style="@if(\Auth::guard('admin')->user()->last_company_id == $v->id ) border-radius: 5px;border: 2px solid #469408b0;font-weight: bold;  @endif text-align: center; padding: 5px;">
                            <div class="company-action pb-2">
                                <a class="out-company  btn-icon btn-circle" href="/admin/company/logout/{{$v->id}}"
                                   title="Thoát khỏi công ty">
                                    <i class="fa fa-sign-out-alt" style="color:#FF3732;"></i>
                                </a>
                                @if(in_array('company_delete', $permissions_company))
                                    <a class="delete-company btn-icon btn-circle" data-company_id="{{ $v->id }}"
                                       style="margin-right: 5px;" title="Xóa công ty">
                                        <i class="flaticon-delete" style="color:#FF3732;"></i>
                                    </a>
                                @endif
                            </div>

                            <a href="{{route('company.switch',['id'=>$v->id])}}"
                               title="{{$v->name}}"><img width="30%"
                                                         src="{{\App\Http\Helpers\CommonHelper::getUrlImageThumb($v->image,100,100)}}"
                                                         title="{{$v->name}}"
                                                         alt="{{$v->name}}">
                                <p style="text-transform: capitalize;">{{$v->short_name}}</p></a>
                        </div>
                    </div>
                @endforeach
                @include('eworkingdashboard::popup.out_company')
                @include('eworkingdashboard::popup.delete_company')
                <div class="col-md-4 col-sm-3">
                    <div style=" text-align: center; padding: 5px;margin-top: 15px;">
                        <a href="{{ url('create-company') }}"
                           title="Tạo mới công ty">
                            <i class="fa fa-plus-square fa-2x" aria-hidden="true"
                               style="color:#469408"></i>
                            <p style="text-transform: capitalize;">Tạo mới công ty</p></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>