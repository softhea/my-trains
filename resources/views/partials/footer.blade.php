<footer class="bg-dark text-white py-4 mt-5">
  <div class="container text-center">
    <p class="mb-0">&copy; {{ date('Y') }} {{ __('My Model Trains') }}. {{ __('All rights reserved.') }}</p>
    <p class="mb-0">
      <!-- <a href="{{ route('privacy-policy') }}" class="footer-link">{{ __('Privacy Policy') }}</a> |  -->
      <a href="{{ route('cookie-policy') }}" class="footer-link">{{ __('Cookie Policy') }}</a> | 
      <!-- <a href="{{ route('terms-and-conditions') }}" class="footer-link">{{ __('Terms & Conditions') }}</a> |  -->
      <!-- <a href="{{ route('legal-notice') }}" class="footer-link">{{ __('Legal Notice') }}</a> -->
    </p>    
  </div>
</footer>

<style>
.footer-link {
  color: white !important;
  text-decoration: none !important;
  transition: color 0.3s ease;
}

.footer-link:hover {
  color: #FFA15C !important;
  text-decoration: none !important;
}
</style>