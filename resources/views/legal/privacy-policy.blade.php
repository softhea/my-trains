@extends('layouts.app')

@section('title', __('Privacy Policy'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h1 class="h3 mb-0">{{ __('Privacy Policy') }}</h1>
                    <p class="text-muted mb-0">{{ __('Last updated:') }} {{ date('F d, Y') }}</p>
                </div>
                <div class="card-body">
                    <div class="legal-content">
                        <h2>{{ __('1. Information We Collect') }}</h2>
                        <p>{{ __('We collect information you provide directly to us, such as when you create an account, make a purchase, or contact us for support. This may include:') }}</p>
                        <ul>
                            <li>{{ __('Name') }}</li>
                            <li>{{ __('Email address and password') }}</li>
                            <li>{{ __('Product preferences and purchase history') }}</li>
                        </ul>

                        <h2>{{ __('2. How We Use Your Information') }}</h2>
                        <p>{{ __('We use the information we collect to:') }}</p>
                        <ul>
                            <li>{{ __('Process and fulfill your orders') }}</li>
                            <li>{{ __('Provide customer support') }}</li>
                            <li>{{ __('Send you important updates about your account and orders') }}</li>
                            <li>{{ __('Improve our website and services') }}</li>
                            <li>{{ __('Comply with legal obligations') }}</li>
                        </ul>

                        <h2>{{ __('3. Information Sharing') }}</h2>
                        <p>{{ __('We do not sell, trade, or otherwise transfer your personal information to third parties without your consent, except:') }}</p>
                        <ul>
                            <li>{{ __('To trusted service providers who assist us in operating our website') }}</li>
                            <li>{{ __('When required by law or to protect our rights') }}</li>
                        </ul>

                        <h2>{{ __('4. Data Security') }}</h2>
                        <p>{{ __('We implement appropriate security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction. However, no method of transmission over the internet is 100% secure.') }}</p>

                        <h2>{{ __('5. Cookies and Tracking') }}</h2>
                        <p>{{ __('We use cookies and similar technologies to enhance your browsing experience. You can control cookie settings through your browser preferences. For more information, please see our Cookie Policy.') }}</p>

                        <h2>{{ __('6. Your Rights') }}</h2>
                        <p>{{ __('You have the right to:') }}</p>
                        <ul>
                            <li>{{ __('Access your personal information') }}</li>
                            <li>{{ __('Correct inaccurate data') }}</li>
                            <li>{{ __('Request deletion of your data') }}</li>
                            <li>{{ __('Object to processing of your data') }}</li>
                            <li>{{ __('Data portability') }}</li>
                        </ul>

                        <h2>{{ __('7. Contact Us') }}</h2>
                        <p>{{ __('If you have any questions about this Privacy Policy, please contact us at:') }}</p>
                        <p>
                            <strong>{{ __('Email:') }}</strong> liviusandulache@gmail.com<br>
                            <strong>{{ __('Address:') }}</strong> Romania, Bucuresti, Sector 4                            
                        </p>

                        <h2>{{ __('8. Changes to This Policy') }}</h2>
                        <p>{{ __('We may update this Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page and updating the "Last updated" date.') }}</p>
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
