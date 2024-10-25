<script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
<script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>

<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>





@isset($rw_dash)
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js" charset="utf-8"></script>
    @include('scripts.rwcharts.poedistribution')
@endisset


@isset($scr_vol)
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js" charset="utf-8"></script>
    @include('scripts.rwcharts.screeningdasboard')
@endisset


@isset($MonthlyIncidenceReportKey)
    <script src="https://cdn.jsdelivr.net/npm/chart.js" charset="utf-8"></script>
    @include('scripts.rwcharts.caseanalysis')
@endisset


@isset($GenderAndRouteKey)
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js" charset="utf-8"></script>
    @include('scripts.rwcharts.genderandroute')
@endisset

@isset($editor)
    <script src="{{ asset('assets/ckeditor/ckeditor.js') }}"></script>


    <script src="{{ asset('assets/ckeditor/adapters/jquery.js') }}"></script>



    <script>
        $(document).ready(function() {
            $('textarea').ckeditor(function(textarea) {
                // Callback function code.
            });
        });
    </script>
@endisset






@include('not.not')
<script src="{{ asset('js/custom.js') }}"></script>
<script src="{{ asset('js/main.js') }}"></script>

@if (isset($rem))
    <script>
        $(function() {
            setInterval(function() {
                @foreach ($rem as $val)
                    // console.log(".x_{{ $val }}");
                    $(".x_{{ $val }}").remove();
                @endforeach
            }, 1000);



        });
    </script>
@endif




</body>


</html>
