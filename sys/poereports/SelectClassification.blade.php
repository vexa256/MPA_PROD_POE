<div class="card-body shadow-lg pt-3 bg-light table-responsive">
    <div class="row">
        <div class="col-md-12">
            <form action="{{ route('GetClassificationData') }}" method="GET">
                @csrf
                <div class="mb-3 col-md-12 py-5 my-5">
                    <label for="classification" class="px-5 my-5 required form-label">Select Priority
                        Classification</label>
                    <select required name="classification" class="form-select py-5 my-5 form-select-solid"
                        data-control="select2" data-placeholder="Select a classification">
                        <option></option>
                        @foreach ($classifications as $classification)
                            <option value="{{ $classification }}">{{ $classification }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="float-end my-3">
                    <button class="btn btn-danger btn-sm shadow-lg" type="submit">
                        Next
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
