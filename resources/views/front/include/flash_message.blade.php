@if ($message = Session::get('success'))
<script>
    var message = @json($message);
        $(function() {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 8000
            });

            Toast.fire({
                type: 'success',
                title: message
            });

            // $(document).Toasts('create', {
            //     class: 'bg-success',
            //     title: 'Success',
            //     autohide: true,
            //     delay: 2050,
            //     body: message
            // })
        });
</script>
@endif


@if ($message = Session::get('error'))
<script>
    var message = @json($message);
        $(function() {

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 80000
            });

            Toast.fire({
                type: 'error',
                title: message
            });

            // $(document).Toasts('create', {
            //     class: 'bg-danger',
            //     title: 'Error',
            //     autohide: true,
            //     delay: 2050,
            //     body: message
            // })
        });
</script>
@endif


@if ($message = Session::get('warning'))
<script>
    var message = @json($message);
        $(function() {

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 80000
            });

            Toast.fire({
                type: 'warning',
                title: message
            });
            // $(document).Toasts('create', {
            //     class: 'bg-warning',
            //     title: 'Warning',
            //     autohide: true,
            //     delay: 2050,
            //     body: message
            // })
        });
</script>
@endif


@if ($message = Session::get('info'))
<script>
    var message = @json($message);
        $(function() {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 80000
            });

            Toast.fire({
                type: 'info',
                title: message
            });

            // $(document).Toasts('create', {
            //     class: 'bg-info',
            //     title: 'Info',
            //     autohide: true,
            //     delay: 2050,
            //     body: message
            // })
        });
</script>
@endif

{{-- AJAX Message --}}
<script>
    function notification(res, message){
        if(res=='success'){
            $(function() {

                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 8000
                });

                Toast.fire({
                    type: 'success',
                    title: message
                });

                // $(document).Toasts('create', {
                //     class: 'bg-success',
                //     title: 'Success',
                //     autohide: true,
                //     delay: 2050,
                //     body: message
                // })
            });
        }

        if(res=='error'){
            $(function() {

                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 80000
                });

                Toast.fire({
                    type: 'error',
                    title: message
                });

                // $(document).Toasts('create', {
                //     class: 'bg-danger',
                //     title: 'Error',
                //     autohide: true,
                //     delay: 2050,
                //     body: message
                // })
            });
        }
    }

</script>