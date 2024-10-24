 <!--begin::Card body-->
 <div class="card-body pt-3 bg-light shadow-lg table-responsive">
     {!! Alert(
         $icon = 'fa-info',
         $class = 'alert-primary',
         $Title = 'Project Database',
         $Msg = 'Manage the project database and all associated records',
     ) !!}
 </div>
 <div class="card-body pt-3 bg-light shadow-lg table-responsive">
     {{ HeaderBtn($Toggle = 'New', $Class = 'btn-danger', $Label = 'New Project', $Icon = 'fa-plus') }}
     <table
         class=" mytable table table-rounded table-bordered  border gy-3 gs-3">
         <thead>
             <tr class="fw-bold  text-gray-800 border-bottom border-gray-200">
                 <th>Project</th>
                 <th>Description</th>
                 <th>Applicant</th>
                 <th>Principal Recipient</th>
                 <th>Grant No.</th>
                 <th>Grant Name.</th>
                 <th>Total Budget</th>
                 <th>Total Grant</th>
                 <th>Funds Released</th>

                 <th>Start Date</th>
                 <th>End Date</th>
                 <th>Project Details</th>
                 <th>Update</th>
                 <th class="bg-dark text-light"> Delete</th>



             </tr>
         </thead>
         <tbody>
             @isset($Projects)
                 @foreach ($Projects as $data)
                     <tr>
                         <td>{{ $data->ProjectName }}</td>
                         <td>{{ $data->ProjectDescription }}</td>
                         <td>{{ $data->ApplicantOrCountry }}</td>
                         <td>{{ $data->PrincipalRecipient }}</td>
                         <td>{{ $data->GrantNumber }}</td>
                         <td>{{ $data->GrantName }}</td>
                         <td>{{ number_format($data->TotalBudgetInUsd) }} USD</td>
                         <td>{{ number_format($data->TotalGrantInUsd) }} USD</td>
                         <td>{{ number_format($data->FundDisbursementAtPresentInUsd) }}
                             USD</td>


                         <td>{{ date('F j, Y', strtotime($data->ImplementationStartDate)) }}
                         </td>
                         <td>{{ date('F j, Y', strtotime($data->ImplementationEndDate)) }}
                         </td>

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
                                         'TableName' => 'projects',
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
 <!--end::Card body-->


 @include('projects.NewProj')


 {{ DescModal($Projects, $Title = 'View the description  attached to selected project ', $ModalID = 'ViewDesc', $col = 'ProjectDetails') }}



 @isset($Projects)
     @foreach ($Projects as $up)
         {{ UpdateModalHeader($Title = 'Update the selected  record', $ModalID = $up->id) }}
         <form action="{{ route('MassUpdate') }}" class="" method="POST">
             @csrf

             <div class="row">





                 <input type="hidden" name="id" value="{{ $up->id }}">

                 <input type="hidden" name="TableName" value="projects">

                 {{ RunUpdateModalFinal($ModalID = $up->id, $Extra = '', $csrf = null, $Title = null, $RecordID = $up->id, $col = '4', $te = '12', $TableName = 'projects') }}
             </div>


             {{ _UpdateModalFooter() }}

         </form>
     @endforeach
 @endisset
