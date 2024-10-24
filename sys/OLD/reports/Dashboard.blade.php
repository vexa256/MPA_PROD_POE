<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                {!! $chart->container() !!}
            </div>
        </div>
    </div>


    <div class="col-md-6">
        <div class="card-body pt-3 bg-light shadow-lg table-responsive">
            {!! Alert(
                $icon = 'fa-info',
                $class = 'alert-primary',
                $Title = 'Project Financial Analysis (TIMS) ' . date('M-Y'),
                $Msg = 'Budget , Actual Expenditure, Variance , Absorption Rate',
            ) !!}
        </div>
        <div class="card-body pt-3 bg-light shadow-lg table-responsive">

            <table class=" table table-rounded table-bordered  border gy-3 gs-3">
                <thead>
                    <tr
                        class="fw-bold  text-gray-800 border-bottom border-gray-200">

                        <th>Funds Disbursed</th>
                        <th>Budget</th>
                        <th>Actual Budget</th>
                        <th>Variance</th>
                        <th>Budget burn rate</th>



                    </tr>
                </thead>
                <tbody>

                    <tr>

                        <td class="bg-primary text-light fw-bolder">
                            {{ number_format($FundDisbursementAtPresentInUsd, 2) }}
                            USD</td>
                        <td class="bg-dark text-light fw-bolder">
                            {{ number_format($TotalBudgetInUsd, 2) }} USD</td>
                        <td class="bg-danger text-light fw-bolder">
                            {{ number_format($Expenditure, 2) }} USD</td>
                        <td class="bg-primary text-light fw-bolder">
                            {{ number_format($TotalBudgetInUsd - $Expenditure, 2) }}
                            USD</td>
                        <td class="bg-success text-light fw-bolder">
                            @if ($TotalBudgetInUsd > 0)
                                {{ number_format(($Expenditure / $TotalBudgetInUsd) * 100, 1) }}%
                            @else
                                N/A
                            @endif
                        </td>




                    </tr>




                </tbody>
            </table>
        </div>
    </div>




    <div class="row mt-2">


        <div class="col-md-6">
            <div class="card-body pt-3 bg-light shadow-lg table-responsive">
                {!! Alert(
                    $icon = 'fa-info',
                    $class = 'alert-primary',
                    $Title = 'Project activity status analysis (TIMS) ' . date('M-Y'),
                    $Msg = 'Track the progress of project activities',
                ) !!}
            </div>
            <div class="card-body pt-3 bg-light shadow-lg table-responsive">

                <table
                    class=" table table-rounded table-bordered  border gy-3 gs-3">
                    <thead>
                        <tr
                            class="fw-bold  text-gray-800 border-bottom border-gray-200">


                            <th>Status</th>
                            <th>Number of activities</th>




                        </tr>
                    </thead>
                    <tbody>

                        @isset($ActivityStatus)

                            @foreach ($ActivityStatus as $data)
                                <tr>


                                    <td class="bg-primary text-light fw-bolder">
                                        {{ $data->ProgressStatus }}</td>

                                    <td class="bg-dark text-light fw-bolder">
                                        {{ $data->total }}</td>



                                </tr>
                            @endforeach

                        @endisset




                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    {!! $charts->container() !!}
                </div>
            </div>
        </div>

    </div>

</div>


<div class="row mt-2">
    <div class="col-md-12">
        <div class="card-body pt-3 bg-light shadow-lg table-responsive">
            {!! Alert(
                $icon = 'fa-info',
                $class = 'alert-danger',
                $Title = 'Module Financial Analysis (TIMS) ' . date('M-Y'),
                $Msg = 'Module Budget, Expenditure , variance and Burn rate',
            ) !!}
        </div>
        <div class="card-body pt-3 bg-light shadow-lg table-responsive">

            <table
                class=" table table-rounded table-bordered  border gy-3 gs-3">

                <thead>
                    <tr
                        class="fw-bold  text-gray-800 border-bottom border-gray-200">


                        <th>Module</th>
                        <th>Budget</th>
                        <th>Actual Budget</th>




                    </tr>
                </thead>

                <tbody>
                    @foreach ($ModuleExpenditure as $row)
                        <tr>
                            @foreach ((array) $row as $cell)
                                <?php $class = [
                                    1 => 'bg-dark text-light fw-bolder',
                                    2 => 'bg-danger text-light fw-bolder',
                                    3 => 'bg-primary text-light fw-bolder',
                                    4 => 'bg-success text-light fw-bolder',
                                ];
                                $rand = mt_rand(1, 4); ?>
                                <td class="<?php echo $class[$rand]; ?>">
                                    {{ $cell }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
                {{-- <tbody>

                    @isset($ModuleExpenditure)

                    @foreach ($ModuleExpenditure as $data)
                    <tr>


                        <td class="bg-dark text-light fw-bolder"> {{ $data->ProjectModuleName }}</td>

                <td class="bg-dark text-light fw-bolder">{{number_format($data->TotalBudgetInUsd,2) }} USD</td>

                <td class="bg-danger text-light fw-bolder">{{number_format( $data->totalAmountSpent, 2) }} USD</td>




                </tr>
                @endforeach

                @endisset




                </tbody> --}}
            </table>
        </div>
    </div>
</div>




{{-- <th>Variance</th>
                        <th>Budget burn rate</th> --}}

{{-- <td class="bg-primary text-light fw-bolder">{{number_format($data->TotalBudgetInUsd - $data->totalAmountSpent, 2) }} USD</td>
<td class="bg-success text-light fw-bolder">{{ number_format(($data->totalAmountSpent/$data->TotalBudgetInUsd)*100, 1) }}%</td> --}}
