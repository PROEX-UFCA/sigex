@props(['action' => 'submit', 'name' => 'g-recaptcha-response'])

@php
    $siteKey = config('services.captcha.site_key');
    $fieldId = 'captcha-' . \Illuminate\Support\Str::random(8);
@endphp

@if (filled($siteKey))
    @once
        <script src="https://www.google.com/recaptcha/api.js?render={{ $siteKey }}"></script>
    @endonce

    <input type="hidden" name="{{ $name }}" id="{{ $fieldId }}">

    <script>
        (function () {
            var field = document.getElementById(@json($fieldId));
            var form = field.closest('form');
            if (!form) return;

            var released = false;

            if (typeof grecaptcha !== 'undefined') {
                try { grecaptcha.ready(function () {}); } catch (e) {}
            }

            form.addEventListener('submit', function (event) {
                if (released) return;
                event.preventDefault();

                var done = false;

                function release() {
                    if (done) return;
                    done = true;
                    released = true;
                    if (typeof form.requestSubmit === 'function') {
                        form.requestSubmit();
                    } else {
                        form.submit();
                    }
                }

                setTimeout(release, 10000);

                if (typeof grecaptcha === 'undefined') {
                    release();
                    return;
                }

                try {
                    grecaptcha.ready(function () {
                        try {
                            grecaptcha.execute(@json($siteKey), { action: @json($action) }).then(
                                function (token) { field.value = token; release(); },
                                function () { field.value = ''; release(); }
                            );
                        } catch (e) {
                            release();
                        }
                    });
                } catch (e) {
                    release();
                }
            });
        })();
    </script>
@endif