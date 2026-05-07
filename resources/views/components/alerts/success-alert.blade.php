<div class="position-fixed top-0 end-0 p-3" style="z-index: 1005">
    <div id="liveToast" class="toast fade show" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <img src="{{ asset('assets/images/logo.png') }}" alt="" class="me-2" height="18">
            <strong class="me-auto">vaakgolvslip.se</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            <p class="text-success"><b>Success : </b>{{ $message }} </p>
        </div>
    </div>
</div>