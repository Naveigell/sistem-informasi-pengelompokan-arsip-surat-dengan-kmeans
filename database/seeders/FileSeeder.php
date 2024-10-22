<?php

namespace Database\Seeders;

use App\Models\File;
use Faker\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;

class FileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = ["LPJ", "LPJK", "Surat Masuk", "Surat Keluar"];
        $faker = Factory::create('id_ID');

        foreach (range(1, 20) as $_) {
            $filename = $types[array_rand($types)] . " dari UKM kampus pada tanggal " . now()->addDays(rand(3, 100))->format('d F Y') . " oleh ketua " . $faker->unique()->name . " dan wakil ketua " . $faker->unique()->name . ".pdf";
            $uploadedName = Uuid::uuid4()->toString() . ".pdf";

            $uploadedFile = UploadedFile::fake()->createWithContent($filename, 'Hello World!');

            Storage::disk('public')->putFileAs('files', $uploadedFile, $uploadedName);

            File::create([
                "real_name" => $filename,
                "upload_name" => $uploadedName,
            ]);
        }
    }
}
