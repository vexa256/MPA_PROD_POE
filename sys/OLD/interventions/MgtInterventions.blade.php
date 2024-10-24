<!--begin::Card body-->
<div class="card-body pt-3 bg-light shadow-lg table-responsive">
    {!! Alert(
        $icon = 'fa-info',
        $class = 'alert-primary',
        $Title = 'Let\'s manage project interventions',
        $Msg = 'Add, remove and edit  the project module inventory',
    ) !!}
</div>
<div class="card-body pt-3 bg-light shadow-lg table-responsive">
    {{ HeaderBtn($Toggle = 'New', $Class = 'btn-danger', $Label = 'New Project Intervention', $Icon = 'fa-plus') }}
    <table class=" mytable table table-rounded table-bordered  border gy-3 gs-3">
        <thead>
            <tr class="fw-bold  text-gray-800 border-bottom border-gray-200">
                <th>Interventions </th>
                {{-- <th>Description</th> --}}
                <th>Budget</th>
                <th>Out Puts</th>
                <th class="bg-dark text-light"> Update </th>
                <th class="bg-danger fw-bolder text-light"> Delete </th>



            </tr>
        </thead>
        <tbody>
            @isset($Interventions)
                @foreach ($Interventions as $data)
                    <tr>

                        <td>{{ $data->InterventionName }}</td>
                        {{-- <td>{{ $data->Description }}</td> --}}
                        <td>{{ number_format($data->TotalBudgetInUsd) }} USD</td>



                        <td>
                            <a data-bs-toggle="modal" class="btn btn-danger btn-sm"
                                href="#ViewDesc{{ $data->id }}">

                                <i class="fas fa-binoculars" aria-hidden="true"></i>
                            </a>

                        </td>



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
                                        'TableName' => 'project_interventions',
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




{{ DescModal($Interventions, $Title = 'View the out puts  attached to selected intervention ', $ModalID = 'ViewDesc', $col = 'OutPuts') }}



@isset($Interventions)
    @foreach ($Interventions as $up)
        {{ UpdateModalHeader($Title = 'Update the selected  record', $ModalID = $up->id) }}
        <form action="{{ route('MassUpdate') }}" class="" method="POST">
            @csrf

            <div class="row">

                <input type="hidden" name="id" value="{{ $up->id }}">

                <input type="hidden" name="TableName"
                    value="project_interventions">

                {{ RunUpdateModalFinal($ModalID = $up->id, $Extra = '', $csrf = null, $Title = null, $RecordID = $up->id, $col = '6', $te = '12', $TableName = 'project_interventions') }}
            </div>


            {{ _UpdateModalFooter() }}

        </form>
    @endforeach
@endisset


@include('interventions.NewIntervention')
