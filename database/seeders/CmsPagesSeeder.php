<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class CmsPagesSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'slug' => 'flights',
                'title' => 'Flights',
                'content' => '<p>Search and book affordable flights with Flightandticketing.</p>',
                'menu_location' => 'top',
                'menu_order' => 1,
                'custom_url' => '/',
                'meta_title' => 'Flights | Flightandticketing',
                'meta_description' => 'Find and book cheap flights with Flightandticketing by Mymore Travels.',
            ],
            [
                'slug' => 'hotels',
                'title' => 'Hotels',
                'content' => '<p>Discover hotels and stays for your next trip. Hotel booking options are coming soon.</p>',
                'menu_location' => 'top',
                'menu_order' => 2,
                'custom_url' => null,
                'meta_title' => 'Hotels | Flightandticketing',
                'meta_description' => 'Browse hotel options and travel stays with Flightandticketing.',
            ],
            [
                'slug' => 'travel-insurance',
                'title' => 'Travel Insurance',
                'content' => '<p>Protect your journey with travel insurance options. Contact us to learn more about coverage for your trip.</p>',
                'menu_location' => 'top',
                'menu_order' => 3,
                'custom_url' => null,
                'meta_title' => 'Travel Insurance | Flightandticketing',
                'meta_description' => 'Learn about travel insurance options available through Flightandticketing.',
            ],
            [
                'slug' => 'bookings',
                'title' => 'Bookings',
                'content' => '<p>Manage your flight bookings and reservations. Log in to view your booking history or contact support for assistance.</p>',
                'menu_location' => 'top',
                'menu_order' => 4,
                'custom_url' => null,
                'meta_title' => 'Bookings | Flightandticketing',
                'meta_description' => 'View and manage your flight bookings with Flightandticketing.',
            ],
            [
                'slug' => 'about-us',
                'title' => 'About Us',
                'content' => '<p>Welcome to Flightandticketing by Mymore Travels. We help travellers find cheap flights, compare fares, and book with confidence.</p>',
                'menu_location' => 'footer',
                'menu_order' => 1,
                'custom_url' => null,
                'meta_title' => 'About Us | Flightandticketing',
                'meta_description' => 'Learn more about Flightandticketing and Mymore Travels.',
            ],
            [
                'slug' => 'faq',
                'title' => 'FAQ',
                'content' => '<h2>Frequently Asked Questions</h2><p><strong>How do I book a flight?</strong></p><p>Search for your route on our home page, select a flight, and complete passenger details and payment.</p><p><strong>How can I contact support?</strong></p><p>Visit our Contact Us page or call our support lines.</p>',
                'menu_location' => 'footer',
                'menu_order' => 2,
                'custom_url' => null,
                'meta_title' => 'FAQ | Flightandticketing',
                'meta_description' => 'Frequently asked questions about booking flights with Flightandticketing.',
            ],
            [
                'slug' => 'contact-us',
                'title' => 'Contact Us',
                'content' => '<p>Get in touch with Flightandticketing support.</p><p><strong>Phone:</strong> (+234) 802-200-8793</p><p><strong>Phone:</strong> (+234) 802-288-8721</p><p><strong>WhatsApp:</strong> +2348022008793</p>',
                'menu_location' => 'footer',
                'menu_order' => 3,
                'custom_url' => null,
                'meta_title' => 'Contact Us | Flightandticketing',
                'meta_description' => 'Contact Flightandticketing support for booking help and enquiries.',
            ],
            [
                'slug' => 'terms-and-conditions',
                'title' => 'Terms & Conditions',
                'content' => '<p>These Terms &amp; Conditions govern your use of Flightandticketing. By using our website and services, you agree to these terms.</p>',
                'menu_location' => 'footer',
                'menu_order' => 4,
                'custom_url' => null,
                'meta_title' => 'Terms & Conditions | Flightandticketing',
                'meta_description' => 'Read the Terms & Conditions for using Flightandticketing.',
            ],
            [
                'slug' => 'privacy-policy',
                'title' => 'Privacy Policy',
                'content' => '<p>This Privacy Policy explains how Flightandticketing collects, uses, and protects your personal information.</p>',
                'menu_location' => 'footer',
                'menu_order' => 5,
                'custom_url' => null,
                'meta_title' => 'Privacy Policy | Flightandticketing',
                'meta_description' => 'Read how Flightandticketing handles your personal data.',
            ],
            [
                'slug' => 'cookies-policy',
                'title' => 'Cookies Policy',
                'content' => '<p>This Cookies Policy explains how Flightandticketing uses cookies and similar technologies on our website.</p>',
                'menu_location' => 'footer',
                'menu_order' => 6,
                'custom_url' => null,
                'meta_title' => 'Cookies Policy | Flightandticketing',
                'meta_description' => 'Learn how Flightandticketing uses cookies on our website.',
            ],
        ];

        foreach ($pages as $data) {
            Page::updateOrCreate(
                ['slug' => $data['slug']],
                array_merge($data, [
                    'status' => 'published',
                    'is_system' => true,
                ])
            );
        }
    }
}
