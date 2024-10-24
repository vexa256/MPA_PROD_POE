<div class="row">

    {{-- <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                {!! $FinancialQuarterChart->container() !!}
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                {!! $ModuleChart->container() !!}
            </div>
        </div>
    </div>
 --}}
    <div class="col-md-6">
        <div class="card-body pt-3 bg-light shadow-lg table-responsive">
            {!! Alert(
            $icon = 'fa-info',
            $class = 'alert-primary',
            $Title = 'Project Financial Analysis (TIMS) '.$Year,
            $Msg = 'Budget , Actual Expenditure, Variance , Absorption Rate',
            ) !!}
        </div>
        <div class="card-body pt-3 bg-light shadow-lg table-responsive">

            <table class="  mytable table  table-rounded table-bordered  border gy-3 gs-3">
                <thead>
                    <tr class="fw-bold  text-gray-800 border-bottom border-gray-200">

                        <th>Funds Disbursed</th>
                        <th>Budget</th>
                        <th>Actual Budget</th>
                        <th>Variance</th>
                        <th>Budget burn rate</th>



                    </tr>
                </thead>
                <tbody>

                    <tr>

                        <td class="bg-primary text-light fw-bolder">{{number_format($FundDisbursementAtPresentInUsd,2) }} USD</td>
                        <td class="bg-dark text-light fw-bolder">{{number_format($TotalBudgetInUsd,2) }} USD</td>
                        <td class="bg-danger text-light fw-bolder">{{number_format( $Expenditure, 2) }} USD</td>
                        <td class="bg-primary text-light fw-bolder">{{number_format($TotalBudgetInUsd - $Expenditure, 2) }} USD</td>
                        <td class="bg-success text-light fw-bolder">{{ number_format(($Expenditure/$TotalBudgetInUsd)*100, 1) }}%</td>



                    </tr>




                </tbody>
            </table>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card-body pt-3 bg-light shadow-lg table-responsive">
            {!! Alert(
            $icon = 'fa-info',
            $class = 'alert-primary',
            $Title = 'Expenditure by quarter analysis '.$Year,
            $Msg = 'Quarterly expenditure',
            ) !!}
        </div>
        <div class="card-body pt-3 bg-light shadow-lg table-responsive">

            <table class="  mytable table  table-rounded table-bordered  border gy-3 gs-3">
                <thead>
                    <tr class="fw-bold  text-gray-800 border-bottom border-gray-200">

                        <th>Financial Quarter</th>
                        <th>Financial Year</th>
                        <th>Amount Spent</th>

                    </tr>
                </thead>
                <tbody>

                    @isset($FinancialQuarter)
                        @foreach ($FinancialQuarter as $d )
                        <tr>

                            <td class="bg-primary text-light fw-bolder">{{ $d->FinancialQuarter}}</td>
                            <td class="bg-success text-light fw-bolder">{{ $Year}}</td>
                            <td class="bg-dark text-light fw-bolder">{{number_format($d->total_amount_spent,2) }} USD</td>




                        </tr>
                        @endforeach
                    @endisset






                </tbody>
            </table>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card-body pt-3 bg-light shadow-lg table-responsive">
            {!! Alert(
            $icon = 'fa-info',
            $class = 'alert-primary',
            $Title = 'Expenditure by module analysis '.$Year,
            $Msg = 'Module expenditure',
            ) !!}
        </div>
        <div class="card-body pt-3 bg-light shadow-lg table-responsive">

            <table class="  mytable table  table-rounded table-bordered  border gy-3 gs-3">
                <thead>
                    <tr class="fw-bold  text-gray-800 border-bottom border-gray-200">

                        <th>Module Name</th>
                        <th>Financial Year</th>
                        <th>Amount Spent</th>

                    </tr>
                </thead>
                <tbody>

                    @isset($GroupByModule)
                        @foreach ($GroupByModule as $c )
                        <tr>

                            <td class="bg-dark text-light fw-bolder">{{ $c->ProjectModuleName}}</td>
                            <td class="bg-success text-light fw-bolder">{{ $Year}}</td>
                            <td class="bg-dark text-light fw-bolder">{{number_format($c->total,2) }} USD</td>




                        </tr>
                        @endforeach
                    @endisset






                </tbody>
            </table>
        </div>
    </div>


    <div class="col-md-6">
        <div class="card-body pt-3 bg-light shadow-lg table-responsive">
            {!! Alert(
            $icon = 'fa-info',
            $class = 'alert-primary',
            $Title = 'Expenditure by cost category analysis '.$Year,
            $Msg = 'Expenditure by cost category',
            ) !!}
        </div>
        <div class="card-body pt-3 bg-light shadow-lg table-responsive">

            <table class="  mytable table  table-rounded table-bordered  border gy-3 gs-3">
                <thead>
                    <tr class="fw-bold  text-gray-800 border-bottom border-gray-200">

                        <th>Cost Input Category</th>
                        <th>Financial Year</th>
                        <th>Amount Spent</th>

                    </tr>
                </thead>
                <tbody>

                    @isset($GroupByCostGroup)
                        @foreach ($GroupByCostGroup as $e )
                        <tr>

                            <td class="bg-danger text-light fw-bolder">{{ $e->ExpenditureGroup}}</td>
                            <td class="bg-primary text-light fw-bolder">{{ $Year}}</td>
                            <td class="bg-dark text-light fw-bolder">{{number_format($e->total,2) }} USD</td>




                        </tr>
                        @endforeach
                    @endisset






                </tbody>
            </table>
        </div>
    </div>
</div>
