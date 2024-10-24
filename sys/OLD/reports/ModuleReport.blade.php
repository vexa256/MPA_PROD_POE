<div class="row">
    <div class="col-md-6">
        <div class="card-body pt-3 bg-light shadow-lg table-responsive">
            {!! Alert(
                $icon = 'fa-info',
                $class = 'alert-primary',
                $Title =
                    'Project Module Financial Analysis (TIMS3) for the date' .
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

                        <th>Module</th>
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
                                    {{ $bva->ProjectModuleName }}
                                </td>
                                <td class="bg-dark text-light fw-bolder">
                                    {{ number_format($bva->TotalBudgetInUsd, 3) }}
                                    USD
                                </td>
                                <td class="bg-danger text-light fw-bolder">
                                    {{ number_format($bva->TotalAmountSpentByModule, 3) }}
                                    USD
                                </td>
                                <td class="bg-primary text-light fw-bolder">
                                    {{ number_format($bva->TotalBudgetInUsd - $bva->TotalAmountSpentByModule, 3) }}
                                    USD</td>
                                <td class="bg-success text-light fw-bolder">
                                    @if ($bva->TotalBudgetInUsd > 0)
                                        {{ number_format(($bva->TotalAmountSpentByModule / $bva->TotalBudgetInUsd) * 100, 3) }}%
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
                $Title = 'TIMS3 Project Module Expenditure By Year',
                $Msg = 'Module Expenditure Filtered By Year',
            ) !!}
        </div>
        <div class="card-body pt-3 bg-light shadow-lg table-responsive">

            <table
                class="mytable table table-rounded table-bordered  border gy-3 gs-3">
                <thead>
                    <tr
                        class="fw-bold  text-gray-800 border-bottom border-gray-200">


                        <th>Module</th>
                        <th>Year</th>
                        <th>Module Budget</th>
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
                                    {{ $byear->ProjectModuleName }}
                                </td>

                                <td class="bg-primary text-light fw-bolder">
                                    {{ floor($byear->FinancialYear) }}
                                </td>


                                <td class="bg-dark text-light fw-bolder">
                                    {{ number_format($byear->TotalBudgetInUsd, 3) }}
                                    USD
                                </td>
                                <td class="bg-danger text-light fw-bolder">
                                    {{ number_format($byear->TotalAmountSpentByModuleByYear, 3) }}
                                    USD
                                </td>
                                <td class="bg-primary text-light fw-bolder">
                                    {{ number_format($byear->TotalBudgetInUsd - $byear->TotalAmountSpentByModuleByYear, 3) }}
                                    USD</td>
                                <td class="bg-success text-light fw-bolder">
                                    @if ($byear->TotalBudgetInUsd > 0)
                                        {{ number_format(($byear->TotalAmountSpentByModuleByYear / $byear->TotalBudgetInUsd) * 100, 1) }}%
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
                $Title = 'TIMS3 Project Module Expenditure By Financial Quarter and Year',
                $Msg = 'Module Expenditure Filtered By  Quarter and Year',
            ) !!}
        </div>
        <div class="card-body pt-3 bg-light shadow-lg table-responsive">

            <table
                class="mytable table table-rounded table-bordered  border gy-3 gs-3">
                <thead>
                    <tr
                        class="fw-bold  text-gray-800 border-bottom border-gray-200">


                        <th>Module Name</th>
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
                                    {{ $byear->ProjectModuleName }}
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
                                    {{ number_format($data->TotalAmountSpentByModuleByQuarter, 3) }}
                                    USD
                                </td>
                                <td class="bg-primary text-light fw-bolder">
                                    {{ number_format($data->TotalBudgetInUsd - $data->TotalAmountSpentByModuleByQuarter, 3) }}
                                    USD</td>
                                <td class="bg-success text-light fw-bolder">
                                    @if ($data->TotalBudgetInUsd > 0)
                                        {{ number_format(($data->TotalAmountSpentByModuleByQuarter / $data->TotalBudgetInUsd) * 100, 4) }}%
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
