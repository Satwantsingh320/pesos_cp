<div class="position-fixed top-0 end-0 p-3" style="z-index: 1005">
    <div id="liveToast" class="toast fade show" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <img src="{{ asset('assets/images/logo-icon.png') }}" alt="" class="me-2" height="18">
            <strong class="me-auto">{{ config('app.name') }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            @if(is_array($message))
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li class="text-danger">{{ $error }}</li>
                    @endforeach
                </ul>
            @else
                <p class="text-danger"><b>Error : </b>{{ $message }} </p>
            @endif
        </div>
    </div>
</div>
