<div class="card-body shadow-lg pt-3 bg-light table-responsive">
    <div class="row">
        <div class="col-md-12">
            <form action="{{ route('AcceptModExYear') }}" method="POST">
                @csrf
                <div class="mb-3 col-md-12  py-5   my-5">
                    <label id="label" for="" class="px-5 my-5 required form-label">Select
                        Finacial Year </label>

                    <select required name="FinancialYear" class="form-select   form-select-solid" data-control="select2" data-placeholder="Select a option">


                        <option value="">
                        </option>

                        @for ($i = date('Y') - 5; $i <= date('Y') + 10; $i++) <option value="{{ $i }}">
                            {{ $i }}</option>
                            @endfor


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
