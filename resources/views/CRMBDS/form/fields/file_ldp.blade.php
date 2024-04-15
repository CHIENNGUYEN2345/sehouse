<label for="{{ $field['name'] }}">{{ @$field['label'] }} @if(strpos(@$field['class'], 'require') !== false)
        <span class="color_btd">*</span>@endif</label>
<div class="col-xs-12">
    @if (@$result->ldp->file_ldp != '')
        <a href="{{ asset('public/filemanager/userfiles/' . @$result->ldp->file_ldp) }}"
           target="_blank">{{ @$result->ldp->file_ldp }}</a>
    @endif
</div>