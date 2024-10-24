<div class="row">
    <div class="col-md-6">
        <div class="card-body pt-3 bg-light shadow-lg table-responsive">
            {!! Alert(
                $icon = 'fa-info',
                $class = 'alert-primary',
                $Title = 'Project Financial Analysis (TIMS3) ' . date('d-M-Y'),
                $Msg = 'Budget , Actual Expenditure, Variance , Absorption Rate',
            ) !!}
        </div>
        <div class=" card-body pt-3 bg-light shadow-lg table-responsive">

            <table
                class="mytable table table-rounded table-bordered  border gy-3 gs-3">
                <thead>
                    <tr
                        class="fw-bold  text-gray-800 border-bottom border-gray-200">

                        <th>Funds Disbursed</th>
                        <th>Total Grant</th>
                        <th>Actual Expenditure</th>
                        <th>Variance</th>
                        <th>Budget burn rate</th>



                    </tr>
                </thead>
                <tbody>
                    @isset($BVA)
                        @foreach ($BVA as $bva)
                            <tr>

                                <td class="bg-primary text-light fw-bolder">
                                    {{ number_format($bva->FundDisbursementAtPresentInUsd, 3) }}
                                    USD</td>
                                <td class="bg-dark text-light fw-bolder">
                                    {{ number_format($bva->TotalGrantInUsd, 3) }}
                                    USD
                                </td>
                                <td class="bg-danger text-light fw-bolder">
                                    {{ number_format($bva->TotalAmountSpent, 3) }}
                                    USD
                                </td>
                                <td class="bg-primary text-light fw-bolder">
                                    {{ number_format($bva->TotalGrantInUsd - $bva->TotalAmountSpent, 3) }}
                                    USD</td>
                                <td class="bg-success text-light fw-bolder">
                                    @if ($bva->TotalGrantInUsd > 0)
                                        {{ number_format(($bva->TotalAmountSpent / $bva->TotalGrantInUsd) * 100, 1) }}%
                                    @else
                                        N/A
                                    @endif
                                </td>




                            </tr>
                        @endforeach
                    @endisset


                </tbody>
            </table>
        </div>
    </div>

    <div class="col-md-6 ">
        <div class="card-body pt-3 bg-light shadow-lg table-responsive">
            {!! Alert(
                $icon = 'fa-info',
                $class = 'alert-primary',
                $Title = 'TIMS3 Project Expenditure By Year',
                $Msg = 'Expenditure Filtered By Year',
            ) !!}
        </div>
        <div class="card-body pt-3 bg-light shadow-lg table-responsive">

            <table
                class="mytable table table-rounded table-bordered  border gy-3 gs-3">
                <thead>
                    <tr
                        class="fw-bold  text-gray-800 border-bottom border-gray-200">


                        <th>Year</th>
                        <th>Total Grant</th>
                        <th>Actual Expenditure</th>
                        <th>Variance</th>
                        <th>Budget burn rate</th>



                    </tr>
                </thead>
                <tbody>
                    @isset($BYear)
                        @foreach ($BYear as $byear)
                            <tr>
                                <td class="bg-primary text-light fw-bolder">
                                    {{ floor($byear->FinancialYear) }}
                                    USD</td>

                                <td class="bg-dark text-light fw-bolder">
                                    {{ number_format($byear->TotalGrantInUsd, 3) }}
                                    USD
                                </td>
                                <td class="bg-danger text-light fw-bolder">
                                    {{ number_format($byear->TotalAmountSpentByFinancialYear, 3) }}
                                    USD
                                </td>
                                <td class="bg-primary text-light fw-bolder">
                                    {{ number_format($byear->TotalGrantInUsd - $byear->TotalAmountSpentByFinancialYear, 3) }}
                                    USD</td>
                                <td class="bg-success text-light fw-bolder">
                                    @if ($byear->TotalGrantInUsd > 0)
                                        {{ number_format(($byear->TotalAmountSpentByFinancialYear / $byear->TotalGrantInUsd) * 100, 1) }}%
                                    @else
                                        N/A
                                    @endif
                                </td>




                            </tr>
                        @endforeach
                    @endisset



                </tbody>
            </table>
        </div>
    </div>

    <div class="col-md-12 mt-5 pt-3">
        <div class="card-body pt-3 bg-light shadow-lg table-responsive">
            {!! Alert(
                $icon = 'fa-info',
                $class = 'alert-success',
                $Title = 'TIMS3 Project Expenditure By Financial Quarter and Year',
                $Msg = 'Expenditure Filtered By Year',
            ) !!}
        </div>
        <div class="card-body pt-3 bg-light shadow-lg table-responsive">

            <table
                class="mytable table table-rounded table-bordered  border gy-3 gs-3">
                <thead>
                    <tr
                        class="fw-bold  text-gray-800 border-bottom border-gray-200">


                        <th>Year</th>
                        <th>Financial Quarter</th>
                        <th>Total Grant</th>
                        <th>Actual Expenditure</th>
                        <th>Variance</th>
                        <th>Budget burn rate</th>



                    </tr>
                </thead>
                <tbody>
                    @isset($BQuarter)
                        @foreach ($BQuarter as $data)
                            <tr>
                                <td class="bg-primary text-light fw-bolder">
                                    {{ floor($data->FinancialYear) }}
                                    USD</td>


                                <td class="bg-primary text-light fw-bolder">
                                    {{ $data->FinancialQuarter }}
                                </td>

                                <td class="bg-dark text-light fw-bolder">
                                    {{ number_format($data->TotalGrantInUsd, 3) }}
                                    USD
                                </td>
                                <td class="bg-danger text-light fw-bolder">
                                    {{ number_format($data->TotalAmountSpentByQuarter, 3) }}
                                    USD
                                </td>
                                <td class="bg-primary text-light fw-bolder">
                                    {{ number_format($data->TotalGrantInUsd - $data->TotalAmountSpentByQuarter, 3) }}
                                    USD</td>
                                <td class="bg-success text-light fw-bolder">
                                    @if ($data->TotalGrantInUsd > 0)
                                        {{ number_format(($data->TotalAmountSpentByQuarter / $data->TotalGrantInUsd) * 100, 4) }}%
                                    @else
                                        N/A
                                    @endif
                                </td>




                            </tr>
                        @endforeach
                    @endisset



                </tbody>
            </table>
        </div>
    </div>

</div>
