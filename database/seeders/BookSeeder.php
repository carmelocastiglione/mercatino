<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $books = [
            [
                'title' => 'I Promessi Sposi',
                'author' => 'Alessandro Manzoni',
                'isbn' => '978-8842652815',
                'description' => 'Un capolavoro della letteratura italiana ambientato in Lombardia nel XVII secolo.',
                'subject' => 'Italiano',
                'school_class' => '3°',
                'original_price' => 15.99,
                'cover_image' => 'https://via.placeholder.com/300x400?text=I+Promessi+Sposi',
            ],
            [
                'title' => 'Divina Commedia',
                'author' => 'Dante Alighieri',
                'isbn' => '978-8817154277',
                'description' => 'L\'opera più importante della letteratura italiana, divisa in tre cantiche.',
                'subject' => 'Italiano',
                'school_class' => '4°',
                'original_price' => 18.50,
                'cover_image' => 'https://via.placeholder.com/300x400?text=Divina+Commedia',
            ],
            [
                'title' => 'Il Decameron',
                'author' => 'Giovanni Boccaccio',
                'isbn' => '978-8845903953',
                'description' => 'Raccolta di 100 novelle ambientate durante la peste nera del 1348.',
                'subject' => 'Italiano',
                'school_class' => '3°',
                'original_price' => 14.99,
                'cover_image' => 'https://via.placeholder.com/300x400?text=Il+Decameron',
            ],
            [
                'title' => 'Matematica Blu 2.0',
                'author' => 'Massimo Bergamini',
                'isbn' => '978-8884883529',
                'description' => 'Manuale completo di matematica per le scuole superiori.',
                'subject' => 'Matematica',
                'school_class' => '2°',
                'original_price' => 32.50,
                'cover_image' => 'https://via.placeholder.com/300x400?text=Matematica+Blu',
            ],
            [
                'title' => 'Fisica per i Licei Scientifici',
                'author' => 'Claudio Romeni',
                'isbn' => '978-8856864236',
                'description' => 'Trattato di fisica moderna per gli studenti di liceo scientifico.',
                'subject' => 'Fisica',
                'school_class' => '3°',
                'original_price' => 38.00,
                'cover_image' => 'https://via.placeholder.com/300x400?text=Fisica',
            ],
            [
                'title' => 'La Chimica Organica',
                'author' => 'Paula Yurkanis Bruice',
                'isbn' => '978-8893853552',
                'description' => 'Manuale essenziale di chimica organica con esercizi pratici.',
                'subject' => 'Chimica',
                'school_class' => '4°',
                'original_price' => 45.99,
                'cover_image' => 'https://via.placeholder.com/300x400?text=Chimica+Organica',
            ],
            [
                'title' => 'Storia Medioevale e Moderna',
                'author' => 'Giorgio Candeloro',
                'isbn' => '978-8817031639',
                'description' => 'Una panoramica completa della storia europea dal Medioevo all\'Età Moderna.',
                'subject' => 'Storia',
                'school_class' => '2°',
                'original_price' => 28.00,
                'cover_image' => 'https://via.placeholder.com/300x400?text=Storia',
            ],
            [
                'title' => 'New Horizons',
                'author' => 'Caroline Krantz',
                'isbn' => '978-8808330900',
                'description' => 'Corso di inglese moderno con esercizi comunicativi e attività interattive.',
                'subject' => 'Inglese',
                'school_class' => '1°',
                'original_price' => 24.50,
                'cover_image' => 'https://via.placeholder.com/300x400?text=New+Horizons',
            ],
            [
                'title' => 'Français Écho',
                'author' => 'Jacky Girardet',
                'isbn' => '978-2090334456',
                'description' => 'Corso di francese per studenti principianti e intermedi.',
                'subject' => 'Francese',
                'school_class' => '1°',
                'original_price' => 22.99,
                'cover_image' => 'https://via.placeholder.com/300x400?text=Francais+Echo',
            ],
            [
                'title' => 'Etica Civica e Costituzione',
                'author' => 'Maurizio Bocchini',
                'isbn' => '978-8847236097',
                'description' => 'Studio approfondito della Costituzione Italiana e dell\'educazione civica.',
                'subject' => 'Educazione Civica',
                'school_class' => '2°',
                'original_price' => 19.50,
                'cover_image' => 'https://via.placeholder.com/300x400?text=Etica+Civica',
            ],
        ];

        foreach ($books as $book) {
            Book::create($book);
        }
    }
}
