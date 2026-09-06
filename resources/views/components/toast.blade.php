<script>
    // Initialize Global Toast Configuration
    window.toastConfig = window.toastConfig || {};
    window.toastConfig.soundEnabled = {{ \App\Models\Setting::get('toast_sound_enabled', true) ? 'true' : 'false' }};
</script>

@if (session('success') || session('status') || session('error') || session('warning') || session('info') || (isset($errors) && $errors->any()))
    <script>
        (function() {
            function triggerServerToasts() {
                if (!window.toast) {
                    // Retry once if script tag is still executing
                    setTimeout(triggerServerToasts, 50);
                    return;
                }

                @if (session('success'))
                    window.toast.success(@json(session('success')));
                @endif

                @if (session('status'))
                    window.toast.success(@json(session('status')));
                @endif

                @if (session('error'))
                    window.toast.error(@json(session('error')));
                @endif

                @if (session('warning'))
                    window.toast.warning(@json(session('warning')));
                @endif

                @if (session('info'))
                    window.toast.info(@json(session('info')));
                @endif

                @if (isset($errors) && $errors->any())
                    @foreach ($errors->all() as $error)
                        window.toast.error(@json($error), "{{ app()->getLocale() === 'ar' ? 'خطأ في إدخال البيانات' : 'Validation Error' }}");
                    @endforeach
                @endif
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', triggerServerToasts);
            } else {
                triggerServerToasts();
            }
        })();
    </script>
@endif
