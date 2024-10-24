<div class="modal fade" id="New">
    <div class="modal-dialog modal-dialog-scrollable modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header bg-gray">
                <h5 class="modal-title">Create a new project expenditure and
                    attach it to the module


                    <span class="text-danger fw-bolder">
                        {{ $ModuleName }}
                    </span>



                </h5>

                <!--begin::Close-->
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2"
                    data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-2x fa-times" aria-hidden="true"></i>
                </div>
                <!--end::Close-->
            </div>

            <div class="modal-body ">

                <form action="{{ route('MassInsert') }}" class="row"
                    method="POST">
                    @csrf
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label id="label" for=""
                                class="required mt-3 form-label">Select
                                Cost Input </label>
                            <select required name="CID"
                                class="form-select   form-select-solid"
                                data-control="select2"
                                data-placeholder="Select a option">
                                <option value=""> </option>
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
                            <label id="label" for=""
                                class="required mt-3 form-label">Select
                                Financial Year
                            </label>
                            <select required name="FinancialYear"
                                class="form-select   form-select-solid"
                                data-control="select2"
                                data-placeholder="Select a option">


                                <option value="">
                                </option>

                                @for ($i = date('Y') - 10; $i <= date('Y') + 10; $i++)
                                    <option value="{{ $i }}">
                                        {{ $i }}</option>
                                @endfor


                            </select>

                        </div>

                    </div>
                    <div class="row">


                        <div class="mb-3 col-md-4">
                            <label id="label" for=""
                                class="required mt-3 form-label">Select
                                Financial Quarter</label>
                            <select required name="FinancialQuarter"
                                class="form-select form-select-solid"
                                data-control="select2"
                                data-placeholder="Select an option">
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
                            <label id="label" for=""
                                class="required mt-3 form-label">Select
                                Activity
                            </label>
                            <select required name="AID"
                                class="form-select   form-select-solid"
                                data-control="select2"
                                data-placeholder="Select a option">
                                <option value=""> </option>
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
                            <label id="label" for=""
                                class="required mt-3 form-label">Select
                                Budget Line
                            </label>
                            <select required name="BudgetLine"
                                class="form-select   form-select-solid"
                                data-control="select2"
                                data-placeholder="Select a option">
                                <option value=""> </option>
                                @isset($Activities)
                                    @foreach ($Activities as $bl)
                                        <option value="{{ $bl->BudgetLine }}">
                                            {{ $bl->BudgetLine }}

                                        </option>
                                    @endforeach
                                @endisset

                            </select>

                        </div>





                        @foreach ($Form as $data)
                            @if ($data['type'] == 'string')
                                {{ CreateInputText($data, $placeholder = null, $col = '6') }}
                            @elseif (
                                'smallint' == $data['type'] ||
                                    'bigint' === $data['type'] ||
                                    'integer' == $data['type'] ||
                                    'decimal' == $data['type'] ||
                                    'float' == $data['type'] ||
                                    'bigint' == $data['type']
                            )
                                {{ CreateInputInteger($data, $placeholder = null, $col = '6') }}
                            @elseif ($data['type'] == 'date' || $data['type'] == 'datetime')
                                {{ CreateInputDate($data, $placeholder = null, $col = '4') }}
                            @endif
                        @endforeach


                        <div class="mb-3 col-md-4">
                            <label id="label" for=""
                                class="required mt-3 form-label">Select
                                Intervention
                            </label>
                            <select required name="IID"
                                class="form-select   form-select-solid"
                                data-control="select2"
                                data-placeholder="Select a option">
                                <option value=""> </option>
                                @isset($Interventions)
                                    @foreach ($Interventions as $i)
                                        <option value="{{ $i->IID }}">
                                            {{ $i->InterventionName }}

                                        </option>
                                    @endforeach
                                @endisset

                            </select>

                        </div>

                    </div>

                    <div class="row">
                        @foreach ($Form as $data)
                            @if ($data['type'] == 'text')
                                {{ CreateInputEditor($data, $placeholder = null, $col = '12') }}
                            @endif
                        @endforeach

                    </div>


                    <input required type="hidden" name="PID"
                        value="{{ $PID }}">

                    <input required type="hidden" name="MID"
                        value="{{ $MID }}">


                    <input required type="hidden" name="EID"
                        value="{{ md5(\Hash::make(uniqid() . 'AFC' . date('Y-m-d H:I:S'))) }}">


                    <input required type="hidden" name="TableName"
                        value="project_expenditures">





            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-info"
                    data-bs-dismiss="modal">Close</button>

                <button type="submit" class="btn btn-dark">Save
                    Changes</button>

                </form>
            </div>

        </div>
    </div>
</div>
