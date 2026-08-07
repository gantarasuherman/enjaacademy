{{--
    Serves the compiled React app.

    `make deploy-spa` builds frontend/ (Vite `base: '/app/'`) and copies the
    output into public/app, so the emitted asset URLs already point at
    /app/assets/… — the HTML is delivered verbatim, hashed filenames and all.
--}}
{!! file_get_contents(public_path('app/index.html')) !!}
