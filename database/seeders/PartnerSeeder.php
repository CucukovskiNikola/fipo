<?php

namespace Database\Seeders;

use App\Models\Partner;
use App\Models\User;
use Illuminate\Database\Seeder;

class PartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first admin user to assign as creator
        $adminUser = User::where('role', 'admin')->first();
        
        if (!$adminUser) {
            $this->command->error('No admin user found. Please create an admin user first.');
            return;
        }

        $partners = [
            [
                'title' => 'Mario\'s Italian Restaurant',
                'name_of_owner' => 'Mario Rossi',
                'category' => 'restaurant',
                'description' => 'Authentic Italian cuisine with traditional recipes passed down through generations. Famous for homemade pasta and wood-fired pizza.',
                'city' => 'New York',
                'zip_code' => '10001',
                'latitude' => 40.7505,
                'longitude' => -73.9934,
                'created_by' => $adminUser->id,
            ],
            [
                'title' => 'Grand Plaza Hotel',
                'name_of_owner' => 'Sarah Johnson',
                'category' => 'hotel',
                'description' => 'Luxury 4-star hotel in the heart of downtown with panoramic city views, spa services, and premium dining options.',
                'city' => 'Chicago',
                'zip_code' => '60601',
                'latitude' => 41.8781,
                'longitude' => -87.6298,
                'created_by' => $adminUser->id,
            ],
            [
                'title' => 'Central Mall Shopping Center',
                'name_of_owner' => null,
                'category' => 'shopping',
                'description' => 'Premier shopping destination featuring over 200 stores, restaurants, and entertainment venues under one roof.',
                'city' => 'Los Angeles',
                'zip_code' => '90210',
                'latitude' => 34.0901,
                'longitude' => -118.4065,
                'created_by' => $adminUser->id,
            ],
            [
                'title' => 'StarLight Cinema',
                'name_of_owner' => 'Michael Chen',
                'category' => 'entertainment',
                'description' => 'Modern movie theater with IMAX screens, comfortable seating, and the latest blockbuster films.',
                'city' => 'San Francisco',
                'zip_code' => '94102',
                'latitude' => 37.7749,
                'longitude' => -122.4194,
                'created_by' => $adminUser->id,
            ],
            [
                'title' => 'City General Hospital',
                'name_of_owner' => 'Dr. Amanda White',
                'category' => 'healthcare',
                'description' => 'Full-service hospital providing comprehensive medical care with state-of-the-art equipment and experienced staff.',
                'city' => 'Houston',
                'zip_code' => '77002',
                'latitude' => 29.7604,
                'longitude' => -95.3698,
                'created_by' => $adminUser->id,
            ],
            [
                'title' => 'Riverside Elementary School',
                'name_of_owner' => 'Principal Robert Davis',
                'category' => 'education',
                'description' => 'Top-rated elementary school focused on academic excellence and character development for grades K-5.',
                'city' => 'Phoenix',
                'zip_code' => '85001',
                'latitude' => 33.4484,
                'longitude' => -112.0740,
                'created_by' => $adminUser->id,
            ],
            [
                'title' => 'QuickFuel Gas Station',
                'name_of_owner' => 'Tony Martinez',
                'category' => 'gas_station',
                'description' => '24/7 gas station with convenience store, car wash services, and competitive fuel prices.',
                'city' => 'Philadelphia',
                'zip_code' => '19019',
                'latitude' => 39.9526,
                'longitude' => -75.1652,
                'created_by' => $adminUser->id,
            ],
            [
                'title' => 'First National Bank',
                'name_of_owner' => null,
                'category' => 'bank',
                'description' => 'Full-service bank offering personal and business banking, loans, and investment services.',
                'city' => 'San Antonio',
                'zip_code' => '78201',
                'latitude' => 29.4241,
                'longitude' => -98.4936,
                'created_by' => $adminUser->id,
            ],
            [
                'title' => 'Central Park Recreation Area',
                'name_of_owner' => null,
                'category' => 'park',
                'description' => 'Beautiful public park with walking trails, playgrounds, picnic areas, and sports facilities.',
                'city' => 'San Diego',
                'zip_code' => '92101',
                'latitude' => 32.7157,
                'longitude' => -117.1611,
                'created_by' => $adminUser->id,
            ],
            [
                'title' => 'FitLife Gym & Wellness',
                'name_of_owner' => 'Jessica Brown',
                'category' => 'gym',
                'description' => 'Modern fitness center with personal training, group classes, and wellness programs for all fitness levels.',
                'city' => 'Dallas',
                'zip_code' => '75201',
                'latitude' => 32.7767,
                'longitude' => -96.7970,
                'created_by' => $adminUser->id,
            ],
            [
                'title' => 'The Daily Grind Cafe',
                'name_of_owner' => 'Emma Wilson',
                'category' => 'cafe',
                'description' => 'Cozy neighborhood cafe serving artisanal coffee, fresh pastries, and light meals in a relaxed atmosphere.',
                'city' => 'Seattle',
                'zip_code' => '98101',
                'latitude' => 47.6062,
                'longitude' => -122.3321,
                'created_by' => $adminUser->id,
            ],
            [
                'title' => 'HealthPlus Pharmacy',
                'name_of_owner' => 'Dr. James Lee',
                'category' => 'pharmacy',
                'description' => 'Full-service pharmacy providing prescription medications, health consultations, and wellness products.',
                'city' => 'Denver',
                'zip_code' => '80202',
                'latitude' => 39.7392,
                'longitude' => -104.9903,
                'created_by' => $adminUser->id,
            ],
            [
                'title' => 'FreshMart Supermarket',
                'name_of_owner' => 'Lisa Garcia',
                'category' => 'supermarket',
                'description' => 'Family-owned grocery store featuring fresh produce, organic options, and local products at affordable prices.',
                'city' => 'Austin',
                'zip_code' => '73301',
                'latitude' => 30.2672,
                'longitude' => -97.7431,
                'created_by' => $adminUser->id,
            ],
            [
                'title' => 'St. Mary\'s Community Church',
                'name_of_owner' => 'Pastor David Thompson',
                'category' => 'church',
                'description' => 'Welcoming community church offering weekly services, youth programs, and community outreach initiatives.',
                'city' => 'Jacksonville',
                'zip_code' => '32099',
                'latitude' => 30.3322,
                'longitude' => -81.6557,
                'created_by' => $adminUser->id,
            ],
            [
                'title' => 'Downtown Public Library',
                'name_of_owner' => 'Head Librarian Karen Moore',
                'category' => 'library',
                'description' => 'Modern public library with extensive book collection, computer access, and community programming.',
                'city' => 'Indianapolis',
                'zip_code' => '46204',
                'latitude' => 39.7684,
                'longitude' => -86.1581,
                'created_by' => $adminUser->id,
            ]
        ];

        foreach ($partners as $partnerData) {
            Partner::create($partnerData);
        }

        $this->command->info('Created ' . count($partners) . ' partner records successfully!');
    }
}