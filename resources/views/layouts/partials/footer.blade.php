<footer class="pc-footer">
      <div class="footer-wrapper container-fluid">
        <div class="row">
          <div class="col-sm-6 my-1">
            <p class="m-0"> &copy; Copyright 2025 | <a href="https://localhost/" target="_blank">{{ config('app.name') }}</a></p>
          </div>
          <div class="col-sm-6 ms-auto my-1">
            <ul class="list-inline footer-link mb-0 justify-content-sm-end d-flex">
              <li class="list-inline-item"><a href="/">Home</a></li>
            </ul>
          </div>
        </div>
      </div>
    </footer>

<!-- Toast Notification -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    let toastEl = document.getElementById('liveToast');
    if (toastEl) {
        let toast = new bootstrap.Toast(toastEl, { delay: 4000 });
        toast.show();
    }
});
</script>

    <!-- [Page Specific JS] start -->
<script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/jsvectormap.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/world.js') }}"></script>
<script src="{{ asset('assets/js/plugins/world-merc.js') }}"></script>
<script src="{{ asset('assets/js/pages/dashboard-sales.js') }}"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
<!-- [Page Specific JS] end -->

<!-- Required Js -->
<script src="{{ asset('assets/js/plugins/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/fonts/custom-font.js') }}"></script>
<script src="{{ asset('assets/js/script.js') }}"></script>
<script src="{{ asset('assets/js/theme.js') }}"></script>
<script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>