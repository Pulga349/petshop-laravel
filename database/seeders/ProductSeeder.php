<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure categories exist
        $this->call(CategorySeeder::class);

        // Build category ID map
        $categoryIds = [];
        foreach (['Alimento', 'Accesorios', 'Higiene', 'Otros'] as $name) {
            $cat = DB::table('categories')->where('name', $name)->first();
            $categoryIds[$name] = $cat->id;
        }

        $suppliers = Supplier::all();

        $products = [
            // --- Alimentos ---
            ['name' => 'Premium Grain-Free Salmon', 'sku' => 'DG-SL-001', 'category_key' => 'Alimento', 'description' => 'High-protein salmon formula for adult dogs.', 'purchase_price' => 32.50, 'sale_price' => 54.99, 'supplier_name' => 'Royal Canine Global'],
            ['name' => 'Puppy Formula Chicken & Rice', 'sku' => 'DG-CH-002', 'category_key' => 'Alimento', 'description' => 'Complete nutrition for growing puppies.', 'purchase_price' => 28.00, 'sale_price' => 48.00, 'supplier_name' => 'Royal Canine Global'],
            ['name' => 'Senior Dog Light Formula', 'sku' => 'DG-SE-003', 'category_key' => 'Alimento', 'description' => 'Low-calorie formula for senior dogs.', 'purchase_price' => 30.00, 'sale_price' => 52.00, 'supplier_name' => 'Royal Canine Global'],
            ['name' => 'Cat Indoor Formula', 'sku' => 'CT-IN-010', 'category_key' => 'Alimento', 'description' => 'Hairball control formula for indoor cats.', 'purchase_price' => 25.00, 'sale_price' => 42.00, 'supplier_name' => 'PetFood Argentina'],
            ['name' => 'Cat Kitten Growth', 'sku' => 'CT-KT-011', 'category_key' => 'Alimento', 'description' => 'High-energy formula for kittens.', 'purchase_price' => 27.00, 'sale_price' => 45.00, 'supplier_name' => 'PetFood Argentina'],
            ['name' => 'Organic Freeze-Dried Liver', 'sku' => 'TR-OR-088', 'category_key' => 'Alimento', 'description' => '100% natural organic beef liver treats.', 'purchase_price' => 18.00, 'sale_price' => 32.50, 'supplier_name' => 'Veterinaria del Litoral'],
            ['name' => 'Dental Sticks Medium', 'sku' => 'TR-DN-089', 'category_key' => 'Alimento', 'description' => 'Dental health treats for medium dogs.', 'purchase_price' => 8.00, 'sale_price' => 15.00, 'supplier_name' => 'Natural Pet Treats'],
            ['name' => 'Grain-Free Turkey Recipe', 'sku' => 'DG-TK-004', 'category_key' => 'Alimento', 'description' => 'Turkey-based formula with sweet potato.', 'purchase_price' => 35.00, 'sale_price' => 59.99, 'supplier_name' => 'BioPet Nutrition'],
            ['name' => 'Fish & Vegetable Mix', 'sku' => 'DG-FV-005', 'category_key' => 'Alimento', 'description' => 'Balanced fish and vegetable formula.', 'purchase_price' => 29.00, 'sale_price' => 49.00, 'supplier_name' => 'Mascotas del Sur'],

            // --- Accesorios ---
            ['name' => 'Ortho-Comfort Pet Bed', 'sku' => 'BD-OR-042', 'category_key' => 'Accesorios', 'description' => 'Memory foam orthopedic bed for large breeds.', 'purchase_price' => 45.00, 'sale_price' => 89.00, 'supplier_name' => 'Premium Pet Accessories'],
            ['name' => 'Ultra-Reflective Safety Collar', 'sku' => 'CL-RF-012', 'category_key' => 'Accesorios', 'description' => 'High-visibility safety collar for night walks.', 'purchase_price' => 12.00, 'sale_price' => 24.99, 'supplier_name' => 'Premium Pet Accessories'],
            ['name' => 'Adjustable Nylon Leash 6ft', 'sku' => 'LS-NY-020', 'category_key' => 'Accesorios', 'description' => 'Durable nylon leash with padded handle.', 'purchase_price' => 8.00, 'sale_price' => 16.50, 'supplier_name' => 'Collar & Correa Co.'],
            ['name' => 'Stainless Steel Bowl Set', 'sku' => 'BW-SS-030', 'category_key' => 'Accesorios', 'description' => 'Non-slip stainless steel food and water bowls.', 'purchase_price' => 15.00, 'sale_price' => 28.00, 'supplier_name' => 'Premium Pet Accessories'],
            ['name' => 'Deluxe Memory Foam Bed', 'sku' => 'BD-MF-043', 'category_key' => 'Accesorios', 'description' => 'Premium memory foam bed with washable cover.', 'purchase_price' => 55.00, 'sale_price' => 99.00, 'supplier_name' => 'CamaPet Deluxe'],
            ['name' => 'Travel Carrier Bag', 'sku' => 'TC-BG-050', 'category_key' => 'Accesorios', 'description' => 'Airline-approved pet carrier bag.', 'purchase_price' => 35.00, 'sale_price' => 65.00, 'supplier_name' => 'Accesorios Patagónicos'],
            ['name' => 'Retractable Leash Pro', 'sku' => 'LS-RT-021', 'category_key' => 'Accesorios', 'description' => '16ft retractable leash with brake button.', 'purchase_price' => 18.00, 'sale_price' => 34.99, 'supplier_name' => 'DogWalk Essentials'],

            // --- Higiene ---
            ['name' => 'Advanced Dental Care Kit', 'sku' => 'HY-DN-005', 'category_key' => 'Higiene', 'description' => 'Professional grade dental hygiene kit for pets.', 'purchase_price' => 15.40, 'sale_price' => 29.99, 'supplier_name' => 'Higiene & Salud Pro'],
            ['name' => 'Hypoallergenic Shampoo 500ml', 'sku' => 'HY-SH-021', 'category_key' => 'Higiene', 'description' => 'Gentle formula for sensitive skin.', 'purchase_price' => 8.50, 'sale_price' => 18.00, 'supplier_name' => 'Higiene & Salud Pro'],
            ['name' => 'Pet Wipes Pack 100', 'sku' => 'HY-WP-022', 'category_key' => 'Higiene', 'description' => 'Alcohol-free cleansing wipes for daily use.', 'purchase_price' => 5.00, 'sale_price' => 12.00, 'supplier_name' => 'Grooming Pro Supplies'],
            ['name' => 'Flea & Tick Shampoo', 'sku' => 'HY-FT-023', 'category_key' => 'Higiene', 'description' => 'Natural flea and tick repellent shampoo.', 'purchase_price' => 10.00, 'sale_price' => 22.00, 'supplier_name' => 'PetClean Solutions'],
            ['name' => 'Ear Cleaning Solution', 'sku' => 'HY-EC-024', 'category_key' => 'Higiene', 'description' => 'Veterinarian recommended ear cleaning formula.', 'purchase_price' => 7.50, 'sale_price' => 16.00, 'supplier_name' => 'Grooming Pro Supplies'],
            ['name' => 'Deodorizing Spray', 'sku' => 'HY-DS-025', 'category_key' => 'Higiene', 'description' => 'Long-lasting deodorizing spray for pets.', 'purchase_price' => 6.00, 'sale_price' => 14.00, 'supplier_name' => 'PetClean Solutions'],

            // --- Salud ---
            ['name' => 'Nordic Omega-3 Fish Oil', 'sku' => 'HL-OM-009', 'category_key' => 'Alimento', 'description' => 'Pure arctic fish oil for coat health.', 'purchase_price' => 22.00, 'sale_price' => 42.00, 'supplier_name' => 'Nordic Fish Oils'],
            ['name' => 'Hip & Joint Supplement', 'sku' => 'HL-HJ-010', 'category_key' => 'Alimento', 'description' => 'Glucosamine chondroitin supplement for joints.', 'purchase_price' => 19.00, 'sale_price' => 36.00, 'supplier_name' => 'VetPharma International'],
            ['name' => 'Probiotic Digestive Support', 'sku' => 'HL-PR-011', 'category_key' => 'Alimento', 'description' => 'Daily probiotic for digestive health.', 'purchase_price' => 16.00, 'sale_price' => 30.00, 'supplier_name' => 'Farmavet Express'],
            ['name' => 'Calming Soft Chews', 'sku' => 'HL-CM-012', 'category_key' => 'Alimento', 'description' => 'Natural calming treats for anxiety.', 'purchase_price' => 14.00, 'sale_price' => 26.00, 'supplier_name' => 'VetPharma International'],

            // --- Juguetes ---
            ['name' => 'Rubber Chew Ball Large', 'sku' => 'JG-RB-060', 'category_key' => 'Otros', 'description' => 'Durable rubber ball for aggressive chewers.', 'purchase_price' => 7.00, 'sale_price' => 15.00, 'supplier_name' => 'JuguetePet SA'],
            ['name' => 'Rope Tug Toy', 'sku' => 'JG-RT-061', 'category_key' => 'Otros', 'description' => 'Multi-strand cotton rope tug toy.', 'purchase_price' => 5.00, 'sale_price' => 11.00, 'supplier_name' => 'JuguetePet SA'],
            ['name' => 'Interactive Treat Dispenser', 'sku' => 'JG-TD-062', 'category_key' => 'Otros', 'description' => 'Puzzle toy that dispenses treats.', 'purchase_price' => 12.00, 'sale_price' => 24.00, 'supplier_name' => 'JuguetePet SA'],
            ['name' => 'Squeaky Plush Duck', 'sku' => 'JG-PL-063', 'category_key' => 'Otros', 'description' => 'Soft plush toy with built-in squeaker.', 'purchase_price' => 6.00, 'sale_price' => 13.50, 'supplier_name' => 'JuguetePet SA'],
            ['name' => 'Frisbee Dog Toy', 'sku' => 'JG-FR-064', 'category_key' => 'Otros', 'description' => 'Floating frisbee for outdoor play.', 'purchase_price' => 8.00, 'sale_price' => 17.00, 'supplier_name' => 'JuguetePet SA'],

            // --- Acuarios ---
            ['name' => 'Aquarium Filter 20L', 'sku' => 'AQ-FL-070', 'category_key' => 'Otros', 'description' => 'Hang-on-back filter for 20L aquariums.', 'purchase_price' => 25.00, 'sale_price' => 45.00, 'supplier_name' => 'AquaPet Supply'],
            ['name' => 'LED Aquarium Light', 'sku' => 'AQ-LT-071', 'category_key' => 'Otros', 'description' => 'Full spectrum LED light for planted tanks.', 'purchase_price' => 30.00, 'sale_price' => 55.00, 'supplier_name' => 'AquaPet Supply'],
            ['name' => 'Fish Food Flakes Premium', 'sku' => 'AQ-FF-072', 'category_key' => 'Otros', 'description' => 'Nutrient-rich flakes for tropical fish.', 'purchase_price' => 4.00, 'sale_price' => 9.00, 'supplier_name' => 'AquaPet Supply'],

            // --- Exóticos ---
            ['name' => 'Hamster Wheel Silent', 'sku' => 'EX-HW-080', 'category_key' => 'Otros', 'description' => 'Silent running wheel for small rodents.', 'purchase_price' => 10.00, 'sale_price' => 20.00, 'supplier_name' => 'Exotic Pets World'],
            ['name' => 'Reptile Heat Lamp 100W', 'sku' => 'EX-HL-081', 'category_key' => 'Otros', 'description' => 'Basking heat lamp for reptile enclosures.', 'purchase_price' => 18.00, 'sale_price' => 35.00, 'supplier_name' => 'Exotic Pets World'],
            ['name' => 'Bird Seed Mix Premium', 'sku' => 'EX-BS-082', 'category_key' => 'Otros', 'description' => 'Premium seed mix for small birds.', 'purchase_price' => 6.00, 'sale_price' => 13.00, 'supplier_name' => 'Exotic Pets World'],
        ];

        foreach ($products as $pData) {
            $supplier = Supplier::where('name', $pData['supplier_name'])->first() ?? $suppliers->random();
            $categoryId = $categoryIds[$pData['category_key']] ?? $categoryIds['Otros'];

            Product::create([
                'name' => $pData['name'],
                'sku' => $pData['sku'],
                'category_id' => $categoryId,
                'category_type' => Category::class,
                'description' => $pData['description'],
                'purchase_price' => $pData['purchase_price'],
                'sale_price' => $pData['sale_price'],
                'supplier_id' => $supplier->id,
            ]);
        }
    }
}
