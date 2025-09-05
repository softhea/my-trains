@extends('layouts.app')

@section('title', __('Legal Notice'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h1 class="h3 mb-0">{{ __('Legal Notice') }}</h1>
                    <p class="text-muted mb-0">{{ __('Last updated:') }} {{ date('F d, Y') }}</p>
                </div>
                <div class="card-body">
                    <div class="legal-content">
                        <h2>{{ __('Company Information') }}</h2>
                        <p><strong>{{ __('Company Name:') }}</strong> My Model Trains LLC</p>
                        <p><strong>{{ __('Legal Form:') }}</strong> Limited Liability Company</p>
                        <p><strong>{{ __('Registration Number:') }}</strong> LLC-2024-001234</p>
                        <p><strong>{{ __('Tax ID:') }}</strong> 12-3456789</p>
                        <p><strong>{{ __('Registered Address:') }}</strong> 123 Train Street, Model City, MC 12345, United States</p>
                        <p><strong>{{ __('Business Address:') }}</strong> 123 Train Street, Model City, MC 12345, United States</p>

                        <h2>{{ __('Contact Information') }}</h2>
                        <p><strong>{{ __('General Inquiries:') }}</strong> info@mytrains.com</p>
                        <p><strong>{{ __('Customer Support:') }}</strong> support@mytrains.com</p>
                        <p><strong>{{ __('Legal Matters:') }}</strong> legal@mytrains.com</p>
                        <p><strong>{{ __('Phone:') }}</strong> (555) 123-4567</p>
                        <p><strong>{{ __('Fax:') }}</strong> (555) 123-4568</p>

                        <h2>{{ __('Website Information') }}</h2>
                        <p><strong>{{ __('Website:') }}</strong> https://mytrains.com</p>
                        <p><strong>{{ __('Domain Registration:') }}</strong> Namecheap Inc.</p>
                        <p><strong>{{ __('Hosting Provider:') }}</strong> DigitalOcean LLC</p>
                        <p><strong>{{ __('SSL Certificate:') }}</strong> Let's Encrypt</p>

                        <h2>{{ __('Responsible for Content') }}</h2>
                        <p><strong>{{ __('Managing Director:') }}</strong> John Smith</p>
                        <p><strong>{{ __('Email:') }}</strong> john.smith@mytrains.com</p>
                        <p><strong>{{ __('Address:') }}</strong> 123 Train Street, Model City, MC 12345, United States</p>

                        <h2>{{ __('Editorial Responsibility') }}</h2>
                        <p>{{ __('The content of our website has been created with the utmost care. However, we cannot guarantee the accuracy, completeness, or timeliness of the information provided.') }}</p>
                        <p>{{ __('As a service provider, we are responsible for our own content on these pages in accordance with general laws. However, we are not obligated to monitor third-party information provided or stored on our website.') }}</p>

                        <h2>{{ __('Liability for Links') }}</h2>
                        <p>{{ __('Our website contains links to external websites of third parties, over whose contents we have no influence. Therefore, we cannot assume any liability for these external contents. The respective provider or operator of the pages is always responsible for the contents of the linked pages.') }}</p>

                        <h2>{{ __('Copyright and Trademark Rights') }}</h2>
                        <p>{{ __('The content and works created by the site operators on these pages are subject to copyright law. The reproduction, processing, distribution, and any kind of exploitation outside the limits of copyright require the written consent of the respective author or creator.') }}</p>
                        <p>{{ __('Downloads and copies of this site are only permitted for private, non-commercial use. Insofar as the content on this site was not created by the operator, the copyrights of third parties are respected. In particular, third-party content is identified as such.') }}</p>

                        <h2>{{ __('Data Protection') }}</h2>
                        <p>{{ __('The use of our website is usually possible without providing personal information. As far as personal data (such as name, address, or email addresses) is collected on our pages, this is always done, as far as possible, on a voluntary basis.') }}</p>
                        <p>{{ __('We point out that data transmission over the Internet (e.g., when communicating by email) may have security vulnerabilities. A complete protection of data against access by third parties is not possible.') }}</p>
                        <p>{{ __('For detailed information on data protection, please refer to our Privacy Policy.') }}</p>

                        <h2>{{ __('Dispute Resolution') }}</h2>
                        <p>{{ __('The European Commission provides a platform for online dispute resolution (ODR): https://ec.europa.eu/consumers/odr/') }}</p>
                        <p>{{ __('Our email address can be found above in the legal notice.') }}</p>
                        <p>{{ __('We are not willing or obliged to participate in dispute resolution proceedings before a consumer arbitration board.') }}</p>

                        <h2>{{ __('Applicable Law') }}</h2>
                        <p>{{ __('This legal notice is governed by the laws of the United States and the state in which our company is registered.') }}</p>

                        <h2>{{ __('Severability Clause') }}</h2>
                        <p>{{ __('If individual provisions of this legal notice are or become invalid, the validity of the remaining provisions shall not be affected.') }}</p>

                        <h2>{{ __('Changes to This Legal Notice') }}</h2>
                        <p>{{ __('We reserve the right to update this legal notice from time to time. Any changes will be posted on this page with an updated revision date.') }}</p>

                        <div class="alert alert-info mt-4">
                            <h5 class="alert-heading">{{ __('Important Note') }}</h5>
                            <p class="mb-0">{{ __('This legal notice is provided for informational purposes only and does not constitute legal advice. For specific legal questions, please consult with a qualified attorney.') }}</p>
                        </div>
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

.legal-content p {
    line-height: 1.6;
    margin-bottom: 1rem;
}

.legal-content strong {
    color: #616161;
    font-weight: 600;
}

.alert-info {
    background-color: #e3f2fd;
    border-color: #bbdefb;
    color: #0d47a1;
}

.alert-heading {
    color: #0d47a1;
    font-weight: 600;
}
</style>
@endsection
