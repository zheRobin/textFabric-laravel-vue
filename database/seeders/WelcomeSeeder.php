<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WelcomeData;

class WelcomeSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'firstBlock' => [
                'title' => [
                    'en' => "Welcome to your Jetstream application!",
                    'de' => "Willkommen in Ihrer Jetstream-Anwendung!"
                ],
                'value' => [
                    'en' => "Laravel Jetstream provides a beautiful, robust starting point for your next Laravel application. Laravel is designed to help you build your application using a development environment that is simple, powerful, and enjoyable. We believe you should love expressing your creativity through programming, so we have spent time carefully crafting the Laravel ecosystem to be a breath of fresh air. We hope you love it.",
                    'de' => "Laravel Jetstream bietet einen schönen, robusten Ausgangspunkt für Ihre nächste Laravel-Anwendung. Laravel wurde entwickelt, um Ihnen beim Aufbau Ihrer Anwendung mit einer einfachen, leistungsstarken und angenehmen Entwicklungsumgebung zu helfen. Wir glauben, dass Sie Ihre Kreativität beim Programmieren lieben sollten, daher haben wir viel Zeit darauf verwendet, das Laravel-Ökosystem sorgfältig zu gestalten, damit es eine frische Brise ist. Wir hoffen, dass es Ihnen gefällt."
                ],
                'link' => [
                    'name' => [
                        'en' => null,
                        'de' => null
                    ],
                    'value' => null
                ],
                'icon' => "fa-solid fa-video"
            ],
            'secondBlock' => [
                'title' => [
                    'en' => "Documentation",
                    'de' => "Dokumentation"
                ],
                'value' => [
                    'en' => "Laravel has wonderful documentation covering every aspect of the framework. Whether you're new to the framework or have previous experience, we recommend reading all of the documentation from beginning to end.",
                    'de' => "Laravel verfügt über eine wunderbare Dokumentation, die jeden Aspekt des Frameworks abdeckt. Egal, ob Sie neu im Framework sind oder bereits Erfahrung haben, wir empfehlen Ihnen, die gesamte Dokumentation von Anfang bis Ende zu lesen."
                ],
                'link' => [
                    'name' => [
                        'en' => "Explore the documentation",
                        'de' => "Erkunden Sie die Dokumentation"
                    ],
                    'value' => "https://laravel.com/docs"
                ],
                'icon' => "fa-solid fa-video"
            ],
            'thirdBlock' => [
                'title' => [
                    'en' => "Laracasts",
                    'de' => "Laracasts"
                ],
                'value' => [
                    'en' => "Laracasts offers thousands of video tutorials on Laravel, PHP, and JavaScript development. Check them out, see for yourself, and massively level up your development skills in the process.",
                    'de' => "Laracasts bietet Tausende von Video-Tutorials zur Laravel-, PHP- und JavaScript-Entwicklung. Schauen Sie sie sich an, überzeugen Sie sich selbst und steigern Sie Ihre Entwicklungsfähigkeiten massiv."
                ],
                'link' => [
                    'name' => [
                        'en' => "Start watching Laracasts",
                        'de' => "Beginnen Sie mit Laracasts"
                    ],
                    'value' => "https://laracasts.com"
                ],
                'icon' => "fa-solid fa-video"
            ],
            'fourthBlock' => [
                'title' => [
                    'en' => "Tailwind",
                    'de' => "Tailwind"
                ],
                'value' => [
                    'en' => "Laravel Jetstream is built with Tailwind, an amazing utility-first CSS framework that doesn't get in your way. You'll be amazed how easily you can build and maintain fresh, modern designs with this wonderful framework at your fingertips.",
                    'de' => "Laravel Jetstream basiert auf Tailwind, einem erstaunlichen Utility-First-CSS-Framework, das Sie nicht behindert. Sie werden erstaunt sein, wie einfach Sie mit diesem wunderbaren Framework frische, moderne Designs erstellen und pflegen können."
                ],
                'link' => [
                    'name' => [
                        'en' => null,
                        'de' => null
                    ],
                    'value' => "https://tailwindcss.com/"
                ],
                'icon' => "fa-solid fa-video"
            ],
            'fifthBlock' => [
                'title' => [
                    'en' => "Authentication",
                    'de' => "Authentifizierung"
                ],
                'value' => [
                    'en' => "Authentication and registration views are included with Laravel Jetstream, as well as support for user email verification and resetting forgotten passwords. So, you're free to get started with what matters most: building your application.",
                    'de' => "Die Ansichten für die Authentifizierung und Registrierung sind in Laravel Jetstream enthalten, ebenso wie die Unterstützung für die Überprüfung der Benutzer-E-Mail und das Zurücksetzen vergessener Passwörter. Sie können also ohne Hindernisse mit dem beginnen, was am wichtigsten ist: dem Aufbau Ihrer Anwendung."
                ],
                'link' => [
                    'name' => [
                        'en' => null,
                        'de' => null
                    ],
                    'value' => ""
                ],
                'icon' => "fa-solid fa-video"
            ],
        ];

        foreach ($data as $block => $content) {
            WelcomeData::create([
                'block_name' => $block,
                'title' => $content['title'],
                'value' => $content['value'],
                'link_name' => $content['link']['name'] ?? null,
                'link' => $content['link']['value'] ?? null,
                'icon' => $content['icon'],
            ]);
        }
    }
}

