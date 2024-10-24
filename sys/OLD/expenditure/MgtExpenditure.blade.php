<!--begin::Card body-->
<div class="card-body pt-3 bg-light shadow-lg table-responsive">
    {!! Alert(
        $icon = 'fa-info',
        $class = 'alert-primary',
        $Title = 'Let\'s manage project expenditure ',
        $Msg = 'Add, remove and edit the project expenditure records',
    ) !!}
</div>
<div class="card-body pt-3 bg-light shadow-lg table-responsive">
    {{ HeaderBtn($Toggle = 'New', $Class = 'btn-danger', $Label = 'Record Expenditure', $Icon = 'fa-plus') }}
    <table class=" mytable table table-rounded table-bordered  border gy-3 gs-3">
        <thead>
            <tr class="fw-bold  text-gray-800 border-bottom border-gray-200">
                <th> Project </th>
                <th> Module </th>
                <th> Intervention </th>
                <th> Activity </th>
                <th> Category </th>
                <th> Narrative</th>
                <th>Budget line</th>
                <th>Amount Spent</th>
                <th>Financial Quarter</th>
                <th>Financial Year</th>
                <th>Expected out comes</th>
                <th class="bg-dark text-light"> Update </th>
                <th class="bg-danger fw-bolder text-light"> Delete </th>



            </tr>
        </thead>
        <tbody>
            @isset($Expenditure)
                @foreach ($Expenditure as $data)
                    <tr>

                        <td>{{ $data->ProjectName }}</td>
                        <td>{{ $data->ProjectModuleName }}</td>
                        <td>{{ $data->InterventionName }}</td>
                        <td>{{ $data->ActivityName }}</td>
                        <td>{{ $data->CostInput }}</td>
                        <td>{{ $data->Narrative }}</td>
                        <td>{{ $data->BudgetLine }}</td>
                        <td>{{ number_format($data->AmountSpentInUsd, 3) }}</td>
                        <td>{{ $data->FinancialQuarter }}</td>
                        <td>{{ floor($data->FinancialYear) }}</td>
                        <td>{{ $data->OutPuts }}</td>





                        <td>

                            <a data-bs-toggle="modal"
                                class="btn shadow-lg btn-dark btn-sm admin"
                                href="#Update{{ $data->id }}">

                                <i class="fas fa-edit" aria-hidden="true"></i>
                            </a>

                        </td>


                        <td>

                            {!! ConfirmBtn(
                                $data = [
                                    'msg' => 'You want to delete this record',
                                    'route' => route('DeleteData', [
                                        'id' => $data->id,
                                        'TableName' => 'project_expenditures',
                                    ]),
                                    'label' => '<i class="fas fa-trash"></i>',
                                    'class' => 'btn btn-danger btn-sm deleteConfirm',
                                ],
                            ) !!}

                        </td>




                    </tr>
                @endforeach
            @endisset



        </tbody>
    </table>
</div>



@isset($Expenditure)
    @foreach ($Expenditure as $Up)
        {{ UpdateModalHeader($Title = 'Update the selected  record', $ModalID = $Up->id) }}
        <form action="{{ route('MassUpdate') }}" class="" method="POST">
            @csrf
            {{-- {{ $Up->id }} --}}
            <div class="row">
                {{-- <div class="dddd"> --}}
                <div class="mb-3 col-md-6">
                    <label id="label" for=""
                        class="required mt-3 form-label">Select
                        Cost Input </label>
                    <select required name="CID"
                        class="form-select   form-select-solid"
                        data-control="select2" data-placeholder="Select a option">
                        <option value="{{ $Up->CID }}">
                            {{ $Up->CostInput }}
                            @isset($CostGroups)
                                @foreach ($CostGroups as $up)
                            <option value="{{ $up->CID }}">
                                {{ $up->CostInput }}

                            </option>
        @endforeach
    @endisset

    </select>

    </div>


    <div class="mb-3 col-md-6">
        <label id="label" for="" class="required mt-3 form-label">Select
            Financial Year
        </label>
        <select required name="FinancialYear"
            class="form-select   form-select-solid" data-control="select2"
            data-placeholder="Select a option">


            <option value="{{ $Up->FinancialYear }}">
                {{ $Up->FinancialYear }} </option>
            @for ($i = date('Y') - 10; $i <= date('Y') + 10; $i++)
                <option value="{{ $i }}">
                    {{ $i }}</option>
            @endfor


        </select>

    </div>

    {{-- </div> --}}



    <div class="mb-3 col-md-6">
        <label id="label" for="" class="required mt-3 form-label">Select
            Financial Quarter</label>
        <select required name="FinancialQuarter"
            class="form-select form-select-solid" data-control="select2"
            data-placeholder="Select an option">

            <option value="{{ $Up->FinancialQuarter }}">
                {{ $Up->FinancialQuarter }}</option>
            @php
                // Generate single-digit quarters (Q1, Q2, ..., Q9)
                for ($i = 1; $i <= 9; $i++) {
                    echo '<option value="Q' . $i . '">Q' . $i . '</option>';
                }

                // Generate all possible two combinations of quarters from 1 to 18
                for ($i = 1; $i <= 18; $i++) {
                    for ($j = $i; $j <= 18; $j++) {
                        $range = 'Q' . $i . '-Q' . $j;
                        echo '<option value="' . $range . '">' . $range . '</option>';
                    }
                }
            @endphp
        </select>
    </div>



    <div class="mb-3 col-md-6">
        <label id="label" for="" class="required mt-3 form-label">Select
            Activity
        </label>
        <select required name="AID" class="form-select   form-select-solid"
            data-control="select2" data-placeholder="Select a option">
            <option value="{{ $Up->AID }}">
                {{ $Up->ActivityName }} </option>
            @isset($Activities)
                @foreach ($Activities as $up2)
                    <option value="{{ $up2->AID }}">
                        {{ $up2->ActivityName }}

                    </option>
                @endforeach
            @endisset

        </select>

    </div>

    <div class="mb-3 col-md-6">
        <label id="label" for="" class="required mt-3 form-label">Select
            Budget Line
        </label>
        <select required name="BudgetLine" class="form-select   form-select-solid"
            data-control="select2" data-placeholder="Select a option">
            <option value="{{ $Up->BudgetLine }}">
                {{ $Up->BudgetLine }} </option>
            @isset($Activities)
                @foreach ($Activities as $bl)
                    <option value="{{ $bl->BudgetLine }}">
                        {{ $bl->BudgetLine }}

                    </option>
                @endforeach
            @endisset

        </select>

    </div>

    <div class="mb-3 col-md-4">
        <label id="label" for="" class="required mt-3 form-label">Select
            Intervention
        </label>
        <select required name="IID" class="form-select   form-select-solid"
            data-control="select2" data-placeholder="Select a option">
            <option value="{{ $Up->IID }}">
                {{ $Up->InterventionName }} </option>
            @isset($Interventions)
                @foreach ($Interventions as $i)
                    <option value="{{ $i->IID }}">
                        {{ $i->InterventionName }}

                    </option>
                @endforeach
            @endisset

        </select>

    </div>
    <input type="hidden" name="id" value="{{ $Up->id }}">

    <input type="hidden" name="TableName" value="project_expenditures">

    {{ RunUpdateModalFinal($ModalID = $Up->id, $Extra = '', $csrf = null, $Title = null, $RecordID = $Up->id, $col = '4', $te = '12', $TableName = 'project_expenditures') }}
    </div>


    {{ _UpdateModalFooter() }}

    </form>
    @endforeach
@endisset


@include('expenditure.NewExpenditure')
