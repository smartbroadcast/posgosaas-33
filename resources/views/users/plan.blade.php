{{-- <div class="col-md-12">
    <div class="card">


        @foreach ($plans as $plan)
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                     
                                        <div class="ms-3">
                                            <h6 class="m-0">{{ $plan->name }}</h6>
                                            <small class="text-muted">{{ env('CURRENCY_SYMBOL') . $plan->price }}
                                                {{ ' / ' . $plan->duration }}</small>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <small class="text-muted">{{ $plan->max_users }}</small>
                                    <h6 class="m-0">{{ __('Users') }}</h6>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $plan->max_customers }}</small>
                                    <h6 class="m-0">{{ __('Customers') }}</h6>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $plan->max_vendors }}</small>
                                    <h6 class="m-0">{{ __('Vendors') }}</h6>
                                </td>
                                <td>
                                    @if ($user->plan_id == $plan->id)
                                        <div class="active-label font14">{{ __('Active') }}</div>
                                    @else
                                        <a href="{{ route('plan.active', [$user->id, $plan->id]) }}"
                                            class="btn btn-sm btn-outline-primary"
                                            title="{{ __('Click to Upgrade Plan') }}"><i
                                                class="fas fa-cart-plus"></i></a>
                                    @endif
                                </td>

                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach

    </div>
</div> --}}



<div class="modal-body">
    <div class="card mb-2">
        <div class="card-body table-border-style">
            <div class="table-responsive">
                <table class="table datatable">
                    <tbody>
                        @foreach ($plans as $plan)
                        <tr>
                            <td>
                                <h6>{{ $plan->name }} {{ (!empty(env('CURRENCY_SYMBOL')) ? env('CURRENCY_SYMBOL') : '$') . $plan->price }}
                                    {{ ' / ' . $plan->duration }}</h6>
                            </td>
                            <td>{{ __('Users') }} : {{ $plan->max_users }}</td>
                            <td>{{ __('Customers') }} : {{ $plan->max_customers }}</td>
                            <td>{{ __('Vendors') }} : {{ $plan->max_vendors }}</td>


                            <td class="Action">
                                <span>
                                    @if ($user->plan_id == $plan->id)

                                        <div class="badge bg-success p-2 px-3 rounded"><i class="ti ti-checks"></i></div>

                                        @else

                                        <a href="{{ route('plan.active', [$user->id, $plan->id]) }}"
                                            class="badge bg-info p-2 px-3 rounded"
                                            title="{{ __('Click to Upgrade Plan') }}"><i class="ti ti-shopping-cart-plus"></i></a>


                                        @endif
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
