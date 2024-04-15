<?php
$ldps = [];
foreach ($bills as $bill) {
    $ldps[@$bill->ldp->career->name][] = [
        'domain' => $bill->domain,
    ];
}
//dd($ldps);
?>
@foreach($ldps as $career_name => $ldp)
    <h4>{{ $career_name }}<h4><br>
    @foreach($ldp as $v)
          &nbsp;&nbsp;&nbsp;&nbsp;<a href="//{{ $v['domain'] }}" target="_blank">{{ $v['domain'] }}</a><br><br>
    @endforeach
@endforeach