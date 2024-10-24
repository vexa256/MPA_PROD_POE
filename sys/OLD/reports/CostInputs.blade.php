<div class="row">
    <div class="col-md-12 ">
        <div class="card-body pt-3 bg-light shadow-lg table-responsive">
            {!! Alert(
                $icon = 'fa-info',
                $class = 'alert-primary',
                $Title =
                    'Project Cost Input Financial Analysis (TIMS3) for the date ' .
                    date('d-M-Y'),
                $Msg = 'Expenditure by cost input category',
            ) !!}
        </div>
        <div class=" card-body pt-3 bg-light shadow-lg table-responsive">

            <table
                class="mytable table table-rounded table-bordered  border gy-3 gs-3">
                <thead>
                    <tr
                        class="fw-bold  text-gray-800 border-bottom border-gray-200">

                        {{-- <th>Project </th> --}}
                        <th>Cost Input</th>
                        <th>Total Amount Spent</th>




                    </tr>
                </thead>
                <tbody>
                    @isset($CostInput)
                        @foreach ($CostInput as $data)
                            <tr>

                                {{-- <td class="bg-primary text-light fw-bolder">
                                    {{ $data->ProjectName }}
                                </td> --}}
                                <td class="bg-dark text-light fw-bolder">
                                    {{ $data->CostInput }}

                                </td>
                                <td class="bg-danger text-light fw-bolder">
                                    {{ number_format($data->TotalAmountSpent, 3) }}
                                    USD
                                </td>





                            </tr>
                        @endforeach
                    @endisset


                </tbody>
            </table>
        </div>
    </div>
</div>
