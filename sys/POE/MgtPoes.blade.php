<div class="row g-5 g-xl-8 mb-5 mb-xl-10">
    <!--begin::Header-->
    <div class="col-md-12">
        <div class="card mb-5">
            <div class="card-body d-flex align-items-center">
                <div class="d-flex flex-column">
                    <h1 class="text-dark fw-bold mb-2">Manage Points of Entry (POEs)</h1>
                    <p class="text-muted fs-6">Below is a list of all POEs. You can update details directly, but adding
                        or removing entries requires the Rwanda National POE Screening App.</p>
                </div>
                {{-- <div class="ms-auto">
                    <button class="btn btn-light-danger" id="btn-add-poe" data-bs-toggle="tooltip"
                        title="Use Rwanda National POE Screening App">
                        <i class="ki-duotone ki-plus-circle fs-2"></i> Add POE
                    </button>
                    <button class="btn btn-light-danger ms-2" id="btn-delete-poe" data-bs-toggle="tooltip"
                        title="Use Rwanda National POE Screening App">
                        <i class="ki-duotone ki-trash fs-2"></i> Delete POE
                    </button>
                </div> --}}
            </div>
        </div>
    </div>
    <!--end::Header-->

    <!--begin::POE Table-->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header border-0 pt-6">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-3">Points of Entry</span>
                    <span class="text-muted mt-1 fw-semibold fs-7">Update POE details as required</span>
                </h3>
            </div>
            <div class="card-body py-4">
                <table class="table table-row-dashed table-row-gray-300 fs-6 gy-5" id="poe_table">
                    <thead>
                        <tr class="text-muted fw-bold fs-7 text-uppercase">
                            <th>POE Name</th>
                            <th>Type</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Capacity</th>
                            {{-- <th class="text-end">Actions</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($poes as $poe)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $poe->name ?? 'N/A' }}</div>
                                </td>
                                <td>
                                    <span
                                        class="badge {{ $poe->type === 'airport' ? 'badge-light-primary' : ($poe->type === 'land_border' ? 'badge-light-success' : 'badge-light-info') }}">
                                        {{ ucfirst($poe->type) }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $location = json_decode($poe->location, true);
                                    @endphp
                                    <span class="text-muted fs-7">{{ $location['country'] ?? 'N/A' }},
                                        {{ $location['district'] ?? 'N/A' }},
                                        {{ $location['province'] ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <span
                                        class="badge {{ $poe->status === 'active' ? 'badge-light-success' : ($poe->status === 'inactive' ? 'badge-light-danger' : 'badge-light-warning') }}">
                                        {{ ucfirst($poe->status) }}
                                    </span>
                                </td>
                                <td>{{ $poe->capacity ?? 'N/A' }}</td>
                                {{-- <td class="text-end">
                                    <button class="btn btn-light-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#editPOEModal_{{ $poe->id }}">
                                        <i class="ki-duotone ki-pencil fs-5"></i> Update
                                    </button>
                                </td> --}}
                            </tr>

                            <!-- Update Modal -->
                            <div class="modal fade" id="editPOEModal_{{ $poe->id }}" tabindex="-1"
                                aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">Update POE: {{ $poe->name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('EditPOE', $poe->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="name" class="form-label">POE Name</label>
                                                    <input type="text" name="name" class="form-control"
                                                        value="{{ $poe->name }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="type" class="form-label">Type</label>
                                                    <select name="type" class="form-select">
                                                        <option value="airport"
                                                            {{ $poe->type === 'airport' ? 'selected' : '' }}>Airport
                                                        </option>
                                                        <option value="land_border"
                                                            {{ $poe->type === 'land_border' ? 'selected' : '' }}>Land
                                                            Border</option>
                                                        <option value="seaport"
                                                            {{ $poe->type === 'seaport' ? 'selected' : '' }}>Seaport
                                                        </option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="location" class="form-label">Location (JSON)</label>
                                                    <textarea name="location" class="form-control" rows="2">{{ json_encode($location, JSON_PRETTY_PRINT) }}</textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="status" class="form-label">Status</label>
                                                    <select name="status" class="form-select">
                                                        <option value="active"
                                                            {{ $poe->status === 'active' ? 'selected' : '' }}>Active
                                                        </option>
                                                        <option value="inactive"
                                                            {{ $poe->status === 'inactive' ? 'selected' : '' }}>
                                                            Inactive</option>
                                                        <option value="maintenance"
                                                            {{ $poe->status === 'maintenance' ? 'selected' : '' }}>
                                                            Maintenance</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="capacity" class="form-label">Capacity</label>
                                                    <input type="number" name="capacity" class="form-control"
                                                        value="{{ $poe->capacity }}">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light"
                                                    data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- End Update Modal -->
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!--end::POE Table-->
</div>

<!-- Modal Notification Script -->
<script>
    document.getElementById('btn-add-poe').addEventListener('click', function() {
        alert('To add a new POE, please use the Rwanda National POE Screening App as an admin.');
    });
    document.getElementById('btn-delete-poe').addEventListener('click', function() {
        alert('To delete a POE, please use the Rwanda National POE Screening App as an admin.');
    });
</script>
