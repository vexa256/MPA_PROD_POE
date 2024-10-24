 <div class="card-body pt-3 bg-light shadow-lg table-responsive">
    {!! Alert(
        'fa-info',
        'alert-primary',
        "Let's manage project activities and their progress tracking tags",
        'Activity Progress Tracking',
    ) !!}
</div>

<div class="card-body pt-3 bg-light shadow-lg table-responsive">
    <table class="mytable table table-rounded table-bordered border gy-3 gs-3">
        <thead>
            <tr class="fw-bold text-gray-800 border-bottom border-gray-200">
                <th>Activity</th>
                <th>Module</th>
                <th>Budget Line</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Status</th>
                <th>Budget</th>
                <th>Objectives</th>
                <th class="bg-dark text-light">Change Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($Activities ?? [] as $data)
                <tr>
                    <td>{{ $data->ActivityName }}</td>
                    <td>{{ $data->ProjectModuleName }}</td>
                    <td>{{ $data->BudgetLine }}</td>
                    <td>{{ date('F j, Y', strtotime($data->StartDate)) }}</td>
                    <td>{{ date('F j, Y', strtotime($data->EndDate)) }}</td>
                    <td class="bg-danger fw-bolder text-light">
                        {{ $data->ProgressStatus }}</td>
                    <td>{{ number_format($data->ActivityBudgetInUsd) }} USD</td>
                    <td>
                        <a data-bs-toggle="modal" class="btn btn-danger btn-sm"
                            href="#ViewDesc{{ $data->id }}">
                            <i class="fas fa-binoculars"></i>
                        </a>
                    </td>
                    <td>
                        <a data-bs-toggle="modal" class="btn btn-primary btn-sm"
                            href="#Status{{ $data->id }}">
                            <i class="fas fa-spinner"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{ DescModal($Activities, 'View the out puts attached to selected activity', 'ViewDesc', 'OutPuts') }}
@include('activities.ActivityStatus')
