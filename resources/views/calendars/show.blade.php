<div class="card-body">
    <div class="table-responsive p-2">
        <table class="table table-bordered mt-1">
            <tbody>

                <tr>
                    <td class="fw-bold">{{ __('Title') }}</td>
                    <td class="text-right">{{ $current_month_event->title }}
                    </td>
                </tr>
                <tr>
                    <td class="fw-bold">{{ __('Description') }}</td>
                    <td class="text-right">
                        {{ !empty($current_month_event->description) ? $current_month_event->description : '-' }}</td>
                </tr>
                <tr>
                    <td class="fw-bold">{{ __('Start Date') }}</td>
                    <td class="text-right">
                        {{ date('d F Y', strtotime($current_month_event->start)) }}</td>
                </tr>
                <tr>
                    <td class="fw-bold">{{ __('End Date') }}</td>
                    <td class="text-right">
                        {{ date('d F Y', strtotime($current_month_event->end)) }}</td>
                </tr>
            </tbody>
        </table>

    </div>
</div>
