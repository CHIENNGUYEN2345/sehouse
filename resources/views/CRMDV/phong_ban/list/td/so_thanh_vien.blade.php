<a href="{{ url('/admin/admin?search=true&phong_ban_id=' . $item->id) }}" target="_blank"
   style="font-size: 14px!important;">
    {{ number_format(\App\Models\Admin::where('status', 1)
    ->where('phong_ban_id', $item->id)
    ->count(), 0, '.', ',') }} thành viên
</a>
