<!--begin::Card body-->
<div class="card-body pt-3 bg-light shadow-lg table-responsive">
    {!! Alert(
    $icon = 'fa-info',
    $class = 'alert-primary',
    $Title = 'Activity Progress Report',
    $Msg = 'Track the progress of activities for the selected module',
    ) !!}
</div>
<div class="card-body pt-3 bg-light shadow-lg table-responsive">
    {{-- {{ HeaderBtn($Toggle = 'New', $Class = 'btn-danger', $Label = 'New Project Activity', $Icon = 'fa-plus') }} --}}
    <table class=" mytable table table-rounded table-bordered  border gy-3 gs-3">
        <thead>
            <tr class="fw-bold  text-gray-800 border-bottom border-gray-200">
                <th>Planned Activity </th>
                {{-- <th>Year </th> --}}
                {{-- <th>Quater </th> --}}
                <th>Module</th>
                {{-- <th>Budget Line</th> --}}
                <th>Description</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th> Status</th>
                <th> Comments</th>
                <th> Budget</th>
                <th>Objectives</th>
                {{-- <th class="bg-dark text-light"> Change Status </th> --}}
                {{-- <th class="bg-dark text-light"> Update </th>
                <th class="bg-danger fw-bolder text-light"> Delete </th> --}}



            </tr>
        </thead>
        <tbody>
            @isset($Activities)
            @foreach ($Activities as $data)
            <tr>

                <td>{{ $data->ActivityName }}</td>
                {{-- <td>{{ $data->FinancialYear }}</td>
                <td>Q{{ $data->FinancialQuater }}</td> --}}
                <td>{{ $data->ProjectModuleName }}</td>
                {{-- <td>{{ $data->BudgetLine }}</td> --}}
                <td>{{ $data->ActivityDescription }}</td>

                <td>{{ date('F j, Y', strtotime($data->StartDate)) }}
                </td>
                <td>{{ date('F j, Y', strtotime($data->EndDate)) }}
                </td>
                <td class="@if($data->ProgressStatus == "Completed") bg-danger @else bg-primary @endif fw-bolder text-light">
                    {{ $data->ProgressStatus }}</td>

                    <td class="bg-dark fw-bolder text-light">
                        {{ $data->Comments }}</td>

                <td>{{ number_format($data->ActivityBudgetInUsd) }} USD
                </td>



                <td>
                    <a data-bs-toggle="modal" class="btn btn-danger btn-sm" href="#ViewDesc{{ $data->id }}">

                        <i class="fas fa-binoculars" aria-hidden="true"></i>
                    </a>

                </td>

{{--
                <td class="">
                    <a data-bs-toggle="modal" class="btn btn-primary btn-sm" href="#Status{{ $data->id }}">

                        <i class="fas fa-spinner" aria-hidden="true"></i>
                    </a>

                </td> --}}






            </tr>
            @endforeach
            @endisset



        </tbody>
    </table>
</div>




{{ DescModal($Activities, $Title = 'View the objectives  attached to selected activity ', $ModalID = 'ViewDesc', $col = 'ActivityObjectives') }}



@isset($Activities)
@foreach ($Activities as $up)
{{ UpdateModalHeader($Title = 'Update the selected  record', $ModalID = $up->id) }}
<form action="{{ route('MassUpdate') }}" class="" method="POST">
    @csrf

    <div class="row">



        <div class="mb-3 col-md-6">
            <label id="label" for="" class="required mt-3 form-label">Select
                Financial Quater
            </label>
            <select required name="FinancialQuater" class="form-select   form-select-solid" data-control="select2" data-placeholder="Select a option">


                <option value="{{ $up->FinancialQuater }}">
                    Q{{ $up->FinancialQuater }}</option>
                <option value="1">Q1</option>
                <option value="2">Q2</option>
                <option value="3">Q3</option>
                <option value="4">Q4</option>
                <option value="5">Q5</option>
                <option value="6">Q6</option>
                <option value="7">Q7</option>
                <option value="8">Q8</option>
                <option value="9">Q9</option>
                <option value="10">Q10</option>
                <option value="11">Q11</option>
                <option value="12">Q12</option>

            </select>

        </div>
        <div class="mb-3 col-md-6">
            <label id="label" for="" class="required mt-3 form-label">Select
                Financial Year
            </label>
            <select required name="FinancialYear" class="form-select   form-select-solid" data-control="select2" data-placeholder="Select a option">


                <option value="">
                </option>

                @for ($i = date('Y') - 5; $i <= date('Y') + 10; $i++) <option value="{{ $i }}">
                    {{ $i }}</option>
                    @endfor


            </select>

        </div>

        <input type="hidden" name="id" value="{{ $up->id }}">

        <input type="hidden" name="TableName" value="project_ativities">

        {{ RunUpdateModalFinal($ModalID = $up->id, $Extra = '', $csrf = null, $Title = null, $RecordID = $up->id, $col = '4', $te = '12', $TableName = 'project_ativities') }}
    </div>


    {{ _UpdateModalFooter() }}

</form>
@endforeach
@endisset


@include('activities.NewActivity')
@include('activities.ActivityStatus')
