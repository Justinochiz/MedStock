<footer class="ms-footer mt-5">
    <div class="ms-footer__background"></div>
    <div class="container position-relative">
        <div class="row g-4 ms-footer__top">
            <div class="col-lg-5 col-md-12">
                <div class="ms-footer__brand-wrap">
                    <h4 class="ms-footer__brand">MedStock</h4>
                    <p class="ms-footer__brand-copy mb-0">
                        Track what you buy, monitor what you use, and keep your medical inventory ready when it matters.
                    </p>
                </div>
            </div>

            <div class="col-lg-3 col-md-4 col-6">
                <h6 class="ms-footer__title">About Us</h6>
                <ul class="ms-footer__links list-unstyled mb-0">
                    <li><a href="{{ route('footer.about') }}">About MedStock</a></li>
                    <li><a href="{{ route('footer.privacy') }}">Policy and Privacy</a></li>
                    <li><a href="{{ route('footer.terms') }}">Terms of Service</a></li>
                </ul>
            </div>

            <div class="col-lg-2 col-md-4 col-6">
                <h6 class="ms-footer__title">Features</h6>
                <ul class="ms-footer__links list-unstyled mb-0">
                    <li><a href="{{ route('footer.features') }}">Inventory tracking</a></li>
                    <li><a href="{{ route('footer.features') }}">Order management</a></li>
                    <li><a href="{{ route('footer.features') }}">Service and product records</a></li>
                    <li><a href="{{ route('footer.features') }}">Admin dashboard analytics</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-4 col-12">
                <h6 class="ms-footer__title">Contact</h6>
                <ul class="ms-footer__links list-unstyled mb-0">
                    <li><a href="{{ route('footer.contact') }}">For more info, contact us</a></li>
                    <li><a href="{{ route('footer.contact') }}">Justine Tomon</a></li>
                    <li><a href="{{ route('footer.contact') }}">Shyr Nicole Belenzo</a></li>
                    <li><a href="mailto:tomonjustine74@gmail.com">tomonjustine74@gmail.com</a></li>
                    <li><a href="{{ route('footer.support') }}">Support</a></li>
                </ul>
            </div>
        </div>

        <hr class="ms-footer__divider">

        <div class="row align-items-center g-3 ms-footer__bottom">
            <div class="col-md-6">
                <p class="ms-footer__copyright mb-0">© {{ date('Y') }} MedStock. All Rights Reserved.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="{{ route('footer.privacy') }}" class="ms-footer__bottom-link">Privacy Policy</a>
                <a href="{{ route('footer.terms') }}" class="ms-footer__bottom-link ms-footer__bottom-link--last">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>

<style>
    .ms-footer {
        position: relative;
        overflow: hidden;
        color: #d9e2f0;
        background: linear-gradient(140deg, #18222f 0%, #101923 55%, #0e1620 100%);
        border-top: 1px solid rgba(120, 166, 214, 0.2);
    }

    .ms-footer__background {
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 12% 18%, rgba(26, 115, 232, 0.2), transparent 34%),
            radial-gradient(circle at 92% 4%, rgba(76, 175, 80, 0.14), transparent 30%);
        pointer-events: none;
    }

    .ms-footer__top {
        padding: 3.25rem 0 1.5rem;
    }

    .ms-footer__brand-wrap {
        padding: 1.2rem 1.25rem;
        border: 1px solid rgba(157, 190, 226, 0.2);
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(1px);
    }

    .ms-footer__brand {
        margin-bottom: 0.65rem;
        font-size: 1.5rem;
        font-weight: 700;
        color: #f5f8fd;
        letter-spacing: 0.3px;
    }

    .ms-footer__brand-copy {
        max-width: 36ch;
        line-height: 1.7;
        color: #b8c9de;
    }

    .ms-footer__title {
        margin-bottom: 0.95rem;
        color: #f1f6fd;
        font-size: 0.86rem;
        text-transform: uppercase;
        letter-spacing: 0.09em;
        font-weight: 700;
    }

    .ms-footer__links li {
        margin-bottom: 0.7rem;
    }

    .ms-footer__links a,
    .ms-footer__bottom-link {
        text-decoration: none;
        color: #9fc0e0;
        transition: color 0.2s ease, opacity 0.2s ease;
    }

    .ms-footer__links a:hover,
    .ms-footer__bottom-link:hover {
        color: #ffffff;
        opacity: 1;
    }

    .ms-footer__divider {
        margin: 0;
        border-color: rgba(140, 177, 214, 0.22);
        opacity: 1;
    }

    .ms-footer__bottom {
        padding: 1rem 0 1.6rem;
    }

    .ms-footer__copyright {
        color: #aac2dc;
        font-size: 0.93rem;
    }

    .ms-footer__bottom-link {
        display: inline-block;
        margin-right: 1.1rem;
        font-size: 0.92rem;
    }

    .ms-footer__bottom-link--last {
        margin-right: 0;
    }

    @media (max-width: 991.98px) {
        .ms-footer__top {
            padding-top: 2.6rem;
        }

        .ms-footer__brand-wrap {
            margin-bottom: 0.4rem;
        }
    }

    @media (max-width: 767.98px) {
        .ms-footer__bottom {
            text-align: center;
        }

        .ms-footer__bottom-link {
            margin: 0 0.55rem;
        }
    }
</style>

