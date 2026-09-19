<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            ['name' => 'María García', 'email' => 'maria.garcia@mail.com', 'phone' => '+54 11 1234-5678', 'address' => 'Calle Florida 123, CABA', 'total_spent' => 1500.00],
            ['name' => 'Juan Pérez', 'email' => 'juan.perez@mail.com', 'phone' => '+54 11 2345-6789', 'address' => 'Av. 9 de Julio 456, CABA', 'total_spent' => 750.00],
            ['name' => 'Ana Rodríguez', 'email' => 'ana.rodriguez@mail.com', 'phone' => '+54 11 3456-7890', 'address' => 'Av. Libertador 789, CABA', 'total_spent' => 3200.00],
            ['name' => 'Carlos López', 'email' => 'carlos.lopez@mail.com', 'phone' => '+54 11 4567-8901', 'address' => 'Calle Maipú 234, CABA', 'total_spent' => 12000.00],
            ['name' => 'Sofia Martínez', 'email' => 'sofia.martinez@mail.com', 'phone' => '+54 11 5678-9012', 'address' => 'Av. San Martín 567, CABA', 'total_spent' => 450.00],
            ['name' => 'Diego Fernández', 'email' => 'diego.fernandez@mail.com', 'phone' => '+54 11 6789-0123', 'address' => 'Calle Paraná 890, CABA', 'total_spent' => 0.00],
            ['name' => 'Laura Gómez', 'email' => 'laura.gomez@mail.com', 'phone' => '+54 11 7890-1234', 'address' => 'Av. Belgrano 1234, CABA', 'total_spent' => 8500.00],
            ['name' => 'Martín Torres', 'email' => 'martin.torres@mail.com', 'phone' => '+54 11 8901-2345', 'address' => 'Calle Reconquista 567, CABA', 'total_spent' => 200.00],
            ['name' => 'Florencia Silva', 'email' => 'florencia.silva@mail.com', 'phone' => '+54 11 9012-3456', 'address' => 'Av. Callao 890, CABA', 'total_spent' => 6000.00],
            ['name' => 'Pablo Ramírez', 'email' => 'pablo.ramirez@mail.com', 'phone' => '+54 11 0123-4567', 'address' => 'Calle Sarmiento 1234, CABA', 'total_spent' => 900.00],
            ['name' => 'Valentina Morales', 'email' => 'valentina.morales@mail.com', 'phone' => '+54 11 1111-2222', 'address' => 'Av. Corrientes 100, CABA', 'total_spent' => 15000.00],
            ['name' => 'Lucas Benítez', 'email' => 'lucas.benitez@mail.com', 'phone' => '+54 11 2222-3333', 'address' => 'Calle Lavalle 200, CABA', 'total_spent' => 100.00],
            ['name' => 'Camila Rojas', 'email' => 'camila.rojas@mail.com', 'phone' => '+54 11 3333-4444', 'address' => 'Av. Rivadavia 300, CABA', 'total_spent' => 4500.00],
            ['name' => 'Matías Herrera', 'email' => 'matias.herrera@mail.com', 'phone' => '+54 11 4444-5555', 'address' => 'Calle Uruguay 400, CABA', 'total_spent' => 300.00],
            ['name' => 'Agustina Vega', 'email' => 'agustina.vega@mail.com', 'phone' => '+54 11 5555-6666', 'address' => 'Av. Pueyrredón 500, CABA', 'total_spent' => 0.00],
            ['name' => 'Nicolás Castro', 'email' => 'nicolas.castro@mail.com', 'phone' => '+54 11 6666-7777', 'address' => 'Calle Ayacucho 600, CABA', 'total_spent' => 7200.00],
            ['name' => 'Mariana Díaz', 'email' => 'mariana.diaz@mail.com', 'phone' => '+54 11 7777-8888', 'address' => 'Av. Scalabrini Ortiz 700, CABA', 'total_spent' => 1800.00],
            ['name' => 'Facundo Romero', 'email' => 'facundo.romero@mail.com', 'phone' => '+54 11 8888-9999', 'address' => 'Calle Junín 800, CABA', 'total_spent' => 50.00],
            ['name' => 'Luciana Medina', 'email' => 'luciana.medina@mail.com', 'phone' => '+54 11 9999-0000', 'address' => 'Av. Córdoba 900, CABA', 'total_spent' => 3500.00],
            ['name' => 'Tomás Guerrero', 'email' => 'tomas.guerrero@mail.com', 'phone' => '+54 11 1010-2020', 'address' => 'Calle Viamonte 1000, CABA', 'total_spent' => 0.00],
            ['name' => 'Julieta Suárez', 'email' => 'julieta.suarez@mail.com', 'phone' => '+54 11 2020-3030', 'address' => 'Av. Santa Fe 1100, CABA', 'total_spent' => 11000.00],
            ['name' => 'Santiago Ortiz', 'email' => 'santiago.ortiz@mail.com', 'phone' => '+54 11 3030-4040', 'address' => 'Calle Tucumán 1200, CABA', 'total_spent' => 650.00],
            ['name' => 'Milagros Navarro', 'email' => 'milagros.navarro@mail.com', 'phone' => '+54 11 4040-5050', 'address' => 'Av. Alem 1300, CABA', 'total_spent' => 2800.00],
            ['name' => 'Joaquín Luna', 'email' => 'joaquin.luna@mail.com', 'phone' => '+54 11 5050-6060', 'address' => 'Calle Esmeralda 1400, CABA', 'total_spent' => 950.00],
            ['name' => 'Delfina Ríos', 'email' => 'delfina.rios@mail.com', 'phone' => '+54 11 6060-7070', 'address' => 'Av. Leandro N. Alem 1500, CABA', 'total_spent' => 0.00],
            ['name' => 'Benjamín Molina', 'email' => 'benjamin.molina@mail.com', 'phone' => '+54 11 7070-8080', 'address' => 'Calle Bartolomé Mitre 1600, CABA', 'total_spent' => 4200.00],
            ['name' => 'Antonella Paz', 'email' => 'antonella.paz@mail.com', 'phone' => '+54 11 8080-9090', 'address' => 'Av. de Mayo 1700, CABA', 'total_spent' => 150.00],
            ['name' => 'Thiago Acosta', 'email' => 'thiago.acosta@mail.com', 'phone' => '+54 11 9090-0101', 'address' => 'Calle Perú 1800, CABA', 'total_spent' => 0.00],
            ['name' => 'Catalina Sosa', 'email' => 'catalina.sosa@mail.com', 'phone' => '+54 11 1212-2323', 'address' => 'Av. Independencia 1900, CABA', 'total_spent' => 8000.00],
            ['name' => 'Gael Figueroa', 'email' => 'gael.figueroa@mail.com', 'phone' => '+54 11 2323-3434', 'address' => 'Calle San Juan 2000, CABA', 'total_spent' => 2500.00],
            ['name' => 'Emma Cabrera', 'email' => 'emma.cabrera@mail.com', 'phone' => '+54 11 3434-4545', 'address' => 'Av. Caseros 2100, CABA', 'total_spent' => 0.00],
            ['name' => 'Liam Campos', 'email' => 'liam.campos@mail.com', 'phone' => '+54 11 4545-5656', 'address' => 'Calle Entre Ríos 2200, CABA', 'total_spent' => 1300.00],
            ['name' => 'Isabella Vidal', 'email' => 'isabella.vidal@mail.com', 'phone' => '+54 11 5656-6767', 'address' => 'Av. San Juan 2300, CABA', 'total_spent' => 0.00],
            ['name' => 'Oliver Giménez', 'email' => 'oliver.gimenez@mail.com', 'phone' => '+54 11 6767-7878', 'address' => 'Calle Lima 2400, CABA', 'total_spent' => 5500.00],
            ['name' => 'Mia Peralta', 'email' => 'mia.peralta@mail.com', 'phone' => '+54 11 7878-8989', 'address' => 'Av. Belgrano 2500, CABA', 'total_spent' => 0.00],
            ['name' => 'Noah Domínguez', 'email' => 'noah.dominguez@mail.com', 'phone' => '+54 11 8989-9090', 'address' => 'Calle Bolívar 2600, CABA', 'total_spent' => 900.00],
            ['name' => 'Ava Carrizo', 'email' => 'ava.carrizo@mail.com', 'phone' => '+54 11 9090-1111', 'address' => 'Av. Brasil 2700, CABA', 'total_spent' => 0.00],
            ['name' => 'Liam Núñez', 'email' => 'liam.nunez@mail.com', 'phone' => '+54 11 1313-2424', 'address' => 'Calle Chile 2800, CABA', 'total_spent' => 7000.00],
            ['name' => 'Zoe Espinoza', 'email' => 'zoe.espinoza@mail.com', 'phone' => '+54 11 2424-3535', 'address' => 'Av. Juan de Garay 2900, CABA', 'total_spent' => 0.00],
            ['name' => 'Mateo Reyes', 'email' => 'mateo.reyes@mail.com', 'phone' => '+54 11 3535-4646', 'address' => 'Calle Humberto 1 3000, CABA', 'total_spent' => 4800.00],
            ['name' => 'Emilia Cabrera', 'email' => 'emilia.cabrera@mail.com', 'phone' => '+54 11 4646-5757', 'address' => 'Av. Paseo Colón 3100, CABA', 'total_spent' => 0.00],
            ['name' => 'Leo Blanco', 'email' => 'leo.blanco@mail.com', 'phone' => '+54 11 5757-6868', 'address' => 'Calle Defensa 3200, CABA', 'total_spent' => 3300.00],
            ['name' => 'Martina Aguirre', 'email' => 'martina.aguirre@mail.com', 'phone' => '+54 11 6868-7979', 'address' => 'Av. Independencia 3300, CABA', 'total_spent' => 0.00],
            ['name' => 'Julián Fuentes', 'email' => 'julian.fuentes@mail.com', 'phone' => '+54 11 7979-8080', 'address' => 'Calle Carlos Calvo 3400, CABA', 'total_spent' => 10500.00],
            ['name' => 'Sara Guzmán', 'email' => 'sara.guzman@mail.com', 'phone' => '+54 11 8080-9191', 'address' => 'Av. Entre Ríos 3500, CABA', 'total_spent' => 0.00],
            ['name' => 'Alejandro Roldán', 'email' => 'alejandro.roldan@mail.com', 'phone' => '+54 11 9191-0202', 'address' => 'Calle San José 3600, CABA', 'total_spent' => 6000.00],
            ['name' => 'Rocío Méndez', 'email' => 'rocio.mendez@mail.com', 'phone' => '+54 11 0202-1313', 'address' => 'Av. San Juan 3700, CABA', 'total_spent' => 0.00],
            ['name' => 'Dante Villanueva', 'email' => 'dante.villanueva@mail.com', 'phone' => '+54 11 1414-2525', 'address' => 'Calle Independencia 3800, CABA', 'total_spent' => 2200.00],
            ['name' => 'Abril Paredes', 'email' => 'abril.paredes@mail.com', 'phone' => '+54 11 2525-3636', 'address' => 'Av. La Plata 3900, CABA', 'total_spent' => 0.00],
            ['name' => 'Gabriel Quinteros', 'email' => 'gabriel.quinteros@mail.com', 'phone' => '+54 11 3636-4747', 'address' => 'Calle Rioja 4000, CABA', 'total_spent' => 500.00],
        ];

        foreach ($clients as $clientData) {
            $client = Client::create([
                'name' => $clientData['name'],
                'email' => $clientData['email'],
                'phone' => $clientData['phone'],
                'address' => $clientData['address'],
                'total_spent' => $clientData['total_spent'],
            ]);
            $client->recalculateTier();
        }
    }
}
