<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="noindex, nofollow" name="robots">
@if(isset($page_title))
<title>{{ trans(@$page_title) }}</title>
@endif
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
<link rel="icon" href="{{ CommonHelper::getUrlImageThumb(@$settings['favicon'], 64, 64) }}" type="image/x-icon" sizes="64x64">
<link rel="shortcut icon" href="{{ CommonHelper::getUrlImageThumb(@$settings['favicon'], 64, 64) }}" type="image/x-icon" sizes="64x64">
