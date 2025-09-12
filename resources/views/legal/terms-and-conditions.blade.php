@extends('layouts.app')

@section('title', __('Terms & Conditions'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h1 class="h3 mb-0">{{ __('Terms & Conditions') }}</h1>
                    <p class="text-muted mb-0">{{ __('Last updated:') }} {{ date('F d, Y') }}</p>
                </div>
                <div class="card-body">
                    <div class="legal-content">
                        <h2>{{ __('1. Acceptance of Terms') }}</h2>
                        <p>{{ __('By accessing and using My Model Trains website, you accept and agree to be bound by the terms and provision of this agreement. If you do not agree to abide by the above, please do not use this service.') }}</p>

                        <h2>{{ __('2. Use License') }}</h2>
                        <p>{{ __('Permission is granted to temporarily download one copy of the materials on My Model Trains website for personal, non-commercial transitory viewing only. This is the grant of a license, not a transfer of title, and under this license you may not:') }}</p>
                        <ul>
                            <li>{{ __('Modify or copy the materials') }}</li>
                            <li>{{ __('Use the materials for any commercial purpose or for any public display') }}</li>
                            <li>{{ __('Attempt to reverse engineer any software contained on the website') }}</li>
                            <li>{{ __('Remove any copyright or other proprietary notations from the materials') }}</li>
                        </ul>

                        <h2>{{ __('3. User Accounts') }}</h2>
                        <p>{{ __('When you create an account with us, you must provide information that is accurate, complete, and current at all times. You are responsible for:') }}</p>
                        <ul>
                            <li>{{ __('Safeguarding the password and all activities under your account') }}</li>
                            <li>{{ __('Notifying us immediately of any unauthorized use of your account') }}</li>
                            <li>{{ __('Ensuring that your account information remains accurate and up-to-date') }}</li>
                        </ul>

                        <h2>{{ __('4. Product Information') }}</h2>
                        <p>{{ __('We strive to provide accurate product descriptions, images, and pricing. However, we do not warrant that product descriptions or other content is accurate, complete, reliable, current, or error-free.') }}</p>
                        <ul>
                            <li>{{ __('Product images are for illustrative purposes only') }}</li>
                            <li>{{ __('Actual colors may vary from those shown') }}</li>
                            <li>{{ __('We reserve the right to correct any errors or omissions') }}</li>
                        </ul>

                        <h2>{{ __('5. Orders and Payment') }}</h2>
                        <p>{{ __('All orders are subject to acceptance and availability. We reserve the right to refuse or cancel your order at any time for certain reasons including but not limited to:') }}</p>
                        <ul>
                            <li>{{ __('Product or service availability') }}</li>
                            <li>{{ __('Errors in the description or price of the product or service') }}</li>
                            <li>{{ __('Errors in your order') }}</li>
                            <li>{{ __('Fraudulent or unauthorized transactions') }}</li>
                        </ul>

                        <h2>{{ __('6. Pricing and Availability') }}</h2>
                        <p>{{ __('Prices are subject to change without notice. We reserve the right to modify or discontinue products or services at any time. We shall not be liable to you or any third party for any modification, price change, suspension, or discontinuance of the service.') }}</p>

                        <h2>{{ __('7. Shipping and Delivery') }}</h2>
                        <p>{{ __('Shipping costs and delivery times are estimates and may vary. We are not responsible for delays caused by shipping carriers or customs. Risk of loss and title for products purchased pass to you upon delivery to the carrier.') }}</p>

                        <h2>{{ __('8. Returns and Refunds') }}</h2>
                        <p>{{ __('Returns must be initiated within 30 days of delivery. Items must be in original condition with all packaging and documentation. We reserve the right to refuse returns that do not meet our return policy.') }}</p>
                        <ul>
                            <li>{{ __('Return shipping costs are the responsibility of the customer') }}</li>
                            <li>{{ __('Refunds will be processed within 5-10 business days') }}</li>
                            <li>{{ __('Custom or personalized items may not be returnable') }}</li>
                        </ul>

                        <h2>{{ __('9. Intellectual Property') }}</h2>
                        <p>{{ __('The service and its original content, features, and functionality are and will remain the exclusive property of My Model Trains and its licensors. The service is protected by copyright, trademark, and other laws.') }}</p>

                        <h2>{{ __('10. Prohibited Uses') }}</h2>
                        <p>{{ __('You may not use our service:') }}</p>
                        <ul>
                            <li>{{ __('For any unlawful purpose or to solicit others to perform unlawful acts') }}</li>
                            <li>{{ __('To violate any international, federal, provincial, or state regulations, rules, laws, or local ordinances') }}</li>
                            <li>{{ __('To infringe upon or violate our intellectual property rights or the intellectual property rights of others') }}</li>
                            <li>{{ __('To harass, abuse, insult, harm, defame, slander, disparage, intimidate, or discriminate') }}</li>
                            <li>{{ __('To submit false or misleading information') }}</li>
                        </ul>

                        <h2>{{ __('11. Disclaimer') }}</h2>
                        <p>{{ __('The information on this website is provided on an "as is" basis. To the fullest extent permitted by law, this Company:') }}</p>
                        <ul>
                            <li>{{ __('Excludes all representations and warranties relating to this website and its contents') }}</li>
                            <li>{{ __('Excludes all liability for damages arising out of or in connection with your use of this website') }}</li>
                        </ul>

                        <h2>{{ __('12. Limitation of Liability') }}</h2>
                        <p>{{ __('In no event shall My Model Trains, nor its directors, employees, partners, agents, suppliers, or affiliates, be liable for any indirect, incidental, special, consequential, or punitive damages, including without limitation, loss of profits, data, use, goodwill, or other intangible losses, resulting from your use of the service.') }}</p>

                        <h2>{{ __('13. Governing Law') }}</h2>
                        <p>{{ __('These Terms shall be interpreted and governed by the laws of the jurisdiction in which My Model Trains operates, without regard to its conflict of law provisions.') }}</p>

                        <h2>{{ __('14. Changes to Terms') }}</h2>
                        <p>{{ __('We reserve the right, at our sole discretion, to modify or replace these Terms at any time. If a revision is material, we will try to provide at least 30 days notice prior to any new terms taking effect.') }}</p>

                        <h2>{{ __('15. Contact Information') }}</h2>
                        <p>{{ __('If you have any questions about these Terms & Conditions, please contact us at:') }}</p>
                        <p>
                            <strong>{{ __('Email:') }}</strong> legal@mytrains.com<br>
                            <strong>{{ __('Address:') }}</strong> 123 Train Street, Model City, MC 12345<br>
                            <strong>{{ __('Phone:') }}</strong> (555) 123-4567
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
