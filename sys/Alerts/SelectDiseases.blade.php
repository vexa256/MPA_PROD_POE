<div class="card-body shadow-lg pt-3 bg-light table-responsive">
    <div class="row">
        <div class="col-md-12">
            <form action="{{ route('getPriorityDiseaseAlerts') }}" method="GET">
                @csrf
                <div class="mb-3 col-md-12 py-5 my-5">
                    <label id="label" for="" class="px-5 my-5 required form-label">Select Priority
                        Disease</label>
                    <select required name="disease" class="form-select py-5 my-5 form-select-solid"
                        data-control="select2" data-placeholder="Select a disease">
                        <option></option>
                        <option value="VHF">VHF</option>
                        <option value="Cholera">Cholera</option>
                        <option value="Mpox">Mpox</option>
                        <option value="Yellow Fever">Yellow Fever</option>
                        <option value="COVID-19">COVID-19</option>
                        <option value="SARS">SARS</option>
                        <option value="MERS">MERS</option>
                        <option value="Polio">Polio</option>
                        <option value="Zika Virus">Zika Virus</option>
                        <option value="Influenza">Influenza</option>
                        <option value="Meningococcal Disease">Meningococcal Disease</option>
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
