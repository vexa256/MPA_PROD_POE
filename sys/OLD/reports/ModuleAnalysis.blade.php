<!--begin::Card body-->
<div class="row">

    <div class="col-md-6">
        <div class="card-body pt-3 bg-light shadow-lg table-responsive">
            {!! Alert(
                $icon = 'fa-info',
                $class = 'alert-primary',
                $Title = 'Module Expenditure Analysis By Quarter',
                $Msg = 'Financial Quarter Analytics Per Module',
            ) !!}
        </div>
        <div class="card-body pt-3 bg-light shadow-lg table-responsive">

            <table
                class=" mytable table table-rounded table-bordered  border gy-3 gs-3">
                <thead>
                    <tr
                        class="fw-bold  text-gray-800 border-bottom border-gray-200">
                        <th>Module </th>
                        <th>Financial Quarter</th>
                        <th>Financial Year</th>
                        <th>Amount Spent</th>
                        <th>Budget</th>
                        <th>Variance</th>
                        <th>Burn Rate</th>


                    </tr>
                </thead>
                <tbody>
                    @isset($PerformanceByQuarter)
                        @foreach ($PerformanceByQuarter as $data)
                            <tr>

                                <td class="bg-dark text-light">
                                    {{ $data->ProjectModuleName }}</td>
                                <td class="bg-primary text-light">
                                    {{ $data->FinancialQuarter }}</td>
                                <td class="bg-danger text-light">
                                    {{ $data->FinancialYear }}</td>
                                <td class="bg-dark text-light">
                                    {{ number_format($data->SumAmount) }} USD</td>
                                <td class="bg-dark text-light">
                                    {{ number_format($data->ModuleBudget) }} USD
                                </td>
                                <td class="bg-dark text-light">
                                    {{ number_format($data->ModuleBudget - $data->SumAmount) }}
                                    USD</td>
                                <td class="bg-success text-light fw-bolder">
                                    {{-- {{ number_format(($data->SumAmount / $data->ModuleBudget) * 100, 1) }}% --}}


                                    @if ($data->SumAmount > 0)
                                        {{ number_format(($data->SumAmount / $data->ModuleBudget) * 100, 1) }}%
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

    <div class="col-md-6">
        <div class="card-body pt-3 bg-light shadow-lg table-responsive">
            {!! Alert(
                $icon = 'fa-info',
                $class = 'alert-danger',
                $Title = 'Module Expenditure Analysis By Cost Category',
                $Msg = 'Cost Inputs',
            ) !!}
        </div>
        <div class="card-body pt-3 bg-light shadow-lg table-responsive">

            <table
                class=" mytable table table-rounded table-bordered  border gy-3 gs-3">
                <thead>
                    <tr
                        class="fw-bold  text-gray-800 border-bottom border-gray-200">
                        <th>Module </th>
                        <th>Cost Category</th>
                        <th>Financial Year</th>
                        <th>Amount Spent</th>
                        <th>Budget</th>



                    </tr>
                </thead>
                <tbody>
                    @isset($PerformanceGroup)
                        @foreach ($PerformanceGroup as $data)
                            <tr>

                                <td class="bg-dark text-light">
                                    {{ $data->ProjectModuleName }}</td>
                                <td class="bg-primary text-light">
                                    {{ $data->ExpenditureGroup }}</td>
                                <td class="bg-danger text-light">
                                    {{ $data->FinancialYear }}</td>
                                <td class="bg-dark text-light">
                                    {{ number_format($data->SumAmount) }} USD</td>
                                <td class="bg-dark text-light">
                                    {{ number_format($data->ModuleBudget) }} USD
                                </td>




                            </tr>
                        @endforeach
                    @endisset



                </tbody>
            </table>
        </div>

    </div>
</div>
