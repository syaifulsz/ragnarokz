@extends('layouts.master')

@section('content')
    <div class="container">
        <div id="wufoo-mp26pbr0ku4p9o">
            Fill out my <a href="https://syaifulsz.wufoo.com/forms/mp26pbr0ku4p9o">online form</a>.
        </div>
        <div id="wuf-adv" style="font-family:inherit;font-size: small;color:#a7a7a7;text-align:center;display:block;">There are tons of <a href="http://www.wufoo.com/features/">Wufoo features</a> to help make your forms awesome.</div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">var mp26pbr0ku4p9o;(function(d, t) {
        var s = d.createElement(t), options = {
            'userName':'syaifulsz',
            'formHash':'mp26pbr0ku4p9o',
            'autoResize':true,
            'height':'841',
            'async':true,
            'host':'wufoo.com',
            'header':'show',
            'ssl':true};
            s.src = ('https:' == d.location.protocol ? 'https://' : 'http://') + 'www.wufoo.com/scripts/embed/form.js';
            s.onload = s.onreadystatechange = function() {
                var rs = this.readyState; if (rs) if (rs != 'complete') if (rs != 'loaded') return;
                try { mp26pbr0ku4p9o = new WufooForm();mp26pbr0ku4p9o.initialize(options);mp26pbr0ku4p9o.display(); } catch (e) {}};
                var scr = d.getElementsByTagName(t)[0], par = scr.parentNode; par.insertBefore(s, scr);
            })(document, 'script');
    </script>
@endpush