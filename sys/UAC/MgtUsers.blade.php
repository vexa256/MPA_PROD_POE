<div class="row">
    <div class="col-md-12">
        <!--begin::Admin Management Card-->
        <div class="card">
            <div class="card-header align-items-center justify-content-between">
                <div class="d-flex flex-column">
                    <h3 class="card-title fw-bolder text-gray-800 mb-1">
                        Admin Management
                    </h3>
                    <span class="text-danger fs-7">
                        Logins within the mobile app utilize <strong>Username and Password</strong>, while this
                        dashboard requires <strong>Email and Password</strong> for authentication.
                    </span>
                </div>
                <div class="card-toolbar">
                    <a href="#" class="btn btn-light-primary d-flex align-items-center" data-bs-toggle="tooltip"
                        title="Admins can only be added through the Rwanda National POE Screening App. Please sign in as an admin there to add new entries.">
                        <i class="ki-duotone ki-plus fs-2 me-2"></i>
                        <span class="d-none d-md-inline">Add Admin</span>
                    </a>
                </div>
            </div>

            <div class="card-body">
                <!--begin::Table-->
                <div class="table-responsive">
                    <table class="table align-middle gs-0 gy-4">
                        <thead>
                            <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                                <th>Username</th>
                                <th>Email</th>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Last Login</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($admins as $admin)
                                <tr>
                                    <td>
                                        <span class="text-gray-700 fw-bold">{{ $admin->username }}</span>
                                    </td>
                                    <td>
                                        <a href="mailto:{{ $admin->email }}" class="text-gray-600 text-hover-primary">
                                            {{ $admin->email }}
                                        </a>
                                    </td>
                                    <td>{{ $admin->name }}</td>
                                    <td>
                                        <span class="badge badge-light-primary fs-7">{{ $admin->role }}</span>
                                    </td>
                                    <td>{{ $admin->lastLogin ? \Carbon\Carbon::parse($admin->lastLogin)->format('Y-m-d H:i') : 'Never' }}
                                    </td>

                                    <td>
                                        <!--begin::Actions-->
                                        <div class="d-flex">
                                            <a href="#" class="btn btn-light btn-sm me-2" data-bs-toggle="tooltip"
                                                title="Editing only available through Rwanda National POE Screening App">
                                                <i class="ki-duotone ki-pencil fs-6"></i> Edit
                                            </a>

                                            <!-- Delete button -->
                                            <form action="{{ route('DeleteAdmin', ['id' => $admin->id]) }}"
                                                method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this admin? This action is not reversible');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-light-danger btn-sm"
                                                    data-bs-toggle="tooltip" title="Delete Admin">
                                                    <i class="ki-duotone ki-trash fs-6"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                        <!--end::Actions-->
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!--end::Table-->
            </div>
        </div>
        <!--end::Admin Management Card-->
    </div>
</div>
