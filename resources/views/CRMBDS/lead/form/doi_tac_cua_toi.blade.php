<label style="cursor: pointer" for="doi_tac_cua_toi" class="kt-checkbox kt-checkbox--tick kt-checkbox--brand">
    <input style="height: 20px; width: 18px; float: left; margin-right: 5px;"
           type="checkbox"
           name="doi_tac_cua_toi"
           id="doi_tac_cua_toi"
           @if(strpos(@$result->partner, '|'.\Auth::guard('admin')->user()->id.'|') !== false)
           		checked
           @endif
           >
    Đánh dấu là đối tác
    <span></span>
</label>