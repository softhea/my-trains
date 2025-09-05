@extends('layouts.app')

@section('title', __('Cookie Policy'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h1 class="h3 mb-0">{{ __('Cookie Policy') }}</h1>
                    <p class="text-muted mb-0">{{ __('Last updated:') }} {{ date('F d, Y') }}</p>
                </div>
                <div class="card-body">
                    <div class="legal-content">
                        <h2>{{ __('What Are Cookies') }}</h2>
                        <p>{{ __('Cookies are small text files that are placed on your computer or mobile device when you visit our website. They help us provide you with a better experience by remembering your preferences and enabling certain functionality.') }}</p>

                        <h2>{{ __('Types of Cookies We Use') }}</h2>
                        <h3>{{ __('Essential Cookies') }}</h3>

                    

                        
                        <p>{{ __('Our website uses only essential cookies, strictly necessary for:') }}</p>
                        
                        <ul>
                            <li>{{ __('Session cookies for user authentication (keeping you logged in)') }}</li>
                            <li>{{ __('Storing language preference') }}</li>
                        </ul>
                        <p>{{ __('These cookies are essential for the proper functioning of the website and do not require user consent under Law 506/2004 and Regulation (EU) 2016/679 (GDPR).') }}</p>

                        <!-- <h3>{{ __('Functional Cookies') }}</h3>
                        <p>{{ __('These cookies enable enhanced functionality and personalization, such as remembering your preferences and settings.') }}</p>
                        <ul>
                            <li>{{ __('User interface customization') }}</li>
                            <li>{{ __('Shopping cart contents') }}</li>
                            <li>{{ __('Form data retention') }}</li>
                        </ul> -->

                        <h3>{{ __('We do not use analytics, marketing, or third-party cookies.') }}</h3>    
                        

                        <h2>{{ __('Managing Cookies') }}</h2>
                        <p>{{ __('You can control and manage cookies in several ways:') }}</p>
                        
                        <h3>{{ __('Browser Settings') }}</h3>
                        <p>{{ __('Most web browsers allow you to control cookies through their settings. You can:') }}</p>
                        <ul>
                            <li>{{ __('Block all cookies') }}</li>
                            <li>{{ __('Block third-party cookies only') }}</li>
                            <li>{{ __('Delete existing cookies') }}</li>
                            <li>{{ __('Set up notifications when cookies are set') }}</li>
                        </ul>

                        <h2>{{ __('Impact of Disabling Cookies') }}</h2>
                        <p>{{ __('If you choose to disable cookies, some features of our website may not function properly:') }}</p>
                        <ul>
                            <li>{{ __('You may need to log in repeatedly') }}</li>
                            <!-- <li>{{ __('Your shopping cart may not be saved') }}</li> -->
                            <li>{{ __('Personalized content may not be available') }}</li>
                            <li>{{ __('Some pages may not load correctly') }}</li>
                        </ul>

                        <h2>{{ __('Cookie Retention') }}</h2>
                        <p>{{ __('Different cookies have different retention periods:') }}</p>
                        <ul>
                            <li>{{ __('Session cookies: Deleted when you close your browser') }}</li>
                            <li>{{ __('Persistent cookies: Remain for a set period (typically 30 days to 2 years)') }}</li>
                            <li>{{ __('Essential cookies: Retained as long as necessary for functionality') }}</li>
                        </ul>

                        <h2>{{ __('Updates to This Policy') }}</h2>
                        <p>{{ __('We may update this Cookie Policy from time to time to reflect changes in our practices or for other operational, legal, or regulatory reasons. We will notify you of any material changes by posting the updated policy on our website.') }}</p>

                        <h2>{{ __('Contact Us') }}</h2>
                        <p>{{ __('If you have any questions about our use of cookies, please contact us at:') }}</p>
                        <p>
                            <strong>{{ __('Email:') }}</strong> liviusandulache@gmail.com<br>
                            <strong>{{ __('Address:') }}</strong> Romania, Bucuresti, Sector 4
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.legal-content h2 {
    color: #FFA15C;
    margin-top: 2rem;
    margin-bottom: 1rem;
    font-size: 1.5rem;
    font-weight: 600;
}

.legal-content h2:first-child {
    margin-top: 0;
}

.legal-content h3 {
    color: #616161;
    margin-top: 1.5rem;
    margin-bottom: 0.75rem;
    font-size: 1.25rem;
    font-weight: 600;
}

.legal-content ul {
    padding-left: 1.5rem;
}

.legal-content li {
    margin-bottom: 0.5rem;
}

.legal-content p {
    line-height: 1.6;
    margin-bottom: 1rem;
}
</style>
@endsection
