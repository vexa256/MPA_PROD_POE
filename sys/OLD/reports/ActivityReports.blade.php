<div class="row">
    <div class="col-md-6 ">
        <div class="card-body pt-3 bg-light shadow-lg table-responsive">
            {!! Alert(
                $icon = 'fa-info',
                $class = 'alert-success',
                $Title = 'TIMS3 Completed Activities as of the date ' . date('d-M-Y'),
                $Msg = 'Completed Activities',
            ) !!}
        </div>
        <div class=" card-body pt-3 bg-light shadow-lg table-responsive">

            <table
                class="mytable table table-rounded table-bordered  border gy-3 gs-3">
                <thead>
                    <tr
                        class="fw-bold  text-gray-800 border-bottom border-gray-200">

                        {{-- <th>Project </th> --}}
                        <th>Activity</th>
                        <th>Module</th>
                        <th>Intervention</th>
                        <th class="bg-dark text-light fw-bolder">Status</th>
                        <th>Quarter</th>
                        <th>Financial Year</th>




                    </tr>
                </thead>
                <tbody>
                    @isset($Completed)
                        @foreach ($Completed as $data)
                            <tr>


                                <td class="bg-primary text-light fw-bolder">
                                    {{ $data->ActivityName }}

                                </td>

                                <td class="bg-warning text-light fw-bolder">
                                    {{ $data->ProjectModuleName }}

                                </td>

                                <td class="bg-dark text-light fw-bolder">
                                    {{ $data->InterventionName }}

                                </td>

                                <td class="bg-success text-light fw-bolder">
                                    {{ $data->ProgressStatus }}

                                </td>



                                <td class="bg-info text-light fw-bolder">
                                    {{ $data->FinancialQuater }}

                                </td>


                                <td class="bg-primary text-light fw-bolder">
                                    {{ floor($data->FinancialYear) }}

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
                $Title = 'TIMS3 Ongoing Activities as of the date ' . date('d-M-Y'),
                $Msg = 'Ongoing Activities',
            ) !!}
        </div>
        <div class=" card-body pt-3 bg-light shadow-lg table-responsive">

            <table
                class="mytable table table-rounded table-bordered  border gy-3 gs-3">
                <thead>
                    <tr
                        class="fw-bold  text-gray-800 border-bottom border-gray-200">

                        {{-- <th>Project </th> --}}
                        <th>Activity</th>
                        <th>Module</th>
                        <th>Intervention</th>
                        <th class="bg-dark text-light fw-bolder">Status</th>
                        <th>Quarter</th>
                        <th>Financial Year</th>




                    </tr>
                </thead>
                <tbody>
                    @isset($Ongoing)
                        @foreach ($Ongoing as $data)
                            <tr>


                                <td class="bg-primary text-light fw-bolder">
                                    {{ $data->ActivityName }}

                                </td>

                                <td class="bg-warning text-light fw-bolder">
                                    {{ $data->ProjectModuleName }}

                                </td>

                                <td class="bg-dark text-light fw-bolder">
                                    {{ $data->InterventionName }}

                                </td>

                                <td class="bg-success text-light fw-bolder">
                                    {{ $data->ProgressStatus }}

                                </td>



                                <td class="bg-info text-light fw-bolder">
                                    {{ $data->FinancialQuater }}

                                </td>


                                <td class="bg-primary text-light fw-bolder">
                                    {{ floor($data->FinancialYear) }}

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
                $class = 'alert-warning',
                $Title = 'TIMS3 Pending Activities as of the date ' . date('d-M-Y'),
                $Msg = 'Pending Activities',
            ) !!}
        </div>
        <div class=" card-body pt-3 bg-light shadow-lg table-responsive">

            <table
                class="mytable table table-rounded table-bordered  border gy-3 gs-3">
                <thead>
                    <tr
                        class="fw-bold  text-gray-800 border-bottom border-gray-200">

                        {{-- <th>Project </th> --}}
                        <th>Activity</th>
                        <th>Module</th>
                        <th>Intervention</th>
                        <th class="bg-dark text-light fw-bolder">Status</th>
                        <th>Quarter</th>
                        <th>Financial Year</th>




                    </tr>
                </thead>
                <tbody>
                    @isset($Pending)
                        @foreach ($Pending as $data)
                            <tr>


                                <td class="bg-primary text-light fw-bolder">
                                    {{ $data->ActivityName }}

                                </td>

                                <td class="bg-warning text-light fw-bolder">
                                    {{ $data->ProjectModuleName }}

                                </td>

                                <td class="bg-dark text-light fw-bolder">
                                    {{ $data->InterventionName }}

                                </td>

                                <td class="bg-success text-light fw-bolder">
                                    {{ $data->ProgressStatus }}

                                </td>



                                <td class="bg-info text-light fw-bolder">
                                    {{ $data->FinancialQuater }}

                                </td>


                                <td class="bg-primary text-light fw-bolder">
                                    {{ floor($data->FinancialYear) }}

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
                $class = 'alert-danger',
                $Title = 'TIMS3 Terminated Activities as of the date ' . date('d-M-Y'),
                $Msg = 'Terminated Activities',
            ) !!}
        </div>
        <div class=" card-body pt-3 bg-light shadow-lg table-responsive">

            <table
                class="mytable table table-rounded table-bordered  border gy-3 gs-3">
                <thead>
                    <tr
                        class="fw-bold  text-gray-800 border-bottom border-gray-200">

                        {{-- <th>Project </th> --}}
                        <th>Activity</th>
                        <th>Module</th>
                        <th>Intervention</th>
                        <th class="bg-dark text-light fw-bolder">Status</th>
                        <th>Quarter</th>
                        <th>Financial Year</th>




                    </tr>
                </thead>
                <tbody>
                    @isset($Terminated)
                        @foreach ($Terminated as $data)
                            <tr>


                                <td class="bg-primary text-light fw-bolder">
                                    {{ $data->ActivityName }}

                                </td>

                                <td class="bg-warning text-light fw-bolder">
                                    {{ $data->ProjectModuleName }}

                                </td>

                                <td class="bg-dark text-light fw-bolder">
                                    {{ $data->InterventionName }}

                                </td>

                                <td class="bg-success text-light fw-bolder">
                                    {{ $data->ProgressStatus }}

                                </td>



                                <td class="bg-info text-light fw-bolder">
                                    {{ $data->FinancialQuater }}

                                </td>


                                <td class="bg-primary text-light fw-bolder">
                                    {{ floor($data->FinancialYear) }}

                                </td>






                            </tr>
                        @endforeach
                    @endisset


                </tbody>
            </table>
        </div>
    </div>


</div>
