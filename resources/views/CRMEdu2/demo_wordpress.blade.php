@foreach($bills as $bill)
    <a href="//{{ $bill->domain }}" target="_blank">{{ $bill->domain }}</a><br><br>
@endforeach