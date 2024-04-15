{!! @$settings['header_mail'] !!}
<tr>
    <td>{!! @$campaign->content !!}</td>
</tr>
{!! @$settings['footer_mail'] !!}
<img src="http://{{ (@$_SERVER['HTTP_HOST'] != 'localhost' && @$_SERVER['HTTP_HOST'] != '') ? $_SERVER['HTTP_HOST'] : env('DOMAIN') }}/admin/marketing-mail/event/open-mail?camp_id={{ $campaign->id }}&user_id={{ @$user->id }}&type={{ @$user->type }}&email={{ @$user->email }}"
     style="display: none;" />