@if(Schema::hasTable('admin_logs'))
    <a href="{{asset('admin/admin_logs?search=true&limit=20&admin_id='.$item->id)}}">{{ \App\Models\AdminLog::where('admin_id',$item->id)->get()->count() }}</a>
@endif