<div class="row">
    <div class="col-md-6">
        <div class="card-body pt-3 bg-light shadow-lg table-responsive">
            {!! Alert(
                $icon = 'fa-info',
                $class = 'alert-primary',
                $Title =
                    'Project Intervention Financial Analysis (TIMS3) for the date' .
                    date('d-M-Y'),
                $Msg = 'Budget , Actual Expenditure, Variance , Absorption Rate',
            ) !!}
        </div>
        <div class=" card-body pt-3 bg-light shadow-lg table-responsive">

            <table
                class="mytable table table-rounded table-bordered  border gy-3 gs-3">
                <thead>
                    <tr
                        class="fw-bold  text-gray-800 border-bottom border-gray-200">

                        <th>Intervention</th>
                        <th>Budget</th>
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
                                    {{ $bva->InterventionName }}
                                </td>
                                <td class="bg-dark text-light fw-bolder">
                                    {{ number_format($bva->TotalBudgetInUsd, 3) }}
                                    USD
                                </td>
                                <td class="bg-danger text-light fw-bolder">
                                    {{ number_format($bva->TotalAmountSpentByIntervention, 3) }}
                                    USD
                                </td>
                                <td class="bg-primary text-light fw-bolder">
                                    {{ number_format($bva->TotalBudgetInUsd - $bva->TotalAmountSpentByIntervention, 3) }}
                                    USD</td>
                                <td class="bg-success text-light fw-bolder">
                                    @if ($bva->TotalBudgetInUsd > 0)
                                        {{ number_format(($bva->TotalAmountSpentByIntervention / $bva->TotalBudgetInUsd) * 100, 3) }}%
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
                $Title = 'TIMS3 Project Intervention Expenditure By Year',
                $Msg = 'Intervention Expenditure Filtered By Year',
            ) !!}
        </div>
        <div class="card-body pt-3 bg-light shadow-lg table-responsive">

            <table
                class="mytable table table-rounded table-bordered  border gy-3 gs-3">
                <thead>
                    <tr
                        class="fw-bold  text-gray-800 border-bottom border-gray-200">


                        <th>Intervention</th>
                        <th>Year</th>
                        <th>Intervention Budget</th>
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
                                    {{ $byear->InterventionName }}
                                </td>

                                <td class="bg-primary text-light fw-bolder">
                                    {{ floor($byear->FinancialYear) }}
                                </td>


                                <td class="bg-dark text-light fw-bolder">
                                    {{ number_format($byear->TotalBudgetInUsd, 3) }}
                                    USD
                                </td>
                                <td class="bg-danger text-light fw-bolder">
                                    {{ number_format($byear->TotalAmountSpentByYear, 3) }}
                                    USD
                                </td>
                                <td class="bg-primary text-light fw-bolder">
                                    {{ number_format($byear->TotalBudgetInUsd - $byear->TotalAmountSpentByYear, 3) }}
                                    USD</td>
                                <td class="bg-success text-light fw-bolder">
                                    @if ($byear->TotalBudgetInUsd > 0)
                                        {{ number_format(($byear->TotalAmountSpentByYear / $byear->TotalBudgetInUsd) * 100, 1) }}%
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
                $Title =
                    'TIMS3 Project Intervention Expenditure By Financial Quarter and Year',
                $Msg = 'Intervention Expenditure Filtered By  Quarter and Year',
            ) !!}
        </div>
        <div class="card-body pt-3 bg-light shadow-lg table-responsive">

            <table
                class="mytable table table-rounded table-bordered  border gy-3 gs-3">
                <thead>
                    <tr
                        class="fw-bold  text-gray-800 border-bottom border-gray-200">


                        <th>Intervention Name</th>
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
                                    {{ $byear->InterventionName }}
                                </td>

                                <td class="bg-primary text-light fw-bolder">
                                    {{ floor($data->FinancialYear) }}
                                </td>


                                <td class="bg-primary text-light fw-bolder">
                                    {{ $data->FinancialQuarter }}
                                </td>

                                <td class="bg-dark text-light fw-bolder">
                                    {{ number_format($data->TotalBudgetInUsd, 3) }}
                                    USD
                                </td>
                                <td class="bg-danger text-light fw-bolder">
                                    {{ number_format($data->TotalAmountSpentByQuarter, 3) }}
                                    USD
                                </td>
                                <td class="bg-primary text-light fw-bolder">
                                    {{ number_format($data->TotalBudgetInUsd - $data->TotalAmountSpentByQuarter, 3) }}
                                    USD</td>
                                <td class="bg-success text-light fw-bolder">
                                    @if ($data->TotalBudgetInUsd > 0)
                                        {{ number_format(($data->TotalAmountSpentByQuarter / $data->TotalBudgetInUsd) * 100, 4) }}%
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
