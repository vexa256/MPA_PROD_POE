@isset($Activities)
    @foreach ($Activities as $data)
        <div class="modal fade" id="Status{{ $data->id }}">
            <div class="modal-dialog modal-dialog-scrollable modal-xl ">
                <div class="modal-content">
                    <div class="modal-header bg-dark text-light fw-bolder">
                        <h5 class="modal-title text-light fw-bolder">Update the
                            progress status of the activity

                            <span
                                class="text-danger fw-bolder">{{ $data->ActivityName }}</span>

                        </h5>

                        <!--begin::Close-->
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2"
                            data-bs-dismiss="modal" aria-label="Close">
                            <i class="fas fa-2x fa-times" aria-hidden="true"></i>
                        </div>
                        <!--end::Close-->
                    </div>

                    <div class="modal-body ">

                        <form action="{{ route('MassUpdate') }}" class="row"
                            method="POST">
                            @csrf

                            <div class="row">


                                <input type="hidden" name="id"
                                    value="{{ $data->id }}">

                                <div class="mb-3 col-md-6">
                                    <label id="label" for=""
                                        class="required mt-3 form-label">Update
                                        Activity Status
                                    </label>
                                    <select required name="ProgressStatus"
                                        class="form-select   form-select-solid"
                                        data-control="select2"
                                        data-placeholder="Select a option">
                                        <option value="{{ $data->ProgressStatus }}">
                                            {{ $data->ProgressStatus }}
                                        </option>

                                        <option value="Ongoing">Ongoing</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Completed">Completed</option>
                                        <option value="Terminated">Terminated
                                        </option>

                                    </select>

                                </div>

                                <div class="mb-3 col-md-6">
                                    <label id="label" for=""
                                        class="required mt-3 form-label">Comments
                                    </label>
                                    <input type="text" name="Comments"
                                        id="" class="form-control">

                                </div>

                            </div>




                            <input required type="hidden" name="TableName"
                                value="project_activities">





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
    @endforeach
@endisset
