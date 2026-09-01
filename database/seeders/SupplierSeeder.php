<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            // Original 5
            [
                'name' => 'Royal Canine Global',
                'contact_person' => 'Jean-Pierre Laurent',
                'email' => 'partners@royalcanine.com',
                'phone' => '+33 4 66 73 33 00',
                'address' => 'Aimargues, France',
                'category' => 'Alimento',
                'status' => 'Active',
            ],
            [
                'name' => 'Premium Pet Accessories',
                'contact_person' => 'Sarah Jenkins',
                'email' => 'sales@premiumpet.uk',
                'phone' => '+44 20 7946 0123',
                'address' => 'London, United Kingdom',
                'category' => 'Accesorios',
                'status' => 'Active',
            ],
            [
                'name' => 'Higiene & Salud Pro',
                'contact_person' => 'Carlos Rodriguez',
                'email' => 'info@higienepro.es',
                'phone' => '+34 91 123 4567',
                'address' => 'Madrid, España',
                'category' => 'Higiene',
                'status' => 'Active',
            ],
            [
                'name' => 'Veterinaria del Litoral',
                'contact_person' => 'Juan Pérez',
                'email' => 'juan@vetlitoral.com',
                'phone' => '+54 342 456-7890',
                'address' => 'Av. Principal 123, Santa Fe',
                'category' => 'Otros',
                'status' => 'Active',
            ],
            [
                'name' => 'Nordic Fish Oils',
                'contact_person' => 'Erik Svensson',
                'email' => 'erik@nordicoils.no',
                'phone' => '+47 21 00 00 00',
                'address' => 'Oslo, Norway',
                'category' => 'Alimento',
                'status' => 'Inactive',
            ],
            // 15 adicionales
            ['name' => 'PetFood Argentina', 'contact_person' => 'Marta Sánchez', 'email' => 'ventas@petfoodar.com', 'phone' => '+54 11 5555-1001', 'address' => 'Buenos Aires, Argentina', 'category' => 'Alimento', 'status' => 'Active'],
            ['name' => 'Accesorios Patagónicos', 'contact_person' => 'Roberto Díaz', 'email' => 'info@accesoriospat.com', 'phone' => '+54 294 555-2002', 'address' => 'Bariloche, Argentina', 'category' => 'Accesorios', 'status' => 'Active'],
            ['name' => 'JuguetePet SA', 'contact_person' => 'Lucía Méndez', 'email' => 'lucia@juguetepep.com', 'phone' => '+54 11 5555-3003', 'address' => 'Córdoba, Argentina', 'category' => 'Otros', 'status' => 'Active'],
            ['name' => 'VetPharma International', 'contact_person' => 'Dr. Alan Foster', 'email' => 'alan@vetpharma.com', 'phone' => '+1 555-4004', 'address' => 'Chicago, USA', 'category' => 'Higiene', 'status' => 'Active'],
            ['name' => 'Natural Pet Treats', 'contact_person' => 'Emma Wilson', 'email' => 'emma@naturalpet.com', 'phone' => '+61 2 5555-5005', 'address' => 'Sydney, Australia', 'category' => 'Alimento', 'status' => 'Active'],
            ['name' => 'Grooming Pro Supplies', 'contact_person' => 'Marc Dupont', 'email' => 'marc@groomingpro.fr', 'phone' => '+33 1 5555-6006', 'address' => 'Paris, France', 'category' => 'Higiene', 'status' => 'Active'],
            ['name' => 'Mascotas del Sur', 'contact_person' => 'Gustavo Torres', 'email' => 'gustavo@mascotassur.com', 'phone' => '+54 291 555-7007', 'address' => 'Bahía Blanca, Argentina', 'category' => 'Alimento', 'status' => 'Active'],
            ['name' => 'AquaPet Supply', 'contact_person' => 'Yuki Tanaka', 'email' => 'yuki@aquapet.jp', 'phone' => '+81 3 5555-8008', 'address' => 'Tokyo, Japan', 'category' => 'Otros', 'status' => 'Active'],
            ['name' => 'Collar & Correa Co.', 'contact_person' => 'Ana Martínez', 'email' => 'ana@collarycorrea.com', 'phone' => '+54 11 5555-9009', 'address' => 'Rosario, Argentina', 'category' => 'Accesorios', 'status' => 'Inactive'],
            ['name' => 'BioPet Nutrition', 'contact_person' => 'Dr. Luis Herrera', 'email' => 'luis@biopet.com', 'phone' => '+52 55 5555-1010', 'address' => 'CDMX, Mexico', 'category' => 'Alimento', 'status' => 'Active'],
            ['name' => 'CamaPet Deluxe', 'contact_person' => 'Patricia Ríos', 'email' => 'patria@camapet.com', 'phone' => '+54 11 5555-1111', 'address' => 'Mendoza, Argentina', 'category' => 'Accesorios', 'status' => 'Active'],
            ['name' => 'Farmavet Express', 'contact_person' => 'Dr. Fernando López', 'email' => 'fernando@farmavet.com', 'phone' => '+54 11 5555-1212', 'address' => 'La Plata, Argentina', 'category' => 'Higiene', 'status' => 'Active'],
            ['name' => 'Exotic Pets World', 'contact_person' => 'Sofia Chen', 'email' => 'sofia@exoticpets.com', 'phone' => '+86 21 5555-1313', 'address' => 'Shanghai, China', 'category' => 'Otros', 'status' => 'Active'],
            ['name' => 'PetClean Solutions', 'contact_person' => 'Ricardo Vargas', 'email' => 'ricardo@petclean.com', 'phone' => '+56 2 5555-1414', 'address' => 'Santiago, Chile', 'category' => 'Higiene', 'status' => 'Active'],
            ['name' => 'DogWalk Essentials', 'contact_person' => 'Isabella Romano', 'email' => 'isabella@dogwalk.it', 'phone' => '+39 06 5555-1515', 'address' => 'Roma, Italy', 'category' => 'Accesorios', 'status' => 'Inactive'],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}